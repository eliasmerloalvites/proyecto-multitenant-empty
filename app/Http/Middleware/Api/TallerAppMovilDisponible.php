<?php

namespace App\Http\Middleware\Api;

use App\Support\ApiRespuesta;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * La API del taller solo responde si el taller esta activo y tiene contratado
 * el modulo "app_movil". Va DESPUES de que se identifica al taller (el tenant
 * ya esta inicializado). Reemplaza para la API a tenant.active y
 * tenant.module, que responden con otro formato de error.
 *
 * Se usa el mismo mensaje para "taller suspendido" y "modulo no contratado"
 * para no revelar el estado comercial del taller a quien no es su cliente.
 */
class TallerAppMovilDisponible
{
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = tenant();

        $disponible = $tenant
            && $tenant->status === 'activo'
            && tenant_has_module('app_movil');

        if (! $disponible) {
            return ApiRespuesta::error(
                'TALLER_NO_DISPONIBLE',
                'Este taller no esta disponible en la aplicacion por el momento.',
                403
            );
        }

        return $next($request);
    }
}
