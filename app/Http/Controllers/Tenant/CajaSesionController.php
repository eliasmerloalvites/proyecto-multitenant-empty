<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Caja;
use App\Models\Tenant\CajaSesion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CajaSesionController extends Controller
{
    /**
     * Fija con qué caja (ya aperturada) va a operar el usuario en esta
     * sesión. Ver tenant_caja_activa_id() en app/helpers.php.
     */
    public function seleccionar(Request $request)
    {
        $validated = $request->validate([
            'caja_id' => 'required|integer',
        ]);

        $caja = Caja::where('CAJ_Id', $validated['caja_id'])
            ->where('CAJ_Status', 1)
            ->whereHas('sesionAbierta')
            ->first();

        if (! $caja) {
            return response()->json(['error' => 'Esa caja no está aperturada. Ábrela primero.'], 422);
        }

        $request->session()->put('caja_activa_id', $caja->CAJ_Id);

        return response()->json(['success' => 'Caja seleccionada: ' . $caja->CAJ_Nombre]);
    }

    /**
     * Abre un turno nuevo para una caja (monto de apertura + timestamp +
     * usuario). Falla si esa caja ya tiene un turno abierto.
     */
    public function abrir(Request $request)
    {
        $validated = $request->validate([
            'caja_id' => 'required|integer|exists:caja,CAJ_Id',
            'monto_apertura' => 'required|numeric|min:0',
        ]);

        $caja = Caja::where('CAJ_Id', $validated['caja_id'])->where('CAJ_Status', 1)->first();

        if (! $caja) {
            return response()->json(['error' => 'Esa caja no existe o está inactiva.'], 422);
        }

        if ($caja->sesionAbierta()->exists()) {
            return response()->json(['error' => 'Esa caja ya tiene un turno abierto.'], 422);
        }

        $sesion = CajaSesion::create([
            'CAJ_Id' => $caja->CAJ_Id,
            'USU_Id_Apertura' => Auth::guard('tenant')->id(),
            'CS_MontoApertura' => $validated['monto_apertura'],
            'CS_FechaApertura' => Carbon::now('America/Lima'),
            'CS_Estado' => 'abierta',
        ]);

        $request->session()->put('caja_activa_id', $caja->CAJ_Id);

        return response()->json(['success' => 'Caja "' . $caja->CAJ_Nombre . '" aperturada.', 'sesion' => $sesion]);
    }

    /**
     * Cierra el turno abierto de una caja: calcula el monto esperado
     * (apertura + ventas en efectivo - compras en efectivo - gastos en
     * efectivo, todo dentro de ese turno) y compara contra lo que el
     * usuario contó físicamente.
     */
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
     * Ingreso por venta ya prorrateado entre sus metodos de pago reales:
     * [MEP_Id => monto]. Una venta de pago simple aporta toda su venta
     * (cantidad*precio - descuento) a su unico metodo, igual que siempre.
     * Una venta con pago mixto (venta_pago con 2+ filas) reparte esa misma
     * venta proporcionalmente a lo que se pago con cada metodo, asi que el
     * vuelto (que siempre sale del efectivo) queda descontado sin necesidad
     * de rastrearlo aparte: si de S/100 en efectivo se dieron S/24 de
     * vuelto sobre una venta de S/76, el efectivo tendido (100) prorratea
     * el 100% de esa venta (76) porque fue el unico metodo usado.
     *
     * Ventas sin fila en venta_pago (de antes de que existiera esa tabla)
     * caen al viejo criterio: toda la venta se atribuye a venta.MEP_Id.
     */
    private static function ventasPorMetodoEnSesion(string $csId): array
    {
        $revenuePorVenta = DB::table('venta as v')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->where('v.CS_Id', $csId)
            ->where('v.VEN_Status', 1)
            ->select('v.VEN_Id', 'v.MEP_Id', DB::raw('SUM((dv.DEV_Cantidad * dv.DEV_PrecioUnitario) - dv.DEV_Descuento) as revenue'))
            ->groupBy('v.VEN_Id', 'v.MEP_Id')
            ->get();

        $pagosPorVenta = DB::table('venta_pago as vp')
            ->join('venta as v', 'v.VEN_Id', '=', 'vp.VEN_Id')
            ->where('v.CS_Id', $csId)
            ->where('v.VEN_Status', 1)
            ->select('vp.VEN_Id', 'vp.MEP_Id', 'vp.VNP_Monto')
            ->get()
            ->groupBy('VEN_Id');

        $porMetodo = [];

        foreach ($revenuePorVenta as $venta) {
            $revenue = (float) $venta->revenue;
            $pagos = $pagosPorVenta->get($venta->VEN_Id);

            if (! $pagos || $pagos->isEmpty()) {
                // Sin detalle de pagos (venta anterior a venta_pago): todo
                // el ingreso va a su unico metodo, como antes.
                $porMetodo[$venta->MEP_Id] = ($porMetodo[$venta->MEP_Id] ?? 0) + $revenue;
                continue;
            }

            $totalTendido = (float) $pagos->sum('VNP_Monto');

            foreach ($pagos as $pago) {
                $porcion = $totalTendido > 0 ? $revenue * ((float) $pago->VNP_Monto / $totalTendido) : 0;
                $porMetodo[$pago->MEP_Id] = ($porMetodo[$pago->MEP_Id] ?? 0) + $porcion;
            }
        }

        return $porMetodo;
    }

    /**
     * Monto que debería haber en la caja al cierre: lo que abrió + ventas
     * en efectivo - compras en efectivo - gastos en efectivo, todo
     * registrado dentro de ese turno (CS_Id). Las ventas con pago mixto
     * solo aportan la porcion que realmente se pago en efectivo.
     */
    public static function calcularMontoEsperado(CajaSesion $sesion): float
    {
        $mepEfectivoId = DB::table('metodo_pago')->where('MEP_Pago', 'Efectivo')->value('MEP_Id');

        if (! $mepEfectivoId) {
            return (float) $sesion->CS_MontoApertura;
        }

        $ventasEfectivo = self::ventasPorMetodoEnSesion((string) $sesion->CS_Id)[$mepEfectivoId] ?? 0;

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

        return round((float) $sesion->CS_MontoApertura + (float) $ventasEfectivo - (float) $comprasEfectivo - (float) $gastosEfectivo, 2);
    }

    /**
     * Historial de turnos de todas las cajas + detalle del flujo (ventas,
     * compras, gastos) de cada uno, para el panel de arqueos.
     */
    public function historial(Request $request)
    {
        if ($request->ajax()) {
            $data = CajaSesion::with(['caja', 'usuarioApertura:id,name', 'usuarioCierre:id,name'])
                ->orderByDesc('CS_Id')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('caja', fn ($row) => $row->caja->CAJ_Nombre ?? '—')
                ->addColumn('apertura', fn ($row) => $row->CS_FechaApertura?->format('d/m/Y H:i'))
                ->addColumn('cierre', fn ($row) => $row->CS_FechaCierre?->format('d/m/Y H:i') ?? '—')
                ->addColumn('monto_apertura', fn ($row) => 'S/ ' . number_format($row->CS_MontoApertura, 2))
                ->addColumn('monto_esperado', fn ($row) => $row->CS_MontoEsperado !== null ? 'S/ ' . number_format($row->CS_MontoEsperado, 2) : '—')
                ->addColumn('monto_real', fn ($row) => $row->CS_MontoReal !== null ? 'S/ ' . number_format($row->CS_MontoReal, 2) : '—')
                ->addColumn('diferencia', function ($row) {
                    if ($row->CS_Diferencia === null) {
                        return '—';
                    }
                    $color = $row->CS_Diferencia == 0 ? '#16A34A' : ($row->CS_Diferencia < 0 ? '#DC2626' : '#D97706');
                    return '<span style="color:' . $color . ';font-weight:600;">S/ ' . number_format($row->CS_Diferencia, 2) . '</span>';
                })
                ->addColumn('estado', function ($row) {
                    return $row->CS_Estado === 'abierta'
                        ? '<span class="badge badge-success">Abierta</span>'
                        : '<span class="badge badge-secondary">Cerrada</span>';
                })
                ->addColumn('usuarios', function ($row) {
                    $apertura = $row->usuarioApertura->name ?? '—';
                    $cierre = $row->usuarioCierre->name ?? '—';
                    return $apertura . ' / ' . $cierre;
                })
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary verDetalleSesion" data-id="' . $row->CS_Id . '"><i class="fa fa-eye"></i></a>';
                })
                ->rawColumns(['diferencia', 'estado', 'action'])
                ->make(true);
        }

        $cajas = Caja::where('CAJ_Status', 1)->orderBy('CAJ_Nombre')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.caja.historial', compact('cajas'));
    }

    /**
     * Detalle de un turno específico: totales + el flujo completo de
     * ventas, compras y gastos que ocurrieron durante ese turno.
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

        // Turno todavía abierto: CS_MontoEsperado aún no se calculó (eso
        // pasa recién al cerrar), así que se calcula al vuelo para que el
        // detalle siempre muestre lo esperado, no un guion.
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
     * Desglose de Ingresos por Ventas / Egresos por Compras / Egresos por
     * Gastos / Totales Netos, con una columna por cada método de pago que
     * exista en el tenant (Efectivo, Bancarizado, Yape/Plin, etc.) — mismo
     * cuadro que ya se usa en el widget de Cobros de la Semana, pero por
     * turno de caja.
     */
    private static function resumenPorMetodoPago(string $csId, float $montoApertura): array
    {
        $metodos = DB::table('metodo_pago')->orderBy('MEP_Id')->pluck('MEP_Pago', 'MEP_Id');

        // Prorrateado por metodo real de pago (ver ventasPorMetodoEnSesion):
        // una venta con pago mixto ya no aparece entera bajo "Pago Mixto".
        $ventasPorMetodo = self::ventasPorMetodoEnSesion($csId);

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
