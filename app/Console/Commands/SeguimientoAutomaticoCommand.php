<?php

namespace App\Console\Commands;

use App\Models\Client;
use App\Models\SeguimientoEvento;
use App\Services\Seguimiento\SeguimientoPlantillas;
use App\Services\Seguimiento\SeguimientoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeguimientoAutomaticoCommand extends Command
{
    protected $signature = 'seguimiento:automatico {--dry-run : Solo mostrar a quién se le escribiría, sin enviar}';

    protected $description = 'Envía correos de seguimiento a clientes en prueba (sin uso, prueba por vencer, prueba vencida). Cada aviso se manda una sola vez por cliente.';

    public function handle(SeguimientoService $seguimiento): int
    {
        $dryRun = (bool) $this->option('dry-run');

        if (! config('saas.seguimiento.automatico') && ! $dryRun) {
            $this->info('Seguimiento automático apagado (SEGUIMIENTO_AUTOMATICO=false).');

            return self::SUCCESS;
        }

        $diasSinUso = (int) config('saas.seguimiento.dias_sin_uso', 2);
        $diasPorVencer = (int) config('saas.seguimiento.dias_por_vencer', 2);
        $diasVencida = (int) config('saas.seguimiento.dias_vencida', 7);
        $hoy = SeguimientoService::hoy();

        $clientes = Client::where('status', 'activo')
            ->whereNotNull('trial_ends_at')
            ->whereNotNull('email')
            ->whereNotExists(fn ($q) => $q->select(DB::raw(1))->from('pagos')->whereColumn('pagos.client_id', 'clients.id'))
            ->get();

        $enviados = 0;

        foreach ($clientes as $cliente) {
            $ficha = $seguimiento->ficha($cliente);

            if ($ficha->no_contactar || in_array($ficha->etapa, ['perdido', 'convertido'], true)) {
                continue;
            }

            $dias = $seguimiento->diasRestantes($cliente);
            $ficha = $seguimiento->medirUso($cliente);
            $diasRegistrado = (int) $cliente->created_at->copy()->startOfDay()->diffInDays($hoy);

            $clave = match (true) {
                $dias < 0 && abs($dias) <= $diasVencida => 'auto_vencida',
                $dias >= 1 && $dias <= $diasPorVencer => 'auto_por_vencer',
                $dias >= 1 && $diasRegistrado >= $diasSinUso && $ficha->uso_nivel === 'sin_uso' => 'auto_sin_uso',
                default => null,
            };

            if (! $clave) {
                continue;
            }

            $yaEnviado = SeguimientoEvento::where('client_id', $cliente->id)->where('clave', $clave)->exists();

            if ($yaEnviado) {
                continue;
            }

            $plantilla = SeguimientoPlantillas::PLANTILLAS[SeguimientoPlantillas::AUTOMATICAS[$clave]];
            $vars = $seguimiento->variables($cliente);
            $asunto = SeguimientoPlantillas::aplicar($plantilla['asunto'], $vars);

            if ($dryRun) {
                $this->line("[simulación] {$cliente->razon_social} <{$cliente->email}> → {$clave}");

                continue;
            }

            [$ok, $msg] = $seguimiento->enviarCorreo($cliente, $asunto, SeguimientoPlantillas::aplicar($plantilla['cuerpo'], $vars), null, $clave);

            $this->line(($ok ? 'OK    ' : 'FALLÓ ') . "{$cliente->razon_social}: {$msg}");
            $enviados += $ok ? 1 : 0;
        }

        $this->info($dryRun ? 'Simulación terminada.' : "Correos enviados: {$enviados}");

        return self::SUCCESS;
    }
}
