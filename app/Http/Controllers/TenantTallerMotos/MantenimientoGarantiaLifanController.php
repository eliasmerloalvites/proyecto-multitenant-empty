<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoGarantiaLifan;
use App\Models\TenantTallerMotos\MantenimientoPlan;
use App\Models\TenantTallerMotos\MglDetalleReemplazo;
use App\Services\Mantenimiento\RepuestosBahiaSync;
use App\Services\TenantTallerMotos\GestionProcesoService;
use App\Models\TenantTallerMotos\MglImagen;
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

class MantenimientoGarantiaLifanController extends Controller
{
    // LISTADO

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $roles = Auth::user()->getRoleNames();
            $idpersonal = Auth::user()->id;

            $rolAdmin = false;
            if ($roles->contains('Admin') || $roles->contains('Gerente') || $roles->contains('Recepcion')) {
                $rolAdmin = true;
            }

            if ($rolAdmin) {
                $data = DB::table('mantenimiento_garantia_lifan as mgl')
                    ->leftJoin('users as p', 'p.id', '=', 'mgl.PER_Id')
                    ->select('mgl.MGL_Id', 'mgl.MGL_Placa', 'mgl.MGL_Propietario', 'mgl.MGL_celular', 'mgl.notificar', 'mgl.MGL_Unidad', 'mgl.MGL_KMEntrada', 'mgl.MGL_FechaCreacion', 'mgl.MGL_FechaTermino', 'mgl.MGL_Estado', DB::raw('CONCAT(p.name) as personal'), DB::raw("EXISTS(SELECT 1 FROM recepcion_respuesta WHERE MTO_Tabla = 'mantenimiento_garantia_lifan' AND MTO_Id = mgl.MGL_Id) as tiene_recepcion"));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MGL_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MGL_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MGL_Estado', $request->estado);
                }

