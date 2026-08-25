<?php

namespace App\Http\Middleware;

use App\Models\Client;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class EnsureClientHasPaidCycle
{
    /**
     * Rutas a las que SIEMPRE se puede entrar aunque el ciclo esté vencido
     * (si no, el usuario quedaría atrapado sin poder ni ver "Mi
     * Facturación" ni cerrar sesión).
     */
    private const RUTAS_EXCLUIDAS = [
        'tenant.facturacion.index',
        'tenant.facturacion.pagar',
        'tenant.logout',
        'tenant.personal.getimagen',
    ];

    /**
     * Bloqueo "suave": cuando el ciclo de pago está vencido pero el tenant
     * TODAVÍA no llegó al día de gracia (eso lo maneja EnsureTenantIsActive,
     * que sí corta todo el acceso), cualquier ruta del panel redirige a
     * "Mi Facturación" en vez de dejar operar con la cuenta vencida.
     *
     * Debe ir DESPUÉS de InitializeTenancyByDomain y dentro del grupo
     * auth:tenant (necesita el usuario logueado para no interferir con el
     * login/landing pública).
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs(self::RUTAS_EXCLUIDAS)) {
            return $next($request);
        }

        $client = Client::where('tenant_id', tenant('id'))->first();

        if (! $client) {
            return $next($request);
        }

        $estado = $client->estadoCicloActual(Carbon::now('America/Lima'));

        if ($estado !== 'vencido') {
            return $next($request);
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'error' => 'Tu pago está vencido. Regulariza tu cuenta para continuar.',
                'redirect' => route('tenant.facturacion.index'),
            ], 402);
        }

        return redirect()->route('tenant.facturacion.index')
            ->with('warning', 'Tu pago está vencido. Regulariza tu cuenta para seguir usando la plataforma sin interrupciones.');
    }
}
