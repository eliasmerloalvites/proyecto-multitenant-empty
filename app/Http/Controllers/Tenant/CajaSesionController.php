<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Caja;
use App\Models\Tenant\CajaSesion;
use App\Services\Caja\FlujoPorMetodoService;
use App\Services\Exportacion\CajaSesionExcel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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
        return FlujoPorMetodoService::porSesion($csId)->ventas();
    }

    /**
     * Abonos (pagos parciales de cuentas por cobrar) recibidos DURANTE este
     * turno, por metodo real: [MEP_Id => monto]. Un abono se liga a la
     * caja/sesion activa al momento de registrarlo, no a la de la venta
     * original (que pudo ser en un turno o hasta un dia distinto) - por eso
     * va aparte de ventasPorMetodoEnSesion, que agrupa por CS_Id de la venta.
     */
    private static function abonosPorMetodoEnSesion(string $csId): array
    {
        return FlujoPorMetodoService::porSesion($csId)->abonos();
    }

    /**
     * Compras activas del turno por metodo real: [MEP_Id => monto]. Una
     * compra con pago mixto (compra_pago) se reparte en sus metodos en vez
     * de quedar entera bajo "Pago Mixto"; las demas van a su MEP_Id.
     */
    private static function comprasPorMetodoEnSesion(string $csId): array
    {
        return FlujoPorMetodoService::porSesion($csId)->compras();
    }

    /**
     * Pagos a proveedores (abonos de cuentas por pagar, incluido el monto
     * inicial de una compra al credito) hechos DURANTE este turno, por
     * metodo: [MEP_Id => monto]. Es dinero que SALE de la caja; la compra al
     * credito en si no sale de caja (va bajo el metodo "Credito").
     */
    private static function pagosProveedorPorMetodoEnSesion(string $csId): array
    {
        return FlujoPorMetodoService::porSesion($csId)->pagosProveedor();
    }

    /**
     * Adelantos de pago sobre cuentas de bahia (todavia abiertas o ya
     * cobradas) recibidos DURANTE este turno, por metodo: [MEP_Id => monto].
     */
    private static function adelantosPorMetodoEnSesion(string $csId): array
    {
        return FlujoPorMetodoService::porSesion($csId)->adelantos();
    }

    /**
     * Monto que debería haber en la caja al cierre: lo que abrió + ventas
     * en efectivo - compras en efectivo - gastos en efectivo + abonos de
     * cuentas por cobrar cobrados en efectivo - pagos a proveedores
     * (cuentas por pagar) en efectivo, todo registrado dentro de
     * ese turno (CS_Id). Las ventas con pago mixto solo aportan la porcion
     * que realmente se pago en efectivo.
     */
    public static function calcularMontoEsperado(CajaSesion $sesion): float
    {
        $mepEfectivoId = DB::table('metodo_pago')->where('MEP_Pago', 'Efectivo')->value('MEP_Id');

        if (! $mepEfectivoId) {
            return (float) $sesion->CS_MontoApertura;
        }

        $ventasEfectivo = self::ventasPorMetodoEnSesion((string) $sesion->CS_Id)[$mepEfectivoId] ?? 0;
        $abonosEfectivo = self::abonosPorMetodoEnSesion((string) $sesion->CS_Id)[$mepEfectivoId] ?? 0;
        $adelantosEfectivo = self::adelantosPorMetodoEnSesion((string) $sesion->CS_Id)[$mepEfectivoId] ?? 0;
        $ventasEfectivo += $abonosEfectivo + $adelantosEfectivo;

        $comprasEfectivo = self::comprasPorMetodoEnSesion((string) $sesion->CS_Id)[$mepEfectivoId] ?? 0;

        $gastosEfectivo = FlujoPorMetodoService::porSesion((string) $sesion->CS_Id)->gastos()[$mepEfectivoId] ?? 0;

        $pagosProveedorEfectivo = self::pagosProveedorPorMetodoEnSesion((string) $sesion->CS_Id)[$mepEfectivoId] ?? 0;

        return round((float) $sesion->CS_MontoApertura + (float) $ventasEfectivo - (float) $comprasEfectivo - (float) $gastosEfectivo - (float) $pagosProveedorEfectivo, 2);
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
                    return '<a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary verDetalleSesion" data-id="' . $row->CS_Id . '" title="Ver detalle"><i class="fa fa-eye"></i></a> '
                        . '<a href="javascript:void(0)" class="btn btn-sm btn-success exportarDetalleSesion" data-id="' . $row->CS_Id . '" title="Descargar el detalle en Excel"><i class="fa fa-file-excel"></i></a>';
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
            ->leftJoin('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->where('v.CS_Id', $id)
            // DEV_PrecioUnitario ya es el precio final con descuento
            // aplicado; no se resta DEV_Descuento de nuevo (ver nota en
            // ventasPorMetodoEnSesion). Se trae DOV_Anulado para que el
            // detalle del turno pueda marcar las ventas/notas anuladas en
            // vez de mostrarlas como si siguieran activas (el resumen por
            // metodo ya las excluye via VEN_Status, pero este listado es
            // historico y las sigue mostrando a proposito).
            ->select('v.VEN_Id', 'c.CLI_Nombre', 'mp.MEP_Pago', 'v.created_at', 'v.VEN_Status', DB::raw('MAX(dov.DOV_Anulado) as DOV_Anulado'), DB::raw('SUM(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) as total'))
            ->groupBy('v.VEN_Id', 'c.CLI_Nombre', 'mp.MEP_Pago', 'v.created_at', 'v.VEN_Status')
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

        if (Schema::hasTable('compra_pago') && $compras->isNotEmpty()) {
            $desglose = DB::table('compra_pago as cp')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cp.MEP_Id')
                ->whereIn('cp.COM_Id', $compras->pluck('COM_Id'))
                ->orderBy('cp.CPG_Id')
                ->get(['cp.COM_Id', 'mp.MEP_Pago', 'cp.CPG_Monto'])
                ->groupBy('COM_Id');
            $compras->each(function ($c) use ($desglose) {
                $pagos = $desglose->get($c->COM_Id);
                if ($pagos) {
                    $c->MEP_Pago = 'Mixto: ' . $pagos->map(fn ($p) => $p->MEP_Pago . ' S/ ' . number_format((float) $p->CPG_Monto, 2))->implode(' + ');
                }
            });
        }

        $gastos = DB::table('gasto as g')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'g.MEP_Id')
            ->where('g.CS_Id', $id)
            ->select('g.GAS_Id', 'g.GAS_Descripcion', 'g.GAS_Monto', 'mp.MEP_Pago', 'g.GAS_Fecha', 'g.GAS_Afecta')
            ->orderByDesc('g.GAS_Id')
            ->get();

        // Abonos de cuentas por cobrar cobrados en este turno (antes solo se
        // veia el total por metodo, sin saber de que cliente/venta era).
        $abonos = Schema::hasTable('cuenta_por_cobrar_abono')
            ? DB::table('cuenta_por_cobrar_abono as a')
                ->join('cuenta_por_cobrar as cpc', 'cpc.CPC_Id', '=', 'a.CPC_Id')
                ->join('venta as v', 'v.VEN_Id', '=', 'cpc.VEN_Id')
                ->leftJoin('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'a.MEP_Id')
                ->leftJoin('users as u', 'u.id', '=', 'a.USU_Id')
                ->where('a.CS_Id', $id)
                ->orderByDesc('a.CPA_Id')
                ->get(['a.CPA_Id', 'v.VEN_Id', 'c.CLI_Nombre', 'mp.MEP_Pago', 'a.CPA_Monto', 'a.created_at', 'u.name as usuario'])
            : collect();

        // Pagos a proveedores (cuentas por pagar) hechos en este turno.
        $pagosProveedor = Schema::hasTable('cuenta_por_pagar_abono')
            ? DB::table('cuenta_por_pagar_abono as a')
                ->join('cuenta_por_pagar as cpp', 'cpp.CPP_Id', '=', 'a.CPP_Id')
                ->join('compra as co', 'co.COM_Id', '=', 'cpp.COM_Id')
                ->leftJoin('proveedor as p', 'p.PROV_Id', '=', 'co.PROV_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'a.MEP_Id')
                ->leftJoin('users as u', 'u.id', '=', 'a.USU_Id')
                ->where('a.CS_Id', $id)
                ->orderByDesc('a.CPPA_Id')
                ->get(['a.CPPA_Id', 'co.COM_Id', 'co.COM_TipoDocumento', 'co.COM_NumDocumento', 'p.PROV_RazonSocial', 'mp.MEP_Pago', 'a.CPPA_Monto', 'a.created_at', 'u.name as usuario'])
            : collect();

        // Adelantos de bahia cobrados en este turno.
        $adelantos = Schema::hasTable('bahia_cuenta_adelanto')
            ? DB::table('bahia_cuenta_adelanto as a')
                ->join('bahia_cuenta as b', 'b.BCT_Id', '=', 'a.BCT_Id')
                ->join('reservacion as r', 'r.RES_Id', '=', 'b.RES_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'a.MEP_Id')
                ->leftJoin('users as u', 'u.id', '=', 'a.USU_Id')
                ->where('a.CS_Id', $id)
                ->orderByDesc('a.BCA_Id')
                ->get(['a.BCA_Id', 'b.BCT_Id', 'b.VEN_Id', 'b.BCT_Estado', 'r.RES_Cliente', 'r.RES_Placa', 'mp.MEP_Pago', 'a.BCA_Monto', 'a.created_at', 'u.name as usuario'])
            : collect();

        // Placa de la moto cuando la venta salio de una cuenta de bahia (la
        // venta en si no guarda placa; una venta de mostrador no tiene).
        $placas = self::placasDeVentas($ventas->pluck('VEN_Id')->merge($abonos->pluck('VEN_Id'))->unique()->all());
        $ventas->each(fn ($v) => $v->placa = $placas[$v->VEN_Id] ?? null);
        $abonos->each(fn ($a) => $a->placa = $placas[$a->VEN_Id] ?? null);

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
            'abonos' => $abonos,
            'adelantos' => $adelantos,
            'pagosProveedor' => $pagosProveedor,
        ]);
    }

    /** [VEN_Id => placa] de las ventas que cerraron una cuenta de bahia. */
    private static function placasDeVentas(array $venIds): array
    {
        if (empty($venIds) || ! Schema::hasTable('bahia_cuenta')) {
            return [];
        }

        return DB::table('bahia_cuenta as b')
            ->join('reservacion as r', 'r.RES_Id', '=', 'b.RES_Id')
            ->whereIn('b.VEN_Id', $venIds)
            ->whereNotNull('r.RES_Placa')
            ->where('r.RES_Placa', '<>', '')
            ->pluck('r.RES_Placa', 'b.VEN_Id')
            ->map(fn ($p) => strtoupper(trim($p)))
            ->all();
    }

    /**
     * Excel con el detalle completo de un turno (resumen del arqueo + ventas,
     * compras y gastos). Parte del mismo detalle() que muestra la pantalla,
     * para que el archivo y el modal nunca discrepen.
     */
    public function exportar(string $id)
    {
        $respuesta = $this->detalle($id);

        if ($respuesta->getStatusCode() !== 200) {
            return $respuesta;
        }

        $sesion = CajaSesion::with(['caja', 'usuarioApertura:id,name', 'usuarioCierre:id,name'])->findOrFail($id);

        return CajaSesionExcel::descargar($sesion, $respuesta->getData(true));
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
        $abonosPorMetodo = self::abonosPorMetodoEnSesion($csId);
        $adelantosPorMetodo = self::adelantosPorMetodoEnSesion($csId);
        $pagosProveedorPorMetodo = self::pagosProveedorPorMetodoEnSesion($csId);

        $comprasPorMetodo = self::comprasPorMetodoEnSesion($csId);

        $gastosPorMetodo = FlujoPorMetodoService::porSesion($csId)->gastos();

        $columnas = [];
        $totalVentas = 0;
        $totalAbonos = 0;
        $totalAdelantos = 0;
        $totalCompras = 0;
        $totalPagosProveedor = 0;
        $totalGastos = 0;

        foreach ($metodos as $mepId => $nombre) {
            $ventas = (float) ($ventasPorMetodo[$mepId] ?? 0);
            $abonos = (float) ($abonosPorMetodo[$mepId] ?? 0);
            $adelantos = (float) ($adelantosPorMetodo[$mepId] ?? 0);
            $compras = (float) ($comprasPorMetodo[$mepId] ?? 0);
            $pagosProveedor = (float) ($pagosProveedorPorMetodo[$mepId] ?? 0);
            $gastos = (float) ($gastosPorMetodo[$mepId] ?? 0);

            $columnas[] = [
                'nombre' => $nombre,
                'ventas' => round($ventas, 2),
                'abonos' => round($abonos, 2),
                'adelantos' => round($adelantos, 2),
                'compras' => round($compras, 2),
                'pagos_proveedor' => round($pagosProveedor, 2),
                'gastos' => round($gastos, 2),
                'neto' => round($ventas + $abonos + $adelantos - $compras - $pagosProveedor - $gastos, 2),
            ];

            $totalVentas += $ventas;
            $totalAbonos += $abonos;
            $totalAdelantos += $adelantos;
            $totalCompras += $compras;
            $totalPagosProveedor += $pagosProveedor;
            $totalGastos += $gastos;
        }

        return [
            'columnas' => $columnas,
            'total_ventas' => round($totalVentas, 2),
            'total_abonos' => round($totalAbonos, 2),
            'total_adelantos' => round($totalAdelantos, 2),
            'total_compras' => round($totalCompras, 2),
            'total_pagos_proveedor' => round($totalPagosProveedor, 2),
            'total_gastos' => round($totalGastos, 2),
            'total_neto' => round($totalVentas + $totalAbonos + $totalAdelantos - $totalCompras - $totalPagosProveedor - $totalGastos, 2),
        ];
    }
}
