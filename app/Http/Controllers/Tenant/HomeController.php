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
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use ReflectionFunction;

class HomeController extends Controller
{
    /**
     * Umbral (unidades totales en almacén) bajo el cual un producto se
     * considera "stock bajo" en el dashboard. Ajustable si lo necesitas.
     */
    const STOCK_BAJO_LIMITE = 5;

    public function index()
    {
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
            // de una línea de venta (misma fórmula que usa VentaController).
            $totalVentaExpr = '(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) - dv.DEV_Descuento';

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

            // ================= CHART: MÉTODOS DE PAGO (mes actual) =================

            $metodosPago = DB::table('venta as v')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
                ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Status', 1)
                ->whereYear('v.created_at', $hoy->year)
                ->whereMonth('v.created_at', $hoy->month)
                ->select('mp.MEP_Pago', DB::raw("SUM($totalVentaExpr) as total"))
                ->groupBy('mp.MEP_Id', 'mp.MEP_Pago')
                ->orderByDesc('total')
                ->get();

            $metodosPagoLabels = $metodosPago->pluck('MEP_Pago')->values();
            $metodosPagoData = $metodosPago->pluck('total')->map(fn ($v) => round((float) $v, 2))->values();

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
                ->select('b.*', 'l.ALM_NombreAlmacen as Local', 'u.id', DB::raw("SUBSTRING_INDEX(u.name, ' ', 1) as Nombre"))  // Solo el primer nombre
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
                ->get(['RES_Id', 'RES_FechaProgramada', 'TUR_Id', 'BAH_Id', 'RES_State', 'RES_Cliente']); // solo las columnas necesarias

            $reservas = [];

            foreach ($reservasRaw as $reserva) {
                $fecha = $reserva->RES_FechaProgramada;
                $turno = $reserva->TUR_Id;
                $bahia = $reserva->BAH_Id;
                $state = $reserva->RES_State;
                $idreserva = $reserva->RES_Id;
                $cliente = $reserva->RES_Cliente;

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
                $reservas[$fecha][$turno][$bahia][] = $cliente;
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
            'RES_FechaProgramada' => 'required|string',
            'RES_Placa' => 'required|string|max:20',
            'RES_Moto' => 'required|string|max:150',
            'RES_Cliente' => 'required|string|max:120',
            'RES_Celular' => 'required|string|max:12',
            'RES_Detalle' => 'nullable|string|max:250',
            'RES_Adicional' => 'nullable|string|max:250',
        ]);

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

        $placaSeleccionada = strtoupper(trim($request->query('Placa', '')));
        $ultimoMtto = null;
        //dd($placaSeleccionada);
        if ($placaSeleccionada !== '') {
            // Mapeo de tablas y metadatos
            $tablas = [
                [
                    'tabla' => 'mantenimiento_general_carburada',
                    'prefix' => 'MGC',
                    'tipo' => 'MTTO GENERAL CARBURADAS',
                    'url' => '/tenant/mantenimientos/generalcarburada',
                ],
                [
                    'tabla' => 'mantenimiento_general_inyectada',
                    'prefix' => 'MGI',
                    'tipo' => 'MTTO GENERAL INYECTADAS',
                    'url' => '/tenant/mantenimientos/generalinyectada'
                ],
                [
                    'tabla' => 'mantenimiento_preventivo_carburada',
                    'prefix' => 'MPC',
                    'tipo' => 'MTTO PREVENTIVOS CARBURADAS',
                    'url' => '/tenant/mantenimientos/preventivocarburada'
                ],
                [
                    'tabla' => 'mantenimiento_preventivo_inyectada',
                    'prefix' => 'MPI',
                    'tipo' => 'MTTO PREVENTIVOS INYECTADAS',
                    'url' => '/tenant/mantenimientos/preventivoinyectada'
                ],
                [
                    'tabla' => 'mantenimiento_actividad_variadas',
                    'prefix' => 'MAV',
                    'tipo' => 'ACTIVIDADES VARIADAS',
                    'url' => '/tenant/actividades/mantenimientoactividadvariada'
                ],
            ];

            $queries = [];

            foreach ($tablas as $item) {
                $p = $item['prefix'];

                $queries[] = DB::table("{$item['tabla']} as t")
                    ->join('users as u', 'u.id', '=', 't.PER_Id')
                    ->select(
                        "t.{$p}_Id as Id",
                        "t.{$p}_Placa as Placa",
                        "t.{$p}_celular as Celular",
                        "t.{$p}_Propietario as Propietario",
                        "t.{$p}_Unidad as Unidad",
                        "t.{$p}_KMEntrada as KMEntrada",
                        "t.{$p}_ProximoCambioAceite as ProximoCambioAceite",
                        "t.{$p}_ProximoServicio as ProximoServicio",
                        "t.{$p}_FechaCreacion as FechaCreacion",
                        DB::raw('CONCAT(u.name) as personal'),
                        DB::raw("'{$item['tipo']}' as Tipo"),
                        DB::raw("'{$item['url']}' as url")
                    )
                    ->whereRaw("UPPER(t.{$p}_Placa) = ?", [$placaSeleccionada]);
            }


            // Unimos las 5 consultas en una sola
            $firstQuery = array_shift($queries);
            foreach ($queries as $query) {
                $firstQuery->unionAll($query);
            }

            // Ejecutamos la consulta consolidada y ordenamos los resultados
            $ultimoMtto = DB::query()
                ->fromSub($firstQuery, 'unioned_maintenances')
                ->orderByDesc('FechaCreacion')
                ->first();
        }

