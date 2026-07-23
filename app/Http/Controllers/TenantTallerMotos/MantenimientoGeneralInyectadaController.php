<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoGeneralInyectada;
use App\Models\TenantTallerMotos\MgiDetalleReemplazo;
use App\Models\TenantTallerMotos\MgiImagen;
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

class MantenimientoGeneralInyectadaController extends Controller
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
                $data = DB::table('mantenimiento_general_inyectada as mgi')
                    ->join('users as p', 'p.id', '=', 'mgi.PER_Id')
                    ->select('mgi.MGI_Id', 'mgi.MGI_Placa', 'mgi.MGI_Propietario', 'mgi.MGI_celular', 'mgi.notificar', 'mgi.MGI_Unidad', 'mgi.MGI_KMEntrada', 'mgi.MGI_FechaCreacion', 'mgi.MGI_Estado', DB::raw('CONCAT(p.name) as personal'));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MGI_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MGI_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MGI_Estado', $request->estado);
                }

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->MGI_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MGI_Estado . '</button>';
                        } else if ($row->MGI_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MGI_Estado . '</button>';
                        } else if ($row->MGI_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MGI_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="/tenant/mantenimientos/generalinyectada/' . $row->MGI_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGeneralInyectadas"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoGeneralInyectadas"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/generalinyectada/' . $row->MGI_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MGI_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoGeneralInyectadas"><i class="fa fa-check"></i></a>';
                        } else if ($row->MGI_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoGeneralInyectadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MGI_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGI_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MGI_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MGI_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_general_inyectada as mgi')
                    ->join('users as p', 'p.id', '=', 'mgi.PER_Id')
                    ->select('mgi.MGI_Id', 'mgi.MGI_Placa', 'mgi.MGI_Propietario', 'mgi.MGI_celular', 'mgi.notificar', 'mgi.MGI_Unidad', 'mgi.MGI_KMEntrada', 'mgi.MGI_FechaCreacion', 'mgi.MGI_Estado', DB::raw('CONCAT(p.name) as personal'))
                    ->where('mgi.PER_Id', '=', $idpersonal)
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action1', function ($row) {
                        if ($row->MGI_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoGeneralInyectadas"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/mantenimientos/generalinyectada/' . $row->MGI_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MGI_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MGI_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.general.inyectadas.index');
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


        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.general.inyectadas.create', ["admin" => $rolAdmin, "personal" => $personal]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_general_inyectadas = new MantenimientoGeneralInyectada;
            $mtto_general_inyectadas->MGI_Placa = $request->get('MGI_Placa');
            $mtto_general_inyectadas->MGI_Propietario = $request->get('MGI_Propietario');
            $mtto_general_inyectadas->MGI_celular = $request->get('MGI_celular');
            $mtto_general_inyectadas->MGI_Unidad = $request->get('MGI_Unidad');
            $mtto_general_inyectadas->MGI_KMEntrada = $request->get('MGI_KMEntrada');
            $mtto_general_inyectadas->MGI_DetalleIngreso = $request->get('MGI_DetalleIngreso');
            $mtto_general_inyectadas->MGI_DetalleObservacion = $request->get('MGI_DetalleObservacion');
            $mtto_general_inyectadas->MGI_Det1=$request->get('MGI_Det1')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det1Informacion=$request->get('MGI_Det1Informacion');
            $mtto_general_inyectadas->MGI_Det2=$request->get('MGI_Det2')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det3=$request->get('MGI_Det3')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det4=$request->get('MGI_Det4')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det5=$request->get('MGI_Det5')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det6=$request->get('MGI_Det6')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det7=$request->get('MGI_Det7')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det8=$request->get('MGI_Det8')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det9=$request->get('MGI_Det9')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det9Admision=$request->get('MGI_Det9Admision');
            $mtto_general_inyectadas->MGI_Det9Escape=$request->get('MGI_Det9Escape');
            $mtto_general_inyectadas->MGI_Det10=$request->get('MGI_Det10')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det10Medida=$request->get('MGI_Det10Medida');
            $mtto_general_inyectadas->MGI_Det11=$request->get('MGI_Det11')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det11Medida=$request->get('MGI_Det11Medida');
            $mtto_general_inyectadas->MGI_Det12=$request->get('MGI_Det12')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det13=$request->get('MGI_Det13')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det14=$request->get('MGI_Det14')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det15=$request->get('MGI_Det15')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det16=$request->get('MGI_Det16')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det17=$request->get('MGI_Det17')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det18=$request->get('MGI_Det18');
            $mtto_general_inyectadas->MGI_Det19=$request->get('MGI_Det19');
            $mtto_general_inyectadas->MGI_Det20=$request->get('MGI_Det20')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det20Humedad=$request->get('MGI_Det20Humedad');
            $mtto_general_inyectadas->MGI_Det21=$request->get('MGI_Det21')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det22=$request->get('MGI_Det22')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det22Ventilador=$request->get('MGI_Det22Ventilador');
            $mtto_general_inyectadas->MGI_Det23=$request->get('MGI_Det23')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det24=$request->get('MGI_Det24')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det24Vida=$request->get('MGI_Det24Vida');
            $mtto_general_inyectadas->MGI_Det24Carga=$request->get('MGI_Det24Carga');
            $mtto_general_inyectadas->MGI_Det24Arranque=$request->get('MGI_Det24Arranque');
            $mtto_general_inyectadas->MGI_Det25=$request->get('MGI_Det25')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det26=$request->get('MGI_Det26')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det27=$request->get('MGI_Det27')?"SI":"NO";
            $mtto_general_inyectadas->MGI_DetalleRealizado = $request->get('MGI_DetalleRealizado');
            $mtto_general_inyectadas->MGI_CorrecionObservacion = $request->get('MGI_CorrecionObservacion');
            $mtto_general_inyectadas->MGI_ProximoCambioAceite = $request->get('MGI_ProximoCambioAceite');
            $mtto_general_inyectadas->MGI_ProximoServicio = $request->get('MGI_ProximoServicio');
            $mtto_general_inyectadas->MGI_FechaCreacion = $mytime->toDateTimeString();
            $mtto_general_inyectadas->MGI_FechaEdicion = $mytime->toDateTimeString();
            $mtto_general_inyectadas->MGI_UsuarioCreacion = $idusu;
            $mtto_general_inyectadas->MGI_UsuarioEditado = $idusu;
            $mtto_general_inyectadas->PER_Id = $request->get('USU_Id');
            $mtto_general_inyectadas->save();

            $MGID_Descripcion = $request->get('MGID_Descripcion');
            $MGI_Precio = $request->get('MGI_Precio');
            if ($MGID_Descripcion) {
                $cont = 0;
                while ($cont < count($MGID_Descripcion)) {
                    $mtto_det_reemplazo = new MgiDetalleReemplazo;
                    $mtto_det_reemplazo->MGI_Id = $mtto_general_inyectadas->MGI_Id;
                    $mtto_det_reemplazo->MGID_Descripcion = $MGID_Descripcion[$cont];
                    $mtto_det_reemplazo->MGID_Item = $cont + 1;
                    $mtto_det_reemplazo->MGI_Precio = $MGI_Precio[$cont];
                    $mtto_det_reemplazo->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Mantenimiento General Inyectadas Registrado Exitosamente.', 'id' => $mtto_general_inyectadas->MGI_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_general_inyectada as mgi')
            ->join('users as u', 'u.id', '=', 'mgi.PER_Id')
            ->select('mgi.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MGI_Id', '=', $id)
            ->first();
        $detalle = DB::table('mgi_detalle_reemplazo')
            ->where('MGI_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mgi_imagen')
            ->where('MGI_Id', '=', $id)
            ->get();

        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }
        $personal = User::select('id', 'name')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.general.inyectadas.edit', [
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

        $totalImagenes = MgiImagen::where('MGI_Id', $mantenimientoId)->count();
        if ($totalImagenes >= tenant('max_images')) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de imágenes.'
            ], 422);
        }
        $ultimoItem = MgiImagen::where('MGI_Id',$mantenimientoId)->max('MGII_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/general_inyectadas/' . $mantenimientoId;

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

        $mgiImagen = new MgiImagen();
        $mgiImagen->MGI_Id = $mantenimientoId;
        $mgiImagen->MGII_Item = $item;
        $mgiImagen->MGII_url = $rutaOriginal;
        $mgiImagen->MGII_Thumb = $rutaThumb;
        $mgiImagen->MGII_Nombre = $nombreArchivo;
        $mgiImagen->MGII_Peso = $tamañoFormateado;
        $mgiImagen->save();

        $datos = MgiImagen::where('MGI_Id',$mantenimientoId)->get();

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
		$datos = DB::table('mantenimiento_general_inyectada as mgi')
				->join('users as u','u.id','=','mgi.PER_Id')
				->select('mgi.*',DB::raw('CONCAT(u.name) as personal'))
				->where('MGI_Id','=',$id)
				->first();

        $detalle_reemplazo = DB::table('mgi_detalle_reemplazo')
				->where('MGI_Id','=',$id)
				->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle =round($total_detalle + $dr->MGI_Precio, 2) ; 
        }

        $imagenes = DB::table('mgi_imagen')
                ->where('MGI_Id','=',$id)
                ->get();

        $url = URL::to('');
		$empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        
		$pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/general/inyectadas/pdf', [
			"mttoPreventivo"=>$datos,
			"detalle"=>$detalle_reemplazo,
			"imagenes"=>$imagenes,
			"url"=>$url,
            "total_detalle"=>$total_detalle,
            "empresa"=>$empresa
		])->setOptions(['defaultFont' => 'sans-serif',
        'chroot'  => public_path('dist/img'), 'isRemoteEnabled' => true]);

		return $pdf->stream('mantenimiento-general-inyectada-' . tenant('id') . '.pdf');
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

            $mtto_general_inyectadas = MantenimientoGeneralInyectada::findOrFail($id);
            $mtto_general_inyectadas->MGI_Placa = $request->get('MGI_Placa');
            $mtto_general_inyectadas->MGI_Propietario = $request->get('MGI_Propietario');
            $mtto_general_inyectadas->MGI_celular = $request->get('MGI_celular');
            $mtto_general_inyectadas->MGI_Unidad = $request->get('MGI_Unidad');
            $mtto_general_inyectadas->MGI_KMEntrada = $request->get('MGI_KMEntrada');
            $mtto_general_inyectadas->MGI_DetalleIngreso = $request->get('MGI_DetalleIngreso');
            $mtto_general_inyectadas->MGI_DetalleObservacion = $request->get('MGI_DetalleObservacion');
            $mtto_general_inyectadas->MGI_Det1=$request->get('MGI_Det1')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det1Informacion=$request->get('MGI_Det1Informacion');
            $mtto_general_inyectadas->MGI_Det2=$request->get('MGI_Det2')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det3=$request->get('MGI_Det3')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det4=$request->get('MGI_Det4')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det5=$request->get('MGI_Det5')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det6=$request->get('MGI_Det6')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det7=$request->get('MGI_Det7')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det8=$request->get('MGI_Det8')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det9=$request->get('MGI_Det9')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det9Admision=$request->get('MGI_Det9Admision');
            $mtto_general_inyectadas->MGI_Det9Escape=$request->get('MGI_Det9Escape');
            $mtto_general_inyectadas->MGI_Det10=$request->get('MGI_Det10')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det10Medida=$request->get('MGI_Det10Medida');
            $mtto_general_inyectadas->MGI_Det11=$request->get('MGI_Det11')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det11Medida=$request->get('MGI_Det11Medida');
            $mtto_general_inyectadas->MGI_Det12=$request->get('MGI_Det12')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det13=$request->get('MGI_Det13')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det14=$request->get('MGI_Det14')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det15=$request->get('MGI_Det15')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det16=$request->get('MGI_Det16')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det17=$request->get('MGI_Det17')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det18=$request->get('MGI_Det18');
            $mtto_general_inyectadas->MGI_Det19=$request->get('MGI_Det19');
            $mtto_general_inyectadas->MGI_Det20=$request->get('MGI_Det20')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det20Humedad=$request->get('MGI_Det20Humedad');
            $mtto_general_inyectadas->MGI_Det21=$request->get('MGI_Det21')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det22=$request->get('MGI_Det22')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det22Ventilador=$request->get('MGI_Det22Ventilador');
            $mtto_general_inyectadas->MGI_Det23=$request->get('MGI_Det23')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det24=$request->get('MGI_Det24')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det24Vida=$request->get('MGI_Det24Vida');
            $mtto_general_inyectadas->MGI_Det24Carga=$request->get('MGI_Det24Carga');
            $mtto_general_inyectadas->MGI_Det24Arranque=$request->get('MGI_Det24Arranque');
            $mtto_general_inyectadas->MGI_Det25=$request->get('MGI_Det25')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det26=$request->get('MGI_Det26')?"SI":"NO";
            $mtto_general_inyectadas->MGI_Det27=$request->get('MGI_Det27')?"SI":"NO";
            $mtto_general_inyectadas->MGI_DetalleRealizado = $request->get('MGI_DetalleRealizado');
            $mtto_general_inyectadas->MGI_CorrecionObservacion = $request->get('MGI_CorrecionObservacion');
            $mtto_general_inyectadas->MGI_ProximoCambioAceite = $request->get('MGI_ProximoCambioAceite');
            $mtto_general_inyectadas->MGI_ProximoServicio = $request->get('MGI_ProximoServicio');
            $mtto_general_inyectadas->MGI_FechaEdicion = $mytime->toDateTimeString();
            $mtto_general_inyectadas->MGI_UsuarioEditado = $idusu;
            $mtto_general_inyectadas->PER_Id = $request->get('USU_Id');
            if ($rolAdmin) {
                $mtto_general_inyectadas->MGI_Estado = 'APROBADO';
            }
            if($request->notificar){
                $mtto_general_inyectadas->notificar = 1;
            }
            $mtto_general_inyectadas->update();


            DB::table('mgi_detalle_reemplazo')
                ->where('MGI_Id', $mtto_general_inyectadas->MGI_Id)
                ->delete();

            $MGID_Descripcion = $request->get('MGID_Descripcion');
            $MGI_Precio = $request->get('MGI_Precio');
            if ($MGID_Descripcion) {
                $cont = 0;
                while ($cont < count($MGID_Descripcion)) {
                    $correctivoresponsable = new MgiDetalleReemplazo;
                    $correctivoresponsable->MGI_Id = $mtto_general_inyectadas->MGI_Id;
                    $correctivoresponsable->MGID_Descripcion = $MGID_Descripcion[$cont];
                    $correctivoresponsable->MGID_Item = $cont + 1;
                    $correctivoresponsable->MGI_Precio = $MGI_Precio[$cont];
                    $correctivoresponsable->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Mantenimiento General Inyectadas Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request,$id)
	{
        try {
            DB::beginTransaction();
            $mtto_general_inyectadas=MantenimientoGeneralInyectada::findOrFail($id);
            if($request->notificar == 1){
                $mtto_general_inyectadas->MGI_Estado="PENDIENTE";
                $mtto_general_inyectadas->notificar=0;
            }
            if($request->estado == "APROBADO"){
                $mtto_general_inyectadas->MGI_Estado="APROBADO";
                if($request->notificar == 2){
                    $mtto_general_inyectadas->notificar=0;
                }else{
                    $mtto_general_inyectadas->notificar=1;
                }
            }
            if($request->observacion){
                $mtto_general_inyectadas->observacion=$request->observacion ;
            }

            if($request->respuesta){
                $mtto_general_inyectadas->respuesta=$request->respuesta ;
            }
            $mtto_general_inyectadas->update();

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
            $imagenDelete = MgiImagen::where('MGI_Id', $id)
                ->where('MGII_Item', $item)
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
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MGII_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MGII_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MgiImagen::where('MGI_Id', $id)
            ->where('MGII_Item', $item)
            ->delete();

            /* RECARGAR IMAGENES */
            $datos = MgiImagen::where('MGI_Id', $id)->get();

            return response()->json(['success' => true,'message' => 'Eliminado Correctamente', 'data'=> $datos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_general_inyectadas = MantenimientoGeneralInyectada::find($id);

            if (!$mtto_general_inyectadas) {
                return response()->json(['success' => false, 'message' => 'MantenimientoGeneralInyectada no encontrado.'], 404);
            }

            $mtto_general_inyectadas->MGI_Estado="PENDIENTE";
            $mtto_general_inyectadas->notificar=0;
            $mtto_general_inyectadas->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoGeneralInyectada activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MgiImagen::where('MGI_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MGII_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MGII_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MGII_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MGII_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mgi_detalle_reemplazo')
                ->where('MGI_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mgi_imagen')
                ->where('MGI_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_general_inyectada')
                ->where('MGI_Id', $id)
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
