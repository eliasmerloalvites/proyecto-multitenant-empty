<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoGarantiaHero;
use App\Models\TenantTallerMotos\MantenimientoPlan;
use App\Models\TenantTallerMotos\MghDetalleReemplazo;
use App\Services\Mantenimiento\RepuestosBahiaSync;
use App\Services\TenantTallerMotos\GestionProcesoService;
use App\Models\TenantTallerMotos\MghImagen;
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

class MantenimientoGarantiaHeroController extends Controller
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
                $data = DB::table('mantenimiento_garantia_hero as mgh')
                    ->leftJoin('users as p', 'p.id', '=', 'mgh.PER_Id')
                    ->select('mgh.MGH_Id', 'mgh.MGH_Placa', 'mgh.MGH_EsGarantia', DB::raw('(SELECT MAR_Nombre FROM marca_vehiculo WHERE MAR_Id = mgh.MAR_Id) as marca'), 'mgh.MGH_Propietario', 'mgh.MGH_celular', 'mgh.notificar', 'mgh.MGH_Unidad', 'mgh.MGH_KMEntrada', 'mgh.MGH_FechaCreacion', 'mgh.MGH_FechaTermino', 'mgh.MGH_Estado', DB::raw('CONCAT(p.name) as personal'), DB::raw("EXISTS(SELECT 1 FROM recepcion_respuesta WHERE MTO_Tabla = 'mantenimiento_garantia_hero' AND MTO_Id = mgh.MGH_Id) as tiene_recepcion"));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MGH_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MGH_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MGH_Estado', $request->estado);
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
                        if ($row->MGH_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MGH_Estado . '</button>';
                        } else if ($row->MGH_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MGH_Estado . '</button>';
                        } else if ($row->MGH_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MGH_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGarantiaHero"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoGarantiaHero"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MGH_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoGarantiaHero"><i class="fa fa-check"></i></a>';
                        } else if ($row->MGH_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoGarantiaHero"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('action5', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/orden-servicio" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Orden de Servicio" class="btn btn-primary btn-sm "><i class="fas fa-clipboard-list"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action6', function ($row) {
                        if (!$row->tiene_recepcion) {
                            return '';
                        }
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/estado-recepcion-pdf" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Estado de Recepción" class="btn btn-secondary btn-sm "><i class="fas fa-motorcycle"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MGH_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGH_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MGH_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGH_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'action5', 'action6', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_garantia_hero as mgh')
                    ->leftJoin('users as p', 'p.id', '=', 'mgh.PER_Id')
                    ->select('mgh.MGH_Id', 'mgh.MGH_Placa', 'mgh.MGH_EsGarantia', DB::raw('(SELECT MAR_Nombre FROM marca_vehiculo WHERE MAR_Id = mgh.MAR_Id) as marca'), 'mgh.MGH_Propietario', 'mgh.MGH_celular', 'mgh.notificar', 'mgh.MGH_Unidad', 'mgh.MGH_KMEntrada', 'mgh.MGH_FechaCreacion', 'mgh.MGH_FechaTermino', 'mgh.MGH_Estado', DB::raw('CONCAT(p.name) as personal'), DB::raw("EXISTS(SELECT 1 FROM recepcion_respuesta WHERE MTO_Tabla = 'mantenimiento_garantia_hero' AND MTO_Id = mgh.MGH_Id) as tiene_recepcion"))
                    ->where('mgh.PER_Id', '=', $idpersonal);

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MGH_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MGH_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MGH_Estado', $request->estado);
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
                        if ($row->MGH_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MGH_Estado . '</button>';
                        } else if ($row->MGH_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MGH_Estado . '</button>';
                        } else if ($row->MGH_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MGH_Estado . '</button>';
                        } else {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-secondary">' . $row->MGH_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        if ($row->MGH_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGarantiaHero"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        return '';
                    })
                    ->addColumn('action5', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/orden-servicio" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Orden de Servicio" class="btn btn-primary btn-sm "><i class="fas fa-clipboard-list"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action6', function ($row) {
                        if (!$row->tiene_recepcion) {
                            return '';
                        }
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/garantiahero/' . $row->MGH_Id . '/estado-recepcion-pdf" data-toggle="tooltip"  data-id="' . $row->MGH_Id . '" data-original-title="Estado de Recepción" class="btn btn-secondary btn-sm "><i class="fas fa-motorcycle"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MGH_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'action5', 'action6', 'estado', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.garantia.hero.index');
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

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.garantia.hero.create', ["admin" => $rolAdmin, "personal" => $personal, "planes" => $planes]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_garantia_hero = new MantenimientoGarantiaHero;
            $mtto_garantia_hero->MGH_Placa = $request->get('MGH_Placa');
            $mtto_garantia_hero->MGH_Propietario = $request->get('MGH_Propietario');
            $mtto_garantia_hero->MGH_celular = $request->get('MGH_celular');
            $mtto_garantia_hero->MGH_Unidad = $request->get('MGH_Unidad');
            $mtto_garantia_hero->MGH_KMEntrada = $request->get('MGH_KMEntrada');
            $mtto_garantia_hero->MGH_DetalleIngreso = $request->get('MGH_DetalleIngreso');
            $mtto_garantia_hero->MGH_DetalleObservacion = $request->get('MGH_DetalleObservacion');
            $mtto_garantia_hero->MGH_Det1 = $request->get('MGH_Det1') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det1Informacion = $request->get('MGH_Det1Informacion');
            $mtto_garantia_hero->MGH_Det2 = $request->get('MGH_Det2') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det3 = $request->get('MGH_Det3') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det4 = $request->get('MGH_Det4') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det5 = $request->get('MGH_Det5') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det6 = $request->get('MGH_Det6') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det7 = $request->get('MGH_Det7') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det7Admision = $request->get('MGH_Det7Admision');
            $mtto_garantia_hero->MGH_Det7Escape = $request->get('MGH_Det7Escape');
            $mtto_garantia_hero->MGH_Det8 = $request->get('MGH_Det8') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det8Medida = $request->get('MGH_Det8Medida');
            $mtto_garantia_hero->MGH_Det9 = $request->get('MGH_Det9');
            $mtto_garantia_hero->MGH_Det10 = $request->get('MGH_Det10');
            $mtto_garantia_hero->MGH_Det11 = $request->get('MGH_Det11') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det11Vida = $request->get('MGH_Det11Vida');
            $mtto_garantia_hero->MGH_Det11Carga = $request->get('MGH_Det11Carga');
            $mtto_garantia_hero->MGH_Det11Arranque = $request->get('MGH_Det11Arranque');
            $mtto_garantia_hero->MGH_DetalleRealizado = $request->get('MGH_DetalleRealizado');
            $mtto_garantia_hero->MGH_CorrecionObservacion = $request->get('MGH_CorrecionObservacion');
            $mtto_garantia_hero->MGH_ProximoCambioAceite = $request->get('MGH_ProximoCambioAceite');
            $mtto_garantia_hero->MGH_ProximoServicio = $request->get('MGH_ProximoServicio');
            $mtto_garantia_hero->MGH_FechaCreacion = $mytime->toDateTimeString();
            $mtto_garantia_hero->MGH_FechaEdicion = $mytime->toDateTimeString();
            $mtto_garantia_hero->MGH_UsuarioCreacion = $idusu;
            $mtto_garantia_hero->MGH_UsuarioEditado = $idusu;
            $mtto_garantia_hero->PER_Id = $request->get('USU_Id');
            $mtto_garantia_hero->PLAN_Id = $request->get('PLAN_Id') ?: null;
            \App\Services\TenantTallerMotos\MarcaVehiculoService::aplicarAMantenimiento($mtto_garantia_hero, 'MGH', $request);
            $mtto_garantia_hero->save();

            RepuestosBahiaSync::reemplazarManuales(
                'MGH',
                $mtto_garantia_hero->MGH_Id,
                $request->get('MGHD_Descripcion', []),
                $request->get('MGH_Precio', [])
            );
            RepuestosBahiaSync::sincronizar('MGH', $mtto_garantia_hero->MGH_Id, $mtto_garantia_hero->RES_Id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Mantenimiento Garantía Hero Registrado Exitosamente.', 'id' => $mtto_garantia_hero->MGH_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_garantia_hero as mgh')
            ->leftJoin('users as u', 'u.id', '=', 'mgh.PER_Id')
            ->select('mgh.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGH_Id', '=', $id)
            ->first();

        RepuestosBahiaSync::sincronizar('MGH', (int) $id, $datos->RES_Id ?? null);

        $detalle = DB::table('mgh_detalle_reemplazo')
            ->where('MGH_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mgh_imagen')
            ->where('MGH_Id', '=', $id)
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

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.garantia.hero.edit', [
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

        $totalImagenes = MghImagen::where('MGH_Id', $mantenimientoId)->count();
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
        $ultimoItem = MghImagen::where('MGH_Id', $mantenimientoId)->max('MGHI_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/garantia_hero/' . $mantenimientoId;

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

        $mghImagen = new MghImagen();
        $mghImagen->MGH_Id = $mantenimientoId;
        $mghImagen->MGHI_Item = $item;
        $mghImagen->MGHI_url = $rutaOriginal;
        $mghImagen->MGHI_Thumb = $rutaThumb;
        $mghImagen->MGHI_Nombre = $nombreArchivo;
        $mghImagen->MGHI_Peso = $tamañoFormateado;
        $mghImagen->save();

        $datos = MghImagen::where('MGH_Id', $mantenimientoId)->get();

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
        $datos = DB::table('mantenimiento_garantia_hero as mgh')
            ->leftJoin('users as u', 'u.id', '=', 'mgh.PER_Id')
            ->select('mgh.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGH_Id', '=', $id)
            ->first();

        $detalle_reemplazo = DB::table('mgh_detalle_reemplazo')
            ->where('MGH_Id', '=', $id)
            ->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle = round($total_detalle + $dr->MGH_Precio, 2);
        }

        $imagenes = DB::table('mgh_imagen')
            ->where('MGH_Id', '=', $id)
            ->get();

        $url = URL::to('');
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/garantia/hero/pdf', [
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

        return $pdf->stream('mantenimiento-garantia-hero-' . tenant('id') . '.pdf');
    }

    /**
     * "Orden de Servicio": mismo contenido que pdf(), mas el Inventario
     * Visual/Inspeccion de la Unidad (Estado de Recepcion) y los datos de
     * facturacion de la venta asociada si la reserva llego a cobrarse. Es
     * un documento nuevo aparte; pdf()/descargarpdf() no se tocan.
     */
    public function ordenServicio($id)
    {
        $datos = DB::table('mantenimiento_garantia_hero as mgh')
            ->leftJoin('users as u', 'u.id', '=', 'mgh.PER_Id')
            ->select('mgh.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGH_Id', '=', $id)
            ->first();

        $detalle_reemplazo = DB::table('mgh_detalle_reemplazo')
            ->where('MGH_Id', '=', $id)
            ->get();

        $total_detalle = round($detalle_reemplazo->sum('MGH_Precio'), 2);

        $imagenes = DB::table('mgh_imagen')
            ->where('MGH_Id', '=', $id)
            ->get();

        $url = URL::to('');
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $estadoRecepcion = null;
        try {
            $estadoRecepcion = GestionProcesoService::estadoRecepcion('mantenimiento_garantia_hero', (int) $id);
        } catch (\Throwable $e) {
            // Sin ficha de recepcion guardada: el partial simplemente no imprime esas secciones.
        }

        $pdf = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/garantia/hero/orden-servicio', [
            "mttoPreventivo" => $datos,
            "detalle" => $detalle_reemplazo,
            "imagenes" => $imagenes,
            "url" => $url,
            "total_detalle" => $total_detalle,
            "empresa" => $empresa,
            "etiquetaTipo" => 'Garantía Hero',
            "estadoRecepcion" => $estadoRecepcion,
            "datosVenta" => GestionProcesoService::datosVentaAsociada($datos->RES_Id ?? null),
        ])->setOptions([
            'defaultFont' => 'sans-serif',
            'chroot' => public_path('dist/img'),
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream('orden-servicio-garantia-hero-' . tenant('id') . '.pdf');
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
        $datos = DB::table('mantenimiento_garantia_hero as mgh')
            ->leftJoin('users as u', 'u.id', '=', 'mgh.PER_Id')
            ->select('mgh.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGH_Id', '=', $id)
            ->first();

        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $estadoRecepcion = null;
        try {
            $estadoRecepcion = GestionProcesoService::estadoRecepcion('mantenimiento_garantia_hero', (int) $id);
        } catch (\Throwable $e) {
            // Sin ficha de recepcion guardada: la vista muestra el aviso de "sin datos".
        }

        $pdf = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/estado-recepcion-pdf', [
            "empresa" => $empresa,
            "etiquetaTipo" => 'Garantía Hero',
            "numeroOrden" => 'MGH-' . str_pad($id, 6, '0', STR_PAD_LEFT),
            "fecha" => $datos ? date('d/m/Y', strtotime($datos->MGH_FechaCreacion)) : '',
            "hora" => $datos ? date('H:i', strtotime($datos->MGH_FechaCreacion)) : '',
            "personal" => $datos->personal ?? '',
            "propietario" => $datos->MGH_Propietario ?? '',
            "placa" => $datos->MGH_Placa ?? '',
            "unidad" => $datos->MGH_Unidad ?? '',
            "km" => $datos->MGH_KMEntrada ?? '',
            "estadoRecepcion" => $estadoRecepcion,
        ])->setOptions([
            'defaultFont' => 'sans-serif',
            'chroot' => public_path('dist/img'),
            'isRemoteEnabled' => true
        ]);

        return $pdf->stream('estado-recepcion-garantia-hero-' . tenant('id') . '.pdf');
    }

    public function descargarpdf($id)
    {
        $datos = DB::table('mantenimiento_garantia_hero as mgh')
            ->leftJoin('users as u', 'u.id', '=', 'mgh.PER_Id')
            ->select('mgh.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGH_Id', '=', $id)
            ->first();

        $detalle_reemplazo = DB::table('mgh_detalle_reemplazo')
            ->where('MGH_Id', '=', $id)
            ->get();

        // Optimización: Suma directa mediante la colección
        $total_detalle = round($detalle_reemplazo->sum('MGH_Precio'), 2);

        $imagenes = DB::table('mgh_imagen')
            ->where('MGH_Id', '=', $id)
            ->get();

        $url = URL::to('');
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $pdf = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/garantia/hero/pdf', [
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
        $nombreArchivo = 'mantenimiento-garantia-hero-' . $id . '-' . tenant('id') . '.pdf';

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

            $mtto_garantia_hero = MantenimientoGarantiaHero::findOrFail($id);
            $mtto_garantia_hero->MGH_Placa = $request->get('MGH_Placa');
            $mtto_garantia_hero->MGH_Propietario = $request->get('MGH_Propietario');
            $mtto_garantia_hero->MGH_celular = $request->get('MGH_celular');
            $mtto_garantia_hero->MGH_Unidad = $request->get('MGH_Unidad');
            // KM es NOT NULL en BD y puede haber quedado guardado como '' desde
            // el check-in; si se edita sin tocar el campo, ConvertEmptyStringsToNull
            // lo vuelve null y la UPDATE truena contra esa constraint. Se
            // preserva el valor ya guardado en vez de pisarlo con vacío.
            if ($request->filled('MGH_KMEntrada')) {
                $mtto_garantia_hero->MGH_KMEntrada = $request->get('MGH_KMEntrada');
            }
            $mtto_garantia_hero->MGH_DetalleIngreso = $request->get('MGH_DetalleIngreso');
            $mtto_garantia_hero->MGH_DetalleObservacion = $request->get('MGH_DetalleObservacion');
            $mtto_garantia_hero->MGH_Det1 = $request->get('MGH_Det1') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det1Informacion = $request->get('MGH_Det1Informacion');
            $mtto_garantia_hero->MGH_Det2 = $request->get('MGH_Det2') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det3 = $request->get('MGH_Det3') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det4 = $request->get('MGH_Det4') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det5 = $request->get('MGH_Det5') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det6 = $request->get('MGH_Det6') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det7 = $request->get('MGH_Det7') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det7Admision = $request->get('MGH_Det7Admision');
            $mtto_garantia_hero->MGH_Det7Escape = $request->get('MGH_Det7Escape');
            $mtto_garantia_hero->MGH_Det8 = $request->get('MGH_Det8') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det8Medida = $request->get('MGH_Det8Medida');
            $mtto_garantia_hero->MGH_Det9 = $request->get('MGH_Det9');
            $mtto_garantia_hero->MGH_Det10 = $request->get('MGH_Det10');
            $mtto_garantia_hero->MGH_Det11 = $request->get('MGH_Det11') ? "SI" : "NO";
            $mtto_garantia_hero->MGH_Det11Vida = $request->get('MGH_Det11Vida');
            $mtto_garantia_hero->MGH_Det11Carga = $request->get('MGH_Det11Carga');
            $mtto_garantia_hero->MGH_Det11Arranque = $request->get('MGH_Det11Arranque');
            $mtto_garantia_hero->MGH_DetalleRealizado = $request->get('MGH_DetalleRealizado');
            $mtto_garantia_hero->MGH_CorrecionObservacion = $request->get('MGH_CorrecionObservacion');
            $mtto_garantia_hero->MGH_ProximoCambioAceite = $request->get('MGH_ProximoCambioAceite');
            $mtto_garantia_hero->MGH_ProximoServicio = $request->get('MGH_ProximoServicio');
            $mtto_garantia_hero->MGH_Recomendacion = $request->get('MGH_Recomendacion');
            $mtto_garantia_hero->MGH_RecomendacionPrioridad = $request->get('MGH_RecomendacionPrioridad') ?: null;
            $mtto_garantia_hero->MGH_VerifArranque = $request->boolean('MGH_VerifArranque');
            $mtto_garantia_hero->MGH_VerifLuces = $request->boolean('MGH_VerifLuces');
            $mtto_garantia_hero->MGH_VerifDireccionales = $request->boolean('MGH_VerifDireccionales');
            $mtto_garantia_hero->MGH_VerifNivelAceite = $request->boolean('MGH_VerifNivelAceite');
            $mtto_garantia_hero->MGH_VerifPruebaRuta = $request->boolean('MGH_VerifPruebaRuta');
            $mtto_garantia_hero->MGH_VerifLavado = $request->boolean('MGH_VerifLavado');
            $mtto_garantia_hero->MGH_VerifOtros = $request->get('MGH_VerifOtros');
            $mtto_garantia_hero->MGH_VerifConforme = $request->boolean('MGH_VerifConforme');
            $mtto_garantia_hero->MGH_FechaEdicion = $mytime->toDateTimeString();
            $mtto_garantia_hero->MGH_FechaInicio = $request->get('MGH_FechaInicio') ? Carbon::parse($request->get('MGH_FechaInicio'))->toDateTimeString() : null;
            $mtto_garantia_hero->MGH_FechaTermino = $request->get('MGH_FechaTermino') ? Carbon::parse($request->get('MGH_FechaTermino'))->toDateTimeString() : null;
            $mtto_garantia_hero->MGH_UsuarioEditado = $idusu;
            $mtto_garantia_hero->PER_Id = $request->get('USU_Id');
            $mtto_garantia_hero->PLAN_Id = $request->get('PLAN_Id') ?: null;
            if ($rolAdmin) {
                $mtto_garantia_hero->MGH_Estado = 'APROBADO';
            }
            if ($request->notificar) {
                $mtto_garantia_hero->notificar = 1;
            }
            \App\Services\TenantTallerMotos\MarcaVehiculoService::aplicarAMantenimiento($mtto_garantia_hero, 'MGH', $request);
            $mtto_garantia_hero->update();

            RepuestosBahiaSync::reemplazarManuales(
                'MGH',
                $mtto_garantia_hero->MGH_Id,
                $request->get('MGHD_Descripcion', []),
                $request->get('MGH_Precio', [])
            );
            RepuestosBahiaSync::sincronizar('MGH', $mtto_garantia_hero->MGH_Id, $mtto_garantia_hero->RES_Id);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Mantenimiento Garantía Hero Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request, $id)
    {
        try {
            DB::beginTransaction();
            $mtto_garantia_hero = MantenimientoGarantiaHero::findOrFail($id);
            if ($request->notificar == 1) {
                $mtto_garantia_hero->MGH_Estado = "PENDIENTE";
                $mtto_garantia_hero->notificar = 0;
            }
            if ($request->estado == "APROBADO") {
                $mtto_garantia_hero->MGH_Estado = "APROBADO";
                if ($request->notificar == 2) {
                    $mtto_garantia_hero->notificar = 0;
                } else {
                    $mtto_garantia_hero->notificar = 1;
                }
            }
            if ($request->observacion) {
                $mtto_garantia_hero->observacion = $request->observacion;
            }

            if ($request->respuesta) {
                $mtto_garantia_hero->respuesta = $request->respuesta;
            }
            $mtto_garantia_hero->update();

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
            $imagenDelete = MghImagen::where('MGH_Id', $id)
                ->where('MGHI_Item', $item)
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
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MGHI_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MGHI_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MghImagen::where('MGH_Id', $id)
                ->where('MGHI_Item', $item)
                ->delete();

            /* RECARGAR IMAGENES */
            $datos = MghImagen::where('MGH_Id', $id)->get();

            return response()->json(['success' => true, 'message' => 'Eliminado Correctamente', 'data' => $datos]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_garantia_hero = MantenimientoGarantiaHero::find($id);

            if (!$mtto_garantia_hero) {
                return response()->json(['success' => false, 'message' => 'MantenimientoGarantiaHero no encontrado.'], 404);
            }

            $mtto_garantia_hero->MGH_Estado = "PENDIENTE";
            $mtto_garantia_hero->notificar = 0;
            $mtto_garantia_hero->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoGarantiaHero activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MghImagen::where('MGH_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MGHI_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MGHI_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MGHI_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MGHI_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mgh_detalle_reemplazo')
                ->where('MGH_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mgh_imagen')
                ->where('MGH_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_garantia_hero')
                ->where('MGH_Id', $id)
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
