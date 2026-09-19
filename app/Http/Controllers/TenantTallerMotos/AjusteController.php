<?php

namespace App\Http\Controllers\TenantTallerMotos;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Ajuste de inventario propio de taller de motos. Hereda toda la logica
 * compartida de App\Http\Controllers\Tenant\AjusteController y solo
 * sobreescribe productos() para excluir los productos marcados como
 * Servicio (PRO_TipoProducto, columna exclusiva de tallermoto): un servicio
 * no tiene stock que ajustar. El binding en AppServiceProvider hace que las
 * rutas de Ajuste de Inventario resuelvan a ESTA clase cuando el tenant es
 * 'tallermoto'.
 */
class AjusteController extends \App\Http\Controllers\Tenant\AjusteController
{
    public function productos(Request $request)
    {
        $request->validate(['ALM_Id' => 'required|integer|exists:almacen,ALM_Id']);

        $columnas = ['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion'];
        $agrupar = ['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion'];

        $productos = DB::table('producto as p')
            ->leftJoin('lote as lt', function ($join) use ($request) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')
                    ->where('lt.ALM_Id', '=', $request->ALM_Id);
            })
            ->select(array_merge($columnas, [DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as stock')]))
            ->where('p.PRO_Status', 1)
            ->where('p.PRO_TipoProducto', 'PRODUCTO')
            ->when($request->filled('search'), function ($q) use ($request) {
                $busqueda = '%' . $request->search . '%';
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('p.PRO_Nombre', 'like', $busqueda)
                        ->orWhere('p.PRO_CodigoInterno', 'like', $busqueda)
                        ->orWhere('p.PRO_CodigoFabricacion', 'like', $busqueda);
                });
            })
            ->groupBy($agrupar)
            ->orderBy('p.PRO_Nombre')
            ->limit(30)
            ->get();

        return response()->json($productos);
    }

    protected function validarProductosAjustables(array $proIds): void
    {
        if (empty($proIds)) {
            return;
        }

        $servicios = DB::table('producto')
            ->whereIn('PRO_Id', $proIds)
            ->where('PRO_TipoProducto', 'SERVICIO')
            ->pluck('PRO_Nombre');

        if ($servicios->isNotEmpty()) {
            throw new Exception(
                'No se puede ajustar stock de un Servicio: ' . $servicios->implode(', ') . '.'
            );
        }
    }
}
