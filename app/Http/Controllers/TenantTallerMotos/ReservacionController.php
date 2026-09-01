<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantTallerMotos\Reservacion;
use App\Models\TenantTallerMotos\Bahia;
use App\Models\Tenant\Almacen;
use App\Models\TenantTallerMotos\MantenimientoActividadVariada;
use App\Models\TenantTallerMotos\MantenimientoGeneralCarburada;
use App\Models\TenantTallerMotos\MantenimientoGeneralInyectada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoCarburada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoInyectada;
use App\Models\TenantTallerMotos\Turno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class ReservacionController extends Controller
{
	public function __construct()
	{

	}

	public function index(Request $request)
	{
		if ($request->ajax()) {
            $roles = Auth::user()->getRoleNames();
            $idpersonal = Auth::user()->id;

            $rolAdmin = false;
            if ($roles->contains('Admin') || $roles->contains('Gerente')) {
                $rolAdmin = true;
            }

            if ($rolAdmin) {
                $data = DB::table('reservacion as p')
                ->join('almacen as a','p.ALM_Id','=','a.ALM_Id')
                ->join('bahia as b','p.BAH_Id','=','b.BAH_Id')
                ->join('turno as t','p.TUR_Id','=','t.TUR_Id')
                ->select('p.*','a.ALM_NombreAlmacen as sede','b.BAH_Nombre','t.TUR_Descripcion');

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('RES_FechaProgramada', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('RES_FechaProgramada', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('RES_State', $request->estado);
                }
               // dd($data->get());
                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('Fecha', function ($row) {
                        $btn = $row->RES_FechaProgramada.' '.$row->TUR_Descripcion;
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->RES_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editReservacion"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        if($row->RES_Estado == 'ACT'){
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->RES_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteReservacion"><i class="fa fa-trash"></i></a>';
                        }else{
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->RES_Id . '" data-original-title="Activar" class="btn btn-success btn-sm activarReservacion"><i class="fa fa-check"></i></a>';
                        }

                        return $btn;
                    })
                    ->rawColumns(['action1','action2','Fecha'])
                    ->make(true);
            }
			
        }
        
        return view('tenant_' . tenant('tipo_negocio') . '.reservaciones.administracion.index');
	}

	public function semanal(Request $request,$idlocal)
	{
		$local = Local::where('LOC_Estado', 'ACT')->get();
        $localFirst = Local::where('LOC_Id', $idlocal)->first();
        $semana = [];
        $horariosdias = DB::table('horario')
        ->where('LOC_Id', $idlocal)
        ->pluck('HOR_Dia')
        ->unique()
        ->toArray();

        $horarios = DB::table('horario')
        ->where('HOR_Estado','ACT')
        ->where('LOC_Id', $idlocal)
        ->get();

        $horariosturno = DB::table('horario')
        ->where('HOR_Estado','ACT')
        ->where('LOC_Id', $idlocal)
        ->pluck('TUR_Id')
        ->unique()
        ->toArray();

        $bahias = DB::table('bahia as b')
            ->join('local as l','l.LOC_Id','=','b.LOC_Id')
            ->join('personal as p','p.PER_Id','=','b.PER_Id')            
            ->select('b.*','l.LOC_Nombre as Local','p.PER_Id',DB::raw("SUBSTRING_INDEX(p.PER_Nombre, ' ', 1) as Nombre"),  // Solo el primer nombre
            DB::raw("SUBSTRING_INDEX(p.PER_Apellido, ' ', 1) as Apellido"))
            ->where('l.LOC_Id', $idlocal)
            ->where('b.BAH_Estado','ACT')
            ->get();
            
        $turnos = Turno::Where('TUR_Estado','ACT')->whereIn('TUR_Id', $horariosturno)->get();

        $totalBahias = Bahia::Where('BAH_Estado','ACT')->where('LOC_Id', $idlocal)->count();

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
            // Guarda tanto la fecha como el nombre del día
            if (in_array($diaNormalizado, $horariosdias)) {                
                $semana[$i-1] = [
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
		
        $reservasRaw = Reservacion::whereBetween('RES_FechaProgramada', [$fechaInicial, $fechaFinal])->where('RES_State','!=','RECHAZADO')
            ->get(['RES_Id', 'RES_FechaProgramada', 'TUR_Id', 'BAH_Id','RES_State','RES_Cliente']); // solo las columnas necesarias

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

        // return view('rrhh.reservacion.semanal',compact('local','localFirst','turnos','totalBahias','semana','bahias', 'reservas','horarioprogramado','fechaInicial','fechaFinal'));
        
        return view('tenant_' . tenant('tipo_negocio') . '.reservaciones.administracion.index',compact('local','localFirst','turnos','totalBahias','semana','bahias', 'reservas','horarioprogramado','fechaInicial','fechaFinal'));
    }

	function normalizarDia($dia) {
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

	public function create(Request $request)
	{
        $idlocal = 1;
        if($request->filled('sede_id')){
            $idlocal = $request->sede_id;
        }
        $local = Almacen::where('ALM_Status', 1)->get();
        $localFirst = Almacen::where('ALM_Id', $idlocal)->first();
        $semana = [];
        $horariosdias = DB::table('horario')
        ->where('ALM_Id', $idlocal)
        ->pluck('HOR_Dia')
        ->unique()
        ->toArray();

        $horarios = DB::table('horario')
        ->where('HOR_Estado','ACT')
        ->where('ALM_Id', $idlocal)
        ->get();

        $horariosturno = DB::table('horario')
        ->where('HOR_Estado','ACT')
        ->where('ALM_Id', $idlocal)
        ->pluck('TUR_Id')
        ->unique()
        ->toArray();

        $bahias = DB::table('bahia as b')
            ->join('almacen as l','l.ALM_Id','=','b.ALM_Id')
            ->join('users as u','u.id','=','b.USU_Id')            
            ->select('b.*','l.ALM_NombreAlmacen as Local','u.id',DB::raw("SUBSTRING_INDEX(u.name, ' ', 1) as Nombre"))  // Solo el primer nombre
            ->where('l.ALM_Id', $idlocal)
            ->where('b.BAH_Estado','ACT')
            ->get();

            
        $turnos = Turno::Where('TUR_Estado','ACT')->whereIn('TUR_Id', $horariosturno)->get();

        $totalBahias = Bahia::Where('BAH_Estado','ACT')->where('ALM_Id', $idlocal)->count();
        
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
                $semana[$i-1] = [
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
        $reservasRaw = Reservacion::whereBetween('RES_FechaProgramada', [$fechaInicial, $fechaFinal])->where('RES_State','!=','RECHAZADO')
            ->get(['RES_Id', 'RES_FechaProgramada', 'TUR_Id', 'BAH_Id','RES_State','RES_Cliente']); // solo las columnas necesarias
       
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

        return view('tenant_' . tenant('tipo_negocio') . '.reservaciones.administracion.create',compact('local','localFirst','turnos','totalBahias','semana','bahias', 'reservas','horarioprogramado','fechaInicial','fechaFinal'));
	}

	public function store (Request $request)
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
            'TIP_Mantenimiento' => 'nullable|string|in:MANTENIMIENTO GENERAL CARBURADA,MANTENIMIENTO GENERAL INYECTADA,MANTENIMIENTO PREVENTIVO CARBURADA,MANTENIMIENTO PREVENTIVO INYECTADA,ACTIVIDAD VARIADA',
            'CAM_Aceite' => 'nullable|string|in:SI,NO',
            'aceite' => 'nullable|string|max:100',
            'CAM_FiltroAceite' => 'nullable|string|in:SI,NO',
        ]);

        // Pre-chequeo: mensaje inmediato en el caso normal (sin carrera).
        if (Reservacion::slotEstaOcupado($validated['BAH_Id'], $validated['TUR_Id'], $validated['RES_FechaProgramada'])) {
            return response()->json(['success' => false, 'message' => 'Esa bahía y turno ya están reservados para esa fecha. Elige otro horario.'], 409);
        }

        try {
            $Reservacion = Reservacion::create(collect($validated)->except([
                'TIP_Mantenimiento', 'CAM_Aceite', 'aceite', 'CAM_FiltroAceite',
            ])->all());
        } catch (QueryException $e) {
            // Protección real ante condición de carrera: el índice único de BD
            // rechazó un segundo INSERT casi simultáneo para el mismo slot.
            if (Reservacion::esConflictoDeSlot($e)) {
                return response()->json(['success' => false, 'message' => 'Esa bahía y turno acaban de ser reservados por otra persona. Elige otro horario.'], 409);
            }
            throw $e;
        }

        // Si se indicó un tipo de mantenimiento, se crea de una vez el
        // registro correspondiente (checklist en blanco, se completa luego
        // al atender). Es opcional: una reserva puede quedar sin esto.
        if (!empty($validated['TIP_Mantenimiento'])) {
            $this->crearMantenimientoDesdeReserva($validated, $Reservacion);
        }

        return response()->json(['success' => 'Reservacion Registrado Exitosamente.']);
	}

    /**
     * Crea el registro de mantenimiento del tipo elegido al reservar, con el
     * checklist en blanco (se completa cuando se atiende la moto). Cubre los
     * 5 tipos existentes; antes solo cubria 3 y no tenia forma de invocarse
     * desde la interfaz.
     */
    private function crearMantenimientoDesdeReserva(array $datos, Reservacion $reserva): void
    {
        $mytime = Carbon::now('America/Lima');
        $idusu = Auth::id();
        $idper = Auth::id();

        $tipoMantenimiento = $datos['TIP_Mantenimiento'];
        $cambioAceite = $datos['CAM_Aceite'] ?? null;
        $aceite = $datos['aceite'] ?? null;
        $cambioFiltro = $datos['CAM_FiltroAceite'] ?? null;

        $detalleServicio = "*Tipo de Mantenimiento: " . $tipoMantenimiento . ". \n";
        if ($cambioAceite) {
            $detalleServicio .= "*Cambio de Aceite: " . $cambioAceite . ". \n";
        }
        if ($cambioAceite === "SI" && $aceite) {
            $detalleServicio .= "*Aceite: " . $aceite . ". \n";
        }
        if ($cambioFiltro) {
            $detalleServicio .= "*Cambio de Filtro de Aceite: " . $cambioFiltro . ". \n";
        }

        $placa = $datos['RES_Placa'];
        $propietario = $datos['RES_Cliente'];
        $celular = $datos['RES_Celular'];
        $unidad = $datos['RES_Moto'];
        $detalleObservacion = $datos['RES_Detalle'] ?? null;

        if ($tipoMantenimiento === 'MANTENIMIENTO GENERAL INYECTADA') {
            $mtto = new MantenimientoGeneralInyectada;
            $mtto->MGI_Placa = $placa;
            $mtto->MGI_Propietario = $propietario;
            $mtto->MGI_celular = $celular;
            $mtto->MGI_Unidad = $unidad;
            $mtto->MGI_KMEntrada = "";
            $mtto->MGI_DetalleIngreso = $detalleServicio;
            $mtto->MGI_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 27) as $n) {
                $mtto->{"MGI_Det{$n}"} = "NO";
            }
            $mtto->MGI_Det1Informacion = "";
            $mtto->MGI_Det9Admision = "";
            $mtto->MGI_Det9Escape = "";
            $mtto->MGI_Det10Medida = "";
            $mtto->MGI_Det11Medida = "";
            $mtto->MGI_Det18 = "";
            $mtto->MGI_Det19 = "";
            $mtto->MGI_Det20Humedad = "";
            $mtto->MGI_Det22Ventilador = "";
            $mtto->MGI_Det24Vida = "";
            $mtto->MGI_Det24Carga = "";
            $mtto->MGI_Det24Arranque = "";
            $mtto->MGI_DetalleRealizado = "";
            $mtto->MGI_CorrecionObservacion = "";
            $mtto->MGI_ProximoCambioAceite = "";
            $mtto->MGI_ProximoServicio = "";
            $mtto->MGI_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MGI_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MGI_UsuarioCreacion = $idusu;
            $mtto->MGI_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'MANTENIMIENTO PREVENTIVO INYECTADA') {
            $mtto = new MantenimientoPreventivoInyectada;
            $mtto->MPI_Placa = $placa;
            $mtto->MPI_Propietario = $propietario;
            $mtto->MPI_celular = $celular;
            $mtto->MPI_Unidad = $unidad;
            $mtto->MPI_KMEntrada = "";
            $mtto->MPI_DetalleIngreso = $detalleServicio;
            $mtto->MPI_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 20) as $n) {
                $mtto->{"MPI_Det{$n}"} = "NO";
            }
            $mtto->MPI_Det1Informacion = "";
            $mtto->MPI_Det7Admision = "";
            $mtto->MPI_Det7Escape = "";
            $mtto->MPI_Det8Medida = "";
            $mtto->MPI_Det15 = "";
            $mtto->MPI_Det16 = "";
            $mtto->MPI_Det17Ventilador = "";
            $mtto->MPI_Det19Vida = "";
            $mtto->MPI_Det19Carga = "";
            $mtto->MPI_Det19Arranque = "";
            $mtto->MPI_DetalleRealizado = "";
            $mtto->MPI_CorrecionObservacion = "";
            $mtto->MPI_ProximoCambioAceite = "";
            $mtto->MPI_ProximoServicio = "";
            $mtto->MPI_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MPI_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MPI_UsuarioCreacion = $idusu;
            $mtto->MPI_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'MANTENIMIENTO GENERAL CARBURADA') {
            $mtto = new MantenimientoGeneralCarburada;
            $mtto->MGC_Placa = $placa;
            $mtto->MGC_Propietario = $propietario;
            $mtto->MGC_celular = $celular;
            $mtto->MGC_Unidad = $unidad;
            $mtto->MGC_KMEntrada = "";
            $mtto->MGC_DetalleIngreso = $detalleServicio;
            $mtto->MGC_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 21) as $n) {
                $mtto->{"MGC_Det{$n}"} = "NO";
            }
            $mtto->MGC_Det1Informacion = "";
            $mtto->MGC_Det8Admision = "";
            $mtto->MGC_Det8Escape = "";
            $mtto->MGC_Det9Medida = "";
            $mtto->MGC_Det16 = "";
            $mtto->MGC_Det17 = "";
            $mtto->MGC_Det18Humedad = "";
            $mtto->MGC_Det19Ventilador = "";
            $mtto->MGC_Det21Vida = "";
            $mtto->MGC_Det21Carga = "";
            $mtto->MGC_Det21Arranque = "";
            $mtto->MGC_DetalleRealizado = "";
            $mtto->MGC_CorrecionObservacion = "";
            $mtto->MGC_ProximoCambioAceite = "";
            $mtto->MGC_ProximoServicio = "";
            $mtto->MGC_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MGC_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MGC_UsuarioCreacion = $idusu;
            $mtto->MGC_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'MANTENIMIENTO PREVENTIVO CARBURADA') {
            $mtto = new MantenimientoPreventivoCarburada;
            $mtto->MPC_Placa = $placa;
            $mtto->MPC_Propietario = $propietario;
            $mtto->MPC_celular = $celular;
            $mtto->MPC_Unidad = $unidad;
            $mtto->MPC_KMEntrada = "";
            $mtto->MPC_DetalleIngreso = $detalleServicio;
            $mtto->MPC_DetalleObservacion = $detalleObservacion;
            foreach (range(1, 11) as $n) {
                $mtto->{"MPC_Det{$n}"} = "NO";
            }
            $mtto->MPC_Det1Informacion = "";
            $mtto->MPC_Det7Admision = "";
            $mtto->MPC_Det7Escape = "";
            $mtto->MPC_Det8Medida = "";
            $mtto->MPC_Det11Vida = "";
            $mtto->MPC_Det11Carga = "";
            $mtto->MPC_Det11Arranque = "";
            $mtto->MPC_DetalleRealizado = "";
            $mtto->MPC_CorrecionObservacion = "";
            $mtto->MPC_ProximoCambioAceite = "";
            $mtto->MPC_ProximoServicio = "";
            $mtto->MPC_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MPC_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MPC_UsuarioCreacion = $idusu;
            $mtto->MPC_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        } elseif ($tipoMantenimiento === 'ACTIVIDAD VARIADA') {
            $mtto = new MantenimientoActividadVariada;
            $mtto->MAV_Placa = $placa;
            $mtto->MAV_Propietario = $propietario;
            $mtto->MAV_celular = $celular;
            $mtto->MAV_Unidad = $unidad;
            $mtto->MAV_KMEntrada = "";
            $mtto->MAV_DetalleIngreso = $detalleServicio;
            $mtto->MAV_DetalleObservacion = $detalleObservacion;
            $mtto->MAV_DetalleRealizado = "";
            $mtto->MAV_CorrecionObservacion = "";
            $mtto->MAV_ProximoCambioAceite = "";
            $mtto->MAV_ProximoServicio = "";
            $mtto->MAV_FechaCreacion = $mytime->toDateTimeString();
            $mtto->MAV_FechaEdicion = $mytime->toDateTimeString();
            $mtto->MAV_UsuarioCreacion = $idusu;
            $mtto->MAV_UsuarioEditado = $idusu;
            $mtto->PER_Id = $idper;
            $mtto->RES_Id = $reserva->RES_Id;
            $mtto->save();
        }
    }

	public function show($id)
	{
		//return view("rrhh.reservacion.show",["Reservacion"=>Reservacion::findOrFail($id)]);
	}

	public function edit($id)
	{
		return $datos = Reservacion::findOrFail($id);
	}

	public function update(Request $request,$id)
	{
		$Reservacion=Reservacion::findOrFail($id);

		// Solo se re-chequea disponibilidad si el cambio realmente puede
		// mover la reserva a otro slot (aprobar/rechazar no toca estos campos).
		$BAH_Id = $request->get('BAH_Id', $Reservacion->BAH_Id);
		$TUR_Id = $request->get('TUR_Id', $Reservacion->TUR_Id);
		$RES_FechaProgramada = $request->get('RES_FechaProgramada', $Reservacion->RES_FechaProgramada);

		if (Reservacion::slotEstaOcupado($BAH_Id, $TUR_Id, $RES_FechaProgramada, $Reservacion->RES_Id)) {
			return response()->json(['success' => false, 'message' => 'Esa bahía y turno ya están reservados para esa fecha. Elige otro horario.'], 409);
		}

		try {
			$Reservacion->update($request->all());
		} catch (QueryException $e) {
			if (Reservacion::esConflictoDeSlot($e)) {
				return response()->json(['success' => false, 'message' => 'Esa bahía y turno acaban de ser reservados por otra persona. Elige otro horario.'], 409);
			}
			throw $e;
		}

		return response()->json(['success' => 'Reservacion Editado Exitosamente.']);
	}

	public function activar($id)
	{
		try{
			$Reservacion=Reservacion::findOrFail($id);
			$Reservacion->RES_Estado = 'ACT';
			$Reservacion->update();
			return response()->json(['success' => 'Activado correctamente.']);
		}
		catch(\Illuminate\Database\QueryException $ex)
		{
			return Redirect::to('rrhh/Reservacion');
		}
	}

	public function destroy($id)
	{
		try{
			$Reservacion=Reservacion::findOrFail($id);
			$Reservacion->RES_Estado = 'DESACT';
			$Reservacion->update();
			return response()->json(['success' => 'Desactivado correctamente.']);
		}
		catch(\Illuminate\Database\QueryException $ex)
		{
			return Redirect::to('rrhh/Reservacion');
		}
	}
}
