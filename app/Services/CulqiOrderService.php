<?php

namespace App\Services;

use App\Models\Client;
use App\Models\OrdenPago;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class CulqiOrderService
{
    private const BASE_URL = 'https://api.culqi.com/v2';

    // Límites de Culqi para el monto de una orden (en soles): S/6.00 - S/7,000.00.
    private const MONTO_MINIMO = 6.0;
    private const MONTO_MAXIMO = 7000.0;

    /**
     * Devuelve una orden de pago vigente para el ciclo actual del cliente,
     * reutilizando la existente si sigue pendiente y no expiró, o creando
     * una nueva en Culqi si no hay una, si la anterior expiró, o si el
     * monto cambió (ej. le acaban de fijar un precio_personalizado nuevo).
     *
     * Null si el monto del cliente es 0 o negativo (cortesía/sin cobro) —
     * no tiene sentido generarle una orden de pago.
     */
    public function ordenParaCicloActual(Client $client, string $periodo, float $monto): ?OrdenPago
    {
        if ($monto <= 0) {
            return null;
        }

        $ordenExistente = OrdenPago::where('client_id', $client->id)
            ->where('periodo', $periodo)
            ->first();

        $sigueVigente = $ordenExistente
            && $ordenExistente->estado === 'pending'
            && $ordenExistente->expires_at->isFuture()
            && (float) $ordenExistente->monto === $monto;

        if ($sigueVigente) {
            return $ordenExistente;
        }

        return $this->crearOrden($client, $periodo, $monto, $ordenExistente);
    }

    private function crearOrden(Client $client, string $periodo, float $monto, ?OrdenPago $ordenExistente): OrdenPago
    {
        if ($monto < self::MONTO_MINIMO || $monto > self::MONTO_MAXIMO) {
            throw new RuntimeException(
                "Monto S/{$monto} fuera del rango que acepta Culqi (S/" . self::MONTO_MINIMO . ' - S/' . self::MONTO_MAXIMO . ').'
            );
        }

        $secretKey = config('services.culqi.secret_key');
        if (! $secretKey) {
            throw new RuntimeException('CULQI_SECRET_KEY no está configurada.');
        }

        [$nombre, $apellido] = $this->partirNombre($client->razon_social);
        $expiraEn = now()->addDays(3);
        $orderNumber = $this->orderNumber($client, $periodo);

        $response = Http::withToken($secretKey)
            ->post(self::BASE_URL . '/orders', [
                'amount' => (int) round($monto * 100),
                'currency_code' => 'PEN',
                'description' => "Suscripción {$periodo} - {$client->razon_social}",
                'order_number' => $orderNumber,
                'expiration_date' => $expiraEn->timestamp,
                'client_details' => [
                    'first_name' => $nombre,
                    'last_name' => $apellido,
                    'email' => $client->email,
                    // Culqi lo exige; sin teléfono registrado se manda un
                    // placeholder — no bloquea el cobro, pero conviene pedirle
                    // al cliente su número real para que le lleguen los
                    // códigos CIP/QR por SMS.
                    'phone_number' => $client->telefono ?: '999999999',
                ],
            ]);

        if ($response->failed()) {
            $mensaje = $response->json('merchant_message') ?? $response->body();
            throw new RuntimeException("Culqi rechazó la orden: {$mensaje}");
        }

        $data = $response->json();

        return OrdenPago::updateOrCreate(
            ['client_id' => $client->id, 'periodo' => $periodo],
            [
                'culqi_order_id' => $data['id'],
                'order_number' => $orderNumber,
                'monto' => $monto,
                'estado' => 'pending',
                'payment_code' => $data['payment_code'] ?? null,
                'qr_url' => $data['qr'] ?? null,
                'expires_at' => $expiraEn,
                'paid_at' => null,
            ]
        );
    }

    /**
     * order_number debe ser único en Culqi y de máximo 36 caracteres. Se
     * incluye un sufijo aleatorio porque un mismo client_id+periodo puede
     * necesitar una SEGUNDA orden (la anterior expiró) y Culqi no acepta
     * reusar un order_number ya usado.
     */
    private function orderNumber(Client $client, string $periodo): string
    {
        return "c{$client->id}-{$periodo}-" . Str::lower(Str::random(6));
    }

    private function partirNombre(string $razonSocial): array
    {
        $partes = explode(' ', trim($razonSocial), 2);

        return [$partes[0], $partes[1] ?? $partes[0]];
    }
}
