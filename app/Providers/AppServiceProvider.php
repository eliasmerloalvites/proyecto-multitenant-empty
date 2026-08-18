<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Models\Tenant\EmpresaFacturacion;
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

        /* $this->loadMigrationsFrom([
            database_path('migrations/central')
        ]);
        // Si la conexión es la central, llama a los de central
        if (config('database.default') === 'central') {
            $this->call(\Database\Seeders\Central\DatabaseSeeder::class);
        } else {
            // Si no, llama a los de tenant
            $this->call(\Database\Seeders\Tenant\DatabaseSeeder::class);
        } */
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
    }
}
