<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Producto;
use Illuminate\Support\Facades\DB;

/**
 * Eliminar un producto de verdad, exclusivo de generico. El
 * ProductoController::destroy() compartido (usado tambien por tallermoto)
 * hace un delete() sin ninguna validacion, y 'lote'/'detalle_compra'/
 * 'detalle_venta' tienen FK con onDelete('cascade') hacia 'producto': si el
 * producto ya tuvo compras, ventas, ajustes de stock o cotizaciones, un
 * delete lo borraria todo en cascada sin avisar, perdiendo el historial e
 * inflando/desinflando reportes que ya se mostraron.
 *
 * En vez de tocar ese controlador compartido, el boton "Eliminar" del
 * listado de productos de generico (inventario/producto/index.blade.php)
 * llama aqui: si el producto tiene cualquier movimiento se rechaza con un
 * mensaje claro; si esta realmente limpio, se elimina de verdad.
 */
class ProductoEliminacionController extends Controller
{
    public function destroy(string $id)
    {
        $producto = Producto::find($id);

        if (!$producto) {
            return response()->json(['error' => 'El producto indicado no existe.'], 404);
        }

        $motivos = [];

        if (DB::table('detalle_compra')->where('PRO_Id', $id)->exists()) {
            $motivos[] = 'tiene compras registradas';
        }

        if (DB::table('detalle_venta')->where('PRO_Id', $id)->exists()) {
            $motivos[] = 'tiene ventas registradas';
        }

        if (DB::table('lote')->where('PRO_Id', $id)->exists()) {
            $motivos[] = 'tiene movimientos de stock (compras, ajustes o ventas)';
        }

        if (DB::getSchemaBuilder()->hasTable('detalle_cotizacion')
            && DB::table('detalle_cotizacion')->where('PRO_Id', $id)->exists()) {
            $motivos[] = 'tiene cotizaciones registradas';
        }

        if (!empty($motivos)) {
            return response()->json([
                'error' => 'No se puede eliminar "' . $producto->PRO_Nombre . '": ' . implode(', ', $motivos) . '. '
                    . 'Eliminarlo borraría ese historial (compras, ventas, kardex). Si ya no lo vendes, deja de usarlo en vez de eliminarlo.',
            ], 422);
        }

        $producto->delete();

        return response()->json(['success' => 'Producto eliminado exitosamente.']);
    }
}
