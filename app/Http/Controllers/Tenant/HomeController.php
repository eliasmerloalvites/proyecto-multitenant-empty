<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\TenantTallerMotos\Bahia;
use App\Models\TenantTallerMotos\Reservacion;
use App\Models\TenantTallerMotos\Turno;
use Carbon\Carbon;
use App\Services\Caja\FlujoPorMetodoService;
use App\Services\Reportes\UtilidadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\RateLimiter;
use ReflectionFunction;

class HomeController extends Controller
{
    /**
     * Umbral (unidades totales en almacén) bajo el cual un producto se
     * considera "stock bajo" en el dashboard. Ajustable si lo necesitas.
     */
    const STOCK_BAJO_LIMITE = 5;

    /**
     * El rol "Contador" solo existe en tallermoto, asi que esto nunca es
     * true para un usuario de generico (no tiene ese rol para empezar) --
     * no hace falta ningun guard de tenant('tipo_negocio') aqui.
     */
    private function esSoloContador(): bool
    {
        $user = Auth::guard('tenant')->user();

        if (! $user) {
            return false;
        }

        $roles = $user->roles->pluck('name');

        return $roles->count() === 1 && $roles->first() === 'Contador';
    }

    public function index()
    {
        // El perfil Contador (rol exclusivo de tallermoto, ver
        // RoleAndPermissionSeeder) no tiene permiso para ver este dashboard
        // comercial -- si es el UNICO rol que tiene, lo mandamos directo a
        // Contabilidad en vez de un dashboard vacio/con accesos denegados.
        if ($this->esSoloContador()) {
            return redirect()->route('tenant.contabilidad.index');
        }

        $tenantid = tenant('id');
        $tiponegocio = tenant('tipo_negocio');

        $hoy = Carbon::now('America/Lima');

        // El dashboard comercial (ventas, gastos, stock, top productos) solo
        // aplica a planes con Productos/Inventario/Compras/Ventas habilitados
        // (Plus/Empresarial). Start/Basic no pueden realizar esas acciones,
        // así que ni se consultan esas tablas ni se muestran esas tarjetas.
        $mostrarVentas = tenant_has_module('ventas') || tenant_has_module('inventario') || tenant_has_module('compras');

        $data = compact('tenantid', 'tiponegocio', 'mostrarVentas');

        if ($mostrarVentas) {
            // Expresión reutilizada en todo el dashboard para el importe real
            // de una línea de venta. DEV_PrecioUnitario ya es el precio final
            // con el descuento aplicado (ver VentaController::store); restar
            // DEV_Descuento de nuevo lo descontaría dos veces.
            $totalVentaExpr = 'dv.DEV_Cantidad * dv.DEV_PrecioUnitario';

            // ================= KPIs =================

            $ventasHoy = (float) DB::table('venta as v')
                ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Status', 1)
                ->whereDate('v.created_at', $hoy->toDateString())
                ->sum(DB::raw($totalVentaExpr));

            $ventasAyer = (float) DB::table('venta as v')
                ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Status', 1)
                ->whereDate('v.created_at', $hoy->copy()->subDay()->toDateString())
                ->sum(DB::raw($totalVentaExpr));

            $ingresosMes = (float) DB::table('venta as v')
                ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Status', 1)
                ->whereYear('v.created_at', $hoy->year)
                ->whereMonth('v.created_at', $hoy->month)
                ->sum(DB::raw($totalVentaExpr));

            $mesAnterior = $hoy->copy()->subMonthNoOverflow();
            $ingresosMesAnterior = (float) DB::table('venta as v')
                ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Status', 1)
                ->whereYear('v.created_at', $mesAnterior->year)
                ->whereMonth('v.created_at', $mesAnterior->month)
                ->sum(DB::raw($totalVentaExpr));

            // GAS_Fecha es nullable en BD, por eso se filtra directo sobre ella
            // (gasto no tiene timestamps habilitados).
            $gastosMes = (float) DB::table('gasto')
                ->where('GAS_Status', 1)
                ->whereYear('GAS_Fecha', $hoy->year)
                ->whereMonth('GAS_Fecha', $hoy->month)
                ->sum('GAS_Monto');

            $gastosMesAnterior = (float) DB::table('gasto')
                ->where('GAS_Status', 1)
                ->whereYear('GAS_Fecha', $mesAnterior->year)
                ->whereMonth('GAS_Fecha', $mesAnterior->month)
                ->sum('GAS_Monto');

            $stockBajo = DB::table('producto as p')
                ->leftJoin('lote as l', 'l.PRO_Id', '=', 'p.PRO_Id')
                ->where('p.PRO_Status', 1)
                ->selectRaw('p.PRO_Id')
                ->groupBy('p.PRO_Id')
                ->havingRaw('COALESCE(SUM(l.LOT_CantidadReal), 0) <= ?', [self::STOCK_BAJO_LIMITE])
                ->get()
                ->count();

            $crecimientoVentas = $this->crecimientoPorcentual($ventasHoy, $ventasAyer);
            $crecimientoIngresos = $this->crecimientoPorcentual($ingresosMes, $ingresosMesAnterior);
            $crecimientoGastos = $this->crecimientoPorcentual($gastosMes, $gastosMesAnterior);

            // ================= CHART: VENTAS VS GASTOS (últimos 6 meses) =================

            $mesesEs = ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'];
            $labelsMeses = [];
            $serieVentasMensual = [];
            $serieGastosMensual = [];

            for ($i = 5; $i >= 0; $i--) {
                $mesRef = $hoy->copy()->subMonthsNoOverflow($i);
                $labelsMeses[] = $mesesEs[$mesRef->month - 1];

                $serieVentasMensual[] = round((float) DB::table('venta as v')
                    ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                    ->where('v.VEN_Status', 1)
                    ->whereYear('v.created_at', $mesRef->year)
                    ->whereMonth('v.created_at', $mesRef->month)
                    ->sum(DB::raw($totalVentaExpr)), 2);

                $serieGastosMensual[] = round((float) DB::table('gasto')
                    ->where('GAS_Status', 1)
                    ->whereYear('GAS_Fecha', $mesRef->year)
                    ->whereMonth('GAS_Fecha', $mesRef->month)
                    ->sum('GAS_Monto'), 2);
            }

            // ================= CHART: COBROS POR MÉTODO (mes actual) =================
            // Dinero que realmente entró en el mes por cada método, con la
            // misma cuenta que la caja (FlujoPorMetodoService): ventas con
            // pago mixto repartidas, abonos de cuentas por cobrar y adelantos
            // de bahía incluidos, y sin "Crédito" (eso aún no se cobró).

            $flujoMes = FlujoPorMetodoService::entreFechas($hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth())->resumen(true);
            $cobrosMes = collect($flujoMes['columnas'])
                ->filter(fn ($c) => $c['ingresos'] > 0)
                ->sortByDesc('ingresos')
                ->values();

            $metodosPagoLabels = $cobrosMes->pluck('nombre')->values();
            $metodosPagoData = $cobrosMes->pluck('ingresos')->values();

            // Cobrado en el mes (ventas al contado + abonos + adelantos) y
            // lo que los clientes aún deben (cuentas por cobrar pendientes).
            $cobradoMes = (float) $flujoMes['total_ingresos'];
            $abonosMes = (float) $flujoMes['total_abonos'];
            $porCobrar = Schema::hasTable('cuenta_por_cobrar')
                ? (float) DB::table('cuenta_por_cobrar')->where('CPC_Estado', 'PENDIENTE')->sum('CPC_MontoFaltante')
                : 0.0;

            // ================= RESUMEN DEL MES (para el gerente) =================
            // Dos miradas del mismo mes:
            // - Utilidad (devengado): vendido - costo de lo vendido - gastos.
            //   Misma cuenta que Reportes > Rentabilidad (UtilidadService).
            // - Flujo de caja: lo que de verdad entro y salio (cobrado -
            //   pagado). Misma cuenta que el arqueo (FlujoPorMetodoService).
            $utilidadMes = UtilidadService::entre($hoy->copy()->startOfMonth(), $hoy->copy()->endOfMonth());
            $utilidadMesAnterior = UtilidadService::entre($mesAnterior->copy()->startOfMonth(), $mesAnterior->copy()->endOfMonth());
            $utilidadMes['variacion'] = $utilidadMesAnterior['utilidadNeta'] != 0.0
                ? round(($utilidadMes['utilidadNeta'] - $utilidadMesAnterior['utilidadNeta']) / abs($utilidadMesAnterior['utilidadNeta']) * 100, 1)
                : null;

            $flujoCajaMes = [
                'cobrado' => (float) $flujoMes['total_ingresos'],
                'ventas' => (float) $flujoMes['total_ventas'],
                'abonos' => (float) $flujoMes['total_abonos'],
                'adelantos' => (float) $flujoMes['total_adelantos'],
                'pagado' => (float) $flujoMes['total_egresos'],
                'compras' => (float) $flujoMes['total_compras'],
                'pagosProveedor' => (float) $flujoMes['total_pagos_proveedor'],
                'gastos' => (float) $flujoMes['total_gastos'],
                'neto' => (float) $flujoMes['total_neto'],
                'porCobrar' => $porCobrar,
                'debesProveedores' => Schema::hasTable('cuenta_por_pagar')
                    ? (float) DB::table('cuenta_por_pagar')->where('CPP_Estado', 'PENDIENTE')->sum('CPP_MontoFaltante')
                    : 0.0,
            ];
            $nombreMes = ucfirst($hoy->locale('es')->translatedFormat('F Y'));

            // ================= ÚLTIMOS MOVIMIENTOS (últimas ventas) =================

            $ultimasVentas = DB::table('venta as v')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                ->select(
                    'v.VEN_Id',
                    'c.CLI_Nombre',
                    'v.VEN_Status',
                    'v.created_at',
                    DB::raw("SUM($totalVentaExpr) as total")
                )
                ->groupBy('v.VEN_Id', 'c.CLI_Nombre', 'v.VEN_Status', 'v.created_at')
                ->orderByDesc('v.VEN_Id')
                ->limit(6)
                ->get();

            // ================= TOP PRODUCTOS =================

            $topProductos = DB::table('detalle_venta as dv')
                ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
                ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
                ->where('v.VEN_Status', 1)
                ->select('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Imagen', DB::raw('SUM(dv.DEV_Cantidad) as unidades'))
                ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Imagen')
                ->orderByDesc('unidades')
                ->limit(3)
                ->get();

            $maxUnidadesTop = (float) ($topProductos->max('unidades') ?: 1);

            $data += compact(
                'ventasHoy',
                'crecimientoVentas',
                'ingresosMes',
                'crecimientoIngresos',
                'gastosMes',
                'crecimientoGastos',
                'stockBajo',
                'labelsMeses',
                'serieVentasMensual',
                'serieGastosMensual',
                'metodosPagoLabels',
                'metodosPagoData',
                'cobradoMes',
                'abonosMes',
                'porCobrar',
                'utilidadMes',
                'flujoCajaMes',
                'nombreMes',
                'ultimasVentas',
                'topProductos',
                'maxUnidadesTop'
            );
        }

        // ================= KPIs Y REPORTES PROPIOS DEL TALLER DE MOTOS =================
        // Disponibles en todos los planes: Mantenimientos + Reservas es el
        // módulo base que incluyen Start, Basic, Plus y Empresarial.

        if ($tiponegocio === 'tallermoto') {
            // Tabla, prefijo de columnas y etiqueta legible de cada tipo de mantenimiento.
            $tiposMantenimiento = [
                ['tabla' => 'mantenimiento_general_inyectada', 'prefix' => 'MGI', 'label' => 'General Inyectada'],
                ['tabla' => 'mantenimiento_general_carburada', 'prefix' => 'MGC', 'label' => 'General Carburada'],
                ['tabla' => 'mantenimiento_preventivo_inyectada', 'prefix' => 'MPI', 'label' => 'Preventivo Inyectada'],
                ['tabla' => 'mantenimiento_preventivo_carburada', 'prefix' => 'MPC', 'label' => 'Preventivo Carburada'],
                ['tabla' => 'mantenimiento_actividad_variadas', 'prefix' => 'MAV', 'label' => 'Actividad Variada'],
            ];

            foreach (\App\Services\TenantTallerMotos\GestionProcesoService::opcionalesActivos() as $opcional) {
                $tiposMantenimiento[] = ['tabla' => $opcional['tabla'], 'prefix' => $opcional['prefijo'], 'label' => $opcional['etiqueta']];
            }

            $data['reservasHoy'] = Reservacion::whereDate('RES_FechaProgramada', $hoy->toDateString())
                ->where('RES_Estado', 'ACT')
                ->where('RES_State', 'APROBADO')
                ->count();

            $data['bahiasActivas'] = Bahia::where('BAH_Estado', 'ACT')->count();

            $data['mantenimientosPendientes'] = 0;
            $data['mantenimientosAprobados'] = 0;
            $data['mantenimientosObservados'] = 0;
            $data['mantenimientosPorTipoLabels'] = [];
            $data['mantenimientosPorTipoData'] = [];

            foreach ($tiposMantenimiento as $tipo) {
                $estadoCol = "{$tipo['prefix']}_Estado";

                $data['mantenimientosPendientes'] += DB::table($tipo['tabla'])->where($estadoCol, 'PENDIENTE')->count();
                $data['mantenimientosAprobados'] += DB::table($tipo['tabla'])->where($estadoCol, 'APROBADO')->count();
                $data['mantenimientosObservados'] += DB::table($tipo['tabla'])->where($estadoCol, 'OBSERVADO')->count();

                $data['mantenimientosPorTipoLabels'][] = $tipo['label'];
                $data['mantenimientosPorTipoData'][] = DB::table($tipo['tabla'])->count();
            }

            // ============ RESERVAS DE LOS ÚLTIMOS 7 DÍAS ============

            $labelsReservas7d = [];
            $serieReservas7d = [];

            for ($i = 6; $i >= 0; $i--) {
                $dia = $hoy->copy()->subDays($i);
                $labelsReservas7d[] = $dia->translatedFormat('d M');

                $serieReservas7d[] = Reservacion::whereDate('RES_FechaProgramada', $dia->toDateString())
                    ->where('RES_State', '!=', 'RECHAZADO')
                    ->count();
            }

            $data['labelsReservas7d'] = $labelsReservas7d;
            $data['serieReservas7d'] = $serieReservas7d;

            // ============ PRÓXIMAS RESERVAS (agenda) ============

            $data['proximasReservas'] = Reservacion::join('bahia as b', 'b.BAH_Id', '=', 'reservacion.BAH_Id')
                ->join('turno as t', 't.TUR_Id', '=', 'reservacion.TUR_Id')
                ->where('reservacion.RES_Estado', 'ACT')
                ->where('reservacion.RES_State', '!=', 'RECHAZADO')
                ->whereDate('reservacion.RES_FechaProgramada', '>=', $hoy->toDateString())
                ->orderBy('reservacion.RES_FechaProgramada')
                ->orderBy('t.TUR_Id')
                ->select('reservacion.*', 'b.BAH_Nombre', 't.TUR_Nombre')
                ->limit(6)
                ->get();
        }

        return view('tenant_' . $tiponegocio . '.menu.home', $data);
    }

