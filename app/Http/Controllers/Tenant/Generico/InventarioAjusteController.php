<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Lote;
use App\Models\Tenant\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Ajustes manuales de stock (Ingreso / Salida), exclusivo del vertical
 * 'generico'. Se piden desde "Control de Inventario" cuando el stock de un
 * producto tiene que subir o bajar por algo que no es una compra ni una
 * venta (conteo fisico, merma, prestamo, correccion, etc.).
 *
 * Igual que una compra, el ajuste queda registrado como una fila mas de
 * 'lote' (LOT_TipoIngreso = AJUSTE_INGRESO / AJUSTE_SALIDA), asi que el
 * stock del producto (SUM(lote.LOT_CantidadReal), usado en todo el
 * sistema: listado de Control de Inventario, HomeController, disponible
 * para vender en VentaController, etc.) sube o baja correctamente en
 * automatico, sin tocar ningun controlador compartido.
 *
 * El Kardex compartido (ProductoController::kardex()) solo lee 'lote'
 * JOIN 'compra'/'venta', asi que estas filas de ajuste no le apareceran
 * ahi. Para que igual "salgan en el Kardex" (pedido explicito del
 * usuario) sin tocar ese controlador, historial() expone estas mismas
 * filas en el mismo formato y la vista de generico las combina en
 * pantalla con lo que ya devuelve el Kardex compartido.
 */
class InventarioAjusteController extends Controller
{
    const TIPO_INGRESO = 'AJUSTE_INGRESO';
    const TIPO_SALIDA = 'AJUSTE_SALIDA';

    public function store(Request $request)
    {
        $request->validate([
            'PRO_Id' => 'required|integer|exists:producto,PRO_Id',
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'tipo' => 'required|in:ingreso,salida',
            'cantidad' => 'required|numeric|min:0.01',
            'motivo' => 'nullable|string|max:255',
        ]);

        $producto = Producto::findOrFail($request->PRO_Id);
        $cantidad = round((float) $request->cantidad, 2);
        $esIngreso = $request->tipo === 'ingreso';

        if (!$esIngreso) {
            $stockActual = (float) DB::table('lote')
                ->where('PRO_Id', $producto->PRO_Id)
                ->where('ALM_Id', $request->ALM_Id)
                ->sum('LOT_CantidadReal');

            if ($cantidad > $stockActual + 0.01) {
                return response()->json([
                    'error' => 'No hay suficiente stock en esa sede para dar salida a ' . $cantidad . '. Disponible: ' . $stockActual . '.',
                ], 422);
            }
        }

        $lote = DB::transaction(function () use ($producto, $request, $cantidad, $esIngreso) {
            return Lote::create([
                'ALM_Id' => $request->ALM_Id,
                'PRO_Id' => $producto->PRO_Id,
                'LOT_TipoIngreso' => $esIngreso ? self::TIPO_INGRESO : self::TIPO_SALIDA,
                'LOT_IdIngreso' => 0,
                'LOT_CantidadReal' => $esIngreso ? $cantidad : -$cantidad,
                'LOT_CantidadIngreso' => $esIngreso ? $cantidad : -$cantidad,
                'LOT_PrecioCompra' => $producto->PRO_PrecioCompra ?? 0,
                'LOT_PrecioVenta' => $producto->PRO_PrecioVenta ?? 0,
                'LOT_Motivo' => $request->motivo,
                'USU_Id' => Auth::id(),
            ]);
        });

        $stockNuevo = (float) DB::table('lote')
            ->where('PRO_Id', $producto->PRO_Id)
            ->sum('LOT_CantidadReal');

        return response()->json([
            'success' => $esIngreso
                ? 'Ingreso de stock registrado correctamente.'
                : 'Salida de stock registrada correctamente.',
            'lote_id' => $lote->LOT_Id,
            'stock_total' => $stockNuevo,
        ]);
    }

    /**
     * Ajustes de un producto en el mismo formato de fila que usa el Kardex
     * compartido (fecha/documento/lote_id/entrada/salida/tipo), para que la
     * vista los combine con lo que devuelve
     * ProductoController::kardex() sin tener que tocar ese metodo.
     *
     * Tambien devuelve 'stock_previo': el stock REAL (suma de TODO 'lote',
     * de cualquier tipo) que tenia el producto justo antes de fecha_inicio,
     * para que el front pueda recalcular el stock acumulado del reporte
     * combinado desde una base correcta (el Kardex compartido solo calcula
     * esa base a partir de compras/ventas, sin contar los ajustes).
     */
    public function historial(Request $request, string $id)
    {
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;

        $query = DB::table('lote')
            ->whereIn('LOT_TipoIngreso', [self::TIPO_INGRESO, self::TIPO_SALIDA])
            ->where('PRO_Id', $id);

        if ($fecha_inicio) {
            $query->where('created_at', '>=', $fecha_inicio . ' 00:00:00');
        }

        if ($fecha_fin) {
            $query->where('created_at', '<=', $fecha_fin . ' 23:59:59');
        }

        $ajustes = $query->orderBy('created_at')->get()->map(function ($lote) {
            $cantidad = (float) $lote->LOT_CantidadReal;

            return [
                'id' => $lote->LOT_Id,
                'documento' => $lote->LOT_Motivo ?: ('Ajuste #' . $lote->LOT_Id),
                'fecha' => $lote->created_at,
                'lote_id' => $lote->LOT_Id,
                'entrada' => $cantidad > 0 ? $cantidad : 0,
                'salida' => $cantidad < 0 ? abs($cantidad) : 0,
                'tipo' => $cantidad >= 0 ? 'Entrada' : 'Salida',
                'origen' => 'AJUSTE',
            ];
        });

        $stock_previo = 0;

        if ($fecha_inicio) {
            $stock_previo = (float) DB::table('lote')
                ->where('PRO_Id', $id)
                ->where('created_at', '<', $fecha_inicio . ' 00:00:00')
                ->sum('LOT_CantidadReal');
        }

        return response()->json([
            'ajustes' => $ajustes,
            'stock_previo' => $stock_previo,
        ]);
    }
}
