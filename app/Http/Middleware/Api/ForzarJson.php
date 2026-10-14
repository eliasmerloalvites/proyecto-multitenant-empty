<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Todas las rutas de la API responden JSON, aunque el cliente olvide enviar
 * "Accept: application/json" (si no, los middleware y errores de Laravel
 * devolverian HTML o redirecciones al login). Tambien asigna un identificador
 * a cada peticion, que viaja en el header X-Request-Id y en los errores, para
 * poder rastrear un problema reportado por un usuario en los logs.
 */
class ForzarJson
{
    public function handle(Request $request, Closure $next): Response
    {
        $request->headers->set('Accept', 'application/json');

        $id = (string) Str::uuid();
        $request->attributes->set('request_id', $id);

        $response = $next($request);
        $response->headers->set('X-Request-Id', $id);

        return $response;
    }
}