    /**
     * % de variación entre dos montos. Si el punto de referencia es 0,
     * se evita la división entre cero.
     */
    private function crecimientoPorcentual(float $actual, float $anterior): float
    {
        if ($anterior <= 0.0) {
            return $actual > 0 ? 100.0 : 0.0;
        }

        return round((($actual - $anterior) / $anterior) * 100, 1);
    }


    public function inicio()
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            // La web multi-página (landing/page/*) solo existe para tallermoto por
            // ahora; el resto de verticales (generico, etc.) usan la web de una
            // sola página (welcome) sin importar el plan, hasta que se construyan
            // sus propias páginas dedicadas.
            if ($plan == 'start' || $tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            // basic, plus y empresarial comparten la web completa (multi-página).
            $colorview = $empresa->tipo_tema ?? 'dark';

            if (tenant_has_module('productos')) {
                // Plus/Empresarial: Query Base para Productos con Lotes acumulados
                $queryProductos = DB::table('producto as pd')
                    ->join('categoria as ct', 'pd.CAT_Id', '=', 'ct.CAT_Id')
                    ->join('lote as lt', 'pd.PRO_Id', '=', 'lt.PRO_Id')
                    ->where('pd.PRO_MostrarCatalogo', 1)
                    ->select(
                        'pd.PRO_Id',
                        'pd.PRO_Nombre',
                        'pd.PRO_Descripcion',
                        'pd.PRO_Marca',
                        'pd.PRO_PrecioVenta',
                        'pd.PRO_Imagen',
                        'ct.CAT_Id',
                        'ct.CAT_Nombre',
                        DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total')
                    )
                    ->groupBy(
                        'pd.PRO_Id',
                        'pd.PRO_Nombre',
                        'pd.PRO_Descripcion',
                        'pd.PRO_Marca',
                        'pd.PRO_PrecioVenta',
                        'pd.PRO_Imagen',
                        'ct.CAT_Id',
                        'ct.CAT_Nombre'
                    );

                // Paginación de 12 en 12 productos (Mantiene la query con string del buscador si aplica)
                $dataProductos = $queryProductos->paginate(4)->withQueryString();
            } else {
                // Basic: sin catálogo de productos habilitado.
                $dataProductos = null;
            }
            return view('tenant_' . $tiponegocio . '.landing.index', compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview', 'dataProductos'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function servicios()
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            if ($plan == 'start' || $tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            $colorview = $empresa->tipo_tema ?? 'dark';
            return view('tenant_' . $tiponegocio . '.landing.page.servicio', compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function reservar(Request $request)
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

            // El sistema de reservas por bahía/turno es específico de tallermoto
            // (usa tablas que no existen en otros tipos de negocio). El resto de
            // verticales no tiene "Reservar" habilitado todavía.
            if ($tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';

                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            $fechaSeleccionada = $request->query('fecha', \Carbon\Carbon::now()->format('Y-m-d'));
            $idlocal = $request->query('almacen', 1);
            $locales = Almacen::where('ALM_Status', 1)->get();
            $localFirst = Almacen::where('ALM_Id', $idlocal)->first();
            $semana = [];
            $horariosdias = DB::table('horario')
                ->where('ALM_Id', $idlocal)
                ->pluck('HOR_Dia')
                ->unique()
                ->toArray();

            $horarios = DB::table('horario')
                ->where('HOR_Estado', 'ACT')
                ->where('ALM_Id', $idlocal)
                ->get();

            $horariosturno = DB::table('horario')
                ->where('HOR_Estado', 'ACT')
                ->where('ALM_Id', $idlocal)
                ->pluck('TUR_Id')
                ->unique()
                ->toArray();

            $bahias = DB::table('bahia as b')
                ->join('almacen as l', 'l.ALM_Id', '=', 'b.ALM_Id')
                ->join('users as u', 'u.id', '=', 'b.USU_Id')
                // Solo lo que la pagina publica necesita de cada bahia: antes se
                // enviaban tambien el id y el nombre del mecanico asignado.
                ->select('b.BAH_Id', 'b.BAH_Nombre', 'b.ALM_Id', 'l.ALM_NombreAlmacen as Local')
                ->where('l.ALM_Id', $idlocal)
                ->where('b.BAH_Estado', 'ACT')
                ->get();


            $turnos = Turno::Where('TUR_Estado', 'ACT')->whereIn('TUR_Id', $horariosturno)->get();

            $totalBahias = Bahia::Where('BAH_Estado', 'ACT')->where('ALM_Id', $idlocal)->count();

            $hoy  = Carbon::now()->locale('es');
            $diaHoy = $hoy->dayOfWeek;
            for ($i = 0; $i < 7; $i++) {
                $diaCarbon = $i % 7; // para mapear domingo=0

                if ($diaCarbon == 0) { // si es domingo
                    $diaCarbon = 0;
                }
                if ($diaCarbon >= $diaHoy) {
                    // fecha de esta semana
                    $fecha = $hoy->copy()->addDays($diaCarbon - $diaHoy);
                } else {
                    // fecha de la siguiente semana
                    $diasRestantes = 7 - $diaHoy + $diaCarbon;
                    $fecha = $hoy->copy()->addDays($diasRestantes);
                }

                $mesDia = $fecha->translatedFormat('F') . ' - ' . $fecha->day;
                $diaNormalizado = self::normalizarDia($fecha->translatedFormat('l'));
                //dd($diaNormalizado, $horariosdias);       
                // Guarda tanto la fecha como el nombre del día
                if (in_array($diaNormalizado, $horariosdias)) {
                    $semana[$i - 1] = [
                        'fecha' => $fecha->format('Y-m-d'),
                        'mesdia' => $mesDia,
                        'dia' => $fecha->translatedFormat('l'),
                        'diaNormalizado' => $diaNormalizado,
                    ];
                }
            }
            $fechas = array_column($semana, 'fecha'); // obtiene solo las fechas

            $fechaInicial = !empty($fechas) ? min($fechas) : null;
            $fechaFinal = !empty($fechas) ? max($fechas) : null;
            $horarioprogramado  = [];
            foreach ($horarios as $horario) {
                $dia = $horario->HOR_Dia;
                $turno = $horario->TUR_Id;

                // Si el día aún no existe en el array, inicialízalo como array vacío
                if (!isset($horarioprogramado[$dia])) {
                    $horarioprogramado[$dia] = [];
                }

                // Agrega el turno al array del día
                $horarioprogramado[$dia][] = $turno;
            }
            $reservasRaw = Reservacion::whereBetween('RES_FechaProgramada', [$fechaInicial, $fechaFinal])->where('RES_State', '!=', 'RECHAZADO')
                // Solo lo necesario para pintar el calendario. NO se trae RES_Cliente:
                // esta pagina es publica y mostraba el nombre completo de quienes
                // ya tenian reserva.
                ->get(['RES_Id', 'RES_FechaProgramada', 'TUR_Id', 'BAH_Id', 'RES_State']);

            $reservas = [];

            foreach ($reservasRaw as $reserva) {
                $fecha = $reserva->RES_FechaProgramada;
                $turno = $reserva->TUR_Id;
                $bahia = $reserva->BAH_Id;
                $state = $reserva->RES_State;
                $idreserva = $reserva->RES_Id;

                // Inicializar si no existe
                if (!isset($reservas[$fecha])) {
                    $reservas[$fecha] = [];
                }
                if (!isset($reservas[$fecha][$turno])) {
                    $reservas[$fecha][$turno] = [];
                } // Agregar bahía al turno
                if (!isset($reservas[$fecha][$bahia])) {
                    $reservas[$fecha][$turno][$bahia] = [];
                }
                $reservas[$fecha][$turno][$bahia][] = $state;
                $reservas[$fecha][$turno][$bahia][] = $idreserva;
            }

            if ($plan == 'start') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview', 'locales', 'localFirst', 'idlocal', 'turnos', 'totalBahias', 'semana', 'bahias', 'reservas', 'horarioprogramado', 'fechaInicial', 'fechaFinal', 'fechaSeleccionada'));
            }

            $colorview = $empresa->tipo_tema ?? 'dark';
            return view('tenant_' . $tiponegocio . '.landing.page.reservar', compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview', 'locales', 'localFirst', 'idlocal', 'turnos', 'totalBahias', 'semana', 'bahias', 'reservas', 'horarioprogramado', 'fechaInicial', 'fechaFinal', 'fechaSeleccionada'));
        } else {
            $tenantid = null;
            return view('welcome',  compact('tenantid'));
        }
    }

    function normalizarDia($dia)
    {
        $sinTildes = strtr($dia, [
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ú' => 'u',
            'Á' => 'A',
            'É' => 'E',
            'Í' => 'I',
            'Ó' => 'O',
            'Ú' => 'U'
        ]);
        return mb_strtoupper($sinTildes, 'UTF-8');
    }

    public function reservar_store(Request $request)
    {
        $validated = $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'TUR_Id' => 'required|integer|exists:turno,TUR_Id',
            'BAH_Id' => 'required|integer|exists:bahia,BAH_Id',
            // Fecha real (la pagina publica solo ofrece los proximos dias).
            'RES_FechaProgramada' => 'required|date_format:Y-m-d|after_or_equal:today|before_or_equal:+60 days',
            'RES_Placa' => 'required|string|max:20',
            'RES_Moto' => 'required|string|max:150',
            'RES_Cliente' => 'required|string|max:120',
            'RES_Celular' => ['required', 'string', 'max:12', 'regex:/^\+?[0-9 \-]{7,12}$/'],
            'RES_Detalle' => 'nullable|string|max:250',
            'RES_Adicional' => 'nullable|string|max:250',
        ], [
            'RES_FechaProgramada.date_format' => 'La fecha de la reserva no es valida.',
            'RES_FechaProgramada.after_or_equal' => 'La fecha de la reserva no puede ser anterior a hoy.',
            'RES_FechaProgramada.before_or_equal' => 'Solo se puede reservar con hasta 60 dias de anticipacion.',
            'RES_Celular.regex' => 'Ingresa un numero de celular valido.',
        ]);

        // La bahia debe ser de la sede elegida y el turno debe estar en el
        // horario de esa sede ese dia: antes solo se validaba que cada id
        // existiera, asi que se podian reservar combinaciones que no existen.
        $bahiaDeLaSede = Bahia::where('BAH_Id', $validated['BAH_Id'])
            ->where('ALM_Id', $validated['ALM_Id'])
            ->where('BAH_Estado', 'ACT')
            ->exists();

        $dia = $this->normalizarDia(Carbon::parse($validated['RES_FechaProgramada'])->locale('es')->translatedFormat('l'));

        $turnoEnHorario = DB::table('horario')
            ->where('ALM_Id', $validated['ALM_Id'])
            ->where('TUR_Id', $validated['TUR_Id'])
            ->where('HOR_Dia', $dia)
            ->where('HOR_Estado', 'ACT')
            ->exists();

        if (!$bahiaDeLaSede || !$turnoEnHorario) {
            return $this->reservaInvalidaResponse($request, 'Ese horario no esta disponible para reservar. Elige otra bahia, turno o dia.');
        }

        // Tope de reservas pendientes por celular: sin esto una sola persona
        // podia ocupar toda la agenda con reservas falsas.
        $celularLimpio = preg_replace('/\D/', '', $validated['RES_Celular']);
        $pendientes = Reservacion::where('RES_Celular', $validated['RES_Celular'])
            ->where('RES_Estado', 'ACT')
            ->where('RES_State', 'PENDIENTE')
            ->count();

        if (strlen($celularLimpio) >= 7 && $pendientes >= 3) {
            return $this->reservaInvalidaResponse($request, 'Ya tienes varias reservas pendientes de confirmar. Espera a que el taller las atienda o llama para modificarlas.');
        }

        // Pre-chequeo: da un mensaje inmediato en el caso normal (sin carrera).
        if (Reservacion::slotEstaOcupado($validated['BAH_Id'], $validated['TUR_Id'], $validated['RES_FechaProgramada'])) {
            return $this->reservaOcupadaResponse($request);
        }

        try {
            $Reservacion = Reservacion::create($validated);
        } catch (QueryException $e) {
            // Protección real contra condición de carrera: si dos personas
            // reservaron el mismo slot casi al mismo tiempo, el índice único
            // de BD rechaza el segundo INSERT y caemos aquí.
            if (Reservacion::esConflictoDeSlot($e)) {
                return $this->reservaOcupadaResponse($request);
            }
            throw $e;
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Su reserva ha sido registrada correctamente.'
            ], 200);
        }

