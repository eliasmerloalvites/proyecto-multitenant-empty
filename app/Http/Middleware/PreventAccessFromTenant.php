<?php

namespace App\Http\Middleware;

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
    public function handle(Request $request, Closure $next): Response
    {
        /* $centralDomains = config('tenancy.central_domains', []);

        if (! in_array($request->getHost(), $centralDomains)) {
            abort(404);
        } */
        if (tenant() !== null) {
            abort(404);
        }
        return $next($request);
    }
}
