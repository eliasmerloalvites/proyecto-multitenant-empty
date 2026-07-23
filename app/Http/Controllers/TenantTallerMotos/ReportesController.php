<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Personal;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use DateTime;
use Carbon\Carbon;

class ReportesController extends Controller
{
    public function __construct() {}

    public function listageneral(Request $request)
    {
        $mytime = Carbon::now('America/Lima');
        $fecha = $mytime->toDateString();
        $idusu = Auth::user()->id;
        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }

        if ($request->ajax()) {


            if ($rolAdmin) {

                $configuraciones = [
                    [
                        'tabla' => 'mantenimiento_general_carburada',
                        'alias' => 'mgc',
                        'prefijo' => 'MGC',
                        'url' => '/tenant/mantenimientos/generalcarburada',
                        'tipo' => 'MTTO GENERAL CARBURADAS'
                    ],
                    [
                        'tabla' => 'mantenimiento_general_inyectada',
                        'alias' => 'mgi',
                        'prefijo' => 'MGI',
                        'url' => '/tenant/mantenimientos/generalinyectada',
                        'tipo' => 'MTTO GENERAL INYECTADAS'
                    ],
                    [
                        'tabla' => 'mantenimiento_preventivo_carburada',
                        'alias' => 'mpc',
                        'prefijo' => 'MPC',
                        'url' => '/tenant/mantenimientos/preventivocarburada',
                        'tipo' => 'MTTO PREVENTIVOS CARBURADAS'
                    ],
                    [
                        'tabla' => 'mantenimiento_preventivo_inyectada',
                        'alias' => 'mpi',
                        'prefijo' => 'MPI',
                        'url' => '/tenant/mantenimientos/preventivoinyectada',
                        'tipo' => 'MTTO PREVENTIVOS INYECTADAS'
                    ],
                    [
                        'tabla' => 'mantenimiento_actividad_variadas',
                        'alias' => 'mav',
                        'prefijo' => 'MAV',
                        'url' => '/tenant/actividades/mantenimientoactividadvariada',
                        'tipo' => 'ACTIVIDADES VARIADAS'
                    ]
                ];

                $datosmtto = collect();
                
                foreach ($configuraciones as $config) {

                    $datosmtto = $datosmtto->merge(
                        $this->obtenerMantenimiento(
                            $request,
                            $config['tabla'],
                            $config['alias'],
                            $config['prefijo'],
                            $config['url'],
                            $config['tipo']
                        )
                    );
                }

                $arrayOrdenado = $datosmtto
                    ->sortByDesc('FechaCreacion')
                    ->values();

                return Datatables::of($arrayOrdenado)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->estado . '</button>';
                        } else if ($row->estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->estado . '</button>';
                        } else if ($row->estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"   data-original-title="Edit" class="edit btn btn-primary btn-sm editar"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  data-original-title="Delete" class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="' . $row->url . '/' . $row->Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  title="Activar" class="btn btn-info btn-sm activar"><i class="fa fa-check"></i></a>';
                        } else if ($row->estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  title="Aprobar" class="btn btn-success btn-sm aprobar"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celularnotificar', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->Celular . ' <a target="_blank" href="https://wa.me/51' . $row->Celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->Id . '" title="Activar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->Celular . ' <a target="_blank" href="https://wa.me/51' . $row->Celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->Id . '" title="Aprobar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('observaciones', function ($row) {
                        if ($row->observacion == null) {
                            $btn = '<button type="button" data-id="' . $row->Id . '" data-url="' . $row->url . '" data-info="" class="btn btn-sm btn-info modalObservar " title="observar" >OBSERVAR</button>';
                        } else {
                            $btn = '<button type="button" data-id="' . $row->Id . '" data-url="' . $row->url . '" data-info="' . $row->observacion . '" class="btn btn-sm btn-danger modalObservar " title="ver  Observacion" >OBSERVADO</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('respuestas', function ($row) {
                        if ($row->respuesta == null && $row->observacion == null) {
                            $btn = '';
                        } else if ($row->respuesta == null && $row->observacion != null) {
                            $btn = '<button type="button" data-id="' . $row->Id . '" data-url="' . $row->url . '" data-infoobservacion="' . $row->observacion . '" data-info="" class="btn btn-sm btn-info modalResponder " title="responder observación" >RESPONDER</button>';
                        } else {
                            $btn = '<button type="button" data-id="' . $row->Id . '" data-url="' . $row->url . '" data-infoobservacion="' . $row->observacion . '" data-info="' . $row->respuesta . '" class="btn btn-sm btn-success modalResponder " title="ver respuesta" >SOLUCIONADO</button>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celularnotificar', 'observaciones', 'respuestas'])
                    ->make(true);
            }
        }
        return view('tenant_' . tenant('tipo_negocio') . '.reportes.listageneral', compact('rolAdmin'));
    }

    public function listageneral1(Request $request)
    {
        $fecha = $request->fechaa;
        $fec    = explode(" - ", $fecha);
        $fechaini = $fec[0];
        $fechafin = $fec[1];
        if ($request->ajax()) {
            $idusu = Auth::user()->id;
            $idpersonal = Auth::user()->PER_Id;
            $ubicaAdministrador = "FALSE";

            $roles = DB::table('role_user')
                ->select('*')
                ->where('user_id', '=', $idusu)
                ->get();

            foreach ($roles as $key => $value) {
                if ($value->role_id == 1) {
                    $ubicaAdministrador = "TRUE";
                }
                if ($value->role_id == 7) {
                    $ubicaAdministrador = "TRUE";
                }
            }

            if ($ubicaAdministrador == "TRUE") {
                /* $data = DB::table('mantenimiento_general_carburada as mgc')
				->join('personal as p','p.PER_Id','=','mgc.PER_Id')
				->select('mgc.MGC_Id','mgc.MGC_Placa','mgc.MGC_Propietario','mgc.MGC_celular','mgc.MGC_Unidad','mgc.MGC_KMEntrada','mgc.MGC_FechaCreacion','mgc.MGC_Estado',DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal'))
				->get(); */

                $datosmtto1 = DB::table('mantenimiento_general_carburada as mgc')
                    ->join('personal as p', 'p.PER_Id', '=', 'mgc.PER_Id')
                    ->select(
                        'mgc.MGC_Id as Id',
                        'mgc.MGC_Placa as Placa',
                        'mgc.MGC_Propietario as Propietario',
                        'mgc.MGC_celular as Celular',
                        'mgc.notificar',
                        'mgc.observacion',
                        'mgc.respuesta',
                        'mgc.MGC_Unidad as Unidad',
                        'mgc.MGC_KMEntrada as KMEntrada',
                        'mgc.MGC_Estado as estado',
                        'mgc.MGC_ProximoCambioAceite as ProximoCambioAceite',
                        'mgc.MGC_ProximoServicio as ProximoServicio',
                        'mgc.MGC_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mgc.MGC_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mgc.MGC_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto2 = DB::table('mantenimiento_general_inyectada as mgi')
                    ->join('personal as p', 'p.PER_Id', '=', 'mgi.PER_Id')
                    ->select(
                        'mgi.MGI_Id as Id',
                        'mgi.MGI_Placa as Placa',
                        'mgi.MGI_Propietario as Propietario',
                        'mgi.MGI_celular as Celular',
                        'mgi.notificar',
                        'mgi.observacion',
                        'mgi.respuesta',
                        'mgi.MGI_Unidad as Unidad',
                        'mgi.MGI_KMEntrada as KMEntrada',
                        'mgi.MGI_Estado as estado',
                        'mgi.MGI_ProximoCambioAceite as ProximoCambioAceite',
                        'mgi.MGI_ProximoServicio as ProximoServicio',
                        'mgi.MGI_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mgi.MGI_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mgi.MGI_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto3 = DB::table('mantenimiento_preventivo_carburadas as mpc')
                    ->join('personal as p', 'p.PER_Id', '=', 'mpc.PER_Id')
                    ->select(
                        'mpc.MPC_Id as Id',
                        'mpc.MPC_Placa as Placa',
                        'mpc.MPC_Propietario as Propietario',
                        'mpc.MPC_celular as Celular',
                        'mpc.notificar',
                        'mpc.observacion',
                        'mpc.respuesta',
                        'mpc.MPC_Unidad as Unidad',
                        'mpc.MPC_KMEntrada as KMEntrada',
                        'mpc.MPC_Estado as estado',
                        'mpc.MPC_ProximoCambioAceite as ProximoCambioAceite',
                        'mpc.MPC_ProximoServicio as ProximoServicio',
                        'mpc.MPC_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mpc.MPC_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mpc.MPC_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto4 = DB::table('mantenimiento_preventivo_inyectadas as mpi')
                    ->join('personal as p', 'p.PER_Id', '=', 'mpi.PER_Id')
                    ->select(
                        'mpi.MPI_Id as Id',
                        'mpi.MPI_Placa as Placa',
                        'mpi.MPI_Propietario as Propietario',
                        'mpi.MPI_celular as Celular',
                        'mpi.notificar',
                        'mpi.observacion',
                        'mpi.respuesta',
                        'mpi.MPI_Unidad as Unidad',
                        'mpi.MPI_KMEntrada as KMEntrada',
                        'mpi.MPI_Estado as estado',
                        'mpi.MPI_ProximoCambioAceite as ProximoCambioAceite',
                        'mpi.MPI_ProximoServicio as ProximoServicio',
                        'mpi.MPI_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mpi.MPI_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mpi.MPI_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto5 = DB::table('mantenimiento_actividad_variadas as mav')
                    ->join('personal as p', 'p.PER_Id', '=', 'mav.PER_Id')
                    ->select(
                        'mav.MAV_Id as Id',
                        'mav.MAV_Placa as Placa',
                        'mav.MAV_Propietario as Propietario',
                        'mav.MAV_celular as Celular',
                        'mav.notificar',
                        'mav.observacion',
                        'mav.respuesta',
                        'mav.MAV_Unidad as Unidad',
                        'mav.MAV_KMEntrada as KMEntrada',
                        'mav.MAV_Estado as estado',
                        'mav.MAV_ProximoCambioAceite as ProximoCambioAceite',
                        'mav.MAV_ProximoServicio as ProximoServicio',
                        'mav.MAV_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mav.MAV_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mav.MAV_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto6 = DB::table('mantenimiento_mavila as mm')
                    ->join('personal as p', 'p.PER_Id', '=', 'mm.PER_Id')
                    ->select(
                        'mm.MMA_Id as Id',
                        'mm.MMA_Placa as Placa',
                        'mm.MMA_Propietario as Propietario',
                        'mm.MMA_celular as Celular',
                        'mm.notificar',
                        'mm.observacion',
                        'mm.respuesta',
                        'mm.MMA_Unidad as Unidad',
                        'mm.MMA_KMEntrada as KMEntrada',
                        'mm.MMA_Estado as estado',
                        'mm.MMA_ProximoCambioAceite as ProximoCambioAceite',
                        'mm.MMA_ProximoServicio as ProximoServicio',
                        'mm.MMA_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mm.MMA_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mm.MMA_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto7 = DB::table('mantenimiento_garantia_mavila as mgm')
                    ->join('personal as p', 'p.PER_Id', '=', 'mgm.PER_Id')
                    ->select(
                        'mgm.MGM_Id as Id',
                        'mgm.MGM_Placa as Placa',
                        'mgm.MGM_Propietario as Propietario',
                        'mgm.MGM_celular as Celular',
                        'mgm.notificar',
                        'mgm.observacion',
                        'mgm.respuesta',
                        'mgm.MGM_Unidad as Unidad',
                        'mgm.MGM_KMEntrada as KMEntrada',
                        'mgm.MGM_Estado as estado',
                        'mgm.MGM_ProximoCambioAceite as ProximoCambioAceite',
                        'mgm.MGM_ProximoServicio as ProximoServicio',
                        'mgm.MGM_FechaCreacion as FechaCreacion',
                        DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal')
                    )
                    ->where(DB::raw('DATE(mgm.MGM_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mgm.MGM_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                $datosmtto = [];

                foreach ($datosmtto1 as $dat) {
                    $dat->url = "/mantenimiento/generalcarburadas";
                    $dat->Tipo = "MTTO GENERAL CARBURADAS";

                    array_push($datosmtto, $dat);
                }
                foreach ($datosmtto2 as $dat) {
                    $dat->url = "/mantenimiento/generalinyectadas";
                    $dat->Tipo = "MTTO GENERAL INYECTADAS";

                    array_push($datosmtto, $dat);
                }
                foreach ($datosmtto3 as $dat) {
                    $dat->url = "/mantenimiento/preventivocarburadas";
                    $dat->Tipo = "MTTO PREVENTIVOS CARBURADAS";

                    array_push($datosmtto, $dat);
                }
                foreach ($datosmtto4 as $dat) {
                    $dat->url = "/mantenimiento/preventivoinyectadas";
                    $dat->Tipo = "MTTO PREVENTIVOS INYECTADAS";


                    array_push($datosmtto, $dat);
                }
                foreach ($datosmtto5 as $dat) {
                    $dat->url = "/mantenimiento/actividadvariadas";
                    $dat->Tipo = "ACTIVIDADES VARIADAS";
                    array_push($datosmtto, $dat);
                }
                foreach ($datosmtto6 as $dat) {
                    $dat->url = "/mantenimiento/mavila";
                    $dat->Tipo = "ATENCION GARANTIA MAVILA";
                    array_push($datosmtto, $dat);
                }
                foreach ($datosmtto7 as $dat) {
                    $dat->url = "/mantenimiento/garantiamavila";
                    $dat->Tipo = "MTTO GARANTIA MAVILA";
                    array_push($datosmtto, $dat);
                }

                $coleccion = collect($datosmtto);
                $coleccionOrdenada = $coleccion->sortByDesc('FechaCreacion');
                $arrayOrdenado = $coleccionOrdenada->values()->all();

                return Datatables::of($arrayOrdenado)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->estado . '</button>';
                        } else if ($row->estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->estado . '</button>';
                        } else if ($row->estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"   data-original-title="Edit" class="edit btn btn-primary btn-sm editar"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  data-original-title="Delete" class="btn btn-danger btn-sm delete"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="' . $row->url . '/' . $row->Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  title="Activar" class="btn btn-info btn-sm activar"><i class="fa fa-check"></i></a>';
                        } else if ($row->estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->Id . '" data-url="' . $row->url . '"  title="Aprobar" class="btn btn-success btn-sm aprobar"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celularnotificar', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->Celular . ' <a target="_blank" href="https://wa.me/51' . $row->Celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->Id . '" title="Activar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->Celular . ' <a target="_blank" href="https://wa.me/51' . $row->Celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->Id . '" title="Aprobar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('observaciones', function ($row) {
                        if ($row->observacion == null) {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-info">OBSERVAR</button>';
                        } else {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">OBSERVADO</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('respuestas', function ($row) {
                        if ($row->respuesta == null && $row->observacion == null) {
                            $btn = '';
                        } else if ($row->respuesta == null && $row->observacion != null) {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-info">RESPONDER</button>';
                        } else {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-sucess">SOLUCIONADO</button>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celularnotificar', 'observaciones', 'respuestas'])
                    ->make(true);
            }
        }
        return view('reporte.listageneral', ["fecha" => $fecha]);
    }

    private function obtenerMantenimiento(
        Request $request,
        string $tabla,
        string $alias,
        string $prefijo,
        string $url,
        string $tipo
    ) {
        $query = DB::table("$tabla as $alias")
            ->join('users as u', 'u.id', '=', "$alias.PER_Id")
            ->select(
                "$alias.{$prefijo}_Id as Id",
                "$alias.{$prefijo}_Placa as Placa",
                "$alias.{$prefijo}_Propietario as Propietario",
                "$alias.{$prefijo}_celular as Celular",
                "$alias.notificar",
                "$alias.observacion",
                "$alias.respuesta",
                "$alias.{$prefijo}_Unidad as Unidad",
                "$alias.{$prefijo}_KMEntrada as KMEntrada",
                "$alias.{$prefijo}_Estado as estado",
                "$alias.{$prefijo}_ProximoCambioAceite as ProximoCambioAceite",
                "$alias.{$prefijo}_ProximoServicio as ProximoServicio",
                "$alias.{$prefijo}_FechaCreacion as FechaCreacion",
                DB::raw('CONCAT(u.name) as personal')
            );

        if ($request->filled('fecha_inicio')) {
            $query->whereDate(
                "$alias.{$prefijo}_FechaCreacion",
                '>=',
                $request->fecha_inicio
            );
        }

        if ($request->filled('fecha_fin')) {
            $query->whereDate(
                "$alias.{$prefijo}_FechaCreacion",
                '<=',
                $request->fecha_fin
            );
        }

        if ($request->filled('estado')) {
            $query->where(
                "$alias.{$prefijo}_Estado",
                $request->estado
            );
        }

        return $query->get()->map(function ($item) use ($url, $tipo) {

            $item->url = $url;
            $item->Tipo = $tipo;

            return $item;
        });
    }

    public function index1(Request $request)
    {
        $fecha = $request->fechaa;
        $fec    = explode(" - ", $fecha);
        $fechaini = $fec[0];
        $fechafin = $fec[1];

        if ($request->ajax()) {
            $idusu = Auth::user()->id;
            $idpersonal = Auth::user()->PER_Id;
            $ubicaAdministrador = "FALSE";

            $roles = DB::table('role_user')
                ->select('*')
                ->where('user_id', '=', $idusu)
                ->get();

            foreach ($roles as $key => $value) {
                if ($value->role_id == 1) {
                    $ubicaAdministrador = "TRUE";
                }
                if ($value->role_id == 7) {
                    $ubicaAdministrador = "TRUE";
                }
            }

            if ($ubicaAdministrador == "TRUE") {
                $data = DB::table('mantenimiento_general_carburada as mgc')
                    ->join('personal as p', 'p.PER_Id', '=', 'mgc.PER_Id')
                    ->select('mgc.MGC_Id', 'mgc.MGC_Placa', 'mgc.MGC_Propietario', 'mgc.MGC_celular', 'mgc.MGC_Unidad', 'mgc.MGC_KMEntrada', 'mgc.MGC_FechaCreacion', 'mgc.MGC_Estado', DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal'))
                    ->where(DB::raw('DATE(mgc.MGC_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mgc.MGC_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->MGC_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MGC_Estado . '</button>';
                        } else if ($row->MGC_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MGC_Estado . '</button>';
                        } else if ($row->MGC_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MGC_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGeneralCarburadas"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoGeneralCarburadas"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/mantenimiento/generalcarburadas/' . $row->MGC_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MGC_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" title="Aprobar" class="btn btn-info btn-sm activarMantenimientoGeneralCarburadas"><i class="fa fa-check"></i></a>';
                        } else if ($row->MGC_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" title="Aprobar" class="btn btn-success btn-sm aprobarMantenimientoGeneralCarburadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_general_carburada as mgc')
                    ->join('personal as p', 'p.PER_Id', '=', 'mgc.PER_Id')
                    ->select('mgc.MGC_Id', 'mgc.MGC_Placa', 'mgc.MGC_Propietario', 'mgc.MGC_celular', 'mgc.MGC_Unidad', 'mgc.MGC_KMEntrada', 'mgc.MGC_FechaCreacion', 'mgc.MGC_Estado', DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal'))
                    ->where('mgc.PER_Id', '=', $idpersonal)
                    ->where(DB::raw('DATE(mgc.MGC_FechaCreacion)'), '>=', ($fechaini))
                    ->where(DB::raw('DATE(mgc.MGC_FechaCreacion)'), '<=', ($fechafin))
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action1', function ($row) {
                        if ($row->MGC_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGeneralCarburadas"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/mantenimiento/generalcarburadas/' . $row->MGC_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3'])
                    ->make(true);
            }
        }
        return view('mantenimiento.generalcarburadas.index', ["fecha" => $fecha]);
    }

    public function create()
    {
        $idusu = Auth::user()->id;
        $idper = Auth::user()->PER_Id;
        $roles = DB::table('role_user')
            ->select('*')
            ->where('user_id', '=', $idusu)
            ->get();
        $ubicaAdministrador = "FALSE";

        foreach ($roles as $key => $value) {
            if ($value->role_id == 1) {
                $ubicaAdministrador = "TRUE";
            }
            if ($value->role_id == 7) {
                $ubicaAdministrador = "TRUE";
            }
        }

        if ($ubicaAdministrador === "TRUE") {
            $peronsal = Personal::select('PER_Id', 'PER_Nombre')->where('PER_EstadoLaboral', 'ACTIVO')->where('PUE_Id', 15)->get();
        } else {
            $peronsal = Personal::select('PER_Id', 'PER_Nombre')->where('PER_EstadoLaboral', 'ACTIVO')->where('PER_Id', $idper)->get();
        }


        return view("mantenimiento.generalcarburadas.create", ["admin" => $ubicaAdministrador, "personal" => $peronsal]);
    }
}
