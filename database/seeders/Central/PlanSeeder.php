<?php

namespace Database\Seeders\Central;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Carga inicial de los 4 planes por cada tipo_negocio, desde
     * config/saas.php como base común. Es idempotente (updateOrCreate,
     * emparejando por tipo_negocio+key) para poder correrla varias veces
     * sin duplicar filas; una vez editados desde el panel, esos valores en
     * BD son la fuente de verdad y este seeder ya no se vuelve a correr en
     * producción para tenants existentes.
     */
    public function run(): void
    {
        $nombres = [
            'start' => 'Start',
            'basic' => 'Basic',
            'plus' => 'Plus',
            'empresarial' => 'Empresarial',
        ];

        // Nuevo tipo_negocio: agrégalo aquí para que tenga sus 4 planes
        // desde el primer despliegue (luego se ajustan precio/límites por
        // vertical desde el panel central de Planes).
        $tiposNegocio = ['tallermoto', 'generico'];

        foreach ($tiposNegocio as $tipoNegocio) {
            foreach (config('saas.plans') as $key => $plan) {
                Plan::updateOrCreate(
                    ['tipo_negocio' => $tipoNegocio, 'key' => $key],
                    [
                        'nombre' => $nombres[$key] ?? ucfirst($key),
                        'price' => $plan['price'] ?? 0,
                        'max_users' => $plan['max_users'],
                        'max_images' => $plan['max_images'],
                        'storage_limit_mb' => $plan['storage_limit_mb'],
                        'custom_domain_enabled' => $plan['custom_domain_enabled'],
                        'custom_branding' => $plan['custom_branding'],
                        'customizable' => $plan['data']['customizable'] ?? false,
                        'modules' => $plan['data']['modules'],
                        'limits' => $plan['data']['limits'],
                        'branding' => $plan['data']['branding'],
                    ]
                );
            }
        }
    }
}
