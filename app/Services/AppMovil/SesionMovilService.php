<?php

namespace App\Services\AppMovil;

use App\Models\TenantTallerMotos\CuentaMovil;
use Illuminate\Support\Carbon;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Emision, rotacion y revocacion de las sesiones (tokens Sanctum) de la app.
 *
 * Cada token lleva dos habilidades: "cliente" y "tenant:{id}". La segunda es
 * una defensa adicional: los tokens ya viven en la base de su taller, pero
 * aqui tambien se comprueba (ver EnsureCuentaMovilActiva) que el token fue
 * emitido para el taller que atiende la peticion.
 */
class SesionMovilService
{
    /**
     * Crea un token nuevo para la cuenta. Si ya tiene mas dispositivos activos
     * que el maximo permitido, revoca los mas antiguos.
     */
    public function emitir(CuentaMovil $cuenta, string $dispositivo): array
    {
        $max = (int) config('app_movil.sesion.max_dispositivos');

        $cuenta->tokens()->orderBy('id')->get()
            ->slice(0, max(0, $cuenta->tokens()->count() - $max + 1))
            ->each->delete();

        $vence = Carbon::now()->addDays((int) config('app_movil.sesion.dias_vigencia'));
        $nombre = config('app_movil.sesion.nombre_token') . ':' . mb_substr($dispositivo, 0, 60);

        $nuevo = $cuenta->createToken($nombre, ['cliente', 'tenant:' . tenant('id')], $vence);

        $cuenta->forceFill(['CMV_UltimoAcceso' => Carbon::now()])->save();

        return ['token' => $nuevo->plainTextToken, 'vence_en' => $vence];
    }

    /** Cambia el token actual por uno nuevo (mismo dispositivo) y revoca el anterior. */
    public function rotar(CuentaMovil $cuenta, PersonalAccessToken $actual): array
    {
        $dispositivo = (string) str($actual->name)->after(':');

        $resultado = $this->emitir($cuenta, $dispositivo);
        $actual->delete();

        return $resultado;
    }

    public function cerrarSesion(PersonalAccessToken $token): void
    {
        $token->delete();
    }

    public function cerrarTodas(CuentaMovil $cuenta): void
    {
        $cuenta->tokens()->delete();
    }
}
