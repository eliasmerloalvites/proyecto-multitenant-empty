<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Caja;
use App\Models\Tenant\CajaSesion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Copia, exclusiva de 'generico', de la parte de
 * App\Http\Controllers\Tenant\CajaSesionController (compartido con
 * tallermoto, NO tocado) que hace matematica por metodo de pago: cerrar()
 * y detalle(), y sus privados calcularMontoEsperado()/resumenPorMetodoPago().
 *
 * La unica diferencia real es que aqui "cuanto de esta venta fue efectivo"
 * se lee de venta_pago (el detalle de pago dividido) en vez de asumir que
 * toda la venta fue de un solo metodo via venta.MEP_Id. Para una venta que
 * todavia no tenga fila en venta_pago (vendida antes de esta funcionalidad,
 * o si el segundo paso de guardado del detalle fallo), se cae de vuelta al
 * calculo de siempre por su unico venta.MEP_Id, asi que ninguna venta se
 * cuenta dos veces ni se pierde.
 *
 * seleccionar(), abrir() e historial() no se duplican aqui: no hacen
 * matematica por metodo de pago, asi que las vistas de generico los siguen
 * llamando en el controlador original compartido.
 */
class CajaSesionReporteController extends Controller
{
    public function cerrar(Request $request)
    {
        $validated = $request->validate([
            'caja_id' => 'required|integer',
            'monto_real' => 'required|numeric|min:0',
            'observacion' => 'nullable|string|max:500',
        ]);

        $sesion = CajaSesion::where('CAJ_Id', $validated['caja_id'])
            ->where('CS_Estado', 'abierta')
            ->first();

        if (! $sesion) {
            return response()->json(['error' => 'Esa caja no tiene un turno abierto.'], 422);
        }

        $montoEsperado = self::calcularMontoEsperado($sesion);

        $sesion->update([
            'USU_Id_Cierre' => Auth::guard('tenant')->id(),
            'CS_MontoEsperado' => $montoEsperado,
            'CS_MontoReal' => $validated['monto_real'],
            'CS_Diferencia' => $validated['monto_real'] - $montoEsperado,
            'CS_FechaCierre' => Carbon::now('America/Lima'),
            'CS_Estado' => 'cerrada',
            'CS_TipoCierre' => 'manual',
            'CS_Observacion' => $validated['observacion'] ?? null,
        ]);

        if ((int) session('caja_activa_id') === (int) $validated['caja_id']) {
            $request->session()->forget('caja_activa_id');
        }

        return response()->json(['success' => 'Caja cerrada correctamente.', 'sesion' => $sesion->fresh()]);
    }

    /**
     * Detalle de un turno: totales + el flujo completo de ventas, compras y
     * gastos que ocurrieron durante ese turno. Misma forma de respuesta que
     * el detalle() original, para que la vista de generico no necesite
     * cambiar nada mas que la URL a la que llama.
     */
    public function detalle(string $id)
    {
        $sesion = CajaSesion::with(['caja', 'usuarioApertura:id,name', 'usuarioCierre:id,name'])->find($id);

        if (! $sesion) {
            return response()->json(['error' => 'Turno no encontrado.'], 404);
        }

        $ventas = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->where('v.CS_Id', $id)
            ->select('v.VEN_Id', 'c.CLI_Nombre', 'mp.MEP_Pago', 'v.created_at', DB::raw('SUM((dv.DEV_Cantidad * dv.DEV_PrecioUnitario) - dv.DEV_Descuento) as total'))
            ->groupBy('v.VEN_Id', 'c.CLI_Nombre', 'mp.MEP_Pago', 'v.created_at')
            ->orderByDesc('v.VEN_Id')
            ->get();

        $compras = DB::table('compra as co')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'co.PROV_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'co.MEP_Id')
            ->join('detalle_compra as dc', 'dc.COM_Id', '=', 'co.COM_Id')
            ->where('co.CS_Id', $id)
            ->select('co.COM_Id', 'p.PROV_RazonSocial', 'mp.MEP_Pago', 'co.created_at', DB::raw('SUM(dc.DCOM_Cantidad * dc.DCOM_PrecioCompra) as total'))
            ->groupBy('co.COM_Id', 'p.PROV_RazonSocial', 'mp.MEP_Pago', 'co.created_at')
            ->orderByDesc('co.COM_Id')
            ->get();

        $gastos = DB::table('gasto as g')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'g.MEP_Id')
            ->where('g.CS_Id', $id)
            ->select('g.GAS_Id', 'g.GAS_Descripcion', 'g.GAS_Monto', 'mp.MEP_Pago', 'g.GAS_Fecha', 'g.GAS_Afecta')
            ->orderByDesc('g.GAS_Id')
            ->get();

