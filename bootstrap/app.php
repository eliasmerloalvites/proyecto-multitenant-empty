<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\ForceCentralGuard;
use App\Http\Middleware\PreventAccessFromTenant;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        // web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'no-tenant' => PreventAccessFromTenant::class,
        ]);

        $middleware->redirectGuestsTo(function () {
        if (tenant()) {
            return route('tenant.login');
        }

        return route('central.login');
    });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
