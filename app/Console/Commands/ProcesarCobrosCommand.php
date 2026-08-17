<?php

namespace App\Console\Commands;

use App\Mail\CobroNotificacionMail;
use App\Models\Client;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcesarCobrosCommand extends Command
{
    protected $signature = 'cobros:procesar {--dry-run : Solo mostrar qué haría, sin enviar correos ni suspender}';

    protected $description = 'Revisa el ciclo de facturación de cada cliente activo: envía recordatorios, avisos de vencido y suspende por mora.';

    public function handle(): int
    {
        $hoy = Carbon::now('America/Lima');
        $periodo = $hoy->format('Y-m');
        $dryRun = (bool) $this->option('dry-run');
        $diasGracia = (int) config('saas.cobros.dias_gracia_suspension', 5);

        $planesConfig = saas_plans_config();

        $clientes = Client::where('clients.status', 'activo')
            ->whereNotNull('billing_day')
            ->with(['pagos' => fn ($q) => $q->where('periodo', $periodo)])
            ->join('domains as d', 'clients.domain_id', '=', 'd.id')
            ->join('tenants as t', 'd.tenant_id', '=', 't.id')
            ->select('clients.*', 't.plan')
            ->get();

        $enviados = ['recordatorio' => 0, 'vencido' => 0, 'suspension' => 0];
        $sinEmail = 0;

        foreach ($clientes as $cliente) {
            $estado = $cliente->estadoCicloActual($hoy);

            if (! in_array($estado, ['por_vencer', 'vencido'], true)) {
                continue;
            }

            $fechaCobro = $cliente->fechaCicloActual($hoy);
            $monto = $planesConfig[$cliente->plan]['price'] ?? 0;

            // ---------- RECORDATORIO (por vencer, dentro de 7 días) ----------
            if ($estado === 'por_vencer') {
                $this->notificarUnaVez($cliente, $periodo, 'recordatorio', $fechaCobro, $monto, $dryRun, $enviados, $sinEmail);

                continue;
            }

            // ---------- VENCIDO ----------
            $this->notificarUnaVez($cliente, $periodo, 'vencido', $fechaCobro, $monto, $dryRun, $enviados, $sinEmail);

            // ---------- SUSPENSIÓN POR MORA ----------
            $diasAtraso = (int) $fechaCobro->startOfDay()->diffInDays($hoy->copy()->startOfDay());

            if ($diasAtraso >= $diasGracia) {
                $yaAvisado = $cliente->notificacionesCobro()
                    ->where('periodo', $periodo)
                    ->where('tipo', 'suspension')
                    ->exists();

                if (! $yaAvisado) {
                    $this->line("→ Suspendiendo a {$cliente->razon_social} ({$diasAtraso} días de atraso)" . ($dryRun ? ' [dry-run]' : ''));

                    if (! $dryRun) {
                        $cliente->status = 'suspendido';
                        $cliente->save();

                        $tenant = Tenant::find($cliente->tenant_id);
                        if ($tenant) {
                            $tenant->status = 'suspendido';
                            $tenant->save();
                        }

                        $this->notificarUnaVez($cliente, $periodo, 'suspension', $fechaCobro, $monto, false, $enviados, $sinEmail);

                        \App\Models\AuditLog::create([
                            'user_id' => null,
                            'user_name' => 'Sistema (cobros:procesar)',
                            'accion' => 'cliente.suspendido.auto',
                            'descripcion' => 'Suspendió automáticamente a "' . $cliente->razon_social . '" por ' . $diasAtraso . ' días de atraso',
                            'datos' => ['client_id' => $cliente->id, 'periodo' => $periodo, 'dias_atraso' => $diasAtraso],
                            'created_at' => now(),
                        ]);
                    }
                }
            }
        }

        $this->info("Procesado: {$enviados['recordatorio']} recordatorios, {$enviados['vencido']} avisos de vencido, {$enviados['suspension']} suspensiones. {$sinEmail} clientes sin email de contacto.");

        return self::SUCCESS;
    }

    /**
     * Envía (o simula) un tipo de notificación una sola vez por cliente+periodo,
     * usando cobro_notificaciones como registro de "ya se avisó esto".
     */
    private function notificarUnaVez(
        Client $cliente,
        string $periodo,
        string $tipo,
        Carbon $fechaCobro,
        float $monto,
        bool $dryRun,
        array &$enviados,
        int &$sinEmail,
    ): void {
        $yaEnviado = $cliente->notificacionesCobro()
            ->where('periodo', $periodo)
            ->where('tipo', $tipo)
            ->exists();

        if ($yaEnviado) {
            return;
        }

        if (! $cliente->email) {
            $sinEmail++;
            Log::warning("cobros:procesar — {$cliente->razon_social} (id={$cliente->id}) no tiene email de contacto, se omite el aviso '{$tipo}'.");

            return;
        }

        $this->line("→ Aviso '{$tipo}' para {$cliente->razon_social} <{$cliente->email}>" . ($dryRun ? ' [dry-run]' : ''));

        if ($dryRun) {
            return;
        }

        Mail::to($cliente->email)->send(new CobroNotificacionMail($cliente, $tipo, $fechaCobro, $monto));

        $cliente->notificacionesCobro()->create([
            'periodo' => $periodo,
            'tipo' => $tipo,
            'enviado_en' => now(),
        ]);

        $enviados[$tipo]++;
    }
}
