<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

/**
 * Igual que el "tenants:run" nativo de stancl/tenancy, pero filtrando solo
 * a los tenants del vertical tallermoto (tipo_negocio = 'tallermoto') — sin
 * esto, "tenants:run" sin --tenants corre contra TODOS los tenants,
 * incluidos los "generico", donde comandos especificos de taller de motos
 * (ej. la migracion que altera las tablas de mantenimiento, o los seeders
 * de Estado de Recepcion) fallarian porque esas tablas/roles no existen
 * ahi.
 *
 * Uso (mismo patron que tenants:run, solo que nunca hace falta --tenants):
 *   php artisan tenants:run-tallermoto migrate --option=path=database/migrations/tenant/tallermoto --option=force=1
 *   php artisan tenants:run-tallermoto db:seed --option=class=Database\\Seeders\\Tenant\\tallermoto\\RecepcionCategoriaSeeder --option=force=1
 */
class TenantsRunTallermotoCommand extends Command
{
    protected $signature = "tenants:run-tallermoto {commandname : El nombre del comando a ejecutar.}
                            {--argument=* : Argumentos para el comando (formato clave=valor).}
                            {--option=* : Opciones para el comando (formato clave=valor).}";

    protected $description = 'Corre un comando artisan para TODOS los tenants tallermoto (tipo_negocio = tallermoto), sin tener que pasar --tenants=<id> uno por uno.';

    public function handle(): int
    {
        $tenants = Tenant::where('tipo_negocio', 'tallermoto')->cursor();

        $parse = function (string $prefix = '') {
            return function (array $arguments, string $argument) use ($prefix) {
                [$key, $value] = explode('=', $argument, 2);
                $arguments[$prefix . $key] = $value;

                return $arguments;
            };
        };

        // ['foo=bar'] -> ['foo' => 'bar']
        $arguments = array_reduce($this->option('argument'), $parse(), []);
        // ['foo=bar'] -> ['--foo' => 'bar']
        $options = array_reduce($this->option('option'), $parse('--'), []);

        $total = 0;

        foreach ($tenants as $tenant) {
            $total++;
            $this->line("Tenant tallermoto: {$tenant->getTenantKey()}");

            $tenant->run(function () use ($arguments, $options) {
                $this->call($this->argument('commandname'), array_merge($arguments, $options));
            });
        }

        if ($total === 0) {
            $this->warn('No se encontro ningun tenant con tipo_negocio = tallermoto.');
        } else {
            $this->info("Listo. Comando corrido en {$total} tenant(s) tallermoto.");
        }

        return self::SUCCESS;
    }
}
