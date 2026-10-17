<?php

namespace App\Services\AppMovil;

use App\Models\TenantTallerMotos\CuentaMovil;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\InvitacionMovil;
use App\Models\TenantTallerMotos\Moto;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Vinculacion de cuentas de la app con motos. Tres caminos:
 *
 *  1. TELEFONO: al ingresar, se buscan en el taller las placas registradas con
 *     el celular verificado de la cuenta y se vinculan con alcance
 *     REGISTROS_PROPIOS. No da acceso a registros hechos con otro celular.
 *  2. INVITACION: codigo generado por el personal en el mostrador; vincula con
 *     alcance COMPLETO.
 *  3. SOLICITUD: la cuenta pide vincular una placa; queda PENDIENTE hasta que
 *     el taller la apruebe (alcance COMPLETO) o la rechace.
 *
 * Nada de esto confia en datos que envia la app salvo la placa solicitada,
 * que solo crea una solicitud pendiente: nunca da acceso por si sola.
 */
class VinculacionMotoService
{
    private const MAX_SOLICITUDES_PENDIENTES = 5;
    private const MINUTOS_ENTRE_SINCRONIZACIONES = 15;
    private const DIAS_VIGENCIA_INVITACION = 7;
    private const ALFABETO_INVITACION = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const LONGITUD_INVITACION = 8;

    public function __construct(private RegistrosMotoService $registros)
    {
    }

    // ------------------------------------------------------------------
    // 1. Por telefono
    // ------------------------------------------------------------------

    /** Busca motos por el celular de la cuenta, como maximo cada 15 minutos (o siempre con $forzar). */
    public function sincronizarSiHaceFalta(CuentaMovil $cuenta, bool $forzar = false): void
    {
        $ultima = $cuenta->CMV_SincronizadoEn;

        if (! $forzar && $ultima && $ultima->diffInMinutes(Carbon::now()) < self::MINUTOS_ENTRE_SINCRONIZACIONES) {
            return;
        }

        $this->sincronizarPorTelefono($cuenta);
    }

    public function sincronizarPorTelefono(CuentaMovil $cuenta): void
    {
        foreach ($this->registros->placasPorCelular($cuenta->CMV_Celular) as $placa => $datos) {
            $moto = $this->motoDePlaca($placa, $datos['descripcion']);

            // Si la cuenta ya tuvo un vinculo con esta moto (aunque sea revocado o
            // rechazado) se respeta: la sincronizacion no revive lo que se dio de baja.
            if (CuentaMovilMoto::where('CMV_Id', $cuenta->CMV_Id)->where('MOT_Id', $moto->MOT_Id)->exists()) {
                continue;
            }

            // Si el taller ya respaldo a otra cuenta como dueña de esta moto, la
            // coincidencia de celular no basta: queda para aprobacion.
            $otraCuentaRespaldada = CuentaMovilMoto::where('MOT_Id', $moto->MOT_Id)
                ->where('CMM_Estado', CuentaMovilMoto::VERIFICADA)
                ->where('CMM_Alcance', CuentaMovilMoto::ALCANCE_COMPLETO)
                ->where('CMV_Id', '!=', $cuenta->CMV_Id)
                ->exists();

            CuentaMovilMoto::create([
                'CMV_Id' => $cuenta->CMV_Id,
                'MOT_Id' => $moto->MOT_Id,
                'CMM_Estado' => $otraCuentaRespaldada ? CuentaMovilMoto::PENDIENTE : CuentaMovilMoto::VERIFICADA,
                'CMM_Metodo' => CuentaMovilMoto::METODO_TELEFONO,
                'CMM_Alcance' => CuentaMovilMoto::ALCANCE_PROPIOS,
                'CMM_VerificadaEn' => $otraCuentaRespaldada ? null : Carbon::now(),
                'CMM_Nota' => $otraCuentaRespaldada ? 'Coincide el celular, pero la moto ya esta vinculada a otra cuenta.' : null,
            ]);
        }

        $cuenta->forceFill(['CMV_SincronizadoEn' => Carbon::now()])->save();
    }

    // ------------------------------------------------------------------
    // 2. Invitacion del taller
    // ------------------------------------------------------------------

    /** Genera un codigo para la moto y devuelve el texto plano (se muestra una sola vez). */
    public function crearInvitacion(Moto $moto, ?int $usuarioId): string
    {
        do {
            $codigo = '';
            for ($i = 0; $i < self::LONGITUD_INVITACION; $i++) {
                $codigo .= self::ALFABETO_INVITACION[random_int(0, strlen(self::ALFABETO_INVITACION) - 1)];
            }
            $hash = $this->hashInvitacion($codigo);
        } while (InvitacionMovil::where('INV_CodigoHash', $hash)->exists());

        InvitacionMovil::create([
            'MOT_Id' => $moto->MOT_Id,
            'INV_CodigoHash' => $hash,
            'INV_ExpiraEn' => Carbon::now()->addDays(self::DIAS_VIGENCIA_INVITACION),
            'INV_CreadaPor' => $usuarioId,
        ]);

        return $codigo;
    }

