<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\VentaPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Detalle de pago dividido (varios metodos en una misma venta), exclusivo
 * del vertical 'generico'. No reemplaza el guardado normal de la venta
 * (VentaController::store(), compartido con tallermoto y NO tocado): esto
 * solo agrega el desglose por metodo, usado por el arqueo de caja de
 * generico (Generico\CajaSesionReporteController) para saber cuanto de una
 * venta "Mixta" fue realmente efectivo.
 */
class VentaPagoController extends Controller
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venta_id' => ['required', 'integer', 'exists:venta,VEN_Id'],
            'pagos' => ['required', 'array', 'min:1'],
            'pagos.*.metodo_pago_id' => ['required', 'integer', 'exists:metodo_pago,MEP_Id'],
            'pagos.*.monto' => ['required', 'numeric', 'min:0.01'],
        ], [
            'venta_id.exists' => 'La venta indicada no existe.',
            'pagos.required' => 'Debe indicar al menos un metodo de pago.',
            'pagos.*.metodo_pago_id.exists' => 'Uno de los metodos de pago indicados no existe.',
            'pagos.*.monto.min' => 'Cada linea de pago debe ser mayor a 0.',
        ]);

        $ventaId = (int) $validated['venta_id'];
        $pagos = $validated['pagos'];

        // "Mixto" es la etiqueta de la venta completa (va en venta.MEP_Id
        // cuando se usan 2+ metodos reales), no un metodo de cobro en si
        // mismo: no tiene sentido que una linea de venta_pago diga que se
        // pago "Mixto".
        $mixtoId = DB::table('metodo_pago')->where('MEP_Pago', 'Mixto')->value('MEP_Id');

        if ($mixtoId && collect($pagos)->contains(fn ($p) => (int) $p['metodo_pago_id'] === (int) $mixtoId)) {
            return response()->json([
                'error' => '"Mixto" no es un metodo de pago valido para una linea individual; es solo la etiqueta de una venta con varios metodos.',
            ], 422);
        }

        // El total real de la venta se recalcula desde detalle_venta (misma
        // formula que ya usan HomeController y CajaSesionController), nunca
        // se confia en un total que venga del navegador.
        //
        // Excepcion: si la venta es al credito (VEN_TipoPago = 2), estas
        // lineas no describen el total de la venta sino solamente el
        // adelanto dejado al momento de venderla (el resto queda pendiente
        // en cuenta_cobrar) — ahi lo que debe cuadrar es contra
        // venta.VEN_Pagado, que es exactamente el adelanto que el store()
        // de la venta ya guardo.
        $venta = DB::table('venta')->where('VEN_Id', $ventaId)->first();
        $esCredito = $venta && (int) $venta->VEN_TipoPago === 2;

        $totalReal = $esCredito
            ? (float) $venta->VEN_Pagado
            : (float) DB::table('detalle_venta')
                ->where('VEN_Id', $ventaId)
                ->sum(DB::raw('(DEV_Cantidad * DEV_PrecioUnitario) - DEV_Descuento'));

        $sumaPagos = round(array_sum(array_map(fn ($p) => (float) $p['monto'], $pagos)), 2);

        if (abs($sumaPagos - round($totalReal, 2)) > self::TOLERANCIA) {
            return response()->json([
                'error' => $esCredito
                    ? ('La suma de los metodos de pago (S/ ' . number_format($sumaPagos, 2) .
                        ') no coincide con el adelanto de la venta (S/ ' . number_format($totalReal, 2) . ').')
                    : ('La suma de los metodos de pago (S/ ' . number_format($sumaPagos, 2) .
                        ') no coincide con el total de la venta (S/ ' . number_format($totalReal, 2) . ').'),
            ], 422);
        }

        try {
            DB::transaction(function () use ($ventaId, $pagos) {
                // Idempotente: si por un reintento del navegador esta ruta se
                // llama dos veces para la misma venta, se reemplaza el
                // detalle en vez de duplicarlo.
                VentaPago::where('VEN_Id', $ventaId)->delete();

                foreach ($pagos as $pago) {
                    VentaPago::create([
                        'VEN_Id' => $ventaId,
                        'MEP_Id' => $pago['metodo_pago_id'],
                        'VPG_Monto' => round((float) $pago['monto'], 2),
                    ]);
                }
            });
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo guardar el detalle de pago. La venta ya quedo registrada.',
            ], 500);
        }

        return response()->json(['success' => 'Detalle de pago guardado correctamente.']);
    }
}
