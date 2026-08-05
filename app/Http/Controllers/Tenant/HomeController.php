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
use ReflectionFunction;

class HomeController extends Controller
{
    public function index()
    {

        $tenantid = tenant('id');
        $tiponegocio = tenant('tipo_negocio');
        return view('tenant_' . $tiponegocio . '.menu.home');
    }


    public function inicio()
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            if ($plan == 'start') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'empresa', 'colorview'));
            } else if ($plan == 'basic') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.landing.index', compact('tenantid', 'empresa', 'plan', 'colorview'));
            }
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
            if ($plan == 'start') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'empresa', 'colorview'));
            } else if ($plan == 'basic') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.landing.page.servicio', compact('tenantid', 'empresa', 'plan', 'colorview'));
            }
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
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'empresa', 'colorview', 'locales', 'localFirst', 'idlocal', 'turnos', 'totalBahias', 'semana', 'bahias', 'reservas', 'horarioprogramado', 'fechaInicial', 'fechaFinal', 'fechaSeleccionada'));
            } else if ($plan == 'basic') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.landing.page.reservar', compact('tenantid', 'empresa', 'plan', 'colorview', 'locales', 'localFirst', 'idlocal', 'turnos', 'totalBahias', 'semana', 'bahias', 'reservas', 'horarioprogramado', 'fechaInicial', 'fechaFinal', 'fechaSeleccionada'));
            }
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
        $Reservacion = Reservacion::create($request->all());
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Su reserva ha sido registrada correctamente.'
            ], 200);
        }

        return redirect()->back()->with('success', 'Reserva realizada con éxito');
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
            return view("tenant_{$tiponegocio}.welcome", compact('tenantid', 'plan', 'empresa', 'colorview', 'data'));
        }

        if ($plan === 'basic') {
            return view("tenant_{$tiponegocio}.landing.page.historial", compact('tenantid', 'empresa', 'plan', 'colorview', 'data'));
        }

        return redirect()->back();
    }

    public function catalogo(Request $request)
    {
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

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

            // 5. Retorno según el plan del Tenant
            if ($plan == 'start') {
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'tiponegocio', 'plan', 'empresa', 'colorview', 'dataProductos', 'dataCategoria', 'dataClase'));
            } else if ($plan == 'basic') {
                return view('tenant_' . $tiponegocio . '.landing.page.catalogo', compact('tenantid', 'tiponegocio', 'empresa', 'plan', 'colorview', 'dataProductos', 'dataCategoria', 'dataClase'));
            }

            // Fallback por si hay otro plan configurado
            return view('tenant_' . $tiponegocio . '.landing.page.catalogo', compact('tenantid', 'tiponegocio', 'empresa', 'plan', 'colorview', 'dataProductos', 'dataCategoria', 'dataClase'));
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
            if ($plan == 'start') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'empresa', 'colorview'));
            } else if ($plan == 'basic') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.landing.page.nosotros', compact('tenantid', 'empresa', 'plan', 'colorview'));
            }
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
            if ($plan == 'start') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'empresa', 'colorview'));
            } else if ($plan == 'basic') {
                $colorview = $empresa->tipo_tema;
                return view('tenant_' . $tiponegocio . '.landing.page.contacto', compact('tenantid', 'empresa', 'plan', 'colorview'));
            }
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
