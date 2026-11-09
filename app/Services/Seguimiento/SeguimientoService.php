<?php

namespace App\Services\Seguimiento;

use App\Mail\SeguimientoMail;
use App\Models\Client;
use App\Models\SeguimientoCliente;
use App\Models\SeguimientoEvento;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Database\Models\Domain;

class SeguimientoService
{
    public const EN_PRUEBA = 'en_prueba';
    public const PRUEBA_VENCIDA = 'prueba_vencida';
    public const CONVERTIDO = 'convertido';
    public const SIN_PRUEBA = 'sin_prueba';

    public function __construct(private UsoClienteService $uso)
    {
    }

    public static function hoy(): Carbon
    {
        return Carbon::now('America/Lima')->startOfDay();
    }

    /** Ficha de seguimiento del cliente (se crea la primera vez que se necesita). */
    public function ficha(Client $client): SeguimientoCliente
    {
        $existente = SeguimientoCliente::where('client_id', $client->id)->first();

        if ($existente) {
            return $existente;
        }

        return SeguimientoCliente::create([
            'client_id' => $client->id,
            'etapa' => $client->pagos()->exists() ? 'convertido' : 'nuevo',
        ]);
    }

    /** En que punto comercial esta: prueba vigente, vencida, ya pago, o sin prueba. */
    public function segmento(Client $client, ?bool $tienePagos = null): string
    {
        $tienePagos ??= $client->pagos()->exists();

        if ($tienePagos) {
            return self::CONVERTIDO;
        }

        if ($client->trial_ends_at === null) {
            return self::SIN_PRUEBA;
        }

        return $client->enPeriodoDePrueba(self::hoy()) ? self::EN_PRUEBA : self::PRUEBA_VENCIDA;
    }

    public function diasRestantes(Client $client): ?int
    {
        if ($client->trial_ends_at === null) {
            return null;
        }

        return (int) self::hoy()->diffInDays($client->trial_ends_at->copy()->startOfDay(), false);
    }

    public function urlPanel(Client $client): ?string
    {
        $dominio = Domain::find($client->domain_id)?->domain;

        if (! $dominio) {
            return null;
        }

        $esquema = str_starts_with((string) config('app.url'), 'https') ? 'https' : 'http';

        return $esquema . '://' . $dominio;
    }

    /** Variables para las plantillas de este cliente. */
    public function variables(Client $client, ?string $asesor = null): array
    {
        $dias = $this->diasRestantes($client);

        return [
            'negocio' => $client->razon_social,
            'dias' => $dias === null ? '' : max($dias, 0),
            'fecha_fin' => $client->trial_ends_at?->format('d/m/Y') ?? '',
            'url' => $this->urlPanel($client) ?? '',
            'asesor' => $asesor ?: 'El equipo de Kael Tech',
        ];
    }

    /** Numero en formato internacional para wa.me (Peru por defecto). */
    public static function telefonoWhatsapp(?string $telefono): ?string
    {
        $digitos = preg_replace('/\D+/', '', (string) $telefono);

        if ($digitos === '') {
            return null;
        }

        if (strlen($digitos) === 9) {
            return '51' . $digitos;
        }

        return strlen($digitos) >= 10 ? $digitos : null;
    }

    public function evento(Client $client, string $tipo, ?string $asunto, ?string $detalle, ?object $user = null, ?string $clave = null): SeguimientoEvento
    {
        return SeguimientoEvento::create([
            'client_id' => $client->id,
            'tipo' => $tipo,
            'asunto' => $asunto,
            'detalle' => $detalle,
            'clave' => $clave,
            'user_id' => $user?->id,
            'user_nombre' => $user?->name,
            'created_at' => now(),
        ]);
    }

