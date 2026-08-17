<?php

namespace App\Services;

use App\Mail\BienvenidaTenantMail;
use App\Models\Client;
use App\Models\Plan;
use App\Models\Tenant;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

/**
 * Aprovisiona un tenant nuevo de punta a punta: crea la BD del tenant, el
 * dominio, el registro comercial en `clients`, corre migraciones/seeders
 * propios del tipo de negocio y crea el usuario admin dentro del tenant.
 *
 * Única fuente de verdad para este flujo: la usan tanto el panel central
 * (ClientController, con staff logueado) como el autoregistro público
 * (RegistroController, sin login) para no duplicar esta lógica sensible.
 */
class TenantProvisioningService
{
    /**
     * @param  array  $data  Debe traer: tipo_negocio, plan, subdomain,
     *                       razon_social, ruc, email, password, billing_day.
     *                       Opcional: custom_domain (si no se manda subdomain).
     *
     * @throws \RuntimeException  Si el dominio ya existe.
     */
    public function provision(array $data): Tenant
    {
        $fullDomain = ! empty($data['custom_domain'])
            ? $data['custom_domain']
            : $data['subdomain'] . '.' . config('app.central_domain');

        if (Domain::where('domain', $fullDomain)->exists()) {
            throw new \RuntimeException('El dominio ya existe.');
        }

        $planConfig = saas_plans_config()[$data['plan']];

        $tenant = null;

        try {
            $tenantId = $data['tipo_negocio'] . '_' . Str::slug($data['subdomain'] ?? $data['razon_social']);

            $tenant = Tenant::create([
                'id' => $tenantId,
                'tipo_negocio' => $data['tipo_negocio'],
                'plan' => $data['plan'],
                'status' => 'activo',

                'max_users' => $planConfig['max_users'],
                'max_images' => $planConfig['max_images'],
                'storage_limit_mb' => $planConfig['storage_limit_mb'],

                'custom_domain_enabled' => $planConfig['custom_domain_enabled'],
                'custom_branding' => $planConfig['custom_branding'],
            ]);
            $tenant->refresh();

            foreach ($planConfig['data'] as $key => $value) {
                $tenant->{$key} = $value;
            }
            $tenant->save();

            $domain = $tenant->domains()->create(['domain' => $fullDomain]);

            Client::create([
                'tenant_id' => $tenant->id,
                'razon_social' => $data['razon_social'],
                'ruc' => $data['ruc'] ?? null,
                'email' => $data['email'],
                'billing_day' => $data['billing_day'],
                'domain_id' => $domain->id,
                'status' => 'activo',
            ]);

            $tenantId = $tenant->id;

            $tenant->run(function () use ($data, $tenantId) {
                $extraPath = 'database/migrations/tenant/' . $data['tipo_negocio'];
                if (is_dir(base_path($extraPath))) {
                    Artisan::call('migrate', [
                        '--path' => $extraPath,
                        '--force' => true,
                    ]);
                }

                Artisan::call('db:seed', [
                    '--class' => 'Database\\Seeders\\Tenant\\' . $data['tipo_negocio'] . '\\DatabaseSeeder',
                    '--force' => true,
                ]);

                $user = User::create([
                    'name' => 'Admin ' . $data['razon_social'],
                    'email' => $data['email'],
                    'password' => Hash::make($data['password']),
                    'email_verified_at' => now(),
                    'estadousuario' => 1,
                    'tipousuario' => 0,
                    'avatar' => '',
                    'PER_Id' => 1,
                ]);

                $user->assignRole('Gerente');

                EmpresaFacturacion::create([
                    'tenant_id' => $tenantId,
                    'ruc' => $data['ruc'] ?? null,
                    'razon_social' => $data['razon_social'],
                ]);
            });

            $this->enviarBienvenida($tenant, $domain, $data);

            return $tenant;
        } catch (\Throwable $e) {
            if ($tenant) {
                $tenant->delete(); // elimina tenant + su BD, para no dejar residuos a medias.
            }

            throw $e;
        }
    }

    /**
     * Envía el correo de bienvenida con el link de acceso. Un fallo aquí
     * (SMTP caído, etc.) no debe deshacer un aprovisionamiento que sí
     * funcionó — se registra el error y se sigue.
     */
    private function enviarBienvenida(Tenant $tenant, Domain $domain, array $data): void
    {
        try {
            $planNombre = Plan::find($data['plan'])->nombre ?? ucfirst($data['plan']);

            Mail::to($data['email'])->send(new BienvenidaTenantMail(
                $data['razon_social'],
                'https://' . $domain->domain . '/tenant/login',
                $data['email'],
                $planNombre,
            ));
        } catch (\Throwable $e) {
            report($e);
        }
    }
}
