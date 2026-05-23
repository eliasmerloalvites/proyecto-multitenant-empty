<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('producto as pd')
                ->join('categoria as ct', 'pd.CAT_Id', '=', 'ct.CAT_Id')
                ->select('pd.*', 'ct.CAT_Nombre')
                ->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->PRO_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editProducto" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->PRO_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteProducto"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->PRO_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeProducto"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action1', 'action2', 'action3'])
                ->make(true);
        }

        $categorias = DB::table('categoria')->get();
        return view('tenant_'.tenant('tipo_negocio').'.inventario.producto.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            $query = Producto::where('PRO_Nombre', '=', $request->get('PRO_Nombre'))->get();
            if ($query->count() != 0) //si lo encuentra, osea si no esta vacia
            {
                return response()->json(['error' => 'Producto ya registrado'], 401);
            } else {
                $producto = new Producto();
                $producto->PRO_Nombre = $request->PRO_Nombre;
                $producto->PRO_Descripcion = $request->PRO_Descripcion;
                $producto->PRO_PrecioCompra = $request->PRO_PrecioCompra;
                $producto->PRO_PrecioVenta = $request->PRO_PrecioVenta;
                $producto->PRO_Marca = $request->PRO_Marca;
                $producto->PRO_Status = $request->PRO_Status ?? 1;
                $producto->CAT_Id = $request->CAT_Id;
                $producto->save();

                $ubicacionNegocio = "";
                $id = null;
                if (tenant()) {
                    // Estás en el contexto de un TENANT
                    $id = tenant('id');
                    $ubicacionNegocio = tenant('tipo_negocio') ;
                }
                $path = public_path('storage/' .$ubicacionNegocio .'/' .$id .'/archivos/producto/');

                $file = $request->file('file');
                if($file){
                    // Crear carpeta si no existe
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    $extension = $file->getClientOriginalExtension();
                    $fileName = $producto->PRO_Id . '.' . $extension;
                    $file->move($path, $fileName);
                    
                    $updateproducto = DB::table('producto')
                    ->where('PRO_Id', $producto->PRO_Id)
                    ->update(['PRO_Imagen' => $fileName]);

                }
            }
            DB::commit();
        } catch (Exception $e)
        {
            DB::rollback();
        }
        return response()->json(['success' => 'Producto Registrado Exitosamente!', compact('producto')]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $producto = DB::table('producto as p')
			->join('categoria as c','p.CAT_Id','=','c.CAT_Id')
            ->select('p.*','c.CAT_Nombre')
            ->where('PRO_Id',$id)
            ->first();
        $imagen="/images/imagen_default.png";
        if($producto->PRO_Imagen){
            $id = tenant('id');
            $ubicacionNegocio = tenant('tipo_negocio') ;
            $imagen = '/storage/' .$ubicacionNegocio .'/' .$id .'/archivos/producto/'.$producto->PRO_Imagen.'?' . uniqid();
        }
        return response()->json(['data' => $producto,'imagen'=> $imagen]);
    }
    
    public function controlinventario(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('producto as pd')
                ->join('categoria as ct', 'pd.CAT_Id', '=', 'ct.CAT_Id')
                ->join('lote as lt','pd.PRO_Id','=','lt.PRO_Id')
                ->select('pd.PRO_Id', 'pd.PRO_Nombre', 'pd.PRO_PrecioVenta', 'pd.PRO_PrecioCompra', 'ct.CAT_Nombre', DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total'))
                ->groupBy('pd.PRO_Id', 'pd.PRO_Nombre', 'pd.PRO_PrecioVenta', 'pd.PRO_PrecioCompra', 'ct.CAT_Nombre')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('lotes', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->PRO_Id . '" data-original-title="Lotes" class="btn btn-warning btn-sm lotesProducto" ><i class="fas fa-layer-group"></i></a>';
                    return $btn;
                })
                ->addColumn('kardex', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->PRO_Id . '" data-original-title="Kardex" class="btn btn-success btn-sm kardexProducto"><i class="fas fa-chart-line"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->PRO_Id . '" data-original-title="Ver" class="btn btn-info btn-sm eyeProducto"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->rawColumns(['lotes', 'kardex', 'action3'])
                ->make(true);
        }

        $categorias = DB::table('categoria')->get();
        return view('tenant_'.tenant('tipo_negocio').'.inventario.producto.controlinventario', compact('categorias'));
    }

    public function lotes(Request $request,string $id)
    {
        $producto = DB::table('producto as p')
            ->join('categoria as c','p.CAT_Id','=','c.CAT_Id')
            ->join('lote as lt','p.PRO_Id','=','lt.PRO_Id')
            ->select('p.PRO_Id', 'p.PRO_Nombre','c.CAT_Nombre', DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total'))
            ->where('p.PRO_Id',$id)
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'c.CAT_Nombre')
            ->first();

        $lotes = DB::table('lote as lt')
            ->join('producto as pd', 'lt.PRO_Id', '=', 'pd.PRO_Id')
            ->select('lt.*', 'pd.PRO_Nombre')
            ->where('lt.PRO_Id', $id)
            ->orderBy('lt.created_at', 'desc')
            ->get();

        return response()->json(['lotes' => $lotes, 'producto' => $producto]);
    }


    public function kardex(Request $request, string $id)
    {
        $fecha_inicio = $request->fecha_inicio;
        $fecha_fin = $request->fecha_fin;
        $tipo = $request->tipo;

        // PRODUCTO
        $producto = DB::table('producto as p')
            ->join('categoria as c', 'p.CAT_Id', '=', 'c.CAT_Id')
            ->join('lote as lt', 'p.PRO_Id', '=', 'lt.PRO_Id')
            ->select('p.PRO_Id', 'p.PRO_Nombre', 'c.CAT_Nombre', DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total'))
            ->where('p.PRO_Id', $id)
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'c.CAT_Nombre')
            ->first();

        /*
        |--------------------------------------------------------------------------
        | QUERIES BASE
        |--------------------------------------------------------------------------
        */

        $EntradasBase = DB::table('lote as lt')
            ->join('compra as cp', 'lt.LOT_IdIngreso', '=', 'cp.COM_Id')
            ->select(
                'cp.COM_Id as id',
                'cp.COM_NumDocumento as documento',
                'cp.created_at as fecha',
                'lt.LOT_Id as lote_id',
                'lt.LOT_CantidadIngreso as entrada',
                DB::raw('0 as salida'),
                DB::raw('"Entrada" as tipo')
            )
            ->where('lt.PRO_Id', $id)
            ->where('lt.LOT_TipoIngreso', 'COMPRA');

        $SalidasBase = DB::table('venta as v')
            ->join('documento_venta as dov', 'v.VEN_Id', '=', 'dov.VEN_Id')
            ->join('detalle_venta as dv', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->select(
                'v.VEN_Id as id',
                DB::raw('CONCAT(dov.DOV_Serie, "-", dov.DOV_Numero) as documento'),
                'v.created_at as fecha',
                'dv.LOT_Id as lote_id',
                DB::raw('0 as entrada'),
                'dv.DEV_Cantidad as salida',
                DB::raw('"Salida" as tipo')
            )
            ->where('dv.PRO_Id', $id);

        /*
        |--------------------------------------------------------------------------
        | CLONAR QUERIES
        |--------------------------------------------------------------------------
        */

        $Entradas = clone $EntradasBase;
        $Salidas = clone $SalidasBase;

        /*
        |--------------------------------------------------------------------------
        | STOCK INICIAL
        |--------------------------------------------------------------------------
        */

        $stock = 0;

        if ($fecha_inicio) {

            // ENTRADAS PREVIAS
            $entradasPrevias = (clone $EntradasBase)
                ->where('cp.created_at','<',$fecha_inicio . ' 00:00:00')
                ->sum('lt.LOT_CantidadIngreso');

            // SALIDAS PREVIAS
            $salidasPrevias = (clone $SalidasBase)
                ->where('v.created_at','<',$fecha_inicio . ' 00:00:00')
                ->sum('dv.DEV_Cantidad');

            // STOCK INICIAL
            $stock = $entradasPrevias - $salidasPrevias;

            // FILTRO FECHA INICIO
            $Entradas->where('cp.created_at','>=',$fecha_inicio . ' 00:00:00');
            $Salidas->where('v.created_at','>=',$fecha_inicio . ' 00:00:00');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO FECHA FIN
        |--------------------------------------------------------------------------
        */

        if ($fecha_fin) {
            $Entradas->where('cp.created_at','<=',$fecha_fin . ' 23:59:59');
            $Salidas->where('v.created_at','<=',$fecha_fin . ' 23:59:59');
        }

        /*
        |--------------------------------------------------------------------------
        | FILTRO TIPO
        |--------------------------------------------------------------------------
        */

        if ($tipo == 'Entrada') {
            $kardex = $Entradas->get();
        } elseif ($tipo == 'Salida') {
            $kardex = $Salidas->get();
        } else {
            $kardex = $Entradas
                ->unionAll($Salidas)
                ->get();
        }

        /*
        |--------------------------------------------------------------------------
        | ORDENAR ASC PARA CALCULAR STOCK
        |--------------------------------------------------------------------------
        */

        $kardex = $kardex
            ->sortBy('fecha')
            ->values();

        /*
        |--------------------------------------------------------------------------
        | CALCULAR STOCK ACUMULADO
        |--------------------------------------------------------------------------
        */

        $kardex = $kardex->map(function ($item) use (&$stock) {

            // STOCK ANTES DEL MOVIMIENTO
            $item->stock_inicial = $stock;

            // CALCULAR NUEVO STOCK
            $stock += $item->entrada;
            $stock -= $item->salida;

            // STOCK FINAL
            $item->stock_final = $stock;

            return $item;
        });


        /*
        |--------------------------------------------------------------------------
        | ORDEN FINAL DESCENDENTE
        |--------------------------------------------------------------------------
        */

        $kardex = $kardex->sortByDesc('fecha')->values();

        return response()->json([
            'kardex' => $kardex,
            'producto' => $producto
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $producto = Producto::find($id);
        return response()->json(['data' => $producto]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            DB::beginTransaction();

            $producto = Producto::find($id);
            $producto->PRO_Nombre = $request->PRO_Nombre;
            $producto->PRO_Descripcion = $request->PRO_Descripcion;
            $producto->PRO_PrecioCompra = $request->PRO_PrecioCompra;
            $producto->PRO_PrecioVenta = $request->PRO_PrecioVenta;
            $producto->PRO_Marca = $request->PRO_Marca;
            $producto->PRO_Status = $request->PRO_Status ?? 1;
            $producto->CAT_Id = $request->CAT_Id;
            $producto->update();

            $ubicacionNegocio = "";
            $id = null;
            if (tenant()) {
                // Estás en el contexto de un TENANT
                $id = tenant('id');
                $ubicacionNegocio = tenant('tipo_negocio') ;
            }

            $path = public_path('storage/' .$ubicacionNegocio .'/' .$id .'/archivos/producto/');
            $file = $request->file('file');
            if($file){
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $producto->PRO_Id . '.' . $extension;
                $file->move($path, $fileName);
                
                $producto = DB::table('producto')
                ->where('PRO_Id', $producto->PRO_Id)
                ->update(['PRO_Imagen' => $fileName]);
            }

            DB::commit();
        } catch (Exception $e)
        {
            DB::rollback();
        }

        return response()->json(['success' => 'Producto Editado Exitosamente.',compact('producto')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $producto = Producto::find($id);
        $producto->delete();
        return response()->json(['success' => 'Producto Eliminado Exitosamente.']);
    }
}