    /**
     * Envia un correo de seguimiento y lo deja en la bitacora.
     * Devuelve [bool enviado, string mensaje].
     */
    public function enviarCorreo(Client $client, string $asunto, string $cuerpo, ?object $user = null, ?string $clave = null): array
    {
        $ficha = $this->ficha($client);

        if ($ficha->no_contactar) {
            return [false, 'Este cliente pidió no recibir mensajes.'];
        }

        if (! filter_var($client->email, FILTER_VALIDATE_EMAIL)) {
            return [false, 'El cliente no tiene un correo válido registrado.'];
        }

        try {
            $url = $this->urlPanel($client);
            Mail::to($client->email)->send(new SeguimientoMail($asunto, $cuerpo, $url, 'Ir a mi panel'));
        } catch (\Throwable $e) {
            report($e);

            return [false, 'No se pudo enviar el correo: ' . $e->getMessage()];
        }

        $this->evento($client, $clave ? 'auto' : 'correo', $asunto, $cuerpo, $user, $clave);
        $this->marcarContacto($ficha);

        return [true, 'Correo enviado a ' . $client->email . '.'];
    }

    /** Un contacto cuenta: actualiza el ultimo contacto y saca al cliente de "nuevo". */
    public function marcarContacto(SeguimientoCliente $ficha): void
    {
        $ficha->ultimo_contacto_at = now();

        if ($ficha->etapa === 'nuevo') {
            $ficha->etapa = 'contactado';
        }

        $ficha->save();
    }

    public function cambiarEtapa(Client $client, string $etapa, ?string $motivo, ?object $user = null): SeguimientoCliente
    {
        $ficha = $this->ficha($client);
        $anterior = $ficha->etapa;

        $ficha->etapa = $etapa;
        $ficha->motivo_perdida = $etapa === 'perdido' ? $motivo : null;
        $ficha->save();

        if ($anterior !== $etapa) {
            $detalle = (SeguimientoCliente::ETAPAS[$anterior] ?? $anterior) . ' → ' . (SeguimientoCliente::ETAPAS[$etapa] ?? $etapa)
                . ($etapa === 'perdido' && $motivo ? ' (' . $motivo . ')' : '');
            $this->evento($client, 'etapa', 'Cambio de etapa', $detalle, $user);
        }

        return $ficha;
    }

    /** Mide el uso del cliente y lo guarda en su ficha. Devuelve la ficha. */
    public function medirUso(Client $client): SeguimientoCliente
    {
        $ficha = $this->ficha($client);
        $medicion = $this->uso->medir($client);

        if ($medicion['nivel'] === null) {
            return $ficha;
        }

        $ficha->uso_nivel = $medicion['nivel'];
        $ficha->uso_datos = ['conteo' => $medicion['datos'], 'resumen' => $medicion['resumen']];
        $ficha->ultima_actividad_at = $medicion['ultima_actividad'];
        $ficha->uso_medido_at = now();
        $ficha->save();

        // Si ya pago, esta convertido aunque nadie lo haya marcado.
        if ($this->segmento($client) === self::CONVERTIDO && $ficha->etapa !== 'convertido') {
            $this->cambiarEtapa($client, 'convertido', null);
        }

        return $ficha->refresh();
    }

    /** Clientes en seguimiento (con datos de contacto y pagos) para listados. */
    public function baseQuery()
    {
        return Client::query()
            ->join('domains as d', 'clients.domain_id', '=', 'd.id')
            ->join('tenants as t', 'd.tenant_id', '=', 't.id')
            ->leftJoin('seguimiento_clientes as s', 's.client_id', '=', 'clients.id')
            ->select(
                'clients.*',
                'd.domain as domain',
                't.plan',
                't.tipo_negocio',
                DB::raw('(SELECT COUNT(*) FROM pagos WHERE pagos.client_id = clients.id) as pagos_count'),
                's.etapa as s_etapa',
                's.motivo_perdida as s_motivo_perdida',
                's.proxima_accion_at as s_proxima_accion_at',
                's.proxima_accion_nota as s_proxima_accion_nota',
                's.ultimo_contacto_at as s_ultimo_contacto_at',
                's.no_contactar as s_no_contactar',
                's.uso_nivel as s_uso_nivel',
                's.uso_datos as s_uso_datos',
                's.ultima_actividad_at as s_ultima_actividad_at',
                's.uso_medido_at as s_uso_medido_at'
            );
    }
}
