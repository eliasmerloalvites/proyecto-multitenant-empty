<?php

namespace App\Http\Middleware\Api;

use App\Support\ApiRespuesta;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Va despues de auth:cliente. Comprueba que:
 *  - la cuenta sigue activa (una cuenta bloqueada pierde acceso de inmediato,
 *    aunque su token no haya vencido), y
 *  - el token fue emitido para ESTE taller (habilidad "tenant:{id}"), como
 *    defensa adicional ademas de que los tokens viven en la base del taller.
 */
class CuentaMovilActiva
{
    public function handle(Request $request, Closure $next): Response
    {
        $cuenta = $request->user('cliente');
        $token = $cuenta?->currentAccessToken();

        if (! $cuenta || ! $token) {
            return ApiRespuesta::error('NO_AUTENTICADO', 'Inicia sesion para continuar.', 401);
        }

        if (! $token->can('tenant:' . tenant('id'))) {
            return ApiRespuesta::error('NO_AUTENTICADO', 'Inicia sesion para continuar.', 401);
        }

        if (! $cuenta->estaActiva()) {
            return ApiRespuesta::error('CUENTA_NO_DISPONIBLE', 'Tu cuenta no esta disponible. Contacta al taller.', 403);
        }

        return $next($request);
    }
}