        return redirect()->back()->with('success', 'Reserva realizada con éxito');
    }

    private function reservaInvalidaResponse(Request $request, string $mensaje)
    {
        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $mensaje], 422);
        }

        return redirect()->back()->withInput()->with('error', $mensaje);
    }

    private function reservaOcupadaResponse(Request $request)
    {
        $mensaje = 'Ese horario acaba de ser reservado por otra persona. Por favor elige otra bahía u otro turno.';

        if ($request->wantsJson()) {
            return response()->json(['success' => false, 'message' => $mensaje], 409);
        }

        return redirect()->back()->withInput()->with('error', $mensaje);
    }

    public function historial(Request $request)
    {
        // Early return si no hay tenant
        if (tenant() === null) {
            return view('welcome', ['tenantid' => null]);
        }

        $tenantid = tenant('id');
        $tiponegocio = tenant('tipo_negocio');
        $plan = tenant('plan');
        $empresa = EmpresaFacturacion::where('tenant_id', $tenantid)->first();

        // El historial de mantenimientos por placa es específico de tallermoto
        // (consulta tablas que no existen en otros tipos de negocio).
        if ($tiponegocio !== 'tallermoto') {
            $colorview = $empresa->tipo_tema ?? 'dark';

            return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
        }

        // Consulta publica: exige la placa Y los ultimos 4 digitos del celular
        // registrado en el taller, y NO devuelve datos personales (ni dueno,
        // ni telefono, ni mecanico, ni enlace a PDF). Antes bastaba conocer una
        // placa para ver la ficha del dueno.
        $placaIngresada = $this->normalizarPlaca((string) $request->query('Placa', ''));
        $ultimos4 = preg_replace('/\D/', '', (string) $request->query('Celular', ''));
        $consultaIntentada = $placaIngresada !== '' || $ultimos4 !== '';
        $ultimoMtto = null;
        $bloqueado = false;

        if ($placaIngresada !== '' && strlen($ultimos4) === 4) {
            // Tope de intentos fallidos POR PLACA (sin importar la IP): con 4
            // digitos hay solo 10.000 combinaciones, asi que sin esto se
            // podria adivinar el celular probando desde varias IP.
            $claveLimite = 'historial-placa:' . tenant('id') . ':' . $placaIngresada;

            if (RateLimiter::tooManyAttempts($claveLimite, 5)) {
                $bloqueado = true;
            } else {
                $ultimoMtto = $this->ultimoMantenimientoPublico($placaIngresada, $ultimos4);

                if (!$ultimoMtto) {
                    RateLimiter::hit($claveLimite, 3600);
                }
            }
        }

        $hayResultados = !is_null($ultimoMtto);

        // Mensaje unico para "no existe" y "el celular no coincide", para que
        // la pagina no sirva para averiguar si una placa esta registrada.
        $data = [
            'validacion' => $hayResultados ? 'true' : 'false',
            'mensaje'    => match (true) {
                $hayResultados => 'Se encontró el mantenimiento de esta placa correctamente',
                $bloqueado => 'Demasiados intentos con esta placa. Inténtalo de nuevo en una hora o consulta en el taller.',
                $consultaIntentada => 'No encontramos un mantenimiento con esos datos. Verifica la placa y los últimos 4 dígitos del celular registrado en el taller.',
                default => '',
            },
        ];

        if ($hayResultados) {
            $p = $ultimoMtto->Prefijo;
            $raw = DB::table($ultimoMtto->Tabla)->where("{$p}_Id", $ultimoMtto->Id)->first();

            $resumen = [
                'cabecera' => [
                    'tipo'                   => $ultimoMtto->Tipo,
                    'fecha'                  => $ultimoMtto->FechaCreacion,
                    'placa'                  => $ultimoMtto->Placa,
                    'unidad'                 => $ultimoMtto->Unidad,
                    'km'                     => $ultimoMtto->KMEntrada,
                    'proximo_servicio'       => $ultimoMtto->ProximoServicio,
                    'proximo_cambio_aceite'  => $ultimoMtto->ProximoCambioAceite,
                ],
                // Metricas universales clave
                'metricas' => [
                    'aceite'         => $raw->{"{$p}_Det1"} ?? 'N/A',
                    'filtro_aceite'  => $raw->{"{$p}_Det2"} ??  'N/A',
                    'valvula_adm'    => $raw->{"{$p}_Det9Admision"} ?? $raw->{"{$p}_Det8Admision"} ?? $raw->{"{$p}_Det7Admision"} ?? null,
                    'valvula_esc'    => $raw->{"{$p}_Det9Escape"} ?? $raw->{"{$p}_Det8Escape"} ?? $raw->{"{$p}_Det7Escape"} ?? null,
                    'bujia_medida'   => $raw->{"{$p}_Det10Medida"} ?? $raw->{"{$p}_Det9Medida"} ?? $raw->{"{$p}_Det8Medida"} ?? null,
                    'psi_delantero'  => $raw->{"MGI_Det18"} ?? $raw->{"{$p}_NeumaticoDelantero"} ?? null,
                    'psi_trasero'    => $raw->{"MGI_Det19"} ?? $raw->{"{$p}_NeumaticoPosterior"} ?? null,
                    'v_carga'        => $raw->{"{$p}_Det24Carga"} ?? $raw->{"{$p}_Det21Carga"} ?? $raw->{"{$p}_Det19Carga"} ?? $raw->{"{$p}_Det11Carga"} ?? null,
                    'v_arranque'     => $raw->{"{$p}_Det24Arranque"} ?? $raw->{"{$p}_Det21Arranque"} ?? $raw->{"{$p}_Det19Arranque"} ?? $raw->{"{$p}_Det11Arranque"} ?? null,
                ],
                // Datos extra especificos si existen
                'extras' => []
            ];

            if (str_contains($ultimoMtto->Tipo, 'INYECTADAS')) {
                $resumen['extras']['escaneo'] = $raw->{"{$p}_Escaneo"} ?? $raw->{"{$p}_PorcentajeVidaUtil"} ?? null;
                $resumen['extras']['porcentaje_humedad'] = $raw->{"{$p}_HumedadFrenos"} ?? $raw->{"{$p}_PorcentajeHumedad"} ?? null;
            }

            if ($ultimoMtto->Tipo === 'ACTIVIDADES VARIADAS') {
                $resumen['extras']['detalle_trabajo'] = $raw->{"{$p}_DetalleRealizado"} ?? $raw->{"{$p}_Detalle"} ?? null;
            }

            $data['resumen'] = $resumen;
        }

        $colorview = $empresa?->tipo_tema;
        // dd($data);
        // Retorno de vistas según plan
        if ($plan === 'start') {
            return view("tenant_{$tiponegocio}.welcome", compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview', 'data'));
        }

        return view("tenant_{$tiponegocio}.landing.page.historial", compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview', 'data'));
    }

    /** Placa en mayusculas y solo letras/numeros: "abc-123" y "ABC 123" son la misma. */
    private function normalizarPlaca(string $placa): string
    {
        return strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $placa));
    }

    /**
     * Ultimo mantenimiento de una placa CUYO celular registrado termina en
     * $ultimos4. Solo se consideran los registros donde ese celular coincide:
     * asi, si la moto cambio de dueno, quien consulta no ve el historial
     * (ni los datos) del dueno anterior o del siguiente.
     *
     * El celular se compara en PHP (no en SQL) porque en la base viene con
     * formatos muy distintos ("987654321", "9465623", "-", vacio), y se
     * exigen al menos 7 digitos para que un valor basura no valide cualquier
     * consulta.
     */
    private function ultimoMantenimientoPublico(string $placaNormalizada, string $ultimos4): ?object
    {
        $tablas = [
            ['tabla' => 'mantenimiento_general_carburada', 'prefix' => 'MGC', 'tipo' => 'MTTO GENERAL CARBURADAS'],
            ['tabla' => 'mantenimiento_general_inyectada', 'prefix' => 'MGI', 'tipo' => 'MTTO GENERAL INYECTADAS'],
            ['tabla' => 'mantenimiento_preventivo_carburada', 'prefix' => 'MPC', 'tipo' => 'MTTO PREVENTIVOS CARBURADAS'],
            ['tabla' => 'mantenimiento_preventivo_inyectada', 'prefix' => 'MPI', 'tipo' => 'MTTO PREVENTIVOS INYECTADAS'],
            ['tabla' => 'mantenimiento_actividad_variadas', 'prefix' => 'MAV', 'tipo' => 'ACTIVIDADES VARIADAS'],
        ];

        // Garantia Hero / Lifan, solo si el taller tiene el modulo activo.
        foreach (\App\Services\TenantTallerMotos\GestionProcesoService::opcionalesActivos() as $opcional) {
            $tablas[] = ['tabla' => $opcional['tabla'], 'prefix' => $opcional['prefijo'], 'tipo' => $opcional['tipo']];
        }

        $queries = [];

        foreach ($tablas as $t) {
            $p = $t['prefix'];

            $queries[] = DB::table($t['tabla'])
                ->select(
                    DB::raw("{$p}_Id as Id"),
                    DB::raw("{$p}_Placa as Placa"),
                    DB::raw("{$p}_celular as Celular"),
                    DB::raw("{$p}_Unidad as Unidad"),
                    DB::raw("{$p}_KMEntrada as KMEntrada"),
                    DB::raw("{$p}_ProximoCambioAceite as ProximoCambioAceite"),
                    DB::raw("{$p}_ProximoServicio as ProximoServicio"),
                    DB::raw("{$p}_FechaCreacion as FechaCreacion"),
                    DB::raw("'{$t['tipo']}' as Tipo"),
                    DB::raw("'{$t['tabla']}' as Tabla"),
                    DB::raw("'{$p}' as Prefijo")
                )
                ->whereRaw("UPPER(REPLACE(REPLACE({$p}_Placa, '-', ''), ' ', '')) = ?", [$placaNormalizada]);
        }

        $union = array_shift($queries);
        foreach ($queries as $q) {
            $union->unionAll($q);
        }

        return DB::query()
            ->fromSub($union, 'u')
            ->orderByDesc('FechaCreacion')
            ->limit(200)
            ->get()
            ->first(function ($fila) use ($ultimos4) {
                $digitos = preg_replace('/\D/', '', (string) $fila->Celular);

                return strlen($digitos) >= 7 && substr($digitos, -4) === $ultimos4;
            });
    }

    public function catalogo(Request $request)
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

            // El catálogo de productos solo está habilitado en planes Plus/Empresarial.
            if (! tenant_has_module('productos')) {
                return redirect()->route('web.servicios');
            }

            // La página de catálogo (landing/page/catalogo + su partial AJAX)
            // solo existe para tallermoto por ahora.
            if ($tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';

                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            // 1. Query Base para Productos con Lotes acumulados
            $queryProductos = DB::table('producto as pd')
                ->join('categoria as ct', 'pd.CAT_Id', '=', 'ct.CAT_Id')
                ->join('lote as lt', 'pd.PRO_Id', '=', 'lt.PRO_Id')
                ->where('pd.PRO_MostrarCatalogo', 1)
                ->select(
                    'pd.PRO_Id',
                    'pd.PRO_Nombre',
                    'pd.PRO_Descripcion',
                    'pd.PRO_Marca',
                    'pd.PRO_PrecioVenta',
                    'pd.PRO_Imagen',
                    'ct.CAT_Id',
                    'ct.CAT_Nombre',
                    DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total')
                )
                ->groupBy(
                    'pd.PRO_Id',
                    'pd.PRO_Nombre',
                    'pd.PRO_Descripcion',
                    'pd.PRO_Marca',
                    'pd.PRO_PrecioVenta',
                    'pd.PRO_Imagen',
                    'ct.CAT_Id',
                    'ct.CAT_Nombre'
                );

            // Filtro 1: Por Categoría
            if ($request->filled('cat_id') && $request->cat_id !== 'all') {
                $queryProductos->where('ct.CAT_Id', $request->cat_id);
            }

            // Filtro 2: Por Buscador
            if ($request->filled('search')) {
                $queryProductos->where('pd.PRO_Nombre', 'LIKE', '%' . $request->search . '%');
            }

            // Paginación de 12 en 12 productos (Mantiene la query con string del buscador si aplica)
            $dataProductos = $queryProductos->paginate(12)->withQueryString();

            $colorview = $empresa->tipo_tema ?? 'dark';

            // 2. Si la petición es AJAX (Botón "Cargar Más" o cambio de Filtros)
            if ($request->ajax()) {
                return response()->json([
                    'html' => view('tenant_tallermoto.landing.sections.partials.product-cards', compact('dataProductos', 'colorview', 'tiponegocio', 'tenantid'))->render(),
                    'next_page' => $dataProductos->nextPageUrl(),
                    'has_more' => $dataProductos->hasMorePages()
                ]);
            }

            // 3. Cargar Categorías (Solo con lotes asociados)
            $dataCategoria = DB::table('categoria as ct')
                ->select('ct.CAT_Id', 'ct.CAT_Nombre', 'ct.CLA_Id')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('producto as pd')
                        ->join('lote as lt', 'pd.PRO_Id', '=', 'lt.PRO_Id')
                        ->whereColumn('pd.CAT_Id', 'ct.CAT_Id');
                })
                ->get();

            // 4. Cargar Clases (Solo con lotes asociados)
            $dataClase = DB::table('clase as c')
                ->select('c.CLA_Id', 'c.CLA_Nombre')
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('categoria as ct')
                        ->join('producto as pd', 'ct.CAT_Id', '=', 'pd.CAT_Id')
                        ->join('lote as lt', 'pd.PRO_Id', '=', 'lt.PRO_Id')
                        ->whereColumn('ct.CLA_Id', 'c.CLA_Id');
                })
                ->get();

            // Solo llegan aquí planes con módulo "productos" habilitado (Plus/Empresarial).
            return view('tenant_' . $tiponegocio . '.landing.page.catalogo', compact('tenantid', 'tiponegocio', 'empresa', 'plan', 'tiponegocio', 'colorview', 'dataProductos', 'dataCategoria', 'dataClase'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function nosotros()
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

            if ($plan == 'start' || $tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            $colorview = $empresa->tipo_tema ?? 'dark';
            return view('tenant_' . $tiponegocio . '.landing.page.nosotros', compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }
    public function contacto()
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            $sede = Almacen::where('ALM_Status', 1)->get();
            if ($plan == 'start' || $tiponegocio !== 'tallermoto') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            $colorview = $empresa->tipo_tema ?? 'dark';
            return view('tenant_' . $tiponegocio . '.landing.page.contacto', compact('tenantid', 'empresa', 'sede', 'plan', 'tiponegocio', 'colorview'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }
    public function salir()
    {
        Auth::guard('tenant')->logout();
        $tenantName = str_replace(tenant()->tipo_negocio . '_', '', tenant()->id);
        return redirect()->route('tenant.login', ['tenant' => $tenantName]);
    }
}