                return Datatables::of($data)
                    ->addIndexColumn()
                    // 'personal' es un alias calculado (nombre del mecanico), no una columna de
                    // la tabla de mantenimiento: sin esto la busqueda global intenta filtrar por
                    // <tabla>.personal y la consulta truena con 'Unknown column'.
                    ->filterColumn('personal', function ($query, $keyword) {
                        $query->whereRaw('LOWER(p.name) LIKE ?', ['%' . mb_strtolower($keyword) . '%']);
                    })
                    ->addColumn('estado', function ($row) {
                        if ($row->MGL_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MGL_Estado . '</button>';
                        } else if ($row->MGL_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MGL_Estado . '</button>';
                        } else if ($row->MGL_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MGL_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGarantiaLifan"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoGarantiaLifan"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MGL_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoGarantiaLifan"><i class="fa fa-check"></i></a>';
                        } else if ($row->MGL_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoGarantiaLifan"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('action5', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/orden-servicio" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Orden de Servicio" class="btn btn-primary btn-sm "><i class="fas fa-clipboard-list"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action6', function ($row) {
                        if (!$row->tiene_recepcion) {
                            return '';
                        }
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/estado-recepcion-pdf" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Estado de Recepción" class="btn btn-secondary btn-sm "><i class="fas fa-motorcycle"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MGL_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGL_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MGL_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGL_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'action5', 'action6', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_garantia_lifan as mgl')
                    ->leftJoin('users as p', 'p.id', '=', 'mgl.PER_Id')
                    ->select('mgl.MGL_Id', 'mgl.MGL_Placa', 'mgl.MGL_Propietario', 'mgl.MGL_celular', 'mgl.notificar', 'mgl.MGL_Unidad', 'mgl.MGL_KMEntrada', 'mgl.MGL_FechaCreacion', 'mgl.MGL_FechaTermino', 'mgl.MGL_Estado', DB::raw('CONCAT(p.name) as personal'), DB::raw("EXISTS(SELECT 1 FROM recepcion_respuesta WHERE MTO_Tabla = 'mantenimiento_garantia_lifan' AND MTO_Id = mgl.MGL_Id) as tiene_recepcion"))
                    ->where('mgl.PER_Id', '=', $idpersonal);

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MGL_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MGL_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MGL_Estado', $request->estado);
                }

                return Datatables::of($data)
                    ->addIndexColumn()
                    // 'personal' es un alias calculado (nombre del mecanico), no una columna de
                    // la tabla de mantenimiento: sin esto la busqueda global intenta filtrar por
                    // <tabla>.personal y la consulta truena con 'Unknown column'.
                    ->filterColumn('personal', function ($query, $keyword) {
                        $query->whereRaw('LOWER(p.name) LIKE ?', ['%' . mb_strtolower($keyword) . '%']);
                    })
                    ->addColumn('estado', function ($row) {
                        if ($row->MGL_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MGL_Estado . '</button>';
                        } else if ($row->MGL_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MGL_Estado . '</button>';
                        } else if ($row->MGL_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MGL_Estado . '</button>';
                        } else {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-secondary">' . $row->MGL_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        if ($row->MGL_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGarantiaLifan"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        return '';
                    })
                    ->addColumn('action5', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/orden-servicio" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Orden de Servicio" class="btn btn-primary btn-sm "><i class="fas fa-clipboard-list"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action6', function ($row) {
                        if (!$row->tiene_recepcion) {
                            return '';
                        }
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantialifan/' . $row->MGL_Id . '/estado-recepcion-pdf" data-toggle="tooltip"  data-id="' . $row->MGL_Id . '" data-original-title="Estado de Recepción" class="btn btn-secondary btn-sm "><i class="fas fa-motorcycle"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MGL_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'action5', 'action6', 'estado', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.garantia.lifan.index');
    }

    // REGISTRAR
    public function create()
    {
        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente') || $roles->contains('Recepcion')) {
            $rolAdmin = true;
        }
        $personal = (tenant('plan') ?? 'start') === 'start'
            ? User::select('id', 'name')->get()
            : User::role('Mecanico')->select('id', 'name')->get();


        $planes = MantenimientoPlan::where('PLAN_Tipo', 'MPC')->where('PLAN_Activo', true)->orderBy('PLAN_Nombre')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.garantia.lifan.create', ["admin" => $rolAdmin, "personal" => $personal, "planes" => $planes]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_garantia_lifan = new MantenimientoGarantiaLifan;
            $mtto_garantia_lifan->MGL_Placa = $request->get('MGL_Placa');
            $mtto_garantia_lifan->MGL_Propietario = $request->get('MGL_Propietario');
            $mtto_garantia_lifan->MGL_celular = $request->get('MGL_celular');
            $mtto_garantia_lifan->MGL_Unidad = $request->get('MGL_Unidad');
            $mtto_garantia_lifan->MGL_KMEntrada = $request->get('MGL_KMEntrada');
            $mtto_garantia_lifan->MGL_DetalleIngreso = $request->get('MGL_DetalleIngreso');
            $mtto_garantia_lifan->MGL_DetalleObservacion = $request->get('MGL_DetalleObservacion');
            $mtto_garantia_lifan->MGL_Det1 = $request->get('MGL_Det1') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det1Informacion = $request->get('MGL_Det1Informacion');
            $mtto_garantia_lifan->MGL_Det2 = $request->get('MGL_Det2') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det3 = $request->get('MGL_Det3') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det4 = $request->get('MGL_Det4') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det5 = $request->get('MGL_Det5') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det6 = $request->get('MGL_Det6') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det7 = $request->get('MGL_Det7') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det7Admision = $request->get('MGL_Det7Admision');
            $mtto_garantia_lifan->MGL_Det7Escape = $request->get('MGL_Det7Escape');
            $mtto_garantia_lifan->MGL_Det8 = $request->get('MGL_Det8') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det8Medida = $request->get('MGL_Det8Medida');
            $mtto_garantia_lifan->MGL_Det9 = $request->get('MGL_Det9');
            $mtto_garantia_lifan->MGL_Det10 = $request->get('MGL_Det10');
            $mtto_garantia_lifan->MGL_Det11 = $request->get('MGL_Det11') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det11Vida = $request->get('MGL_Det11Vida');
            $mtto_garantia_lifan->MGL_Det11Carga = $request->get('MGL_Det11Carga');
            $mtto_garantia_lifan->MGL_Det11Arranque = $request->get('MGL_Det11Arranque');
            $mtto_garantia_lifan->MGL_DetalleRealizado = $request->get('MGL_DetalleRealizado');
            $mtto_garantia_lifan->MGL_CorrecionObservacion = $request->get('MGL_CorrecionObservacion');
            $mtto_garantia_lifan->MGL_ProximoCambioAceite = $request->get('MGL_ProximoCambioAceite');
            $mtto_garantia_lifan->MGL_ProximoServicio = $request->get('MGL_ProximoServicio');
            $mtto_garantia_lifan->MGL_FechaCreacion = $mytime->toDateTimeString();
            $mtto_garantia_lifan->MGL_FechaEdicion = $mytime->toDateTimeString();
            $mtto_garantia_lifan->MGL_UsuarioCreacion = $idusu;
            $mtto_garantia_lifan->MGL_UsuarioEditado = $idusu;
            $mtto_garantia_lifan->PER_Id = $request->get('USU_Id');
            $mtto_garantia_lifan->PLAN_Id = $request->get('PLAN_Id') ?: null;
            $mtto_garantia_lifan->save();

            RepuestosBahiaSync::reemplazarManuales(
                'MGL',
                $mtto_garantia_lifan->MGL_Id,
                $request->get('MGLD_Descripcion', []),
                $request->get('MGL_Precio', [])
            );
            RepuestosBahiaSync::sincronizar('MGL', $mtto_garantia_lifan->MGL_Id, $mtto_garantia_lifan->RES_Id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Mantenimiento Garantía Lifan Registrado Exitosamente.', 'id' => $mtto_garantia_lifan->MGL_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_garantia_lifan as mgl')
            ->leftJoin('users as u', 'u.id', '=', 'mgl.PER_Id')
            ->select('mgl.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGL_Id', '=', $id)
            ->first();

        RepuestosBahiaSync::sincronizar('MGL', (int) $id, $datos->RES_Id ?? null);

        $detalle = DB::table('mgl_detalle_reemplazo')
            ->where('MGL_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mgl_imagen')
            ->where('MGL_Id', '=', $id)
            ->get();

        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente') || $roles->contains('Recepcion')) {
            $rolAdmin = true;
        }
        $personal = (tenant('plan') ?? 'start') === 'start'
            ? User::select('id', 'name')->get()
            : User::role('Mecanico')->select('id', 'name')->get();

        $planes = MantenimientoPlan::where('PLAN_Tipo', 'MPC')->where('PLAN_Activo', true)->orderBy('PLAN_Nombre')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.garantia.lifan.edit', [
            "datos" => $datos,
            "admin" => $rolAdmin,
            "userAprobador" => $rolAdmin,
            "personal" => $personal,
            "detalle" => $detalle,
            "imagenes" => $imagenes,
            "id" => $id,
            "planes" => $planes,
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

        $request->validate(['file' => 'required|image|max:5120']);

        $mantenimientoId = $id;
        $tenantId = tenant('id') ?? 'central';
        $tipoNegocio = tenant('tipo_negocio') ?? 'central';

        $totalImagenes = MglImagen::where('MGL_Id', $mantenimientoId)->count();
        if ($totalImagenes >= tenant('max_images')) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de imágenes.'
            ], 422);
        }

        $limiteStorage = (float) tenant('storage_limit_mb');
        if ($limiteStorage > 0 && tenant_storage_usado_mb() + ($request->file('file')->getSize() / 1024 / 1024) > $limiteStorage) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de almacenamiento (' . $limiteStorage . ' MB).'
            ], 422);
        }
        $ultimoItem = MglImagen::where('MGL_Id', $mantenimientoId)->max('MGLI_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/garantia_lifan/' . $mantenimientoId;

        $pathOriginal = $basePath . '/original/';
        $pathThumb = $basePath . '/thumb/';

        /* ORIGINAL */
        $imageOriginal = Image::read($file);
        $imageOriginal->scaleDown(width: 1200);

        Storage::disk('public')->put(
            $pathOriginal . $nombreArchivo,
            (string) $imageOriginal->toWebp(70)
        );

        $pesoFinal = Storage::disk('public')->size($pathOriginal . $nombreArchivo);
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

        $mglImagen = new MglImagen();
        $mglImagen->MGL_Id = $mantenimientoId;
        $mglImagen->MGLI_Item = $item;
        $mglImagen->MGLI_url = $rutaOriginal;
        $mglImagen->MGLI_Thumb = $rutaThumb;
        $mglImagen->MGLI_Nombre = $nombreArchivo;
        $mglImagen->MGLI_Peso = $tamañoFormateado;
        $mglImagen->save();

        $datos = MglImagen::where('MGL_Id', $mantenimientoId)->get();

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
        $datos = DB::table('mantenimiento_garantia_lifan as mgl')
            ->leftJoin('users as u', 'u.id', '=', 'mgl.PER_Id')
            ->select('mgl.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGL_Id', '=', $id)
            ->first();

        $detalle_reemplazo = DB::table('mgl_detalle_reemplazo')
            ->where('MGL_Id', '=', $id)
            ->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle = round($total_detalle + $dr->MGL_Precio, 2);
        }

        $imagenes = DB::table('mgl_imagen')
            ->where('MGL_Id', '=', $id)
            ->get();

        $url = URL::to('');
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/garantia/lifan/pdf', [
            "mttoPreventivo" => $datos,
            "detalle" => $detalle_reemplazo,
            "imagenes" => $imagenes,
            "url" => $url,
            "total_detalle" => $total_detalle,
            "empresa" => $empresa
        ])->setOptions([
            'defaultFont' => 'sans-serif',
            'chroot'  => public_path('dist/img'),
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream('mantenimiento-garantia-lifan-' . tenant('id') . '.pdf');
    }

    /**
     * "Orden de Servicio": mismo contenido que pdf(), mas el Inventario
     * Visual/Inspeccion de la Unidad (Estado de Recepcion) y los datos de
     * facturacion de la venta asociada si la reserva llego a cobrarse. Es
     * un documento nuevo aparte; pdf()/descargarpdf() no se tocan.
     */
    public function ordenServicio($id)
    {
        $datos = DB::table('mantenimiento_garantia_lifan as mgl')
            ->leftJoin('users as u', 'u.id', '=', 'mgl.PER_Id')
            ->select('mgl.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGL_Id', '=', $id)
            ->first();

        $detalle_reemplazo = DB::table('mgl_detalle_reemplazo')
            ->where('MGL_Id', '=', $id)
            ->get();

        $total_detalle = round($detalle_reemplazo->sum('MGL_Precio'), 2);

        $imagenes = DB::table('mgl_imagen')
            ->where('MGL_Id', '=', $id)
            ->get();

        $url = URL::to('');
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $estadoRecepcion = null;
        try {
            $estadoRecepcion = GestionProcesoService::estadoRecepcion('mantenimiento_garantia_lifan', (int) $id);
        } catch (\Throwable $e) {
            // Sin ficha de recepcion guardada: el partial simplemente no imprime esas secciones.
        }

        $pdf = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/garantia/lifan/orden-servicio', [
            "mttoPreventivo" => $datos,
            "detalle" => $detalle_reemplazo,
            "imagenes" => $imagenes,
            "url" => $url,
            "total_detalle" => $total_detalle,
            "empresa" => $empresa,
            "etiquetaTipo" => 'Garantía Lifan',
            "estadoRecepcion" => $estadoRecepcion,
            "datosVenta" => GestionProcesoService::datosVentaAsociada($datos->RES_Id ?? null),
        ])->setOptions([
            'defaultFont' => 'sans-serif',
            'chroot' => public_path('dist/img'),
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream('orden-servicio-garantia-lifan-' . tenant('id') . '.pdf');
    }

    /**
     * PDF con SOLO el estado de recepcion (Inventario Visual +
     * Inspeccion de la Unidad) — para imprimir/compartir nada mas lo de
     * "como llego la moto". Usa una vista compartida por los 5 tipos
     * (mantenimientos/estado-recepcion-pdf) porque esta parte no depende
     * de columnas Det especificas de cada tipo.
     */
    public function estadoRecepcionPdf($id)
    {
        $datos = DB::table('mantenimiento_garantia_lifan as mgl')
            ->leftJoin('users as u', 'u.id', '=', 'mgl.PER_Id')
            ->select('mgl.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGL_Id', '=', $id)
            ->first();

        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $estadoRecepcion = null;
        try {
            $estadoRecepcion = GestionProcesoService::estadoRecepcion('mantenimiento_garantia_lifan', (int) $id);
        } catch (\Throwable $e) {
            // Sin ficha de recepcion guardada: la vista muestra el aviso de "sin datos".
        }

        $pdf = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/estado-recepcion-pdf', [
            "empresa" => $empresa,
            "etiquetaTipo" => 'Garantía Lifan',
            "numeroOrden" => 'MGL-' . str_pad($id, 6, '0', STR_PAD_LEFT),
            "fecha" => $datos ? date('d/m/Y', strtotime($datos->MGL_FechaCreacion)) : '',
            "hora" => $datos ? date('H:i', strtotime($datos->MGL_FechaCreacion)) : '',
            "personal" => $datos->personal ?? '',
            "propietario" => $datos->MGL_Propietario ?? '',
            "placa" => $datos->MGL_Placa ?? '',
            "unidad" => $datos->MGL_Unidad ?? '',
            "km" => $datos->MGL_KMEntrada ?? '',
            "estadoRecepcion" => $estadoRecepcion,
        ])->setOptions([
            'defaultFont' => 'sans-serif',
            'chroot' => public_path('dist/img'),
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream('estado-recepcion-garantia-lifan-' . tenant('id') . '.pdf');
    }

    public function descargarpdf($id)
    {
        $datos = DB::table('mantenimiento_garantia_lifan as mgl')
            ->leftJoin('users as u', 'u.id', '=', 'mgl.PER_Id')
            ->select('mgl.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGL_Id', '=', $id)
            ->first();

        $detalle_reemplazo = DB::table('mgl_detalle_reemplazo')
            ->where('MGL_Id', '=', $id)
            ->get();

        // Optimización: Suma directa mediante la colección
        $total_detalle = round($detalle_reemplazo->sum('MGL_Precio'), 2);

        $imagenes = DB::table('mgl_imagen')
            ->where('MGL_Id', '=', $id)
            ->get();

        $url = URL::to('');
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $pdf = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/garantia/lifan/pdf', [
            "mttoPreventivo" => $datos,
            "detalle"        => $detalle_reemplazo,
            "imagenes"       => $imagenes,
            "url"            => $url,
            "total_detalle"  => $total_detalle,
            "empresa"        => $empresa
        ])->setOptions([
            'defaultFont'     => 'sans-serif',
            'chroot'          => public_path('dist/img'),
            'isRemoteEnabled' => true
        ]);

        // Nombre único del archivo descargado
        $nombreArchivo = 'mantenimiento-garantia-lifan-' . $id . '-' . tenant('id') . '.pdf';

        // Se reemplaza ->stream() por ->download()
        return $pdf->download($nombreArchivo);
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
            if ($roles->contains('Admin') || $roles->contains('Gerente') || $roles->contains('Recepcion')) {
                $rolAdmin = true;
            }

            $mtto_garantia_lifan = MantenimientoGarantiaLifan::findOrFail($id);
            $mtto_garantia_lifan->MGL_Placa = $request->get('MGL_Placa');
            $mtto_garantia_lifan->MGL_Propietario = $request->get('MGL_Propietario');
            $mtto_garantia_lifan->MGL_celular = $request->get('MGL_celular');
            $mtto_garantia_lifan->MGL_Unidad = $request->get('MGL_Unidad');
            // KM es NOT NULL en BD y puede haber quedado guardado como '' desde
            // el check-in; si se edita sin tocar el campo, ConvertEmptyStringsToNull
            // lo vuelve null y la UPDATE truena contra esa constraint. Se
            // preserva el valor ya guardado en vez de pisarlo con vacío.
            if ($request->filled('MGL_KMEntrada')) {
                $mtto_garantia_lifan->MGL_KMEntrada = $request->get('MGL_KMEntrada');
            }
            $mtto_garantia_lifan->MGL_DetalleIngreso = $request->get('MGL_DetalleIngreso');
            $mtto_garantia_lifan->MGL_DetalleObservacion = $request->get('MGL_DetalleObservacion');
            $mtto_garantia_lifan->MGL_Det1 = $request->get('MGL_Det1') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det1Informacion = $request->get('MGL_Det1Informacion');
            $mtto_garantia_lifan->MGL_Det2 = $request->get('MGL_Det2') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det3 = $request->get('MGL_Det3') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det4 = $request->get('MGL_Det4') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det5 = $request->get('MGL_Det5') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det6 = $request->get('MGL_Det6') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det7 = $request->get('MGL_Det7') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det7Admision = $request->get('MGL_Det7Admision');
            $mtto_garantia_lifan->MGL_Det7Escape = $request->get('MGL_Det7Escape');
            $mtto_garantia_lifan->MGL_Det8 = $request->get('MGL_Det8') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det8Medida = $request->get('MGL_Det8Medida');
            $mtto_garantia_lifan->MGL_Det9 = $request->get('MGL_Det9');
            $mtto_garantia_lifan->MGL_Det10 = $request->get('MGL_Det10');
            $mtto_garantia_lifan->MGL_Det11 = $request->get('MGL_Det11') ? "SI" : "NO";
            $mtto_garantia_lifan->MGL_Det11Vida = $request->get('MGL_Det11Vida');
            $mtto_garantia_lifan->MGL_Det11Carga = $request->get('MGL_Det11Carga');
            $mtto_garantia_lifan->MGL_Det11Arranque = $request->get('MGL_Det11Arranque');
            $mtto_garantia_lifan->MGL_DetalleRealizado = $request->get('MGL_DetalleRealizado');
            $mtto_garantia_lifan->MGL_CorrecionObservacion = $request->get('MGL_CorrecionObservacion');
            $mtto_garantia_lifan->MGL_ProximoCambioAceite = $request->get('MGL_ProximoCambioAceite');
            $mtto_garantia_lifan->MGL_ProximoServicio = $request->get('MGL_ProximoServicio');
            $mtto_garantia_lifan->MGL_Recomendacion = $request->get('MGL_Recomendacion');
            $mtto_garantia_lifan->MGL_RecomendacionPrioridad = $request->get('MGL_RecomendacionPrioridad') ?: null;
            $mtto_garantia_lifan->MGL_VerifArranque = $request->boolean('MGL_VerifArranque');
            $mtto_garantia_lifan->MGL_VerifLuces = $request->boolean('MGL_VerifLuces');
            $mtto_garantia_lifan->MGL_VerifDireccionales = $request->boolean('MGL_VerifDireccionales');
            $mtto_garantia_lifan->MGL_VerifNivelAceite = $request->boolean('MGL_VerifNivelAceite');
            $mtto_garantia_lifan->MGL_VerifPruebaRuta = $request->boolean('MGL_VerifPruebaRuta');
            $mtto_garantia_lifan->MGL_VerifLavado = $request->boolean('MGL_VerifLavado');
            $mtto_garantia_lifan->MGL_VerifOtros = $request->get('MGL_VerifOtros');
            $mtto_garantia_lifan->MGL_VerifConforme = $request->boolean('MGL_VerifConforme');
            $mtto_garantia_lifan->MGL_FechaEdicion = $mytime->toDateTimeString();
            $mtto_garantia_lifan->MGL_FechaInicio = $request->get('MGL_FechaInicio') ? Carbon::parse($request->get('MGL_FechaInicio'))->toDateTimeString() : null;
            $mtto_garantia_lifan->MGL_FechaTermino = $request->get('MGL_FechaTermino') ? Carbon::parse($request->get('MGL_FechaTermino'))->toDateTimeString() : null;
            $mtto_garantia_lifan->MGL_UsuarioEditado = $idusu;
            $mtto_garantia_lifan->PER_Id = $request->get('USU_Id');
            $mtto_garantia_lifan->PLAN_Id = $request->get('PLAN_Id') ?: null;
            if ($rolAdmin) {
                $mtto_garantia_lifan->MGL_Estado = 'APROBADO';
            }
            if ($request->notificar) {
                $mtto_garantia_lifan->notificar = 1;
            }
            $mtto_garantia_lifan->update();

            RepuestosBahiaSync::reemplazarManuales(
                'MGL',
                $mtto_garantia_lifan->MGL_Id,
                $request->get('MGLD_Descripcion', []),
                $request->get('MGL_Precio', [])
            );
            RepuestosBahiaSync::sincronizar('MGL', $mtto_garantia_lifan->MGL_Id, $mtto_garantia_lifan->RES_Id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Mantenimiento Garantía Lifan Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $mtto_garantia_lifan = MantenimientoGarantiaLifan::findOrFail($id);
            if ($request->notificar == 1) {
                $mtto_garantia_lifan->MGL_Estado = "PENDIENTE";
                $mtto_garantia_lifan->notificar = 0;
            }
            if ($request->estado == "APROBADO") {
                $mtto_garantia_lifan->MGL_Estado = "APROBADO";
                if ($request->notificar == 2) {
                    $mtto_garantia_lifan->notificar = 0;
                } else {
                    $mtto_garantia_lifan->notificar = 1;
                }
            }
            if ($request->observacion) {
                $mtto_garantia_lifan->observacion = $request->observacion;
            }

            if ($request->respuesta) {
                $mtto_garantia_lifan->respuesta = $request->respuesta;
            }
            $mtto_garantia_lifan->update();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        if ($request->notificar == 1) {
            return response()->json(['success' => true, 'message' => 'Mantenimiento Se Desactivo Correctamente.']);
        }
        if ($request->estado == "APROBADO") {
            return response()->json(['success' => true, 'message' => 'Mantenimiento Se Aprobó Correctamente.']);
        }
        if ($request->observacion) {
            return response()->json(['success' => true, 'message' => 'Observación Registrada Correctamente.']);
        }
        if ($request->respuesta) {
            return response()->json(['success' => true, 'message' => 'Respuesta Registrada Correctamente.']);
        }
    }

    public function destroyimagen($id, $item)
    {
        try {
            $imagenDelete = MglImagen::where('MGL_Id', $id)
                ->where('MGLI_Item', $item)
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
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MGLI_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MGLI_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MglImagen::where('MGL_Id', $id)
                ->where('MGLI_Item', $item)
                ->delete();

            /* RECARGAR IMAGENES */
            $datos = MglImagen::where('MGL_Id', $id)->get();

            return response()->json(['success' => true, 'message' => 'Eliminado Correctamente', 'data' => $datos]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_garantia_lifan = MantenimientoGarantiaLifan::find($id);

            if (!$mtto_garantia_lifan) {
                return response()->json(['success' => false, 'message' => 'MantenimientoGarantiaLifan no encontrado.'], 404);
            }

            $mtto_garantia_lifan->MGL_Estado = "PENDIENTE";
            $mtto_garantia_lifan->notificar = 0;
            $mtto_garantia_lifan->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoGarantiaLifan activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MglImagen::where('MGL_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MGLI_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MGLI_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MGLI_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MGLI_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mgl_detalle_reemplazo')
                ->where('MGL_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mgl_imagen')
                ->where('MGL_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_garantia_lifan')
                ->where('MGL_Id', $id)
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
