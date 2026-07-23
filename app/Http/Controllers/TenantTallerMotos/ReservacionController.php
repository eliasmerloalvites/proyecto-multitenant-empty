<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantTallerMotos\Reservacion;
use App\Models\TenantTallerMotos\Bahia;
use App\Models\Tenant\Almacen;
use App\Models\TenantTallerMotos\MantenimientoGeneralCarburada;
use App\Models\TenantTallerMotos\MantenimientoGeneralInyectada;
use App\Models\TenantTallerMotos\MantenimientoPreventivoInyectada;
use App\Models\TenantTallerMotos\Turno;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReservacionController extends Controller
{
	public function __construct()
	{

	}

	public function index(Request $request)
	{
		if ($request->ajax()) {
            $roles = Auth::user()->getRoleNames();
            $idpersonal = Auth::user()->PER_Id;

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

        // return view('rrhh.reservacion.semanal',compact('local','localFirst','turnos','totalBahias','semana','bahias', 'reservas','horarioprogramado','fechaInicial','fechaFinal'));
        
        return view('tenant_' . tenant('tipo_negocio') . '.reservaciones.administracion.create',compact('local','localFirst','turnos','totalBahias','semana','bahias', 'reservas','horarioprogramado','fechaInicial','fechaFinal'));
	}

	public function store (Request $request)
	{
        $Reservacion = Reservacion::create($request->all());
        return response()->json(['success' => 'Reservacion Registrado Exitosamente.']);
	}
	
	public function storecliente (Request $request)
	{
        $mytime = Carbon::now('America/Lima');
        $idusu = 1;
        $idper = 1;

        $Reservacion = Reservacion::create($request->all());
        $TipoMantenimiento = $request->TIP_Mantenimiento;
        $CambioAceite = $request->CAM_Aceite;
        $Aceite = $request->aceite;
        $CambioFiltro = $request->CAM_FiltroAceite;
        $detalleServicio = "";
        $detalleServicio .= "*Tipo de Mantenimiento: ".$TipoMantenimiento.". \n";
        $detalleServicio .= "*Cambio de Aceite: ".$CambioAceite.". \n";
        // $detalleServicio = "Tipo de Mantenimiento: ".$TipoMantenimiento.". Cambio de Aceite: ".$CambioAceite.". Aceite: ".$Aceite.". Cambio de Filtro de Aceite: ".$CambioFiltro;
        if($CambioAceite == "SI"){
            $detalleServicio .= "*Aceite: ".$Aceite.". \n";
        }

        if($CambioFiltro == "SI"){
            $detalleServicio .= "*Cambio de Filtro de Aceite: ".$CambioFiltro.". \n";
        }

        if ($TipoMantenimiento === 'MANTENIMIENTO GENERAL INYECTADA') {
            $mtto_gener_inyectadas=new MantenimientoGeneralInyectada;
            $mtto_gener_inyectadas->MGI_Placa=$request->get('Placa');
            $mtto_gener_inyectadas->MGI_Propietario=$request->get('RES_Cliente');
            $mtto_gener_inyectadas->MGI_celular=$request->get('RES_Celular');
            $mtto_gener_inyectadas->MGI_Unidad=$request->get('RES_Moto');
            $mtto_gener_inyectadas->MGI_KMEntrada="";
            $mtto_gener_inyectadas->MGI_DetalleIngreso=$detalleServicio;
            $mtto_gener_inyectadas->MGI_DetalleObservacion=$request->get('RES_Detalle');
            $mtto_gener_inyectadas->MGI_Det1="NO";
            $mtto_gener_inyectadas->MGI_Det1Informacion="";
            $mtto_gener_inyectadas->MGI_Det2="NO";
            $mtto_gener_inyectadas->MGI_Det3="NO";
            $mtto_gener_inyectadas->MGI_Det4="NO";
            $mtto_gener_inyectadas->MGI_Det5="NO";
            $mtto_gener_inyectadas->MGI_Det6="NO";
            $mtto_gener_inyectadas->MGI_Det7="NO";
            $mtto_gener_inyectadas->MGI_Det8="NO";
            $mtto_gener_inyectadas->MGI_Det9="NO";
            $mtto_gener_inyectadas->MGI_Det9Admision="";
            $mtto_gener_inyectadas->MGI_Det9Escape="";
            $mtto_gener_inyectadas->MGI_Det10="NO";
            $mtto_gener_inyectadas->MGI_Det10Medida="";
            $mtto_gener_inyectadas->MGI_Det11="NO";
            $mtto_gener_inyectadas->MGI_Det11Medida="";
            $mtto_gener_inyectadas->MGI_Det12="NO";
            $mtto_gener_inyectadas->MGI_Det13="NO";
            $mtto_gener_inyectadas->MGI_Det14="NO";
            $mtto_gener_inyectadas->MGI_Det15="NO";
            $mtto_gener_inyectadas->MGI_Det16="NO";
            $mtto_gener_inyectadas->MGI_Det17="NO";
            $mtto_gener_inyectadas->MGI_Det18="";
            $mtto_gener_inyectadas->MGI_Det19="";
            $mtto_gener_inyectadas->MGI_Det20="NO";
            $mtto_gener_inyectadas->MGI_Det20Humedad="";
            $mtto_gener_inyectadas->MGI_Det21="NO";
            $mtto_gener_inyectadas->MGI_Det22="NO";
            $mtto_gener_inyectadas->MGI_Det22Ventilador="";
            $mtto_gener_inyectadas->MGI_Det23="NO";
            $mtto_gener_inyectadas->MGI_Det24="NO";
            $mtto_gener_inyectadas->MGI_Det24Vida="";
            $mtto_gener_inyectadas->MGI_Det24Carga="";
            $mtto_gener_inyectadas->MGI_Det24Arranque="";
            $mtto_gener_inyectadas->MGI_Det25="NO";
            $mtto_gener_inyectadas->MGI_Det26="NO";
            $mtto_gener_inyectadas->MGI_Det27="NO";
            $mtto_gener_inyectadas->MGI_DetalleRealizado="";
            $mtto_gener_inyectadas->MGI_CorrecionObservacion="";
            $mtto_gener_inyectadas->MGI_ProximoCambioAceite="";
            $mtto_gener_inyectadas->MGI_ProximoServicio="";
            $mtto_gener_inyectadas->MGI_FechaCreacion=$mytime->toDateTimeString();
            $mtto_gener_inyectadas->MGI_FechaEdicion=$mytime->toDateTimeString();
            $mtto_gener_inyectadas->MGI_UsuarioCreacion=$idusu;
            $mtto_gener_inyectadas->MGI_UsuarioEditado=$idusu;
            $mtto_gener_inyectadas->PER_Id=$idper;
            $mtto_gener_inyectadas->save();
            
        } else if ($TipoMantenimiento === 'MANTENIMIENTO PREVENTIVO INYECTADA') {
            $mtto_prev_inyec=new MantenimientoPreventivoInyectada;
            $mtto_prev_inyec->MPI_Placa=$request->get('Placa');
            $mtto_prev_inyec->MPI_Propietario=$request->get('RES_Cliente');
            $mtto_prev_inyec->MPI_celular=$request->get('RES_Celular');
            $mtto_prev_inyec->MPI_Unidad=$request->get('RES_Moto');
            $mtto_prev_inyec->MPI_KMEntrada="";
            $mtto_prev_inyec->MPI_DetalleIngreso=$detalleServicio;
            $mtto_prev_inyec->MPI_DetalleObservacion=$request->get('RES_Detalle');
            $mtto_prev_inyec->MPI_Det1="NO";
            $mtto_prev_inyec->MPI_Det1Informacion="";
            $mtto_prev_inyec->MPI_Det2="NO";
            $mtto_prev_inyec->MPI_Det3="NO";
            $mtto_prev_inyec->MPI_Det4="NO";
            $mtto_prev_inyec->MPI_Det5="NO";
            $mtto_prev_inyec->MPI_Det6="NO";
            $mtto_prev_inyec->MPI_Det7="NO";
            $mtto_prev_inyec->MPI_Det7Admision="";
            $mtto_prev_inyec->MPI_Det7Escape="";
            $mtto_prev_inyec->MPI_Det8="NO";
            $mtto_prev_inyec->MPI_Det8Medida="";
            $mtto_prev_inyec->MPI_Det9="NO";
            $mtto_prev_inyec->MPI_Det10="NO";
            $mtto_prev_inyec->MPI_Det11="NO";
            $mtto_prev_inyec->MPI_Det12="NO";
            $mtto_prev_inyec->MPI_Det13="NO";
            $mtto_prev_inyec->MPI_Det14="NO";
            $mtto_prev_inyec->MPI_Det15="";
            $mtto_prev_inyec->MPI_Det16="";
            $mtto_prev_inyec->MPI_Det17="NO";
            $mtto_prev_inyec->MPI_Det17Ventilador="";
            $mtto_prev_inyec->MPI_Det18="NO";
            $mtto_prev_inyec->MPI_Det19="NO";
            $mtto_prev_inyec->MPI_Det19Vida="";
            $mtto_prev_inyec->MPI_Det19Carga="";
            $mtto_prev_inyec->MPI_Det19Arranque="";
            $mtto_prev_inyec->MPI_Det20="NO";
            $mtto_prev_inyec->MPI_DetalleRealizado="";
            $mtto_prev_inyec->MPI_CorrecionObservacion="";
            $mtto_prev_inyec->MPI_ProximoCambioAceite="";
            $mtto_prev_inyec->MPI_ProximoServicio="";
            $mtto_prev_inyec->MPI_FechaCreacion=$mytime->toDateTimeString();
            $mtto_prev_inyec->MPI_FechaEdicion=$mytime->toDateTimeString();
            $mtto_prev_inyec->MPI_UsuarioCreacion=$idusu;
            $mtto_prev_inyec->MPI_UsuarioEditado=$idusu;
            $mtto_prev_inyec->PER_Id=$idper;
            $mtto_prev_inyec->save();
        } else if ($TipoMantenimiento === 'MANTENIMIENTO GENERAL CARBURADA') {
            $mtto_gener_carburadas=new MantenimientoGeneralCarburada;
            $mtto_gener_carburadas->MGC_Placa=$request->get('Placa');
            $mtto_gener_carburadas->MGC_Propietario=$request->get('RES_Cliente');
            $mtto_gener_carburadas->MGC_celular=$request->get('RES_Celular');
            $mtto_gener_carburadas->MGC_Unidad=$request->get('RES_Moto');
            $mtto_gener_carburadas->MGC_KMEntrada="";
            $mtto_gener_carburadas->MGC_DetalleIngreso=$detalleServicio;
            $mtto_gener_carburadas->MGC_DetalleObservacion=$request->get('RES_Detalle');
            $mtto_gener_carburadas->MGC_Det1="NO";
            $mtto_gener_carburadas->MGC_Det1Informacion="";
            $mtto_gener_carburadas->MGC_Det2="NO";
            $mtto_gener_carburadas->MGC_Det3="NO";
            $mtto_gener_carburadas->MGC_Det4="NO";
            $mtto_gener_carburadas->MGC_Det5="NO";
            $mtto_gener_carburadas->MGC_Det6="NO";
            $mtto_gener_carburadas->MGC_Det7="NO";
            $mtto_gener_carburadas->MGC_Det8="NO";
            $mtto_gener_carburadas->MGC_Det8Admision="";
            $mtto_gener_carburadas->MGC_Det8Escape="";
            $mtto_gener_carburadas->MGC_Det9="NO";
            $mtto_gener_carburadas->MGC_Det9Medida="";
            $mtto_gener_carburadas->MGC_Det10="NO";
            $mtto_gener_carburadas->MGC_Det11="NO";
            $mtto_gener_carburadas->MGC_Det12="NO";
            $mtto_gener_carburadas->MGC_Det13="NO";
            $mtto_gener_carburadas->MGC_Det14="NO";
            $mtto_gener_carburadas->MGC_Det15="NO";
            $mtto_gener_carburadas->MGC_Det16="";
            $mtto_gener_carburadas->MGC_Det17="";
            $mtto_gener_carburadas->MGC_Det18="NO";
            $mtto_gener_carburadas->MGC_Det18Humedad="";
            $mtto_gener_carburadas->MGC_Det19="NO";
            $mtto_gener_carburadas->MGC_Det19Ventilador="";
            $mtto_gener_carburadas->MGC_Det20="NO";
            $mtto_gener_carburadas->MGC_Det21="NO";
            $mtto_gener_carburadas->MGC_Det21Vida="";
            $mtto_gener_carburadas->MGC_Det21Carga="";
            $mtto_gener_carburadas->MGC_Det21Arranque="";
            $mtto_gener_carburadas->MGC_DetalleRealizado="";
            $mtto_gener_carburadas->MGC_CorrecionObservacion="";
            $mtto_gener_carburadas->MGC_ProximoCambioAceite="";
            $mtto_gener_carburadas->MGC_ProximoServicio="";
            $mtto_gener_carburadas->MGC_FechaCreacion=$mytime->toDateTimeString();
            $mtto_gener_carburadas->MGC_FechaEdicion=$mytime->toDateTimeString();
            $mtto_gener_carburadas->MGC_UsuarioCreacion=$idusu;
            $mtto_gener_carburadas->MGC_UsuarioEditado=$idusu;
            $mtto_gener_carburadas->PER_Id=$idper;
            $mtto_gener_carburadas->save();
        } 

        return response()->json(['success' => 'Reservacion Registrado Exitosamente.']);
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
		$Reservacion->update($request->all());
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
