<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\OrdenPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CulqiWebhookController extends Controller
{
    private const BASE_URL = 'https://api.culqi.com/v2';

    /**
     * Culqi no firma sus webhooks (no hay header HMAC que verificar, a
     * diferencia de Stripe) — su propio patrón de seguridad documentado es
     * ignorar el body que llega y volver a consultar el evento por su ID
     * contra su API con nuestra llave secreta, así confirmamos que el
     * evento es real antes de marcar algo como pagado. Nunca confiar en
     * $request->input('data') directo.
     */
    public function handle(Request $request)
    {
        $eventId = $request->input('id');
        $tipo = $request->input('type');

        if (! $eventId || ! str_starts_with($eventId, 'evt_')) {
            return response()->json(['error' => 'payload inválido'], 400);
        }

        $secretKey = config('services.culqi.secret_key');
        $response = Http::withToken($secretKey)->get(self::BASE_URL . "/events/{$eventId}");

        if ($response->failed()) {
            Log::warning("Webhook Culqi: no se pudo confirmar el evento {$eventId} (tipo {$tipo}).", [
                'status' => $response->status(),
            ]);

            return response()->json(['error' => 'no se pudo confirmar el evento'], 422);
        }

        $evento = $response->json();

        if (($evento['type'] ?? null) !== 'order.status.changed') {
            // Evento real pero de un tipo que no nos interesa (ej. cargos
            // sueltos fuera del flujo de órdenes). Se responde 200 para que
            // Culqi no siga reintentando algo que no vamos a procesar.
            return response()->json(['ok' => true]);
        }

        $orden = $evento['data'] ?? [];
        $culqiOrderId = $orden['id'] ?? null;
        $estado = $orden['state'] ?? null;

        $ordenPago = OrdenPago::where('culqi_order_id', $culqiOrderId)->first();

        if (! $ordenPago) {
            Log::warning("Webhook Culqi: orden {$culqiOrderId} no corresponde a ninguna orden_pagos registrada.");

            return response()->json(['ok' => true]);
        }

        if ($estado !== 'paid') {
            $ordenPago->update(['estado' => $estado === 'expired' ? 'expired' : $ordenPago->estado]);

            return response()->json(['ok' => true]);
        }

        if ($ordenPago->estado === 'paid') {
            // Ya procesado (Culqi puede reenviar el mismo webhook).
            return response()->json(['ok' => true]);
        }

        $client = $ordenPago->client;

        if ($client->pagos()->where('periodo', $ordenPago->periodo)->exists()) {
            // Ya existe un Pago para este periodo (ej. lo registró un admin
            // a mano mientras la orden seguía pendiente). No duplicar.
            $ordenPago->update(['estado' => 'paid', 'paid_at' => now()]);

            return response()->json(['ok' => true]);
        }

        $pago = $client->pagos()->create([
            'monto' => $ordenPago->monto,
            'periodo' => $ordenPago->periodo,
            'fecha_pago' => now(),
            'metodo_pago' => 'pasarela',
            'nota' => "Culqi — orden {$ordenPago->order_number}",
            'registrado_por' => null,
        ]);

        $ordenPago->update(['estado' => 'paid', 'paid_at' => now()]);

        AuditLog::registrar(
            'pago.registrado',
            'Pago automático vía Culqi de S/ ' . number_format($ordenPago->monto, 2) . ' de "' . $client->razon_social . '" (periodo ' . $ordenPago->periodo . ')',
            ['client_id' => $client->id, 'periodo' => $ordenPago->periodo, 'monto' => (float) $ordenPago->monto, 'metodo_pago' => 'pasarela', 'pago_id' => $pago->id]
        );

        return response()->json(['ok' => true]);
    }
}
