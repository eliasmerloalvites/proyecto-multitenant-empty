<?php

namespace App\Http\Middleware;

use App\Services\TenantTallerMotos\FirmasMantenimientoService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Impide modificar un mantenimiento que ya firmo el cliente (ver
 * FirmasMantenimientoService). Se aplica a todo el grupo de rutas de
 * mantenimientos, pero solo actua en las que lo cambian: update, destroy,
 * actualizarestado, crop (subir foto) y destroyimagen (borrar foto).
 */
class BloquearMantenimientoFirmado
{
    private const ACCIONES = ['update', 'destroy', 'actualizarestado', 'crop', 'destroyimagen'];

    public function handle(Request $request, Closure $next): Response
    {
        $nombre = (string) optional($request->route())->getName();

        if (preg_match('/^tenant\.(?:mantenimientos|actividades)\.([a-z]+)\.([a-z]+)$/', $nombre, $m)
            && in_array($m[2], self::ACCIONES, true)
            && ($meta = FirmasMantenimientoService::tipoPorSlug($m[1]))) {
            $id = (int) $request->route($m[1]);

            if ($id && FirmasMantenimientoService::estaCerrado($meta['tabla'], $id)) {
                $mensaje = 'Este mantenimiento ya fue firmado por el cliente y está cerrado. Un administrador puede reabrirlo desde la sección Firmas.';

                // Mismas claves que leen las pantallas (error/message/msg).
                return $request->expectsJson() || $request->ajax()
                    ? response()->json(['error' => $mensaje, 'message' => $mensaje, 'msg' => $mensaje, 'status' => 0], 423)
                    : back()->with('error', $mensaje);
            }
        }

        return $next($request);
    }
}
