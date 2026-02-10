<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ForceCentralGuard
{
    public function handle($request, Closure $next)
    {
        Auth::shouldUse('central'); // o 'central' si lo definiste así
        return $next($request);
    }
}
