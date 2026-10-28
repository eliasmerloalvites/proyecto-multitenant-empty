<?php

namespace App\Services\Reportes;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Utilidad de un periodo (criterio devengado: cada venta cuenta en su fecha
 * por su total, se haya cobrado o no):
 *
 *   vendido - costo de lo vendido - gastos = utilidad neta
 *
 * El costo es el del lote especifico que se vendio (dv.LOT_Id), no el precio
 * de compra actual. Las compras no restan aqui: son mercaderia que entra al
 * stock y recien cuentan como costo cuando se venden. Lo usan el reporte de
 * Rentabilidad y el resumen del mes del dashboard, para que nunca discrepen.
 */
class UtilidadService
{
    public static function entre(Carbon $desde, Carbon $hasta, ?int $almacenId = null): array
    {
        $ventas = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->leftJoin('lote as l', 'l.LOT_Id', '=', 'dv.LOT_Id')
            ->where('v.VEN_Status', 1)
            ->whereBetween('v.created_at', [$desde, $hasta])
            ->when($almacenId, fn ($q) => $q->where('v.ALM_Id', $almacenId))
            // DEV_PrecioUnitario ya es el precio final con el descuento aplicado.
            ->selectRaw('
                COALESCE(SUM(dv.DEV_Cantidad * dv.DEV_PrecioUnitario), 0) as ingresos,
                COALESCE(SUM(dv.DEV_Cantidad * COALESCE(l.LOT_PrecioCompra, 0)), 0) as costo
            ')
            ->first();

        $gastos = (float) DB::table('gasto')
            ->where('GAS_Status', 1)
            ->when($almacenId, fn ($q) => $q->where('ALM_Id', $almacenId))
            ->whereBetween('GAS_Fecha', [$desde->toDateString(), $hasta->toDateString()])
            ->sum('GAS_Monto');

        $ingresos = (float) $ventas->ingresos;
        $costo = (float) $ventas->costo;
        $utilidadBruta = $ingresos - $costo;
        $utilidadNeta = $utilidadBruta - $gastos;

        return [
            'ingresos' => round($ingresos, 2),
            'costo' => round($costo, 2),
            'utilidadBruta' => round($utilidadBruta, 2),
            'gastos' => round($gastos, 2),
            'utilidadNeta' => round($utilidadNeta, 2),
            'margen' => $ingresos > 0 ? round($utilidadNeta / $ingresos * 100, 1) : 0.0,
        ];
    }
}
