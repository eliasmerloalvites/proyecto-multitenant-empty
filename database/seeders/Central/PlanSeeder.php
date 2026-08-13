<?php

namespace Database\Seeders\Central;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Carga inicial de los 4 planes desde config/saas.php. Es idempotente
     * (updateOrCreate) para poder correrla varias veces sin duplicar filas;
     * una vez editados desde el panel, esos valores en BD son la fuente de
     * verdad y este seeder ya no se vuelve a correr en producción.
     */
    public function run(): void
    {
        $nombres = [
            'start' => 'Start',
            'basic' => 'Basic',
            'plus' => 'Plus',
            'empresarial' => 'Empresarial',
        ];

        foreach (config('saas.plans') as $key => $plan) {
            Plan::updateOrCreate(
                ['key' => $key],
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
