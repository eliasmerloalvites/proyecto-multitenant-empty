<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\Caja;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Transporte de correo vía la API HTTP de Brevo (puerto 443), en vez
        // de SMTP (puertos 587/465): el VPS de producción tiene esos puertos
        // bloqueados por el proveedor de hosting, así que se evita el
        // problema hablando por HTTPS en lugar de una conexión SMTP directa.
        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory())->create(
                new Dsn('brevo+api', 'default', env('BREVO_API_KEY'))
            );
        });

        /* Si la conexión es la central, llama a los de central
        if (config('database.default') === 'central') {
            $this->call(\Database\Seeders\Central\DatabaseSeeder::class);
        } else {
            // Si no, llama a los de tenant
            $this->call(\Database\Seeders\Tenant\DatabaseSeeder::class);
        } */

        // Los Feature tests solo cubren el lado central (Plan, Client,
        // Pago, Tenant, etc.) contra la BD dedicada
        // proyecto_multitenant_empty_test (ver phpunit.xml). Registrar esto
        // solo en 'testing' evita tocar el flujo normal de migrate en local
        // /producción, donde ya se corre manualmente con --path.
        if ($this->app->environment('testing')) {
            $this->loadMigrationsFrom(database_path('migrations/central'));
        }
        /* if(env('app.env') !== 'local') {
        URL::forceScheme('https');
        } */

        // El sidebar y el layout del panel (tallermoto/generico) usan $empresa
        // (logo, nombre, tema claro/oscuro) sin depender de que cada
        // controlador la pase manualmente. Se resuelve una sola vez por
        // request y se cachea en memoria para no repetir la consulta si la
        // vista se renderiza más de una vez en la misma respuesta.
        View::composer([
            'tenant_tallermoto.partials.sidebar',
            'tenant_generico.partials.sidebar',
            'tenant_tallermoto.layout.appAdminLte',
            'tenant_generico.layout.appAdminLte',
        ], function ($view) {
            static $empresa = null;
            static $resuelto = false;

            if (! $resuelto) {
                $resuelto = true;
                if (tenant() !== null) {
                    $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
                }
            }

            $view->with('empresa', $view->empresa ?? $empresa);
        });

        // Caja con la que opera el usuario logueado: el layout usa esto para
        // el indicador de la barra superior y para forzar el modal de
        // selección cuando hay más de una caja activa y todavía no eligió
        // ninguna. Ver tenant_caja_activa_id() en app/helpers.php.
        View::composer([
            'tenant_tallermoto.layout.appAdminLte',
            'tenant_generico.layout.appAdminLte',
        ], function ($view) {
            if (! tenant() || ! Auth::guard('tenant')->check()) {
                $view->with(['cajaActiva' => null, 'cajasDisponibles' => collect(), 'requiereSeleccionCaja' => false]);

                return;
            }

            // Todas las cajas activas (para ofrecer "aperturar" las que estén
            // cerradas), con su sesión abierta precargada si la tienen.
            $cajasDisponibles = Caja::where('CAJ_Status', 1)->with('sesionAbierta')->orderBy('CAJ_Nombre')->get();
            $cajasAbiertas = $cajasDisponibles->filter(fn ($c) => $c->sesionAbierta !== null);

            $cajaActivaId = tenant_caja_activa_id();
            $cajaActiva = $cajaActivaId ? $cajasDisponibles->firstWhere('CAJ_Id', $cajaActivaId) : null;

            $view->with([
                'cajaActiva' => $cajaActiva,
                'cajasDisponibles' => $cajasDisponibles,
                // Modal obligatorio solo cuando hay 2+ cajas YA aperturadas
                // y todavía no se eligió con cuál operar (si solo hay una
                // abierta se autoselecciona sola, sin pedir nada).
                'requiereSeleccionCaja' => $cajasAbiertas->count() > 1 && ! $cajaActiva,
            ]);
        });

        // Aviso de "tu pago está por vencer/vencido" en el dashboard del
        // tenant (banner no bloqueante — el bloqueo real cuando ya está
        // vencido lo hace el middleware EnsureClientHasPaidCycle). Mismo
        // Client::estadoCicloActual() que usa cobros:procesar del lado
        // central, para no tener dos fuentes de verdad sobre el estado del
        // ciclo de pago.
        View::composer([
            'tenant_tallermoto.partials.container',
            'tenant_generico.partials.container',
        ], function ($view) {
            static $resuelto = false;
            static $datos = ['estadoCicloPago' => null, 'fechaCicloPago' => null];

            if (! $resuelto) {
                $resuelto = true;

                if (tenant() && Auth::guard('tenant')->check()) {
                    $client = Client::where('tenant_id', tenant('id'))->first();

                    if ($client) {
                        $hoy = Carbon::now('America/Lima');
                        $datos = [
                            'estadoCicloPago' => $client->estadoCicloActual($hoy),
                            'fechaCicloPago' => $client->fechaCicloActual($hoy),
                        ];
                    }
                }
            }

            $view->with($datos);
        });
    }
}