    /**
     * Canjea un codigo. Devuelve el vinculo creado, o null si el codigo no
     * existe, vencio, fue revocado o ya se uso (sin distinguirlos).
     */
    public function canjearInvitacion(CuentaMovil $cuenta, string $codigo): ?CuentaMovilMoto
    {
        $codigo = strtoupper(trim($codigo));

        if (! preg_match('/^[' . self::ALFABETO_INVITACION . ']{' . self::LONGITUD_INVITACION . '}$/', $codigo)) {
            return null;
        }

        return DB::transaction(function () use ($cuenta, $codigo) {
            $invitacion = InvitacionMovil::where('INV_CodigoHash', $this->hashInvitacion($codigo))->lockForUpdate()->first();

            if (! $invitacion || ! $invitacion->estaVigente()) {
                return null;
            }

            $invitacion->forceFill(['INV_UsadaEn' => Carbon::now(), 'INV_UsadaPor' => $cuenta->CMV_Id])->save();

            // La invitacion respalda a la cuenta, asi que reemplaza cualquier
            // estado anterior (pendiente, rechazado o revocado).
            return CuentaMovilMoto::updateOrCreate(
                ['CMV_Id' => $cuenta->CMV_Id, 'MOT_Id' => $invitacion->MOT_Id],
                [
                    'CMM_Estado' => CuentaMovilMoto::VERIFICADA,
                    'CMM_Metodo' => CuentaMovilMoto::METODO_INVITACION,
                    'CMM_Alcance' => CuentaMovilMoto::ALCANCE_COMPLETO,
                    'CMM_VerificadaPor' => $invitacion->INV_CreadaPor,
                    'CMM_VerificadaEn' => Carbon::now(),
                    'CMM_Nota' => null,
                ]
            );
        });
    }

    public function revocarInvitacion(InvitacionMovil $invitacion): void
    {
        if ($invitacion->INV_UsadaEn === null && $invitacion->INV_RevocadaEn === null) {
            $invitacion->forceFill(['INV_RevocadaEn' => Carbon::now()])->save();
        }
    }

    // ------------------------------------------------------------------
    // 3. Solicitud manual + decision del taller
    // ------------------------------------------------------------------

    public const SOLICITUD_CREADA = 'creada';
    public const SOLICITUD_LIMITE = 'limite';

    /**
     * La cuenta pide vincular una placa. Si ya tiene un vinculo vigente o
     * pendiente con esa moto no cambia nada. Siempre queda PENDIENTE: nunca da
     * acceso por si sola.
     */
    public function solicitar(CuentaMovil $cuenta, string $placaNormalizada, ?string $nota): string
    {
        $pendientes = CuentaMovilMoto::where('CMV_Id', $cuenta->CMV_Id)->where('CMM_Estado', CuentaMovilMoto::PENDIENTE)->count();

        if ($pendientes >= self::MAX_SOLICITUDES_PENDIENTES) {
            return self::SOLICITUD_LIMITE;
        }

        $moto = $this->motoDePlaca($placaNormalizada, null);

        $existente = CuentaMovilMoto::where('CMV_Id', $cuenta->CMV_Id)->where('MOT_Id', $moto->MOT_Id)->first();

        if ($existente && in_array($existente->CMM_Estado, [CuentaMovilMoto::PENDIENTE, CuentaMovilMoto::VERIFICADA], true)) {
            return self::SOLICITUD_CREADA;
        }

        CuentaMovilMoto::updateOrCreate(
            ['CMV_Id' => $cuenta->CMV_Id, 'MOT_Id' => $moto->MOT_Id],
            [
                'CMM_Estado' => CuentaMovilMoto::PENDIENTE,
                'CMM_Metodo' => null,
                'CMM_Alcance' => CuentaMovilMoto::ALCANCE_PROPIOS,
                'CMM_VerificadaPor' => null,
                'CMM_VerificadaEn' => null,
                'CMM_Nota' => $nota ? mb_substr($nota, 0, 250) : null,
            ]
        );

        return self::SOLICITUD_CREADA;
    }

    public function aprobar(CuentaMovilMoto $vinculo, ?int $usuarioId): void
    {
        $vinculo->forceFill([
            'CMM_Estado' => CuentaMovilMoto::VERIFICADA,
            'CMM_Metodo' => CuentaMovilMoto::METODO_APROBACION,
            'CMM_Alcance' => CuentaMovilMoto::ALCANCE_COMPLETO,
            'CMM_VerificadaPor' => $usuarioId,
            'CMM_VerificadaEn' => Carbon::now(),
        ])->save();
    }

    public function rechazar(CuentaMovilMoto $vinculo, ?int $usuarioId): void
    {
        $vinculo->forceFill([
            'CMM_Estado' => CuentaMovilMoto::RECHAZADA,
            'CMM_VerificadaPor' => $usuarioId,
            'CMM_VerificadaEn' => Carbon::now(),
        ])->save();
    }

    /** Da de baja un vinculo (la cuenta suelta la moto, o el taller revoca el acceso). */
    public function revocar(CuentaMovilMoto $vinculo, ?int $usuarioId = null): void
    {
        $vinculo->forceFill([
            'CMM_Estado' => CuentaMovilMoto::REVOCADA,
            'CMM_VerificadaPor' => $usuarioId ?? $vinculo->CMM_VerificadaPor,
        ])->save();
    }

    // ------------------------------------------------------------------

    /** Busca la moto por placa normalizada o la crea; actualiza su descripcion si llega una. */
    public function motoDePlaca(string $placaNormalizada, ?string $descripcion): Moto
    {
        $moto = Moto::firstOrCreate(
            ['MOT_Placa' => $placaNormalizada],
            ['MOT_Descripcion' => $descripcion ? mb_substr($descripcion, 0, 150) : null]
        );

        if ($descripcion && ! $moto->MOT_Descripcion) {
            $moto->forceFill(['MOT_Descripcion' => mb_substr($descripcion, 0, 150)])->save();
        }

        return $moto;
    }

    private function hashInvitacion(string $codigo): string
    {
        return hash_hmac('sha256', 'invitacion|' . $codigo, (string) config('app.key'));
    }
}
