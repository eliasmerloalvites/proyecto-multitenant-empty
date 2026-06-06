<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoGeneralCarburada;
use App\Models\TenantTallerMotos\MgcDetalleReemplazo;
use App\Models\TenantTallerMotos\MgcImagen;
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

class MantenimientoGeneralCarburadaController extends Controller
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
                $data = DB::table('mantenimiento_general_carburada as mgi')
                    ->join('users as p', 'p.id', '=', 'mgi.PER_Id')
                    ->select('mgi.MGC_Id', 'mgi.MGC_Placa', 'mgi.MGC_Propietario', 'mgi.MGC_celular', 'mgi.notificar', 'mgi.MGC_Unidad', 'mgi.MGC_KMEntrada', 'mgi.MGC_FechaCreacion', 'mgi.MGC_Estado', DB::raw('CONCAT(p.name) as personal'));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MGC_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MGC_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MGC_Estado', $request->estado);
                }

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
                        $btn = '<a href="/tenant/mantenimientos/generalcarburada/' . $row->MGC_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGeneralCarburadas"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoGeneralCarburadas"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/generalcarburada/' . $row->MGC_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MGC_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoGeneralCarburadas"><i class="fa fa-check"></i></a>';
                        } else if ($row->MGC_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoGeneralCarburadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MGC_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGC_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MGC_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGC_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_general_inyectada as mgi')
                    ->join('users as p', 'p.id', '=', 'mgi.PER_Id')
                    ->select('mgi.MGC_Id', 'mgi.MGC_Placa', 'mgi.MGC_Propietario', 'mgi.MGC_celular', 'mgi.notificar', 'mgi.MGC_Unidad', 'mgi.MGC_KMEntrada', 'mgi.MGC_FechaCreacion', 'mgi.MGC_Estado', DB::raw('CONCAT(p.name) as personal'))
                    ->where('mgi.PER_Id', '=', $idpersonal)
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
                        $btn = '<a  target="_blank" href="/mantenimientos/generalcarburada/' . $row->MGC_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGC_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MGC_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.general.carburadas.index');
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


        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.general.carburadas.create', ["admin" => $rolAdmin, "personal" => $personal]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_general_carburadas = new MantenimientoGeneralCarburada;
            $mtto_general_carburadas->MGC_Placa = $request->get('MGC_Placa');
            $mtto_general_carburadas->MGC_Propietario = $request->get('MGC_Propietario');
            $mtto_general_carburadas->MGC_celular = $request->get('MGC_celular');
            $mtto_general_carburadas->MGC_Unidad = $request->get('MGC_Unidad');
            $mtto_general_carburadas->MGC_KMEntrada = $request->get('MGC_KMEntrada');
            $mtto_general_carburadas->MGC_DetalleIngreso = $request->get('MGC_DetalleIngreso');
            $mtto_general_carburadas->MGC_DetalleObservacion = $request->get('MGC_DetalleObservacion');
            $mtto_general_carburadas->MGC_Det1=$request->get('MGC_Det1')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det1Informacion=$request->get('MGC_Det1Informacion');
            $mtto_general_carburadas->MGC_Det2=$request->get('MGC_Det2')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det3=$request->get('MGC_Det3')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det4=$request->get('MGC_Det4')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det5=$request->get('MGC_Det5')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det6=$request->get('MGC_Det6')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det7=$request->get('MGC_Det7')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det8=$request->get('MGC_Det8')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det8Admision=$request->get('MGC_Det8Admision');
            $mtto_general_carburadas->MGC_Det8Escape=$request->get('MGC_Det8Escape');
            $mtto_general_carburadas->MGC_Det9=$request->get('MGC_Det9')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det9Medida=$request->get('MGC_Det9Medida');
            $mtto_general_carburadas->MGC_Det10=$request->get('MGC_Det10')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det11=$request->get('MGC_Det11')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det12=$request->get('MGC_Det12')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det13=$request->get('MGC_Det13')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det14=$request->get('MGC_Det14')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det15=$request->get('MGC_Det15')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det16=$request->get('MGC_Det16');
            $mtto_general_carburadas->MGC_Det17=$request->get('MGC_Det17');
            $mtto_general_carburadas->MGC_Det18=$request->get('MGC_Det18')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det18Humedad=$request->get('MGC_Det18Humedad');
            $mtto_general_carburadas->MGC_Det19=$request->get('MGC_Det19')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det19Ventilador=$request->get('MGC_Det19Ventilador');
            $mtto_general_carburadas->MGC_Det20=$request->get('MGC_Det20')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det21=$request->get('MGC_Det21')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det21Vida=$request->get('MGC_Det21Vida');
            $mtto_general_carburadas->MGC_Det21Carga=$request->get('MGC_Det21Carga');
            $mtto_general_carburadas->MGC_Det21Arranque=$request->get('MGC_Det21Arranque');
            $mtto_general_carburadas->MGC_DetalleRealizado = $request->get('MGC_DetalleRealizado');
            $mtto_general_carburadas->MGC_CorrecionObservacion = $request->get('MGC_CorrecionObservacion');
            $mtto_general_carburadas->MGC_ProximoCambioAceite = $request->get('MGC_ProximoCambioAceite');
            $mtto_general_carburadas->MGC_ProximoServicio = $request->get('MGC_ProximoServicio');
            $mtto_general_carburadas->MGC_FechaCreacion = $mytime->toDateTimeString();
            $mtto_general_carburadas->MGC_FechaEdicion = $mytime->toDateTimeString();
            $mtto_general_carburadas->MGC_UsuarioCreacion = $idusu;
            $mtto_general_carburadas->MGC_UsuarioEditado = $idusu;
            $mtto_general_carburadas->PER_Id = $request->get('USU_Id');
            $mtto_general_carburadas->save();

            $MGCD_Descripcion = $request->get('MGCD_Descripcion');
            $MGC_Precio = $request->get('MGC_Precio');
            if ($MGCD_Descripcion) {
                $cont = 0;
                while ($cont < count($MGCD_Descripcion)) {
                    $mtto_det_reemplazo = new MgcDetalleReemplazo;
                    $mtto_det_reemplazo->MGC_Id = $mtto_general_carburadas->MGC_Id;
                    $mtto_det_reemplazo->MGCD_Descripcion = $MGCD_Descripcion[$cont];
                    $mtto_det_reemplazo->MGCD_Item = $cont + 1;
                    $mtto_det_reemplazo->MGC_Precio = $MGC_Precio[$cont];
                    $mtto_det_reemplazo->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Mantenimiento General Carburadas Registrado Exitosamente.', 'id' => $mtto_general_carburadas->MGC_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_general_carburada as mgi')
            ->join('users as u', 'u.id', '=', 'mgi.PER_Id')
            ->select('mgi.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGC_Id', '=', $id)
            ->first();
        $detalle = DB::table('mgc_detalle_reemplazo')
            ->where('MGC_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mgc_imagen')
            ->where('MGC_Id', '=', $id)
            ->get();

        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }
        $personal = User::select('id', 'name')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.general.carburadas.edit', [
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

        $totalImagenes = MgcImagen::where('MGC_Id', $mantenimientoId)->count();
        if ($totalImagenes >= tenant('max_images')) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de imágenes.'
            ], 422);
        }
        $ultimoItem = MgcImagen::where('MGC_Id',$mantenimientoId)->max('MGCI_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/general_carburadas/' . $mantenimientoId;

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

        $mgiImagen = new MgcImagen();
        $mgiImagen->MGC_Id = $mantenimientoId;
        $mgiImagen->MGCI_Item = $item;
        $mgiImagen->MGCI_url = $rutaOriginal;
        $mgiImagen->MGCI_Thumb = $rutaThumb;
        $mgiImagen->MGCI_Nombre = $nombreArchivo;
        $mgiImagen->MGCI_Peso = $tamañoFormateado;
        $mgiImagen->save();

        $datos = MgcImagen::where('MGC_Id',$mantenimientoId)->get();

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
		$datos = DB::table('mantenimiento_general_carburada as mgi')
				->join('users as u','u.id','=','mgi.PER_Id')
				->select('mgi.*',DB::raw('CONCAT(u.name) as personal'))
				->where('MGC_Id','=',$id)
				->first();

        $detalle_reemplazo = DB::table('mgc_detalle_reemplazo')
				->where('MGC_Id','=',$id)
				->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle =round($total_detalle + $dr->MGC_Precio, 2) ; 
        }

        $imagenes = DB::table('mgc_imagen')
                ->where('MGC_Id','=',$id)
                ->get();

        $url = URL::to('');
		$empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        
		$pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/general/carburadas/pdf', [
			"mttoPreventivo"=>$datos,
			"detalle"=>$detalle_reemplazo,
			"imagenes"=>$imagenes,
			"url"=>$url,
            "total_detalle"=>$total_detalle,
            "empresa"=>$empresa
		])->setOptions(['defaultFont' => 'sans-serif',
        'chroot'  => public_path('dist/img'), 'isRemoteEnabled' => true]);

		return $pdf->stream('mantenimiento-general-carburada-' . tenant('id') . '.pdf');
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

            $mtto_general_carburadas = MantenimientoGeneralCarburada::findOrFail($id);
            $mtto_general_carburadas->MGC_Placa = $request->get('MGC_Placa');
            $mtto_general_carburadas->MGC_Propietario = $request->get('MGC_Propietario');
            $mtto_general_carburadas->MGC_celular = $request->get('MGC_celular');
            $mtto_general_carburadas->MGC_Unidad = $request->get('MGC_Unidad');
            $mtto_general_carburadas->MGC_KMEntrada = $request->get('MGC_KMEntrada');
            $mtto_general_carburadas->MGC_DetalleIngreso = $request->get('MGC_DetalleIngreso');
            $mtto_general_carburadas->MGC_DetalleObservacion = $request->get('MGC_DetalleObservacion');
            $mtto_general_carburadas->MGC_Det1=$request->get('MGC_Det1')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det1Informacion=$request->get('MGC_Det1Informacion');
            $mtto_general_carburadas->MGC_Det2=$request->get('MGC_Det2')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det3=$request->get('MGC_Det3')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det4=$request->get('MGC_Det4')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det5=$request->get('MGC_Det5')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det6=$request->get('MGC_Det6')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det7=$request->get('MGC_Det7')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det8=$request->get('MGC_Det8')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det8Admision=$request->get('MGC_Det8Admision');
            $mtto_general_carburadas->MGC_Det8Escape=$request->get('MGC_Det8Escape');
            $mtto_general_carburadas->MGC_Det9=$request->get('MGC_Det9')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det9Medida=$request->get('MGC_Det9Medida');
            $mtto_general_carburadas->MGC_Det10=$request->get('MGC_Det10')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det11=$request->get('MGC_Det11')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det12=$request->get('MGC_Det12')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det13=$request->get('MGC_Det13')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det14=$request->get('MGC_Det14')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det15=$request->get('MGC_Det15')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det16=$request->get('MGC_Det16');
            $mtto_general_carburadas->MGC_Det17=$request->get('MGC_Det17');
            $mtto_general_carburadas->MGC_Det18=$request->get('MGC_Det18')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det18Humedad=$request->get('MGC_Det18Humedad');
            $mtto_general_carburadas->MGC_Det19=$request->get('MGC_Det19')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det19Ventilador=$request->get('MGC_Det19Ventilador');
            $mtto_general_carburadas->MGC_Det20=$request->get('MGC_Det20')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det21=$request->get('MGC_Det21')?"SI":"NO";
            $mtto_general_carburadas->MGC_Det21Vida=$request->get('MGC_Det21Vida');
            $mtto_general_carburadas->MGC_Det21Carga=$request->get('MGC_Det21Carga');
            $mtto_general_carburadas->MGC_Det21Arranque=$request->get('MGC_Det21Arranque');
            $mtto_general_carburadas->MGC_DetalleRealizado = $request->get('MGC_DetalleRealizado');
            $mtto_general_carburadas->MGC_CorrecionObservacion = $request->get('MGC_CorrecionObservacion');
            $mtto_general_carburadas->MGC_ProximoCambioAceite = $request->get('MGC_ProximoCambioAceite');
            $mtto_general_carburadas->MGC_ProximoServicio = $request->get('MGC_ProximoServicio');
            $mtto_general_carburadas->MGC_FechaEdicion = $mytime->toDateTimeString();
            $mtto_general_carburadas->MGC_UsuarioEditado = $idusu;
            $mtto_general_carburadas->PER_Id = $request->get('USU_Id');
            if ($rolAdmin) {
                $mtto_general_carburadas->MGC_Estado = 'APROBADO';
            }
            if($request->notificar){
                $mtto_general_carburadas->notificar = 1;
            }
            $mtto_general_carburadas->update();


            DB::table('mgc_detalle_reemplazo')
                ->where('MGC_Id', $mtto_general_carburadas->MGC_Id)
                ->delete();

            $MGCD_Descripcion = $request->get('MGCD_Descripcion');
            $MGC_Precio = $request->get('MGC_Precio');
            if ($MGCD_Descripcion) {
                $cont = 0;
                while ($cont < count($MGCD_Descripcion)) {
                    $correctivoresponsable = new MgcDetalleReemplazo;
                    $correctivoresponsable->MGC_Id = $mtto_general_carburadas->MGC_Id;
                    $correctivoresponsable->MGCD_Descripcion = $MGCD_Descripcion[$cont];
                    $correctivoresponsable->MGCD_Item = $cont + 1;
                    $correctivoresponsable->MGC_Precio = $MGC_Precio[$cont];
                    $correctivoresponsable->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Mantenimiento General Carburadas Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request,$id)
	{
        try {
            DB::beginTransaction();
            $mtto_general_carburadas=MantenimientoGeneralCarburada::findOrFail($id);
            if($request->notificar == 1){
                $mtto_general_carburadas->MGC_Estado="PENDIENTE";
                $mtto_general_carburadas->notificar=0;
            }
            if($request->estado == "APROBADO"){
                $mtto_general_carburadas->MGC_Estado="APROBADO";
                if($request->notificar == 2){
                    $mtto_general_carburadas->notificar=0;
                }else{
                    $mtto_general_carburadas->notificar=1;
                }
            }
            if($request->observacion){
                $mtto_general_carburadas->observacion=$request->observacion ;
            }

            if($request->respuesta){
                $mtto_general_carburadas->respuesta=$request->respuesta ;
            }
            $mtto_general_carburadas->update();

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
            $imagenDelete = MgcImagen::where('MGC_Id', $id)
                ->where('MGCI_Item', $item)
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
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MGCI_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MGCI_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MgcImagen::where('MGC_Id', $id)
            ->where('MGCI_Item', $item)
            ->delete();

            /* RECARGAR IMAGENES */
            $datos = MgcImagen::where('MGC_Id', $id)->get();

            return response()->json(['success' => true,'message' => 'Eliminado Correctamente', 'data'=> $datos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_general_carburadas = MantenimientoGeneralCarburada::find($id);

            if (!$mtto_general_carburadas) {
                return response()->json(['success' => false, 'message' => 'MantenimientoGeneralCarburada no encontrado.'], 404);
            }

            $mtto_general_carburadas->MGC_Estado="PENDIENTE";
            $mtto_general_carburadas->notificar=0;
            $mtto_general_carburadas->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoGeneralCarburada activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MgcImagen::where('MGC_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MGCI_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MGCI_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MGCI_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MGCI_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mgc_detalle_reemplazo')
                ->where('MGC_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mgc_imagen')
                ->where('MGC_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_general_carburada')
                ->where('MGC_Id', $id)
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
