<?php

namespace App\Console\Commands;

use App\Models\AuditLog;
use App\Models\Tenant;
use App\Models\Tenant\Caja;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AuditarLimitesPlanCommand extends Command
{
    protected $signature = 'plan:auditar-limites {--dry-run : Solo mostrar qué encontraría, sin registrar en la auditoría}';

    protected $description = 'Revisa cada tenant activo contra los límites de SU plan actual (usuarios, sedes/locales, cajas) y registra en auditoría a los que quedaron por encima tras un downgrade. El enforcement normal solo bloquea la creación de registros nuevos; esto audita a los que ya existían antes del cambio de plan.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $tenantsConExceso = 0;

        foreach (Tenant::where('status', 'activo')->get() as $tenant) {
            $violaciones = $tenant->run(function () {
                $violaciones = [];

                $totalUsuarios = \App\Models\User::where('estadousuario', 1)->count();
                $limiteUsuarios = (int) tenant('max_users');
                if ($limiteUsuarios > 0 && $totalUsuarios > $limiteUsuarios) {
                    $violaciones['usuarios'] = ['actual' => $totalUsuarios, 'limite' => $limiteUsuarios];
                }

                // 'Sede' y 'Almacén' son la misma tabla `almacen`, contra el
                // mismo límite (limits.branches) — ver SedeController.php:98-103.
                $totalSedes = DB::table('almacen')->count();
                $limiteSedes = (int) (tenant('limits')['branches'] ?? 1);
                if ($totalSedes > $limiteSedes) {
                    $violaciones['sedes'] = ['actual' => $totalSedes, 'limite' => $limiteSedes];
                }

                $totalCajas = Caja::count();
                $limiteCajas = (int) (tenant('limits')['cash_registers'] ?? 1);
                if ($totalCajas > $limiteCajas) {
                    $violaciones['cajas'] = ['actual' => $totalCajas, 'limite' => $limiteCajas];
                }

                return $violaciones;
            });

            if (empty($violaciones)) {
                continue;
            }

            $tenantsConExceso++;

            $detalle = collect($violaciones)
                ->map(fn ($v, $tipo) => "{$tipo}: {$v['actual']}/{$v['limite']}")
                ->implode(', ');

            $this->line("→ {$tenant->id} (plan {$tenant->plan}) excede su límite — {$detalle}" . ($dryRun ? ' [dry-run]' : ''));

            if (! $dryRun) {
                AuditLog::registrar(
                    'tenant.limite_excedido',
                    "El tenant \"{$tenant->id}\" (plan {$tenant->plan}) está por encima de su límite: {$detalle}",
                    ['tenant_id' => $tenant->id, 'plan' => $tenant->plan, 'violaciones' => $violaciones]
                );
            }
        }

        $this->info("Auditoría completada: {$tenantsConExceso} tenant(s) por encima de su límite actual.");

        return self::SUCCESS;
    }
}
