<?php

namespace App\Services\AppMovil;

use App\Models\Tenant\EmpresaFacturacion;
use App\Services\AppMovil\Contracts\OtpChannel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Codigos de verificacion de un solo uso para la app movil.
 *
 * - El codigo se genera con un generador criptografico y en la base solo se
 *   guarda su HASH (HMAC con la clave de la aplicacion y el celular).
 * - Vence a los pocos minutos, tiene un maximo de intentos y se consume al
 *   usarse (un codigo no sirve dos veces).
 * - Limites por celular (espera entre envios y maximo por hora) calculados
 *   desde la propia tabla, sin depender de la cache.
 *
 * Esta clase no decide nada sobre la cuenta ni la sesion: solo emite y valida
 * codigos. El resultado de verificar() es un booleano; los motivos de fallo
 * no se distinguen a proposito (ver abajo) para no ayudar a un atacante.
 */
class OtpService
{
    public function __construct(private OtpChannel $canal)
    {
    }

    public const RESULTADO_ENVIADO = 'enviado';
    public const RESULTADO_ESPERAR = 'esperar';
    public const RESULTADO_LIMITE_HORA = 'limite_hora';

    /**
     * Emite un codigo nuevo para el celular (ya normalizado). Devuelve uno de
     * los RESULTADO_* y, cuando hay que esperar, los segundos restantes.
     *
     * @return array{resultado: string, esperar_segundos: int}
     */
    public function solicitar(string $celular, ?string $ip = null, ?string $dispositivoId = null): array
    {
        $espera = (int) config('app_movil.otp.espera_reenvio_segundos');
        $ahora = Carbon::now();

        $ultimo = DB::table('cuenta_movil_otp')
            ->where('CMO_Celular', $celular)
            ->orderByDesc('CMO_Id')
            ->first();

        if ($ultimo) {
            $segundos = Carbon::parse($ultimo->created_at)->diffInSeconds($ahora, true);

            if ($segundos < $espera) {
                return ['resultado' => self::RESULTADO_ESPERAR, 'esperar_segundos' => (int) ceil($espera - $segundos)];
            }
        }

        $enviosUltimaHora = DB::table('cuenta_movil_otp')
            ->where('CMO_Celular', $celular)
            ->where('created_at', '>=', $ahora->copy()->subHour())
            ->count();

        if ($enviosUltimaHora >= (int) config('app_movil.otp.max_envios_por_hora')) {
            return ['resultado' => self::RESULTADO_LIMITE_HORA, 'esperar_segundos' => 3600];
        }

        $longitud = (int) config('app_movil.otp.longitud');
        $codigo = str_pad((string) random_int(0, (10 ** $longitud) - 1), $longitud, '0', STR_PAD_LEFT);

        // Un solo codigo vigente por celular: el nuevo invalida los anteriores.
        DB::table('cuenta_movil_otp')
            ->where('CMO_Celular', $celular)
            ->whereNull('CMO_ConsumidoEn')
            ->update(['CMO_ConsumidoEn' => $ahora]);

        DB::table('cuenta_movil_otp')->insert([
            'CMO_Celular' => $celular,
            'CMO_CodigoHash' => $this->hash($codigo, $celular),
            'CMO_Intentos' => 0,
            'CMO_ExpiraEn' => $ahora->copy()->addMinutes((int) config('app_movil.otp.ttl_minutos')),
            'CMO_Ip' => $ip,
            'CMO_DispositivoId' => $dispositivoId,
            'created_at' => $ahora,
            'updated_at' => $ahora,
        ]);

        $this->canal->enviar($celular, $codigo, $this->nombreTaller());

        return ['resultado' => self::RESULTADO_ENVIADO, 'esperar_segundos' => $espera];
    }

    /**
     * Valida el codigo. Devuelve true solo si es correcto, esta vigente y no
     * se uso; en ese caso lo consume. Cualquier otra situacion (no existe,
     * vencido, ya usado, demasiados intentos, codigo incorrecto) devuelve
     * false sin distinguirlas.
     */
    public function verificar(string $celular, string $codigo): bool
    {
        $otp = DB::table('cuenta_movil_otp')
            ->where('CMO_Celular', $celular)
            ->whereNull('CMO_ConsumidoEn')
            ->where('CMO_ExpiraEn', '>', Carbon::now())
            ->orderByDesc('CMO_Id')
            ->first();

        if (! $otp) {
            return false;
        }

        if ((int) $otp->CMO_Intentos >= (int) config('app_movil.otp.max_intentos')) {
            return false;
        }

        // Se cuenta el intento ANTES de comparar, para que un codigo no se
        // pueda adivinar con intentos en paralelo.
        DB::table('cuenta_movil_otp')->where('CMO_Id', $otp->CMO_Id)->increment('CMO_Intentos');

        if (! hash_equals($otp->CMO_CodigoHash, $this->hash($codigo, $celular))) {
            return false;
        }

        // Consumo atomico: si dos peticiones validan el mismo codigo a la vez,
        // solo una logra marcarlo como usado.
        $consumido = DB::table('cuenta_movil_otp')
            ->where('CMO_Id', $otp->CMO_Id)
            ->whereNull('CMO_ConsumidoEn')
            ->update(['CMO_ConsumidoEn' => Carbon::now()]);

        return $consumido === 1;
    }

    private function hash(string $codigo, string $celular): string
    {
        return hash_hmac('sha256', $celular . '|' . $codigo, (string) config('app.key'));
    }

    private function nombreTaller(): string
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        return (string) ($empresa?->nombre_comercial ?: $empresa?->razon_social ?: 'Taller');
    }
}
