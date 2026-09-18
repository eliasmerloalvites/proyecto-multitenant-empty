<?php

namespace App\Console\Commands;

use App\Models\Tenant as CentralTenant;
use App\Http\Controllers\Tenant\VentaController;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Repara ventas que quedaron sin fila en documento_venta (por ejemplo, las
 * generadas por demo:poblar-tallermoto antes de que ese comando creara el
 * documento). "Lista de Ventas" hace INNER JOIN con documento_venta, asi
 * que sin esto esas ventas existen en la base de datos pero no aparecen
 * en ningun listado ni se les puede sacar ticket/PDF.
 *
 *   php artisan demo:reparar-documento-venta tallermoto_demo
 */
class RepararDocumentoVentaCommand extends Command
{
    protected $signature = 'demo:reparar-documento-venta {tenant}';

    protected $description = 'Crea el documento_venta (Nota de Venta) que le falte a ventas ya existentes que quedaron sin uno.';

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');
        $tenant = CentralTenant::find($tenantId);

        if (!$tenant) {
            $this->error("No existe ningún tenant con id \"{$tenantId}\".");

            return self::FAILURE;
        }

        $resultado = self::SUCCESS;

        $tenant->run(function () use ($tenantId, &$resultado) {
            $ventasSinDocumento = DB::table('venta as v')
                ->leftJoin('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->whereNull('dov.DOV_Id')
                ->select('v.VEN_Id', 'v.ALM_Id')
                ->get();

            if ($ventasSinDocumento->isEmpty()) {
                $this->info("Tenant \"{$tenantId}\": no hay ventas sin documento_venta. No hay nada que reparar.");

                return;
            }

            $this->info("Tenant \"{$tenantId}\": reparando {$ventasSinDocumento->count()} venta(s) sin documento_venta...");

            $reparadas = 0;

            foreach ($ventasSinDocumento as $venta) {
                try {
                    VentaController::CrearDocumentoDetalleVentaLibre($venta->VEN_Id, $venta->ALM_Id);
                    $reparadas++;
                } catch (\Throwable $e) {
                    $this->warn("Venta #{$venta->VEN_Id}: no se pudo reparar ({$e->getMessage()}).");
                }
            }

            $this->info("Listo. {$reparadas} venta(s) reparada(s).");
        });

        return $resultado;
    }
}
