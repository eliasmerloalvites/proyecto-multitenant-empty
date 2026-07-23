<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoPreventivoCarburada;
use App\Models\TenantTallerMotos\MpcDetalleReemplazo;
use App\Models\TenantTallerMotos\MpcImagen;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Laravel\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\URL;

class MantenimientoPreventivoCarburadaController extends Controller
{
    // LISTADO

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
                $data = DB::table('mantenimiento_preventivo_carburada as mpc')
                    ->join('users as p', 'p.id', '=', 'mpc.PER_Id')
                    ->select('mpc.MPC_Id', 'mpc.MPC_Placa', 'mpc.MPC_Propietario', 'mpc.MPC_celular', 'mpc.notificar', 'mpc.MPC_Unidad', 'mpc.MPC_KMEntrada', 'mpc.MPC_FechaCreacion', 'mpc.MPC_Estado', DB::raw('CONCAT(p.name) as personal'));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MPC_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MPC_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MPC_Estado', $request->estado);
                }

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->MPC_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MPC_Estado . '</button>';
                        } else if ($row->MPC_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MPC_Estado . '</button>';
                        } else if ($row->MPC_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MPC_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="/tenant/mantenimientos/preventivocarburada/' . $row->MPC_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoPreventivoCarburadas"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoPreventivoCarburadas"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/preventivocarburada/' . $row->MPC_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MPC_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoPreventivoCarburadas"><i class="fa fa-check"></i></a>';
                        } else if ($row->MPC_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoPreventivoCarburadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MPC_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MPC_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MPC_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MPC_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_preventivo_carburada as mpc')
                    ->join('users as p', 'p.id', '=', 'mpc.PER_Id')
                    ->select('mpc.MPC_Id', 'mpc.MPC_Placa', 'mpc.MPC_Propietario', 'mpc.MPC_celular', 'mpc.notificar', 'mpc.MPC_Unidad', 'mpc.MPC_KMEntrada', 'mpc.MPC_FechaCreacion', 'mpc.MPC_Estado', DB::raw('CONCAT(p.name) as personal'))
                    ->where('mpc.PER_Id', '=', $idpersonal)
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action1', function ($row) {
                        if ($row->MPC_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoPreventivoCarburadas"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/mantenimientos/preventivocarburada/' . $row->MPC_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MPC_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MPC_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.preventivo.carburadas.index');
    }

    // REGISTRAR
    public function create()
    {
        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }
        $personal = User::select('id', 'name')->get();


        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.preventivo.carburadas.create', ["admin" => $rolAdmin, "personal" => $personal]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_preventivo_carburadas = new MantenimientoPreventivoCarburada;
            $mtto_preventivo_carburadas->MPC_Placa = $request->get('MPC_Placa');
            $mtto_preventivo_carburadas->MPC_Propietario = $request->get('MPC_Propietario');
            $mtto_preventivo_carburadas->MPC_celular = $request->get('MPC_celular');
            $mtto_preventivo_carburadas->MPC_Unidad = $request->get('MPC_Unidad');
            $mtto_preventivo_carburadas->MPC_KMEntrada = $request->get('MPC_KMEntrada');
            $mtto_preventivo_carburadas->MPC_DetalleIngreso = $request->get('MPC_DetalleIngreso');
            $mtto_preventivo_carburadas->MPC_DetalleObservacion = $request->get('MPC_DetalleObservacion');
            $mtto_preventivo_carburadas->MPC_Det1=$request->get('MPC_Det1')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det1Informacion=$request->get('MPC_Det1Informacion');
            $mtto_preventivo_carburadas->MPC_Det2=$request->get('MPC_Det2')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det3=$request->get('MPC_Det3')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det4=$request->get('MPC_Det4')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det5=$request->get('MPC_Det5')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det6=$request->get('MPC_Det6')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det7=$request->get('MPC_Det7')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det7Admision=$request->get('MPC_Det7Admision');
            $mtto_preventivo_carburadas->MPC_Det7Escape=$request->get('MPC_Det7Escape');
            $mtto_preventivo_carburadas->MPC_Det8=$request->get('MPC_Det8')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det8Medida=$request->get('MPC_Det8Medida');
            $mtto_preventivo_carburadas->MPC_Det9=$request->get('MPC_Det9');
            $mtto_preventivo_carburadas->MPC_Det10=$request->get('MPC_Det10');
            $mtto_preventivo_carburadas->MPC_Det11=$request->get('MPC_Det11')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det11Vida=$request->get('MPC_Det11Vida');
            $mtto_preventivo_carburadas->MPC_Det11Carga=$request->get('MPC_Det11Carga');
            $mtto_preventivo_carburadas->MPC_Det11Arranque=$request->get('MPC_Det11Arranque');
            $mtto_preventivo_carburadas->MPC_DetalleRealizado = $request->get('MPC_DetalleRealizado');
            $mtto_preventivo_carburadas->MPC_CorrecionObservacion = $request->get('MPC_CorrecionObservacion');
            $mtto_preventivo_carburadas->MPC_ProximoCambioAceite = $request->get('MPC_ProximoCambioAceite');
            $mtto_preventivo_carburadas->MPC_ProximoServicio = $request->get('MPC_ProximoServicio');
            $mtto_preventivo_carburadas->MPC_FechaCreacion = $mytime->toDateTimeString();
            $mtto_preventivo_carburadas->MPC_FechaEdicion = $mytime->toDateTimeString();
            $mtto_preventivo_carburadas->MPC_UsuarioCreacion = $idusu;
            $mtto_preventivo_carburadas->MPC_UsuarioEditado = $idusu;
            $mtto_preventivo_carburadas->PER_Id = $request->get('USU_Id');
            $mtto_preventivo_carburadas->save();

            $MPCD_Descripcion = $request->get('MPCD_Descripcion');
            $MPC_Precio = $request->get('MPC_Precio');
            if ($MPCD_Descripcion) {
                $cont = 0;
                while ($cont < count($MPCD_Descripcion)) {
                    $mtto_det_reemplazo = new MpcDetalleReemplazo;
                    $mtto_det_reemplazo->MPC_Id = $mtto_preventivo_carburadas->MPC_Id;
                    $mtto_det_reemplazo->MPCD_Descripcion = $MPCD_Descripcion[$cont];
                    $mtto_det_reemplazo->MPCD_Item = $cont + 1;
                    $mtto_det_reemplazo->MPC_Precio = $MPC_Precio[$cont];
                    $mtto_det_reemplazo->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Mantenimiento Preventivo Carburadas Registrado Exitosamente.', 'id' => $mtto_preventivo_carburadas->MPC_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_preventivo_carburada as mpc')
            ->join('users as u', 'u.id', '=', 'mpc.PER_Id')
            ->select('mpc.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MPC_Id', '=', $id)
            ->first();
        $detalle = DB::table('mpc_detalle_reemplazo')
            ->where('MPC_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mpc_imagen')
            ->where('MPC_Id', '=', $id)
            ->get();

        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }
        $personal = User::select('id', 'name')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.preventivo.carburadas.edit', [
            "datos" => $datos,
            "admin" => $rolAdmin,
            "userAprobador" => $rolAdmin,
            "personal" => $personal,
            "detalle" => $detalle,
            "imagenes" => $imagenes,
            "id" => $id
        ]);
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }


    public function crop(Request $request, $id)
    {

        $request->validate([ 'file' => 'required|image|max:5120']);

        $mantenimientoId = $id;
        $tenantId = tenant('id') ?? 'central';
        $tipoNegocio = tenant('tipo_negocio') ?? 'central';

        $totalImagenes = MpcImagen::where('MPC_Id', $mantenimientoId)->count();
        if ($totalImagenes >= tenant('max_images')) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de imágenes.'
            ], 422);
        }
        $ultimoItem = MpcImagen::where('MPC_Id',$mantenimientoId)->max('MPCI_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/preventivo_carburadas/' . $mantenimientoId;

        $pathOriginal = $basePath . '/original/';
        $pathThumb = $basePath . '/thumb/';

        /* ORIGINAL */
        $imageOriginal = Image::read($file);
        $imageOriginal->scaleDown(width: 1200);

        Storage::disk('public')->put(
            $pathOriginal . $nombreArchivo,
            (string) $imageOriginal->toWebp(70)
        );

        $pesoFinal = Storage::disk('public')->size($pathOriginal.$nombreArchivo);
        $tamañoFormateado = $this->formatBytes($pesoFinal);
        
        /* THUMBNAIL */
        $imageThumb = Image::read($file);
        $imageThumb->scaleDown(width: 300);
        Storage::disk('public')->put(
            $pathThumb . $nombreArchivo,
            (string) $imageThumb->toWebp(60)
        );

        $rutaOriginal = Storage::url($pathOriginal . $nombreArchivo);
        $rutaThumb = Storage::url($pathThumb . $nombreArchivo);

        $mpcImagen = new MpcImagen();
        $mpcImagen->MPC_Id = $mantenimientoId;
        $mpcImagen->MPCI_Item = $item;
        $mpcImagen->MPCI_url = $rutaOriginal;
        $mpcImagen->MPCI_Thumb = $rutaThumb;
        $mpcImagen->MPCI_Nombre = $nombreArchivo;
        $mpcImagen->MPCI_Peso = $tamañoFormateado;
        $mpcImagen->save();

        $datos = MpcImagen::where('MPC_Id',$mantenimientoId)->get();

        return response()->json([
            'status' => 1,
            'msg' => [
                'data' => $datos,
                'mensaje' => 'Se cargó correctamente.'
            ]
        ]);
    }

    public function pdf($id)
	{
		$datos = DB::table('mantenimiento_preventivo_carburada as mpc')
				->join('users as u','u.id','=','mpc.PER_Id')
				->select('mpc.*',DB::raw('CONCAT(u.name) as personal'))
				->where('MPC_Id','=',$id)
				->first();

        $detalle_reemplazo = DB::table('mpc_detalle_reemplazo')
				->where('MPC_Id','=',$id)
				->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle =round($total_detalle + $dr->MPC_Precio, 2) ; 
        }

        $imagenes = DB::table('mpc_imagen')
                ->where('MPC_Id','=',$id)
                ->get();

        $url = URL::to('');
		$empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        
		$pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/preventivo/carburadas/pdf', [
			"mttoPreventivo"=>$datos,
			"detalle"=>$detalle_reemplazo,
			"imagenes"=>$imagenes,
			"url"=>$url,
            "total_detalle"=>$total_detalle,
            "empresa"=>$empresa
		])->setOptions(['defaultFont' => 'sans-serif',
        'chroot'  => public_path('dist/img'), 'isRemoteEnabled' => true]);

		return $pdf->stream('mantenimiento-preventivo-carburada-' . tenant('id') . '.pdf');
	}

    // ACTUALIZAR

    public function update(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;
            $roles = Auth::user()->getRoleNames();
            $rolAdmin = false;
            if ($roles->contains('Admin') || $roles->contains('Gerente')) {
                $rolAdmin = true;
            }

            $mtto_preventivo_carburadas = MantenimientoPreventivoCarburada::findOrFail($id);
            $mtto_preventivo_carburadas->MPC_Placa = $request->get('MPC_Placa');
            $mtto_preventivo_carburadas->MPC_Propietario = $request->get('MPC_Propietario');
            $mtto_preventivo_carburadas->MPC_celular = $request->get('MPC_celular');
            $mtto_preventivo_carburadas->MPC_Unidad = $request->get('MPC_Unidad');
            $mtto_preventivo_carburadas->MPC_KMEntrada = $request->get('MPC_KMEntrada');
            $mtto_preventivo_carburadas->MPC_DetalleIngreso = $request->get('MPC_DetalleIngreso');
            $mtto_preventivo_carburadas->MPC_DetalleObservacion = $request->get('MPC_DetalleObservacion');
            $mtto_preventivo_carburadas->MPC_Det1=$request->get('MPC_Det1')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det1Informacion=$request->get('MPC_Det1Informacion');
            $mtto_preventivo_carburadas->MPC_Det2=$request->get('MPC_Det2')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det3=$request->get('MPC_Det3')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det4=$request->get('MPC_Det4')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det5=$request->get('MPC_Det5')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det6=$request->get('MPC_Det6')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det7=$request->get('MPC_Det7')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det7Admision=$request->get('MPC_Det7Admision');
            $mtto_preventivo_carburadas->MPC_Det7Escape=$request->get('MPC_Det7Escape');
            $mtto_preventivo_carburadas->MPC_Det8=$request->get('MPC_Det8')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det8Medida=$request->get('MPC_Det8Medida');
            $mtto_preventivo_carburadas->MPC_Det9=$request->get('MPC_Det9');
            $mtto_preventivo_carburadas->MPC_Det10=$request->get('MPC_Det10');
            $mtto_preventivo_carburadas->MPC_Det11=$request->get('MPC_Det11')?"SI":"NO";
            $mtto_preventivo_carburadas->MPC_Det11Vida=$request->get('MPC_Det11Vida');
            $mtto_preventivo_carburadas->MPC_Det11Carga=$request->get('MPC_Det11Carga');
            $mtto_preventivo_carburadas->MPC_Det11Arranque=$request->get('MPC_Det11Arranque');
            $mtto_preventivo_carburadas->MPC_DetalleRealizado = $request->get('MPC_DetalleRealizado');
            $mtto_preventivo_carburadas->MPC_CorrecionObservacion = $request->get('MPC_CorrecionObservacion');
            $mtto_preventivo_carburadas->MPC_ProximoCambioAceite = $request->get('MPC_ProximoCambioAceite');
            $mtto_preventivo_carburadas->MPC_ProximoServicio = $request->get('MPC_ProximoServicio');
            $mtto_preventivo_carburadas->MPC_FechaEdicion = $mytime->toDateTimeString();
            $mtto_preventivo_carburadas->MPC_UsuarioEditado = $idusu;
            $mtto_preventivo_carburadas->PER_Id = $request->get('USU_Id');
            if ($rolAdmin) {
                $mtto_preventivo_carburadas->MPC_Estado = 'APROBADO';
            }
            if($request->notificar){
                $mtto_preventivo_carburadas->notificar = 1;
            }
            $mtto_preventivo_carburadas->update();


            DB::table('mpc_detalle_reemplazo')
                ->where('MPC_Id', $mtto_preventivo_carburadas->MPC_Id)
                ->delete();

            $MPCD_Descripcion = $request->get('MPCD_Descripcion');
            $MPC_Precio = $request->get('MPC_Precio');
            if ($MPCD_Descripcion) {
                $cont = 0;
                while ($cont < count($MPCD_Descripcion)) {
                    $correctivoresponsable = new MpcDetalleReemplazo;
                    $correctivoresponsable->MPC_Id = $mtto_preventivo_carburadas->MPC_Id;
                    $correctivoresponsable->MPCD_Descripcion = $MPCD_Descripcion[$cont];
                    $correctivoresponsable->MPCD_Item = $cont + 1;
                    $correctivoresponsable->MPC_Precio = $MPC_Precio[$cont];
                    $correctivoresponsable->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Mantenimiento Preventivo Carburadas Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request,$id)
	{
        try {
            DB::beginTransaction();
            $mtto_preventivo_carburadas=MantenimientoPreventivoCarburada::findOrFail($id);
            if($request->notificar == 1){
                $mtto_preventivo_carburadas->MPC_Estado="PENDIENTE";
                $mtto_preventivo_carburadas->notificar=0;
            }
            if($request->estado == "APROBADO"){
                $mtto_preventivo_carburadas->MPC_Estado="APROBADO";
                if($request->notificar == 2){
                    $mtto_preventivo_carburadas->notificar=0;
                }else{
                    $mtto_preventivo_carburadas->notificar=1;
                }
            }
            if($request->observacion){
                $mtto_preventivo_carburadas->observacion=$request->observacion ;
            }

            if($request->respuesta){
                $mtto_preventivo_carburadas->respuesta=$request->respuesta ;
            }
            $mtto_preventivo_carburadas->update();

            DB::commit();

        } catch (Exception $e)
        {
        DB::rollback();
        }

        if($request->notificar == 1){
            return response()->json(['success' => true, 'message' => 'Mantenimiento Se Desactivo Correctamente.']);
        }
        if($request->estado == "APROBADO"){
            return response()->json(['success' => true, 'message' => 'Mantenimiento Se Aprobó Correctamente.']);
        }
        if($request->observacion){
            return response()->json(['success' => true, 'message' => 'Observación Registrada Correctamente.']);
        }
        if($request->respuesta){
            return response()->json(['success' => true, 'message' => 'Respuesta Registrada Correctamente.']);
        }
	}

    public function destroyimagen($id, $item)
    {
        try {
            $imagenDelete = MpcImagen::where('MPC_Id', $id)
                ->where('MPCI_Item', $item)
                ->first();

            if (!$imagenDelete) {
                return response()->json([
                    'status' => 0,
                    'msg' => [
                        'mensaje' => 'Imagen no encontrada.'
                    ]
                ]);
            }

            /* ELIMINAR ORIGINAL */
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MPCI_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MPCI_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MpcImagen::where('MPC_Id', $id)
            ->where('MPCI_Item', $item)
            ->delete();

            /* RECARGAR IMAGENES */
            $datos = MpcImagen::where('MPC_Id', $id)->get();

            return response()->json(['success' => true,'message' => 'Eliminado Correctamente', 'data'=> $datos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_preventivo_carburadas = MantenimientoPreventivoCarburada::find($id);

            if (!$mtto_preventivo_carburadas) {
                return response()->json(['success' => false, 'message' => 'MantenimientoPreventivoCarburada no encontrado.'], 404);
            }

            $mtto_preventivo_carburadas->MPC_Estado="PENDIENTE";
            $mtto_preventivo_carburadas->notificar=0;
            $mtto_preventivo_carburadas->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoPreventivoCarburada activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MpcImagen::where('MPC_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MPCI_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MPCI_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MPCI_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MPCI_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mpc_detalle_reemplazo')
                ->where('MPC_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mpc_imagen')
                ->where('MPC_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_preventivo_carburada')
                ->where('MPC_Id', $id)
                ->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
