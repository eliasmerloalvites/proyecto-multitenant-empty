<?php

namespace App\Jobs;

use App\Models\RegistroVerificacion;
use App\Services\TenantProvisioningService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * Crea el tenant (BD, migraciones, seeders, usuario admin) en segundo
 * plano, fuera de la petición HTTP que atiende el link de verificación —
 * ver RegistroController::verificar(). Esto es lo mismo que antes hacía
 * TenantProvisioningService::provision() de forma sincrona ahi mismo, solo
 * que ahora corre en cola: un aprovisionamiento lento ya no puede tirar un
 * 504 de nginx (el usuario ve una pantalla de "creando tu cuenta" que
 * consulta el estado por AJAX, sin depender de que esta peticion siga
 * abierta).
 */
class ProvisionarTenantJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public $tries = 1; // TenantProvisioningService ya hace su propio rollback si falla; reintentar duplicaria el intento de creación.

    public $timeout = 600;

    public function __construct(public int $verificacionId)
    {
    }

    public function handle(TenantProvisioningService $provisioning): void
    {
        $verificacion = RegistroVerificacion::find($this->verificacionId);

        if (! $verificacion) {
            return;
        }

        $trialEndsAt = now()->addDays((int) config('saas.cobros.dias_prueba_gratis', 7));

        try {
            $tenant = $provisioning->provision([
                'tipo_negocio' => $verificacion->tipo_negocio,
                'plan' => $verificacion->plan,
                'subdomain' => $verificacion->subdomain,
                'razon_social' => $verificacion->razon_social,
                'ruc' => $verificacion->ruc,
                'email' => $verificacion->email,
                // Ya viene hasheada; TenantProvisioningService la vuelve a
                // hashear con Hash::make, así que le pasamos una contraseña
                // aleatoria interna y actualizamos el hash real después.
                'password' => \Illuminate\Support\Str::random(40),
                'billing_day' => min($trialEndsAt->day, 28),
                'trial_ends_at' => $trialEndsAt->toDateString(),
                'vendedor_id' => $verificacion->vendedor_id,
            ]);

            // Sobreescribimos con el hash real que el usuario eligió
            // (provision() no lo conoce porque solo recibe contraseñas en
            // texto plano).
            $tenant->run(function () use ($verificacion) {
                \App\Models\Tenant\User::where('email', $verificacion->email)
                    ->update(['password' => $verificacion->password]);
            });

            $domain = $tenant->domains()->first();

            $verificacion->update([
                'verificado_en' => now(),
                'estado' => 'completado',
                'tenant_domain' => $domain->domain,
            ]);
        } catch (\Throwable $e) {
            report($e);
            Log::error('ProvisionarTenantJob falló para verificacion #' . $this->verificacionId, [
                'error' => $e->getMessage(),
            ]);

            $verificacion->update([
                'estado' => 'error',
                'error_mensaje' => 'No se pudo crear tu empresa. Intenta nuevamente o contáctanos.',
            ]);
        }
    }
}