        $hayResultados = !is_null($ultimoMtto);

        $data = [
            'validacion' => $hayResultados ? 'true' : 'false',
            'mensaje'    => $hayResultados
                ? 'Se encontró mantenimientos de esta placa correctamente'
                : 'Error, no se encontraron mantenimientos con esta Placa',
        ];

        if ($hayResultados) {
            if ($ultimoMtto) {
                // 1. Mapeo de tabla y prefijo según el tipo encontrado
                $mapa = match ($ultimoMtto->Tipo) {
                    'MTTO GENERAL CARBURADAS'     => ['tabla' => 'mantenimiento_general_carburada', 'p' => 'MGC'],
                    'MTTO GENERAL INYECTADAS'     => ['tabla' => 'mantenimiento_general_inyectada', 'p' => 'MGI'],
                    'MTTO PREVENTIVOS CARBURADAS' => ['tabla' => 'mantenimiento_preventivo_carburadas', 'p' => 'MPC'],
                    'MTTO PREVENTIVOS INYECTADAS' => ['tabla' => 'mantenimiento_preventivo_inyectadas', 'p' => 'MPI'],
                    'ACTIVIDADES VARIADAS'        => ['tabla' => 'mantenimiento_actividad_variadas', 'p' => 'MAV'],
                };

                $p = $mapa['p'];
                $raw = DB::table($mapa['tabla'])->where("{$p}_Id", $ultimoMtto->Id)->first();

                // 2. Extraemos los puntos clave unificados (estándar para todas)
                $resumen = [
                    'cabecera' => [
                        'id'          => $ultimoMtto->Id,
                        'tipo'        => $ultimoMtto->Tipo,
                        'url_pdf'     => $ultimoMtto->url,
                        'fecha'       => $ultimoMtto->FechaCreacion,
                        'placa'       => $ultimoMtto->Placa,
                        'propietario' => $ultimoMtto->Propietario,
                        'celular'     => $ultimoMtto->Celular,
                        'unidad'      => $ultimoMtto->Unidad,
                        'km'          => $ultimoMtto->KMEntrada,
                        'mecanico'    => $ultimoMtto->personal,
                    ],
                    // Métricas universales clave
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
                    // Datos extra específicos si existen
                    'extras' => []
                ];

                // 3. Agregamos solo lo adicional según la especialidad
                if (str_contains($ultimoMtto->Tipo, 'INYECTADAS')) {
                    $resumen['extras']['escaneo'] = $raw->{"{$p}_Escaneo"} ?? $raw->{"{$p}_PorcentajeVidaUtil"} ?? null;
                    $resumen['extras']['porcentaje_humedad'] = $raw->{"{$p}_HumedadFrenos"} ?? $raw->{"{$p}_PorcentajeHumedad"} ?? null;
                }

                if ($ultimoMtto->Tipo === 'ACTIVIDADES VARIADAS') {
                    $resumen['extras']['detalle_trabajo'] = $raw->{"{$p}_DetalleRealizado"} ?? $raw->{"{$p}_Detalle"} ?? null;
                }

                $data['resumen'] = $resumen;
            }
        }

        $colorview = $empresa?->tipo_tema;
        // dd($data);
        // Retorno de vistas según plan
        if ($plan === 'start') {
            return view("tenant_{$tiponegocio}.welcome", compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview', 'data'));
        }

        return view("tenant_{$tiponegocio}.landing.page.historial", compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview', 'data'));
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
