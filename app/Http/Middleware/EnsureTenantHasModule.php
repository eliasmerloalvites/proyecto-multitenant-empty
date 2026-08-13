<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantHasModule
{
    /**
     * Bloquea el acceso a rutas de un módulo (productos, inventario, compras,
     * ventas, ...) que no esté incluido en el plan contratado por el tenant,
     * evitando el acceso directo por URL aunque el módulo esté oculto del menú.
     *
     * Debe ir DESPUÉS de InitializeTenancyByDomain para que tenant() esté disponible.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $module)
    {
        if (tenant_has_module($module)) {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Tu plan actual no incluye este módulo. Contacta al administrador para actualizar tu plan.',
            ], 403);
        }

        abort(403, 'Tu plan actual no incluye este módulo. Contacta al administrador para actualizar tu plan.');
    }
}
