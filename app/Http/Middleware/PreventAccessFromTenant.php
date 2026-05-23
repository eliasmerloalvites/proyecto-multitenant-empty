<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Stancl\Tenancy\Facades\Tenancy;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessFromTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        
        $host = str_replace('www.', '', $request->getHost());
        $centralDomains = config('tenancy.central_domains');
        // DOMINIO CENTRAL
        if (in_array($host, $centralDomains)) {
            return $next($request);
        }

        $tenant = null;
    
        // SUBDOMINIOS
        foreach ($centralDomains as $centralDomain) {
            if (str_contains($host, '.' . $centralDomain)) {
                $subdomain = explode('.', $host)[0];
                $tenant = Tenant::find($subdomain);
                break;
            }
        }
        
        // DOMINIOS PERSONALIZADOS
        if (!$tenant) {
            $domain = \Stancl\Tenancy\Database\Models\Domain::where('domain', $host)->first();

            if ($domain) {
                $tenant = $domain->tenant;
            }
        }

        if (!$tenant) {
            abort(404);
        }

        tenancy()->initialize($tenant);

        return $next($request);
    }
}
