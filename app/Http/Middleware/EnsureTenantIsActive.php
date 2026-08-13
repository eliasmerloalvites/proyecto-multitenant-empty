<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureTenantIsActive
{
    /**
     * Bloquea el acceso completo al tenant (landing pública + panel + login)
     * cuando su estado comercial no es 'activo'. El estado se administra
     * desde el módulo de Clientes en el dominio central (Cliente/Tenant::status)
     * y se sincroniza entre ambas tablas allí.
     *
     * Debe ir DESPUÉS de InitializeTenancyByDomain en el grupo de rutas para
     * que el helper tenant() ya esté disponible.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $tenant = tenant();

        if (!$tenant || $tenant->status === 'activo') {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => $this->mensaje($tenant->status),
            ], 403);
        }

        return response()->view('tenant.suspended', [
            'status' => $tenant->status,
            'mensaje' => $this->mensaje($tenant->status),
        ], 403);
    }

    protected function mensaje(string $status): string
    {
        return $status === 'cancelado'
            ? 'Este servicio fue cancelado. Contacta al administrador de la plataforma para más información.'
            : 'Este servicio se encuentra suspendido temporalmente. Contacta al administrador de la plataforma para regularizar tu acceso.';
    }
}
