<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoPreventivoInyectada;
use App\Models\TenantTallerMotos\MpiDetalleReemplazo;
use App\Models\TenantTallerMotos\MpiImagen;
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

class MantenimientoPreventivoInyectadaController extends Controller
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
                $data = DB::table('mantenimiento_preventivo_inyectada as mpi')
                    ->join('users as p', 'p.id', '=', 'mpi.PER_Id')
                    ->select('mpi.MPI_Id', 'mpi.MPI_Placa', 'mpi.MPI_Propietario', 'mpi.MPI_celular', 'mpi.notificar', 'mpi.MPI_Unidad', 'mpi.MPI_KMEntrada', 'mpi.MPI_FechaCreacion', 'mpi.MPI_Estado', DB::raw('CONCAT(p.name) as personal'));

                if ($request->filled('fecha_inicio')) {
                    $data->whereDate('MPI_FechaCreacion', '>=', $request->fecha_inicio);
                }

                if ($request->filled('fecha_fin')) {
                    $data->whereDate('MPI_FechaCreacion', '<=', $request->fecha_fin);
                }

                if ($request->filled('estado')) {
                    $data->where('MPI_Estado', $request->estado);
                }

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('estado', function ($row) {
                        if ($row->MPI_Estado == 'APROBADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-success">' . $row->MPI_Estado . '</button>';
                        } else if ($row->MPI_Estado == 'PENDIENTE') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-warning">' . $row->MPI_Estado . '</button>';
                        } else if ($row->MPI_Estado == 'OBSERVADO') {
                            $btn = '<button type="button" class="btn btn-sm btn-outline-danger">' . $row->MPI_Estado . '</button>';
                        }
                        return $btn;
                    })
                    ->addColumn('action1', function ($row) {
                        $btn = '<a href="/tenant/mantenimientos/preventivoinyectada/' . $row->MPI_Id . '/edit" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoPreventivoInyectadas"><i class="fa fa-edit"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMantenimientoPreventivoInyectadas"><i class="fa fa-trash"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/tenant/mantenimientos/preventivoinyectada/' . $row->MPI_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('action4', function ($row) {
                        if ($row->MPI_Estado == 'APROBADO') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" title="Activar" class="btn btn-info btn-sm activarMantenimientoPreventivoInyectadas"><i class="fa fa-check"></i></a>';
                        } else if ($row->MPI_Estado == 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" title="Aprobar y Notificar" class="btn btn-success btn-sm aprobarMantenimientoPreventivoInyectadas"><i class="fa fa-check"></i></a>';
                        }
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        if ($row->notificar == 1) {
                            $btn = $row->MPI_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MPI_celular . '?text=Hola%20quiero%20informarte" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" title="Notificar" class="btn btn-success btn-sm"><i class="fab fa-whatsapp"></i></a>';
                        } else {
                            $btn = $row->MPI_celular . ' <a target="_blank" href="https://wa.me/51' . $row->MPI_celular . '?text=Hola%20quiero%20informarte"  data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" title="No Notificar" class="btn btn-danger btn-sm "><i class="fab fa-whatsapp"></i></a>';
                        }
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'action4', 'estado', 'celular'])
                    ->make(true);
            } else {
                $data = DB::table('mantenimiento_preventivo_inyectada as mpi')
                    ->join('users as p', 'p.id', '=', 'mpi.PER_Id')
                    ->select('mpi.MPI_Id', 'mpi.MPI_Placa', 'mpi.MPI_Propietario', 'mpi.MPI_celular', 'mpi.notificar', 'mpi.MPI_Unidad', 'mpi.MPI_KMEntrada', 'mpi.MPI_FechaCreacion', 'mpi.MPI_Estado', DB::raw('CONCAT(p.name) as personal'))
                    ->where('mpi.PER_Id', '=', $idpersonal)
                    ->get();

                return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action1', function ($row) {
                        if ($row->MPI_Estado === 'PENDIENTE') {
                            $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMantenimientoPreventivoInyectadas"><i class="fa fa-edit"></i></a>';
                        } else {
                            $btn = '';
                        }
                        return $btn;
                    })
                    ->addColumn('action2', function ($row) {
                        return '';
                    })
                    ->addColumn('action3', function ($row) {
                        $btn = '<a  target="_blank" href="/mantenimientos/preventivoinyectada/' . $row->MPI_Id . '/pdf" data-toggle="tooltip"  data-id="' . $row->MPI_Id . '" data-original-title="Pdf" class="btn btn-danger btn-sm "><i class="fas fa-file-pdf"></i></a>';
                        return $btn;
                    })
                    ->addColumn('celular', function ($row) {
                        $btn = $row->MPI_celular;
                        return $btn;
                    })
                    ->rawColumns(['action1', 'action2', 'action3', 'celular'])
                    ->make(true);
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.preventivo.inyectadas.index');
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


        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.preventivo.inyectadas.create', ["admin" => $rolAdmin, "personal" => $personal]);
    }
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $idusu = Auth::user()->id;

            $mtto_preventivo_inyectadas = new MantenimientoPreventivoInyectada;
            $mtto_preventivo_inyectadas->MPI_Placa = $request->get('MPI_Placa');
            $mtto_preventivo_inyectadas->MPI_Propietario = $request->get('MPI_Propietario');
            $mtto_preventivo_inyectadas->MPI_celular = $request->get('MPI_celular');
            $mtto_preventivo_inyectadas->MPI_Unidad = $request->get('MPI_Unidad');
            $mtto_preventivo_inyectadas->MPI_KMEntrada = $request->get('MPI_KMEntrada');
            $mtto_preventivo_inyectadas->MPI_DetalleIngreso = $request->get('MPI_DetalleIngreso');
            $mtto_preventivo_inyectadas->MPI_DetalleObservacion = $request->get('MPI_DetalleObservacion');
            $mtto_preventivo_inyectadas->MPI_Det1=$request->get('MPI_Det1')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det1Informacion=$request->get('MPI_Det1Informacion');
            $mtto_preventivo_inyectadas->MPI_Det2=$request->get('MPI_Det2')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det3=$request->get('MPI_Det3')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det4=$request->get('MPI_Det4')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det5=$request->get('MPI_Det5')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det6=$request->get('MPI_Det6')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det7=$request->get('MPI_Det7')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det7Admision=$request->get('MPI_Det7Admision');
            $mtto_preventivo_inyectadas->MPI_Det7Escape=$request->get('MPI_Det7Escape');
            $mtto_preventivo_inyectadas->MPI_Det8=$request->get('MPI_Det8')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det8Medida=$request->get('MPI_Det8Medida');
            $mtto_preventivo_inyectadas->MPI_Det9=$request->get('MPI_Det9')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det10=$request->get('MPI_Det10')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det11=$request->get('MPI_Det11')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det12=$request->get('MPI_Det12')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det13=$request->get('MPI_Det13')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det14=$request->get('MPI_Det14')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det15=$request->get('MPI_Det15');
            $mtto_preventivo_inyectadas->MPI_Det16=$request->get('MPI_Det16');
            $mtto_preventivo_inyectadas->MPI_Det17=$request->get('MPI_Det17')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det17Ventilador=$request->get('MPI_Det17Ventilador');
            $mtto_preventivo_inyectadas->MPI_Det18=$request->get('MPI_Det18')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det19=$request->get('MPI_Det19')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det19Vida=$request->get('MPI_Det19Vida');
            $mtto_preventivo_inyectadas->MPI_Det19Carga=$request->get('MPI_Det19Carga');
            $mtto_preventivo_inyectadas->MPI_Det19Arranque=$request->get('MPI_Det19Arranque');
            $mtto_preventivo_inyectadas->MPI_Det20=$request->get('MPI_Det20')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_DetalleRealizado = $request->get('MPI_DetalleRealizado');
            $mtto_preventivo_inyectadas->MPI_CorrecionObservacion = $request->get('MPI_CorrecionObservacion');
            $mtto_preventivo_inyectadas->MPI_ProximoCambioAceite = $request->get('MPI_ProximoCambioAceite');
            $mtto_preventivo_inyectadas->MPI_ProximoServicio = $request->get('MPI_ProximoServicio');
            $mtto_preventivo_inyectadas->MPI_FechaCreacion = $mytime->toDateTimeString();
            $mtto_preventivo_inyectadas->MPI_FechaEdicion = $mytime->toDateTimeString();
            $mtto_preventivo_inyectadas->MPI_UsuarioCreacion = $idusu;
            $mtto_preventivo_inyectadas->MPI_UsuarioEditado = $idusu;
            $mtto_preventivo_inyectadas->PER_Id = $request->get('USU_Id');
            $mtto_preventivo_inyectadas->save();

            $MPID_Descripcion = $request->get('MPID_Descripcion');
            $MPI_Precio = $request->get('MPI_Precio');
            if ($MPID_Descripcion) {
                $cont = 0;
                while ($cont < count($MPID_Descripcion)) {
                    $mtto_det_reemplazo = new MpiDetalleReemplazo;
                    $mtto_det_reemplazo->MPI_Id = $mtto_preventivo_inyectadas->MPI_Id;
                    $mtto_det_reemplazo->MPID_Descripcion = $MPID_Descripcion[$cont];
                    $mtto_det_reemplazo->MPID_Item = $cont + 1;
                    $mtto_det_reemplazo->MPI_Precio = $MPI_Precio[$cont];
                    $mtto_det_reemplazo->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => 'Mantenimiento Preventivo Inyectadas Registrado Exitosamente.', 'id' => $mtto_preventivo_inyectadas->MPI_Id]);
    }

    // EDITAR

    public function edit(string $id)
    {
        $datos = DB::table('mantenimiento_preventivo_inyectada as mpi')
            ->join('users as u', 'u.id', '=', 'mpi.PER_Id')
            ->select('mpi.*', DB::raw('CONCAT(u.name) as personal'))
            ->where('MPI_Id', '=', $id)
            ->first();
        $detalle = DB::table('mpi_detalle_reemplazo')
            ->where('MPI_Id', '=', $id)
            ->get();
        $imagenes = DB::table('mpi_imagen')
            ->where('MPI_Id', '=', $id)
            ->get();

        $roles = Auth::user()->getRoleNames();

        $rolAdmin = false;
        if ($roles->contains('Admin') || $roles->contains('Gerente')) {
            $rolAdmin = true;
        }
        $personal = User::select('id', 'name')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.mantenimientos.preventivo.inyectadas.edit', [
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

        $totalImagenes = MpiImagen::where('MPI_Id', $mantenimientoId)->count();
        if ($totalImagenes >= tenant('max_images')) {
            return response()->json([
                'status' => 0,
                'msg' => 'Tu plan alcanzó el límite de imágenes.'
            ], 422);
        }
        $ultimoItem = MpiImagen::where('MPI_Id',$mantenimientoId)->max('MPII_Item');
        $item = $ultimoItem ? $ultimoItem + 1 : 1;

        $file = $request->file('file');
        $nombreArchivo = Str::uuid() . '.webp';

        $basePath = $tipoNegocio . '/' . $tenantId . '/mantenimiento/preventivo_inyectadas/' . $mantenimientoId;

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

        $mpiImagen = new MpiImagen();
        $mpiImagen->MPI_Id = $mantenimientoId;
        $mpiImagen->MPII_Item = $item;
        $mpiImagen->MPII_url = $rutaOriginal;
        $mpiImagen->MPII_Thumb = $rutaThumb;
        $mpiImagen->MPII_Nombre = $nombreArchivo;
        $mpiImagen->MPII_Peso = $tamañoFormateado;
        $mpiImagen->save();

        $datos = MpiImagen::where('MPI_Id',$mantenimientoId)->get();

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
		$datos = DB::table('mantenimiento_preventivo_inyectada as mpi')
				->join('users as u','u.id','=','mpi.PER_Id')
				->select('mpi.*',DB::raw('CONCAT(u.name) as personal'))
				->where('MPI_Id','=',$id)
				->first();

        $detalle_reemplazo = DB::table('mpi_detalle_reemplazo')
				->where('MPI_Id','=',$id)
				->get();

        $total_detalle = 0;
        foreach ($detalle_reemplazo as $dr) {
            $total_detalle =round($total_detalle + $dr->MPI_Precio, 2) ; 
        }

        $imagenes = DB::table('mpi_imagen')
                ->where('MPI_Id','=',$id)
                ->get();

        $url = URL::to('');
		$empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        
		$pdf   = Pdf::loadView('/tenant_' . tenant('tipo_negocio') . '/mantenimientos/preventivo/inyectadas/pdf', [
			"mttoPreventivo"=>$datos,
			"detalle"=>$detalle_reemplazo,
			"imagenes"=>$imagenes,
			"url"=>$url,
            "total_detalle"=>$total_detalle,
            "empresa"=>$empresa
		])->setOptions(['defaultFont' => 'sans-serif',
        'chroot'  => public_path('dist/img'), 'isRemoteEnabled' => true]);

		return $pdf->stream('mantenimiento-preventivo-inyectada-' . tenant('id') . '.pdf');
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

            $mtto_preventivo_inyectadas = MantenimientoPreventivoInyectada::findOrFail($id);
            $mtto_preventivo_inyectadas->MPI_Placa = $request->get('MPI_Placa');
            $mtto_preventivo_inyectadas->MPI_Propietario = $request->get('MPI_Propietario');
            $mtto_preventivo_inyectadas->MPI_celular = $request->get('MPI_celular');
            $mtto_preventivo_inyectadas->MPI_Unidad = $request->get('MPI_Unidad');
            $mtto_preventivo_inyectadas->MPI_KMEntrada = $request->get('MPI_KMEntrada');
            $mtto_preventivo_inyectadas->MPI_DetalleIngreso = $request->get('MPI_DetalleIngreso');
            $mtto_preventivo_inyectadas->MPI_DetalleObservacion = $request->get('MPI_DetalleObservacion');
            $mtto_preventivo_inyectadas->MPI_Det1=$request->get('MPI_Det1')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det1Informacion=$request->get('MPI_Det1Informacion');
            $mtto_preventivo_inyectadas->MPI_Det2=$request->get('MPI_Det2')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det3=$request->get('MPI_Det3')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det4=$request->get('MPI_Det4')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det5=$request->get('MPI_Det5')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det6=$request->get('MPI_Det6')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det7=$request->get('MPI_Det7')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det7Admision=$request->get('MPI_Det7Admision');
            $mtto_preventivo_inyectadas->MPI_Det7Escape=$request->get('MPI_Det7Escape');
            $mtto_preventivo_inyectadas->MPI_Det8=$request->get('MPI_Det8')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det8Medida=$request->get('MPI_Det8Medida');
            $mtto_preventivo_inyectadas->MPI_Det9=$request->get('MPI_Det9')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det10=$request->get('MPI_Det10')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det11=$request->get('MPI_Det11')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det12=$request->get('MPI_Det12')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det13=$request->get('MPI_Det13')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det14=$request->get('MPI_Det14')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det15=$request->get('MPI_Det15');
            $mtto_preventivo_inyectadas->MPI_Det16=$request->get('MPI_Det16');
            $mtto_preventivo_inyectadas->MPI_Det17=$request->get('MPI_Det17')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det17Ventilador=$request->get('MPI_Det17Ventilador');
            $mtto_preventivo_inyectadas->MPI_Det18=$request->get('MPI_Det18')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det19=$request->get('MPI_Det19')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_Det19Vida=$request->get('MPI_Det19Vida');
            $mtto_preventivo_inyectadas->MPI_Det19Carga=$request->get('MPI_Det19Carga');
            $mtto_preventivo_inyectadas->MPI_Det19Arranque=$request->get('MPI_Det19Arranque');
            $mtto_preventivo_inyectadas->MPI_Det20=$request->get('MPI_Det20')?"SI":"NO";
            $mtto_preventivo_inyectadas->MPI_DetalleRealizado = $request->get('MPI_DetalleRealizado');
            $mtto_preventivo_inyectadas->MPI_CorrecionObservacion = $request->get('MPI_CorrecionObservacion');
            $mtto_preventivo_inyectadas->MPI_ProximoCambioAceite = $request->get('MPI_ProximoCambioAceite');
            $mtto_preventivo_inyectadas->MPI_ProximoServicio = $request->get('MPI_ProximoServicio');
            $mtto_preventivo_inyectadas->MPI_FechaEdicion = $mytime->toDateTimeString();
            $mtto_preventivo_inyectadas->MPI_UsuarioEditado = $idusu;
            $mtto_preventivo_inyectadas->PER_Id = $request->get('USU_Id');
            if ($rolAdmin) {
                $mtto_preventivo_inyectadas->MPI_Estado = 'APROBADO';
            }
            if($request->notificar){
                $mtto_preventivo_inyectadas->notificar = 1;
            }
            $mtto_preventivo_inyectadas->update();


            DB::table('mpi_detalle_reemplazo')
                ->where('MPI_Id', $mtto_preventivo_inyectadas->MPI_Id)
                ->delete();

            $MPID_Descripcion = $request->get('MPID_Descripcion');
            $MPI_Precio = $request->get('MPI_Precio');
            if ($MPID_Descripcion) {
                $cont = 0;
                while ($cont < count($MPID_Descripcion)) {
                    $correctivoresponsable = new MpiDetalleReemplazo;
                    $correctivoresponsable->MPI_Id = $mtto_preventivo_inyectadas->MPI_Id;
                    $correctivoresponsable->MPID_Descripcion = $MPID_Descripcion[$cont];
                    $correctivoresponsable->MPID_Item = $cont + 1;
                    $correctivoresponsable->MPI_Precio = $MPI_Precio[$cont];
                    $correctivoresponsable->save();

                    $cont = $cont + 1;
                }
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
        }

        return response()->json(['success' => true, "message" => 'Mantenimiento Preventivo Inyectadas Editado Exitosamente.']);
    }

    public function actualizarestado(Request $request,$id)
	{
        try {
            DB::beginTransaction();
            $mtto_preventivo_inyectadas=MantenimientoPreventivoInyectada::findOrFail($id);
            if($request->notificar == 1){
                $mtto_preventivo_inyectadas->MPI_Estado="PENDIENTE";
                $mtto_preventivo_inyectadas->notificar=0;
            }
            if($request->estado == "APROBADO"){
                $mtto_preventivo_inyectadas->MPI_Estado="APROBADO";
                if($request->notificar == 2){
                    $mtto_preventivo_inyectadas->notificar=0;
                }else{
                    $mtto_preventivo_inyectadas->notificar=1;
                }
            }
            if($request->observacion){
                $mtto_preventivo_inyectadas->observacion=$request->observacion ;
            }

            if($request->respuesta){
                $mtto_preventivo_inyectadas->respuesta=$request->respuesta ;
            }
            
            $mtto_preventivo_inyectadas->update();

            DB::commit();

        } catch (Exception $e)
        {
            dd($e);
            DB::rollback();
        }
        
        if($request->notificar == 1){
            return response()->json(['success' => true, 'message' => 'Mantenimiento Se Activo Correctamente.']);
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
            $imagenDelete = MpiImagen::where('MPI_Id', $id)
                ->where('MPII_Item', $item)
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
            $rutaOriginal = str_replace('/storage/', '', $imagenDelete->MPII_url);
            if (Storage::disk('public')->exists($rutaOriginal)) {
                Storage::disk('public')->delete($rutaOriginal);
            }
            /* ELIMINAR THUMB */

            $rutaThumb = str_replace('/storage/', '', $imagenDelete->MPII_Thumb);
            if (Storage::disk('public')->exists($rutaThumb)) {
                Storage::disk('public')->delete($rutaThumb);
            }

            /* ELIMINAR REGISTRO */
            MpiImagen::where('MPI_Id', $id)
            ->where('MPII_Item', $item)
            ->delete();

            /* RECARGAR IMAGENES */
            $datos = MpiImagen::where('MPI_Id', $id)->get();

            return response()->json(['success' => true,'message' => 'Eliminado Correctamente', 'data'=> $datos]);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function activar(string $id)
    {
        try {

            $mtto_preventivo_inyectadas = MantenimientoPreventivoInyectada::find($id);

            if (!$mtto_preventivo_inyectadas) {
                return response()->json(['success' => false, 'message' => 'MantenimientoPreventivoInyectada no encontrado.'], 404);
            }

            $mtto_preventivo_inyectadas->MPI_Estado="PENDIENTE";
            $mtto_preventivo_inyectadas->notificar=0;
            $mtto_preventivo_inyectadas->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoPreventivoInyectada activado exitosamente.']);
        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El mantenimiento activdad variada falló al activarse.'], 500);
        }
    }

    // ELIMINAR

    public function destroy($id)
    {
        DB::beginTransaction();
        try {

            $imagenes = MpiImagen::where('MPI_Id', $id)->get();
            foreach ($imagenes as $img) {
                /* ORIGINAL */
                if (!empty($img->MPII_url)) {
                    $rutaOriginal = str_replace('/storage/', '', $img->MPII_url);
                    if (Storage::disk('public')->exists($rutaOriginal)) {
                        Storage::disk('public')->delete($rutaOriginal);
                    }
                }
                /* THUMB */
                if (!empty($img->MPII_Thumb)) {
                    $rutaThumb = str_replace('/storage/', '', $img->MPII_Thumb);
                    if (Storage::disk('public')->exists($rutaThumb)) {
                        Storage::disk('public')->delete($rutaThumb);
                    }
                }
            }

            /*  ELIMINAR DETALLES */

            DB::table('mpi_detalle_reemplazo')
                ->where('MPI_Id', $id)
                ->delete();

            /* ELIMINAR IMAGENES */
            DB::table('mpi_imagen')
                ->where('MPI_Id', $id)
                ->delete();

            /*  ELIMINAR PRINCIPAL */

            DB::table('mantenimiento_preventivo_inyectada')
                ->where('MPI_Id', $id)
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