        $montoEsperado = $sesion->CS_Estado === 'abierta'
            ? self::calcularMontoEsperado($sesion)
            : (float) $sesion->CS_MontoEsperado;

        $resumenPorMetodo = self::resumenPorMetodoPago($id, (float) $sesion->CS_MontoApertura);

        return response()->json([
            'sesion' => $sesion,
            'montoEsperado' => $montoEsperado,
            'resumenPorMetodo' => $resumenPorMetodo,
            'ventas' => $ventas,
            'compras' => $compras,
            'gastos' => $gastos,
        ]);
    }

    /**
     * Igual que CajaSesionController::calcularMontoEsperado(), pero la
     * porcion de "ventas en efectivo" se calcula desde venta_pago cuando la
     * venta tiene su detalle de pago dividido guardado, y cae al calculo
     * legacy (por venta.MEP_Id) para las que no lo tienen.
     */
    public static function calcularMontoEsperado(CajaSesion $sesion): float
    {
        $mepEfectivoId = DB::table('metodo_pago')->where('MEP_Pago', 'Efectivo')->value('MEP_Id');

        if (! $mepEfectivoId) {
            return (float) $sesion->CS_MontoApertura;
        }

        $ventasConDetalle = DB::table('venta_pago as vp')
            ->join('venta as v', 'v.VEN_Id', '=', 'vp.VEN_Id')
            ->where('v.CS_Id', $sesion->CS_Id)
            ->where('v.VEN_Status', 1)
            ->pluck('vp.VEN_Id')
            ->unique()
            ->all();

        $efectivoConDetalle = (float) DB::table('venta_pago as vp')
            ->join('venta as v', 'v.VEN_Id', '=', 'vp.VEN_Id')
            ->where('v.CS_Id', $sesion->CS_Id)
            ->where('v.VEN_Status', 1)
            ->where('vp.MEP_Id', $mepEfectivoId)
            ->sum('vp.VPG_Monto');

        $efectivoSinDetalle = (float) DB::table('venta as v')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->where('v.CS_Id', $sesion->CS_Id)
            ->where('v.MEP_Id', $mepEfectivoId)
            ->where('v.VEN_Status', 1)
            ->whereNotIn('v.VEN_Id', $ventasConDetalle ?: [0])
            ->sum(DB::raw('(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) - dv.DEV_Descuento'));

        $ventasEfectivo = $efectivoConDetalle + $efectivoSinDetalle;

        // Compras y gastos no tienen pago dividido (fuera del alcance de esta
        // funcionalidad): se calculan exactamente igual que en el original.
        $comprasEfectivo = DB::table('compra as co')
            ->join('detalle_compra as dc', 'dc.COM_Id', '=', 'co.COM_Id')
            ->where('co.CS_Id', $sesion->CS_Id)
            ->where('co.MEP_Id', $mepEfectivoId)
            ->where('co.COM_Status', 1)
            ->sum(DB::raw('dc.DCOM_Cantidad * dc.DCOM_PrecioCompra'));

        $gastosEfectivo = DB::table('gasto')
            ->where('CS_Id', $sesion->CS_Id)
            ->where('MEP_Id', $mepEfectivoId)
            ->where('GAS_Status', 1)
            ->where('GAS_Afecta', 'SI')
            ->sum('GAS_Monto');

        return round((float) $sesion->CS_MontoApertura + $ventasEfectivo - (float) $comprasEfectivo - (float) $gastosEfectivo, 2);
    }

    /**
     * Igual que CajaSesionController::resumenPorMetodoPago(), pero el lado
     * de ventas se arma combinando venta_pago (ventas con detalle dividido)
     * con el calculo legacy por venta.MEP_Id (ventas sin detalle).
     */
    private static function resumenPorMetodoPago(string $csId, float $montoApertura): array
    {
        $metodos = DB::table('metodo_pago')->orderBy('MEP_Id')->pluck('MEP_Pago', 'MEP_Id');

        $ventasConDetalle = DB::table('venta_pago as vp')
            ->join('venta as v', 'v.VEN_Id', '=', 'vp.VEN_Id')
            ->where('v.CS_Id', $csId)
            ->where('v.VEN_Status', 1)
            ->pluck('vp.VEN_Id')
            ->unique()
            ->all();

        $ventasPorMetodoDetalle = DB::table('venta_pago as vp')
            ->join('venta as v', 'v.VEN_Id', '=', 'vp.VEN_Id')
            ->where('v.CS_Id', $csId)
            ->where('v.VEN_Status', 1)
            ->select('vp.MEP_Id', DB::raw('SUM(vp.VPG_Monto) as total'))
            ->groupBy('vp.MEP_Id')
            ->pluck('total', 'MEP_Id');

        $ventasPorMetodoSinDetalle = DB::table('venta as v')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->where('v.CS_Id', $csId)
            ->where('v.VEN_Status', 1)
            ->whereNotIn('v.VEN_Id', $ventasConDetalle ?: [0])
            ->select('v.MEP_Id', DB::raw('SUM((dv.DEV_Cantidad * dv.DEV_PrecioUnitario) - dv.DEV_Descuento) as total'))
            ->groupBy('v.MEP_Id')
            ->pluck('total', 'MEP_Id');

        $ventasPorMetodo = [];
        foreach ($metodos as $mepId => $nombre) {
            $ventasPorMetodo[$mepId] = (float) ($ventasPorMetodoDetalle[$mepId] ?? 0)
                + (float) ($ventasPorMetodoSinDetalle[$mepId] ?? 0);
        }

        $comprasPorMetodo = DB::table('compra as co')
            ->join('detalle_compra as dc', 'dc.COM_Id', '=', 'co.COM_Id')
            ->where('co.CS_Id', $csId)
            ->where('co.COM_Status', 1)
            ->select('co.MEP_Id', DB::raw('SUM(dc.DCOM_Cantidad * dc.DCOM_PrecioCompra) as total'))
            ->groupBy('co.MEP_Id')
            ->pluck('total', 'MEP_Id');

        $gastosPorMetodo = DB::table('gasto')
            ->where('CS_Id', $csId)
            ->where('GAS_Status', 1)
            ->where('GAS_Afecta', 'SI')
            ->select('MEP_Id', DB::raw('SUM(GAS_Monto) as total'))
            ->groupBy('MEP_Id')
            ->pluck('total', 'MEP_Id');

        $columnas = [];
        $totalVentas = 0;
        $totalCompras = 0;
        $totalGastos = 0;

        foreach ($metodos as $mepId => $nombre) {
            $ventas = (float) ($ventasPorMetodo[$mepId] ?? 0);
            $compras = (float) ($comprasPorMetodo[$mepId] ?? 0);
            $gastos = (float) ($gastosPorMetodo[$mepId] ?? 0);

            $columnas[] = [
                'nombre' => $nombre,
                'ventas' => round($ventas, 2),
                'compras' => round($compras, 2),
                'gastos' => round($gastos, 2),
                'neto' => round($ventas - $compras - $gastos, 2),
            ];

            $totalVentas += $ventas;
            $totalCompras += $compras;
            $totalGastos += $gastos;
        }

        return [
            'columnas' => $columnas,
            'total_ventas' => round($totalVentas, 2),
            'total_compras' => round($totalCompras, 2),
            'total_gastos' => round($totalGastos, 2),
            'total_neto' => round($totalVentas - $totalCompras - $totalGastos, 2),
        ];
    }
}
