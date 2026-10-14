<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\PreventAccessFromTenant;
use App\Http\Middleware\EnsureTenantIsActive;
use App\Http\Middleware\EnsureTenantHasModule;
use App\Http\Middleware\EnsureClientHasPaidCycle;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Culqi llama a /webhooks/culqi server-a-server, sin sesión ni
        // token CSRF (no es un form del navegador). La autenticidad se
        // valida aparte, reconsultando el evento contra la API de Culqi
        // (ver CulqiWebhookController) en vez de depender de CSRF.
        $middleware->validateCsrfTokens(except: [
            'webhooks/culqi',
        ]);

        $middleware->alias([
            'no-tenant' => PreventAccessFromTenant::class,
            'tenant.active' => EnsureTenantIsActive::class,
            'tenant.module' => EnsureTenantHasModule::class,
            'tenant.pagado' => EnsureClientHasPaidCycle::class,

            // API movil de clientes (ver routes/api_*.php).
            'api.json' => \App\Http\Middleware\Api\ForzarJson::class,
            'api.taller' => \App\Http\Middleware\Api\TallerAppMovilDisponible::class,
            'api.cuenta' => \App\Http\Middleware\Api\CuentaMovilActiva::class,
        ]);

        $middleware->redirectGuestsTo(function () {
        if (tenant()) {
            $tenantName = str_replace(tenant()->tipo_negocio . '_','',tenant()->id);
            return route('tenant.login', ['tenant' => $tenantName]);
        }

        return route('central.login');
    });
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Toda excepcion de una ruta /api/* se responde con el formato de error
        // unico de la API movil, sin exponer detalles internos.
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            if ($request->is('api/*')) {
                return \App\Support\ApiErrores::responder($e);
            }

            return null;
        });
    })->create();
