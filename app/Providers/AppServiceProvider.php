<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use App\Models\Tenant\EmpresaFacturacion;

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
