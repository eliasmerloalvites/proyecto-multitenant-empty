<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CuentaPagar;
use App\Models\Tenant\Lote;
use App\Models\Tenant\Movimiento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Anulacion de una compra, exclusiva del vertical 'generico'. No existe un
 * "eliminar" real: el boton de Eliminar del listado de Compras (compartido
 * con tallermoto, CompraController::destroy() sigue vacio y sin usar)
 * llama aqui en su lugar.
 *
 * Por que anular y no borrar de verdad: la compra ya pudo haber sumado
 * stock (via 'lote', tabla sin ninguna relacion de llave foranea hacia
 * 'compra') y ese stock ya pudo venderse. Borrar la fila de 'compra'
 * dejaria 'lote' y 'movimiento' huerfanos (sin ningun error de base de
 * datos que lo avise) y el stock del producto -que se calcula sumando
 * lote.LOT_CantidadReal- quedaria inflado para siempre. En cambio, aqui:
 * - la compra y su detalle_compra se conservan intactos (solo se marca
 *   COM_Status = 0), para que quede rastro completo de que existio;
 * - se crea un lote de reversa (mismo COM_Id, mismo LOT_TipoIngreso =
 *   'COMPRA') con cantidades en negativo, para que el Kardex compartido
 *   (ProductoController::kardex(), que lee 'lote' JOIN 'compra' sin
 *   filtrar por COM_Status) muestre tambien la reversa como una fila mas,
 *   con su propia fecha, sin tener que tocar ese controlador compartido;
 * - solo se permite si NINGUNO de los lotes de esta compra ya fue tocado
 *   por una venta (LOT_CantidadReal distinto de LOT_CantidadIngreso
 *   significa que VentaController::descontarStock() ya descargo algo de
 *   ahi via FIFO) y si la cuenta por pagar (si existe) no tiene abonos.
 */
class CompraAnulacionController extends Controller
{
    /** Tolerancia de redondeo (centimos) al comparar cantidades. */
    const TOLERANCIA = 0.01;

    public function store(Request $request, string $compra)
    {
        $compraId = (int) $compra;

        $compraRow = DB::table('compra')->where('COM_Id', $compraId)->first();

        if (!$compraRow) {
            return response()->json(['error' => 'La compra indicada no existe.'], 404);
        }

        if ((int) $compraRow->COM_Status === 0) {
            return response()->json(['error' => 'Esta compra ya está anulada.'], 422);
        }

        $cuentaPagar = CuentaPagar::where('COM_Id', $compraId)->first();

        if ($cuentaPagar && $cuentaPagar->abonos()->exists()) {
            return response()->json([
                'error' => 'Esta compra tiene una cuenta por pagar con abonos registrados. Resuelve esos pagos antes de anularla.',
            ], 409);
        }

        $lotes = DB::table('lote')
            ->where('LOT_TipoIngreso', 'COMPRA')
            ->where('LOT_IdIngreso', $compraId)
            ->get();

        if ($lotes->isEmpty()) {
            return response()->json([
                'error' => 'No se encontró el stock generado por esta compra; no se puede anular automáticamente.',
            ], 422);
        }

        foreach ($lotes as $lote) {
            $diferencia = abs((float) $lote->LOT_CantidadReal - (float) $lote->LOT_CantidadIngreso);

            if ($diferencia > self::TOLERANCIA) {
                return response()->json([
                    'error' => 'No se puede anular: ya se vendió parte del stock que ingresó esta compra. Revisa el Kardex del producto antes de continuar.',
                ], 422);
            }
        }

        DB::transaction(function () use ($compraId, $lotes, $cuentaPagar) {
            foreach ($lotes as $lote) {
                Lote::create([
                    'ALM_Id' => $lote->ALM_Id,
                    'PRO_Id' => $lote->PRO_Id,
                    'LOT_TipoIngreso' => 'COMPRA',
                    'LOT_IdIngreso' => $compraId,
                    'LOT_CantidadReal' => -$lote->LOT_CantidadReal,
                    'LOT_CantidadIngreso' => -$lote->LOT_CantidadIngreso,
                    'LOT_PrecioCompra' => $lote->LOT_PrecioCompra,
                    'LOT_PrecioVenta' => $lote->LOT_PrecioVenta,
                ]);
            }

            $movi = new Movimiento();
            $movi->tipo = 'Salida';
            $movi->idcv = $compraId;
            $movi->save();

            if ($cuentaPagar) {
                // No deberia tener abonos (ya se valido arriba); la
                // cascada de la FK limpia sus cuotas.
                $cuentaPagar->delete();
            }

            DB::table('compra')->where('COM_Id', $compraId)->update(['COM_Status' => 0]);
        });

        return response()->json([
            'success' => 'Compra anulada correctamente. El stock que había ingresado fue revertido.',
        ]);
    }
}
