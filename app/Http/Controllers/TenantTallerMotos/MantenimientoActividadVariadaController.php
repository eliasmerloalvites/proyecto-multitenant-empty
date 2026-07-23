<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoActividadVariada;
use App\Models\TenantTallerMotos\MavDetalleReemplazo;
use App\Models\TenantTallerMotos\MavImagen;
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

class MantenimientoActividadVariadaController extends Controller
{
    // VALIDACIONES

    private function validateMantenimientoActividadVariada(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'BAH_Nombre' => ['required', 'string', 'max:60', 'unique:bahia,BAH_Nombre,' . $id . ',BAH_Id'],
            'ALM_Id' => ['required'],
            'USU_Id' => ['required'],
            'BAH_Estado' => ['nullable', 'in:ACT,DESACT']
        ]);
    }

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
                $data = DB::table('mantenimiento_actividad_variadas as mav')
                    ->join('users as p', 'p.id', '=', 'mav.PER_Id')
                    ->select('mav.MAV_Id', 'mav.MAV_Placa', 'mav.MAV_Propietario', 'mav.MAV_celular', 'mav.notificar', 'mav.MAV_Unidad', 'mav.MAV_KMEntrada', 'mav.MAV_FechaCreacion', 'mav.MAV_Estado', DB::raw('CONCAT(p.name) as personal'));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MAV_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MAV_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MAV_Estado', $request->estado);
                }

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->MAV_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MAV_Estado . '</button>';
                        } else if ($row->MAV_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MAV_Estado . '</button>';
                        } else if ($row->MAV_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MAV_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="/tenant/actividades/mantenimientoactividadvariada/' . $row->MAV_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoActividadVariadas"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoActividadVariadas"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/actividades/mantenimientoactividadvariada/' . $row->MAV_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MAV_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoActividadVariadas"><i class="fa fa-check"></i></a>';
                        } else if ($row->MAV_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoActividadVariadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MAV_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MAV_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MAV_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MAV_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_actividad_variadas as mav')
                    ->join('users as p', 'p.id', '=', 'mav.PER_Id')
                    ->select('mav.MAV_Id', 'mav.MAV_Placa', 'mav.MAV_Propietario', 'mav.MAV_celular', 'mav.notificar', 'mav.MAV_Unidad', 'mav.MAV_KMEntrada', 'mav.MAV_FechaCreacion', 'mav.MAV_Estado', DB::raw('CONCAT(p.name) as personal'))
                    ->where('mav.PER_Id', '=', $idpersonal)
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action1', function ($row) {
                        if ($row->MAV_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoActividadVariadas"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/mantenimiento/actividadvariadas/' . $row->MAV_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MAV_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.actividades.variadas.index');
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


        return view('tenant_' . tenant('tipo_negocio') . '.actividades.variadas.create', ["admin" => $rolAdmin, "personal" => $personal]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_act_variadas = new MantenimientoActividadVariada;
            $mtto_act_variadas->MAV_Placa = $request->get('MAV_Placa');
            $mtto_act_variadas->MAV_Propietario = $request->get('MAV_Propietario');
            $mtto_act_variadas->MAV_celular = $request->get('MAV_celular');
            $mtto_act_variadas->MAV_Unidad = $request->get('MAV_Unidad');
            $mtto_act_variadas->MAV_KMEntrada = $request->get('MAV_KMEntrada');
            $mtto_act_variadas->MAV_DetalleIngreso = $request->get('MAV_DetalleIngreso');
            $mtto_act_variadas->MAV_DetalleObservacion = $request->get('MAV_DetalleObservacion');
            $mtto_act_variadas->MAV_DetalleRealizado = $request->get('MAV_DetalleRealizado');
            $mtto_act_variadas->MAV_CorrecionObservacion = $request->get('MAV_CorrecionObservacion');
            $mtto_act_variadas->MAV_ProximoCambioAceite = $request->get('MAV_ProximoCambioAceite');
            $mtto_act_variadas->MAV_ProximoServicio = $request->get('MAV_ProximoServicio');
            $mtto_act_variadas->MAV_FechaCreacion = $mytime->toDateTimeString();
            $mtto_act_variadas->MAV_FechaEdicion = $mytime->toDateTimeString();
            $mtto_act_variadas->MAV_UsuarioCreacion = $idusu;
            $mtto_act_variadas->MAV_UsuarioEditado = $idusu;
            $mtto_act_variadas->PER_Id = $request->get('USU_Id');
            $mtto_act_variadas->save();

            $MAVD_Descripcion = $request->get('MAVD_Descripcion');
            $MAV_Precio = $request->get('MAV_Precio');
            if ($MAVD_Descripcion) {
                $cont = 0;
                while ($cont < count($MAVD_Descripcion)) {
                    $mtto_det_reemplazo = new MavDetalleReemplazo;
                    $mtto_det_reemplazo->MAV_Id = $mtto_act_variadas->MAV_Id;
                    $mtto_det_reemplazo->MAVD_Descripcion = $MAVD_Descripcion[$cont];
                    $mtto_det_reemplazo->MAVD_Item = $cont + 1;
                    $mtto_det_reemplazo->MAV_Precio = $MAV_Precio[$cont];
                    $mtto_det_reemplazo->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Actividades Variadas Registrado Exitosamente.', 'id' => $mtto_act_variadas->MAV_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_actividad_variadas as mav')
            ->join('users as u', 'u.id', '=', 'mav.PER_Id')
            ->select('mav.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MAV_Id', '=', $id)
            ->first();
        $detalle = DB::table('mav_detalle_reemplazo')
            ->where('MAV_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mav_imagen')
            ->where('MAV_Id', '=', $id)
            ->get();

        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }
        $personal = User::select('id', 'name')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.actividades.variadas.edit', [
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

        $totalImagenes = MavImagen::where('MAV_Id', $mantenimientoId)->count();
        if ($totalImagenes >= tenant('max_images')) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de imágenes.'
            ], 422);
        }
        $ultimoItem = MavImagen::where('MAV_Id',$mantenimientoId)->max('MAVI_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/actividad_variada/' . $mantenimientoId;

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

        $mavImagen = new MavImagen();
        $mavImagen->MAV_Id = $mantenimientoId;
        $mavImagen->MAVI_Item = $item;
        $mavImagen->MAVI_url = $rutaOriginal;
        $mavImagen->MAVI_Thumb = $rutaThumb;
        $mavImagen->MAVI_Nombre = $nombreArchivo;
        $mavImagen->MAVI_Peso = $tamañoFormateado;
        $mavImagen->save();

        $datos = MavImagen::where('MAV_Id',$mantenimientoId)->get();

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
		$datos = DB::table('mantenimiento_actividad_variadas as mav')
				->join('users as u','u.id','=','mav.PER_Id')
				->select('mav.*',DB::raw('CONCAT(u.name) as personal'))
				->where('MAV_Id','=',$id)
				->first();

        $detalle_reemplazo = DB::table('mav_detalle_reemplazo')
				->where('MAV_Id','=',$id)
				->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle =round($total_detalle + $dr->MAV_Precio, 2) ; 
        }

        $imagenes = DB::table('mav_imagen')
                ->where('MAV_Id','=',$id)
                ->get();

        $url = URL::to('');
		$empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        
		$pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/actividades/variadas/pdf', [
			"mttoPreventivo"=>$datos,
			"detalle"=>$detalle_reemplazo,
			"imagenes"=>$imagenes,
			"url"=>$url,
            "total_detalle"=>$total_detalle,
            "empresa"=>$empresa
		])->setOptions(['defaultFont' => 'sans-serif',
        'chroot'  => public_path('dist/img'), 'isRemoteEnabled' => true]);

		return $pdf->stream('mantenimiento-actividad-variada-' . tenant('id') . '.pdf');
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

            $mtto_act_variadas = MantenimientoActividadVariada::findOrFail($id);
            $mtto_act_variadas->MAV_Placa = $request->get('MAV_Placa');
            $mtto_act_variadas->MAV_Propietario = $request->get('MAV_Propietario');
            $mtto_act_variadas->MAV_celular = $request->get('MAV_celular');
            $mtto_act_variadas->MAV_Unidad = $request->get('MAV_Unidad');
            $mtto_act_variadas->MAV_KMEntrada = $request->get('MAV_KMEntrada');
            $mtto_act_variadas->MAV_DetalleIngreso = $request->get('MAV_DetalleIngreso');
            $mtto_act_variadas->MAV_DetalleObservacion = $request->get('MAV_DetalleObservacion');
            $mtto_act_variadas->MAV_DetalleRealizado = $request->get('MAV_DetalleRealizado');
            $mtto_act_variadas->MAV_CorrecionObservacion = $request->get('MAV_CorrecionObservacion');
            $mtto_act_variadas->MAV_ProximoCambioAceite = $request->get('MAV_ProximoCambioAceite');
            $mtto_act_variadas->MAV_ProximoServicio = $request->get('MAV_ProximoServicio');
            $mtto_act_variadas->MAV_FechaEdicion = $mytime->toDateTimeString();
            $mtto_act_variadas->MAV_UsuarioEditado = $idusu;
            $mtto_act_variadas->PER_Id = $request->get('USU_Id');
            if ($rolAdmin) {
                $mtto_act_variadas->MAV_Estado = 'APROBADO';
            }
            if($request->notificar){
                $mtto_act_variadas->notificar = 1;
            }
            $mtto_act_variadas->update();


            DB::table('mav_detalle_reemplazo')
                ->where('MAV_Id', $mtto_act_variadas->MAV_Id)
                ->delete();

            $MAVD_Descripcion = $request->get('MAVD_Descripcion');
            $MAV_Precio = $request->get('MAV_Precio');
            if ($MAVD_Descripcion) {
                $cont = 0;
                while ($cont < count($MAVD_Descripcion)) {
                    $correctivoresponsable = new MAVDetalleReemplazo;
                    $correctivoresponsable->MAV_Id = $mtto_act_variadas->MAV_Id;
                    $correctivoresponsable->MAVD_Descripcion = $MAVD_Descripcion[$cont];
                    $correctivoresponsable->MAVD_Item = $cont + 1;
                    $correctivoresponsable->MAV_Precio = $MAV_Precio[$cont];
                    $correctivoresponsable->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Actividades Variadas Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request,$id)
	{
        try {
            DB::beginTransaction();
            $mtto_act_variadas=MantenimientoActividadVariada::findOrFail($id);
            if($request->notificar == 1){
                $mtto_act_variadas->MAV_Estado="PENDIENTE";
                $mtto_act_variadas->notificar=0;
            }
            if($request->estado == "APROBADO"){
                $mtto_act_variadas->MAV_Estado="APROBADO";
                if($request->notificar == 2){
                    $mtto_act_variadas->notificar=0;
                }else{
                    $mtto_act_variadas->notificar=1;
                }
            }
            if($request->observacion){
                $mtto_act_variadas->observacion=$request->observacion ;
            }

            if($request->respuesta){
                $mtto_act_variadas->respuesta=$request->respuesta ;
            }
            $mtto_act_variadas->update();

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
            $imagenDelete = MavImagen::where('MAV_Id', $id)
                ->where('MAVI_Item', $item)
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
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MAVI_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MAVI_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MavImagen::where('MAV_Id', $id)
            ->where('MAVI_Item', $item)
            ->delete();

            /* RECARGAR IMAGENES */
            $datos = MavImagen::where('MAV_Id', $id)->get();

            return response()->json(['success' => true,'message' => 'Eliminado Correctamente', 'data'=> $datos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_act_variadas = MantenimientoActividadVariada::find($id);

            if (!$mtto_act_variadas) {
                return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
            }

            $mtto_act_variadas->MAV_Estado="PENDIENTE";
            $mtto_act_variadas->notificar=0;
            $mtto_act_variadas->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MavImagen::where('MAV_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MAVI_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MAVI_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MAVI_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MAVI_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mav_detalle_reemplazo')
                ->where('MAV_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mav_imagen')
                ->where('MAV_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_actividad_variadas')
                ->where('MAV_Id', $id)
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
