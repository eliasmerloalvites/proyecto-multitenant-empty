<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Concerns\ResuelvePeriodo;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Reportes orientados al dueño del negocio: rentabilidad real (ingresos -
 * costo de lo vendido - gastos) e inventario valorizado. A diferencia del
 * dashboard (que solo muestra ingresos/gastos de hoy y del mes en curso),
 * estos reportes permiten elegir el periodo (hoy/semana/mes/personalizado),
 * filtrar por sede y comparar contra el periodo anterior equivalente.
 */
class ReportesFinancierosController extends Controller
{
    use ResuelvePeriodo;

    /**
     * Utilidad = ingresos de las ventas - costo real de los productos
     * vendidos (tomado del lote especifico que se vendio, dv.LOT_Id) -
     * gastos del periodo. Es el numero que hoy no calcula ningun reporte
     * existente en el sistema.
     */
    public function rentabilidad(Request $request)
    {
        if (!$request->ajax()) {
            return view('tenant_tallermoto.reportes.rentabilidad', [
                'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
            ]);
        }

        [$inicio, $fin] = $this->resolverPeriodo($request);
        [$inicioAnt, $finAnt] = $this->periodoAnterior($inicio, $fin);

        $almacenId = $request->input('almacen_id');

        $calcular = function (Carbon $desde, Carbon $hasta) use ($almacenId) {
            $ventas = DB::table('detalle_venta as dv')
                ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
                ->leftJoin('lote as l', 'l.LOT_Id', '=', 'dv.LOT_Id')
                ->where('v.VEN_Status', 1)
                ->whereBetween('v.created_at', [$desde, $hasta])
                ->when($almacenId, fn ($q) => $q->where('v.ALM_Id', $almacenId))
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

            return [
                'ingresos' => round($ingresos, 2),
                'costo' => round($costo, 2),
                'utilidadBruta' => round($utilidadBruta, 2),
                'gastos' => round($gastos, 2),
                'utilidadNeta' => round($utilidadBruta - $gastos, 2),
            ];
        };

        $actual = $calcular($inicio, $fin);
        $anterior = $calcular($inicioAnt, $finAnt);

        $porProducto = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->leftJoin('lote as l', 'l.LOT_Id', '=', 'dv.LOT_Id')
            ->where('v.VEN_Status', 1)
            ->whereBetween('v.created_at', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('v.ALM_Id', $almacenId))
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre')
            ->select(
                'p.PRO_Id',
                'p.PRO_Nombre',
                DB::raw('SUM(dv.DEV_Cantidad) as unidades'),
                DB::raw('COALESCE(SUM(dv.DEV_Cantidad * dv.DEV_PrecioUnitario), 0) as ingreso'),
                DB::raw('COALESCE(SUM(dv.DEV_Cantidad * COALESCE(l.LOT_PrecioCompra, 0)), 0) as costo')
            )
            ->orderByDesc('ingreso')
            ->limit(15)
            ->get()
            ->map(function ($row) {
                $row->ingreso = round((float) $row->ingreso, 2);
                $row->costo = round((float) $row->costo, 2);
                $row->utilidad = round($row->ingreso - $row->costo, 2);
                $row->margen_pct = $row->ingreso > 0 ? round(($row->utilidad / $row->ingreso) * 100, 1) : 0;

                return $row;
            });

        $serie = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->leftJoin('lote as l', 'l.LOT_Id', '=', 'dv.LOT_Id')
            ->where('v.VEN_Status', 1)
            ->whereBetween('v.created_at', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('v.ALM_Id', $almacenId))
            ->selectRaw("
                DATE(v.created_at) as fecha,
                COALESCE(SUM(dv.DEV_Cantidad * dv.DEV_PrecioUnitario), 0) as ingreso,
                COALESCE(SUM(dv.DEV_Cantidad * COALESCE(l.LOT_PrecioCompra, 0)), 0) as costo
            ")
            ->groupBy(DB::raw('DATE(v.created_at)'))
            ->orderBy('fecha')
            ->get()
            ->map(fn ($r) => [
                'fecha' => $r->fecha,
                'ingreso' => round((float) $r->ingreso, 2),
                'costo' => round((float) $r->costo, 2),
                'utilidad' => round((float) $r->ingreso - (float) $r->costo, 2),
            ]);

        return response()->json([
            'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'actual' => $actual,
            'anterior' => $anterior,
            'variacion' => [
                'ingresos' => $this->crecimientoPorcentual($actual['ingresos'], $anterior['ingresos']),
                'utilidadNeta' => $this->crecimientoPorcentual($actual['utilidadNeta'], $anterior['utilidadNeta']),
            ],
            'porProducto' => $porProducto,
            'serie' => $serie,
        ]);
    }

    /**
     * Cuanto capital hay inmovilizado en stock ahora mismo (es una foto del
     * presente, no depende del periodo) y, usando el periodo elegido, que
     * productos con stock no se vendieron ni una vez en ese rango (posible
     * stock muerto).
     */
    public function inventario(Request $request)
    {
        if (!$request->ajax()) {
            return view('tenant_tallermoto.reportes.inventario', [
                'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
            ]);
        }

        [$inicio, $fin] = $this->resolverPeriodo($request);
        $almacenId = $request->input('almacen_id');

        $productos = DB::table('producto as p')
            ->join('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->leftJoin('lote as l', function ($join) use ($almacenId) {
                $join->on('l.PRO_Id', '=', 'p.PRO_Id');
                if ($almacenId) {
                    $join->where('l.ALM_Id', '=', $almacenId);
                }
            })
            ->where('p.PRO_Status', 1)
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'cat.CAT_Nombre', 'p.PRO_PrecioCompra', 'p.PRO_PrecioVenta')
            ->select(
                'p.PRO_Id',
                'p.PRO_Nombre',
                'cat.CAT_Nombre as categoria',
                DB::raw('COALESCE(SUM(l.LOT_CantidadReal), 0) as stock'),
                DB::raw('COALESCE(
                    SUM(CASE WHEN l.LOT_CantidadReal > 0 THEN l.LOT_CantidadReal * l.LOT_PrecioCompra ELSE 0 END)
                    / NULLIF(SUM(CASE WHEN l.LOT_CantidadReal > 0 THEN l.LOT_CantidadReal ELSE 0 END), 0),
                    p.PRO_PrecioCompra
                ) as costo_promedio'),
                'p.PRO_PrecioVenta'
            )
            ->get()
            ->map(function ($p) {
                $p->stock = (float) $p->stock;
                $p->costo_promedio = round((float) $p->costo_promedio, 2);
                $p->valor_costo = round($p->stock * $p->costo_promedio, 2);
                $p->valor_venta = round($p->stock * (float) $p->PRO_PrecioVenta, 2);

                return $p;
            });

