<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="Aprobar" class="btn btn-success btn-sm aprobarMantenimientoActividadVariadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MAV_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MAV_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="Activar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MAV_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MAV_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MAV_Id . '" title="Aprobar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
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
            ->join('personal as p', 'p.PER_Id', '=', 'mav.PER_Id')
            ->select('mav.*', DB::raw('CONCAT(p.PER_Nombre," ",p.PER_Apellido) as personal'))
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

        $request->validate([
            'file' => 'required|image|max:5120'
        ]);

        /*
    |--------------------------------------------------------------------------
    | IDS
    |--------------------------------------------------------------------------
    */

        $mantenimientoId = $id;

        $tenantId = tenant('id') ?? 'central';

        $tipoNegocio = tenant('tipo_negocio') ?? 'central';

        /*
    |--------------------------------------------------------------------------
    | ITEM
    |--------------------------------------------------------------------------
    */

        $ultimoItem = MavImagen::where(
            'MAV_Id',
            $mantenimientoId
        )->max('MAVI_Item');

        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        /*
    |--------------------------------------------------------------------------
    | ARCHIVO
    |--------------------------------------------------------------------------
    */

        $file = $request->file('file');

        /*
    |--------------------------------------------------------------------------
    | NOMBRE UNICO
    |--------------------------------------------------------------------------
    */

        $nombreArchivo = Str::uuid() . '.webp';

        /*
    |--------------------------------------------------------------------------
    | DIRECTORIOS
    |--------------------------------------------------------------------------
    */

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/actividad_variada/' . $mantenimientoId;

        $pathOriginal = $basePath . '/original/';
        $pathThumb = $basePath . '/thumb/';

        /*
    |--------------------------------------------------------------------------
    | IMAGEN ORIGINAL
    |--------------------------------------------------------------------------
    */

        $imageOriginal = Image::read($file);
        $imageOriginal->scaleDown(
            width: 1200
        );

        /*
    |--------------------------------------------------------------------------
    | GUARDAR ORIGINAL
    |--------------------------------------------------------------------------
    */

        Storage::disk('public')->put(

            $pathOriginal . $nombreArchivo,

            (string) $imageOriginal->toWebp(70)

        );
        $pesoFinal = Storage::disk('public')->size(
            $pathOriginal.$nombreArchivo
        );
        $tamañoFormateado = $this->formatBytes($pesoFinal);

        /*
    |--------------------------------------------------------------------------
    | THUMBNAIL
    |--------------------------------------------------------------------------
    */

        $imageThumb = Image::read($file);
        $imageThumb->scaleDown(
            width: 300
        );

        /*
    |--------------------------------------------------------------------------
    | GUARDAR THUMB
    |--------------------------------------------------------------------------
    */

        Storage::disk('public')->put(
            $pathThumb . $nombreArchivo,
            (string) $imageThumb->toWebp(60)

        );

        /*
    |--------------------------------------------------------------------------
    | URLS
    |--------------------------------------------------------------------------
    */

        $rutaOriginal = Storage::url(
            $pathOriginal . $nombreArchivo
        );

        $rutaThumb = Storage::url(
            $pathThumb . $nombreArchivo
        );

        /*
    |--------------------------------------------------------------------------
    | GUARDAR BD
    |--------------------------------------------------------------------------
    */

        $mavImagen = new MavImagen();

        $mavImagen->MAV_Id = $mantenimientoId;
        $mavImagen->MAVI_Item = $item;

        $mavImagen->MAVI_url = $rutaOriginal;

        // OPCIONAL
        $mavImagen->MAVI_Thumb = $rutaThumb;

        $mavImagen->MAVI_Nombre = $nombreArchivo;
        $mavImagen->MAVI_Peso = $tamañoFormateado;

        $mavImagen->save();

        /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */

        $datos = MavImagen::where(
            'MAV_Id',
            $mantenimientoId
        )->get();

        return response()->json([

            'status' => 1,

            'msg' => [

                'data' => $datos,

                'mensaje' => 'Se cargó correctamente.'

            ]

        ]);
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

        return response()->json(['success' => 'Actividades Variadas Editado Exitosamente.']);
    }

    // ACTIVAR

    public function activar(string $id)
    {
        try {

            $bahia = MantenimientoActividadVariada::find($id);

            if (!$bahia) {
                return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
            }

            $bahia->BAH_Estado = 'ACT';
            $bahia->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El bahia falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy(string $id)
    {
        try {

            $bahia = MantenimientoActividadVariada::find($id);

            if (!$bahia) {
                return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
            }

            $bahia->BAH_Estado = 'DESACT';
            $bahia->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada eliminado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El bahia falló al eliminarse.'], 500);
        }
    }
}