        $vendidosEnPeriodo = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->where('v.VEN_Status', 1)
            ->whereBetween('v.created_at', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('v.ALM_Id', $almacenId))
            ->distinct()
            ->pluck('dv.PRO_Id')
            ->all();

        $sinMovimiento = $productos
            ->where('stock', '>', 0)
            ->whereNotIn('PRO_Id', $vendidosEnPeriodo)
            ->sortByDesc('valor_costo')
            ->values();

        $totales = [
            'valor_costo' => round($productos->sum('valor_costo'), 2),
            'valor_venta' => round($productos->sum('valor_venta'), 2),
            'ganancia_potencial' => round($productos->sum('valor_venta') - $productos->sum('valor_costo'), 2),
            'stock_critico' => $productos->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'sin_stock' => $productos->where('stock', '<=', 0)->count(),
        ];

        return response()->json([
            'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'totales' => $totales,
            'productos' => $productos->sortByDesc('valor_costo')->values(),
            'sinMovimiento' => $sinMovimiento,
        ]);
    }

    /**
     * Cuanto sale del negocio en compras de mercaderia y en gastos
     * operativos, por proveedor y por tipo de gasto. Complementa a
     * Rentabilidad (que ya usa el costo del lote vendido, no las compras
     * directamente) mostrando el detalle de a donde se va la plata.
     */
    public function comprasGastos(Request $request)
    {
        if (!$request->ajax()) {
            return view('tenant_tallermoto.reportes.compras-gastos', [
                'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
                'proveedores' => DB::table('proveedor')->where('PROV_Status', 1)->orderBy('PROV_RazonSocial')->get(),
            ]);
        }

        [$inicio, $fin] = $this->resolverPeriodo($request);
        [$inicioAnt, $finAnt] = $this->periodoAnterior($inicio, $fin);

        $almacenId = $request->input('almacen_id');
        $proveedorId = $request->input('proveedor_id');

        $totalCompras = function (Carbon $desde, Carbon $hasta) use ($almacenId, $proveedorId) {
            return (float) DB::table('detalle_compra as dc')
                ->join('compra as c', 'c.COM_Id', '=', 'dc.COM_Id')
                ->where('c.COM_Status', 1)
                ->whereBetween('c.created_at', [$desde, $hasta])
                ->when($almacenId, fn ($q) => $q->where('dc.ALM_Id', $almacenId))
                ->when($proveedorId, fn ($q) => $q->where('c.PROV_Id', $proveedorId))
                ->sum(DB::raw('dc.DCOM_Cantidad * dc.DCOM_PrecioCompra'));
        };

        $totalGastos = function (Carbon $desde, Carbon $hasta) use ($almacenId, $proveedorId) {
            return (float) DB::table('gasto')
                ->where('GAS_Status', 1)
                ->whereBetween('GAS_Fecha', [$desde->toDateString(), $hasta->toDateString()])
                ->when($almacenId, fn ($q) => $q->where('ALM_Id', $almacenId))
                ->when($proveedorId, fn ($q) => $q->where('PROV_Id', $proveedorId))
                ->sum('GAS_Monto');
        };

        $comprasActual = round($totalCompras($inicio, $fin), 2);
        $gastosActual = round($totalGastos($inicio, $fin), 2);
        $comprasAnterior = round($totalCompras($inicioAnt, $finAnt), 2);
        $gastosAnterior = round($totalGastos($inicioAnt, $finAnt), 2);

        $egresosActual = $comprasActual + $gastosActual;
        $egresosAnterior = $comprasAnterior + $gastosAnterior;

        // ================= COMPRAS POR PROVEEDOR =================

        $comprasPorProveedor = DB::table('detalle_compra as dc')
            ->join('compra as c', 'c.COM_Id', '=', 'dc.COM_Id')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
            ->where('c.COM_Status', 1)
            ->whereBetween('c.created_at', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('dc.ALM_Id', $almacenId))
            ->when($proveedorId, fn ($q) => $q->where('c.PROV_Id', $proveedorId))
            ->groupBy('p.PROV_Id', 'p.PROV_RazonSocial')
            ->select(
                'p.PROV_Id',
                'p.PROV_RazonSocial',
                DB::raw('COUNT(DISTINCT c.COM_Id) as compras'),
                DB::raw('SUM(dc.DCOM_Cantidad * dc.DCOM_PrecioCompra) as monto')
            )
            ->orderByDesc('monto')
            ->get()
            ->map(function ($r) {
                $r->monto = round((float) $r->monto, 2);
                return $r;
            });

        // ================= GASTOS POR TIPO =================

        $gastosPorTipo = DB::table('gasto as g')
            ->join('tipo_gasto as tg', 'tg.TG_Id', '=', 'g.TG_Id')
            ->where('g.GAS_Status', 1)
            ->whereBetween('g.GAS_Fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->when($almacenId, fn ($q) => $q->where('g.ALM_Id', $almacenId))
            ->when($proveedorId, fn ($q) => $q->where('g.PROV_Id', $proveedorId))
            ->groupBy('tg.TG_Id', 'tg.TG_Descripcion')
            ->select('tg.TG_Id', 'tg.TG_Descripcion', DB::raw('COUNT(*) as cantidad'), DB::raw('SUM(g.GAS_Monto) as monto'))
            ->orderByDesc('monto')
            ->get()
            ->map(function ($r) {
                $r->monto = round((float) $r->monto, 2);
                return $r;
            });

        // ================= DETALLE DE GASTOS =================

        $detalleGastos = DB::table('gasto as g')
            ->join('tipo_gasto as tg', 'tg.TG_Id', '=', 'g.TG_Id')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'g.PROV_Id')
            ->where('g.GAS_Status', 1)
            ->whereBetween('g.GAS_Fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->when($almacenId, fn ($q) => $q->where('g.ALM_Id', $almacenId))
            ->when($proveedorId, fn ($q) => $q->where('g.PROV_Id', $proveedorId))
            ->select(
                'g.GAS_Fecha',
                'g.GAS_Descripcion',
                'g.GAS_Monto',
                'tg.TG_Descripcion as tipo',
                'p.PROV_RazonSocial as proveedor'
            )
            ->orderByDesc('g.GAS_Fecha')
            ->limit(50)
            ->get();

        // ================= SERIE DIARIA =================

        $serieCompras = DB::table('detalle_compra as dc')
            ->join('compra as c', 'c.COM_Id', '=', 'dc.COM_Id')
            ->where('c.COM_Status', 1)
            ->whereBetween('c.created_at', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('dc.ALM_Id', $almacenId))
            ->when($proveedorId, fn ($q) => $q->where('c.PROV_Id', $proveedorId))
            ->selectRaw('DATE(c.created_at) as fecha, SUM(dc.DCOM_Cantidad * dc.DCOM_PrecioCompra) as monto')
            ->groupBy(DB::raw('DATE(c.created_at)'))
            ->pluck('monto', 'fecha');

        $serieGastos = DB::table('gasto')
            ->where('GAS_Status', 1)
            ->whereBetween('GAS_Fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->when($almacenId, fn ($q) => $q->where('ALM_Id', $almacenId))
            ->when($proveedorId, fn ($q) => $q->where('PROV_Id', $proveedorId))
            ->selectRaw('DATE(GAS_Fecha) as fecha, SUM(GAS_Monto) as monto')
            ->groupBy(DB::raw('DATE(GAS_Fecha)'))
            ->pluck('monto', 'fecha');

        $fechas = $serieCompras->keys()->merge($serieGastos->keys())->unique()->sort()->values();
        $serie = $fechas->map(fn ($f) => [
            'fecha' => $f,
            'compras' => round((float) ($serieCompras[$f] ?? 0), 2),
            'gastos' => round((float) ($serieGastos[$f] ?? 0), 2),
        ]);

        return response()->json([
            'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'actual' => [
                'compras' => $comprasActual,
                'gastos' => $gastosActual,
                'egresos' => round($egresosActual, 2),
            ],
            'variacion' => [
                'egresos' => $this->crecimientoPorcentual($egresosActual, $egresosAnterior),
            ],
            'comprasPorProveedor' => $comprasPorProveedor,
            'gastosPorTipo' => $gastosPorTipo,
            'detalleGastos' => $detalleGastos,
            'serie' => $serie,
        ]);
    }

    /**
     * Quienes compran mas (para fidelizar) y cuantos clientes nuevos se
     * registraron en el periodo (para medir si el negocio esta creciendo
     * su cartera o solo vendiendole a los de siempre).
     */
    public function clientes(Request $request)
    {
        if (!$request->ajax()) {
            return view('tenant_tallermoto.reportes.clientes', [
                'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
            ]);
        }

        [$inicio, $fin] = $this->resolverPeriodo($request);
        $almacenId = $request->input('almacen_id');

        // DEV_PrecioUnitario ya es el precio final con el descuento aplicado
        // (ver VentaController::store); restar DEV_Descuento de nuevo lo
        // descontaria dos veces.
        $totalVentaExpr = 'dv.DEV_Cantidad * dv.DEV_PrecioUnitario';

        $porCliente = DB::table('venta as v')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('v.VEN_Status', 1)
            ->whereBetween('v.created_at', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('v.ALM_Id', $almacenId))
            ->groupBy('c.CLI_Id', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_Celular')
            ->select(
                'c.CLI_Id',
                'c.CLI_Nombre',
                'c.CLI_NumDocumento',
                'c.CLI_Celular',
                DB::raw('COUNT(DISTINCT v.VEN_Id) as compras'),
                DB::raw("SUM($totalVentaExpr) as monto")
            )
            ->orderByDesc('monto')
            ->get()
            ->map(function ($r) {
                $r->monto = round((float) $r->monto, 2);
                $r->ticket_promedio = $r->compras > 0 ? round($r->monto / $r->compras, 2) : 0;

                return $r;
            });

        $clientesNuevos = DB::table('cliente')
            ->where('CLI_Status', 1)
            ->whereBetween('created_at', [$inicio, $fin])
            ->orderByDesc('created_at')
            ->select('CLI_Id', 'CLI_Nombre', 'CLI_NumDocumento', 'CLI_Celular', 'created_at')
            ->get();

        $totalClientesActivos = DB::table('cliente')->where('CLI_Status', 1)->count();

        return response()->json([
            'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'totales' => [
                'clientesQueCompraron' => $porCliente->count(),
                'clientesNuevos' => $clientesNuevos->count(),
                'clientesActivos' => $totalClientesActivos,
                'ticketPromedioGeneral' => $porCliente->sum('compras') > 0
                    ? round($porCliente->sum('monto') / $porCliente->sum('compras'), 2)
                    : 0,
            ],
            'topClientes' => $porCliente->take(20)->values(),
            'clientesNuevos' => $clientesNuevos,
        ]);
    }

    /**
     * Cuadre de caja acumulado: cuantas sesiones cerraron con diferencia
     * (sobrante o faltante) y por que cajero, para detectar patrones que
     * un cierre individual no muestra.
     */
    public function caja(Request $request)
    {
        if (!$request->ajax()) {
            return view('tenant_tallermoto.reportes.caja', [
                'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
            ]);
        }

        [$inicio, $fin] = $this->resolverPeriodo($request);
        $almacenId = $request->input('almacen_id');

        $base = DB::table('caja_sesion as cs')
            ->join('caja as c', 'c.CAJ_Id', '=', 'cs.CAJ_Id')
            ->where('cs.CS_Estado', 'cerrada')
            ->whereBetween('cs.CS_FechaCierre', [$inicio, $fin])
            ->when($almacenId, fn ($q) => $q->where('c.ALM_Id', $almacenId));

        $totales = (clone $base)->selectRaw('
                COUNT(*) as sesiones,
                COALESCE(SUM(cs.CS_MontoReal), 0) as monto_manejado,
                COALESCE(SUM(cs.CS_Diferencia), 0) as diferencia_total,
                COALESCE(SUM(CASE WHEN cs.CS_Diferencia <> 0 THEN 1 ELSE 0 END), 0) as con_descuadre
            ')
            ->first();

        $porCajero = (clone $base)
            ->join('users as u', 'u.id', '=', 'cs.USU_Id_Cierre')
            ->groupBy('u.id', 'u.name')
            ->select(
                'u.id',
                'u.name',
                DB::raw('COUNT(*) as sesiones'),
                DB::raw('COALESCE(SUM(cs.CS_Diferencia), 0) as diferencia_total'),
                DB::raw('COALESCE(SUM(CASE WHEN cs.CS_Diferencia <> 0 THEN 1 ELSE 0 END), 0) as con_descuadre')
            )
            ->orderByDesc('sesiones')
            ->get()
            ->map(function ($r) {
                $r->diferencia_total = round((float) $r->diferencia_total, 2);
                return $r;
            });

        $detalle = (clone $base)
            ->join('users as u', 'u.id', '=', 'cs.USU_Id_Cierre')
            ->select(
                'cs.CS_Id',
                'c.CAJ_Nombre',
                'u.name as cajero',
                'cs.CS_FechaApertura',
                'cs.CS_FechaCierre',
                'cs.CS_MontoEsperado',
                'cs.CS_MontoReal',
                'cs.CS_Diferencia',
                'cs.CS_TipoCierre'
            )
            ->orderByDesc('cs.CS_FechaCierre')
            ->limit(50)
            ->get();

        $serie = (clone $base)
            ->selectRaw('DATE(cs.CS_FechaCierre) as fecha, COALESCE(SUM(cs.CS_Diferencia), 0) as diferencia')
            ->groupBy(DB::raw('DATE(cs.CS_FechaCierre)'))
            ->orderBy('fecha')
            ->get()
            ->map(fn ($r) => ['fecha' => $r->fecha, 'diferencia' => round((float) $r->diferencia, 2)]);

        return response()->json([
            'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'totales' => [
                'sesiones' => (int) $totales->sesiones,
                'montoManejado' => round((float) $totales->monto_manejado, 2),
                'diferenciaTotal' => round((float) $totales->diferencia_total, 2),
                'conDescuadre' => (int) $totales->con_descuadre,
            ],
            'porCajero' => $porCajero,
            'detalle' => $detalle,
            'serie' => $serie,
        ]);
    }

    /**
     * Cuantas reservas se cumplen vs se rechazan/quedan pendientes, y como
     * se reparte la ocupacion entre bahias y turnos. Complementa a
     * rendimientoMecanicos (que mide mantenimientos ya en curso/terminados)
     * mirando el embudo de reservas antes de que lleguen a atenderse.
     */
    public function operacionTaller(Request $request)
    {
        if (!$request->ajax()) {
            return view('tenant_tallermoto.reportes.operacion-taller', [
                'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
            ]);
        }

        [$inicio, $fin] = $this->resolverPeriodo($request);
        $almacenId = $request->input('almacen_id');
        $rango = [$inicio->toDateString(), $fin->toDateString()];

        $base = DB::table('reservacion as r')
            ->where('r.RES_Estado', 'ACT')
            ->whereBetween('r.RES_FechaProgramada', $rango)
            ->when($almacenId, fn ($q) => $q->where('r.ALM_Id', $almacenId));

        $porEstado = (clone $base)
            ->groupBy('r.RES_State')
            ->select('r.RES_State', DB::raw('COUNT(*) as cantidad'))
            ->pluck('cantidad', 'RES_State');

        $aprobadas = (int) ($porEstado['APROBADO'] ?? 0);
        $rechazadas = (int) ($porEstado['RECHAZADO'] ?? 0);
        $pendientes = (int) ($porEstado['PENDIENTE'] ?? 0);
        $totalReservas = $aprobadas + $rechazadas + $pendientes;
        $resueltas = $aprobadas + $rechazadas;

        $porBahia = (clone $base)
            ->join('bahia as b', 'b.BAH_Id', '=', 'r.BAH_Id')
            ->groupBy('b.BAH_Id', 'b.BAH_Nombre')
            ->select(
                'b.BAH_Nombre',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN r.RES_State = 'APROBADO' THEN 1 ELSE 0 END) as aprobadas"),
                DB::raw("SUM(CASE WHEN r.RES_State = 'RECHAZADO' THEN 1 ELSE 0 END) as rechazadas")
            )
            ->orderByDesc('total')
            ->get();

        $porTurno = (clone $base)
            ->join('turno as t', 't.TUR_Id', '=', 'r.TUR_Id')
            ->groupBy('t.TUR_Id', 't.TUR_Descripcion')
            ->select('t.TUR_Descripcion', DB::raw('COUNT(*) as total'))
            ->orderBy('t.TUR_Id')
            ->get();

        $serie = (clone $base)
            ->selectRaw("
                RES_FechaProgramada as fecha,
                SUM(CASE WHEN RES_State = 'APROBADO' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN RES_State = 'RECHAZADO' THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN RES_State = 'PENDIENTE' THEN 1 ELSE 0 END) as pendientes
            ")
            ->groupBy('RES_FechaProgramada')
            ->orderBy('RES_FechaProgramada')
            ->get();

        // Mantenimientos creados en el periodo, por estado (para completar
        // el embudo: reserva aprobada -> mantenimiento realmente hecho).
        $tablasMtto = [
            ['tabla' => 'mantenimiento_general_carburada', 'prefijo' => 'MGC'],
            ['tabla' => 'mantenimiento_general_inyectada', 'prefijo' => 'MGI'],
            ['tabla' => 'mantenimiento_preventivo_carburada', 'prefijo' => 'MPC'],
            ['tabla' => 'mantenimiento_preventivo_inyectada', 'prefijo' => 'MPI'],
            ['tabla' => 'mantenimiento_actividad_variadas', 'prefijo' => 'MAV'],
        ];

        $mtto = ['PENDIENTE' => 0, 'APROBADO' => 0, 'OBSERVADO' => 0];
        foreach ($tablasMtto as $t) {
            $conteos = DB::table($t['tabla'])
                ->whereBetween("{$t['prefijo']}_FechaCreacion", [$inicio, $fin])
                ->groupBy("{$t['prefijo']}_Estado")
                ->select("{$t['prefijo']}_Estado as estado", DB::raw('COUNT(*) as cantidad'))
                ->pluck('cantidad', 'estado');

            foreach ($conteos as $estado => $cantidad) {
                if (isset($mtto[$estado])) {
                    $mtto[$estado] += (int) $cantidad;
                }
            }
        }

        return response()->json([
            'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
            'totales' => [
                'totalReservas' => $totalReservas,
                'aprobadas' => $aprobadas,
                'rechazadas' => $rechazadas,
                'pendientes' => $pendientes,
                'tasaCumplimiento' => $resueltas > 0 ? round(($aprobadas / $resueltas) * 100, 1) : 0,
            ],
            'porBahia' => $porBahia,
            'porTurno' => $porTurno,
            'serie' => $serie,
            'mantenimientos' => $mtto,
        ]);
    }
}
