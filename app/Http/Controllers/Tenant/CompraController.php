<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\Categoria;
use App\Models\Tenant\Compra;
use App\Models\Tenant\DetalleCompra;
use App\Models\Tenant\Lote;
use App\Models\Tenant\MetodoPago;
use App\Models\Tenant\Movimiento;
use App\Models\Tenant\Producto;
use App\Models\Tenant\Proveedor;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DB::table('compra as c')
                ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'c.MEP_Id')
                ->select('c.*','p.PROV_RazonSocial','mp.MEP_Pago')
                ->get();


            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->COM_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editCompra" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->COM_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteCompra"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->COM_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeCompra"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action1', 'action2', 'action3'])
                ->make(true);
        }
        $proveedor = Proveedor::all();
        $metodo_pago = MetodoPago::all();
        return view('tenant_'.tenant('tipo_negocio').'.compras.compra.index', compact('proveedor', 'metodo_pago'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {        
        $proveedor = Proveedor::all();
        $metodo_pago = MetodoPago::all();
        $detalleCompra = DetalleCompra::all();
        $almacen = Almacen::all();
        $producto =  DB::table('producto as p')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.*', 'c.*')
            ->get();

        $categoria = Categoria::all();
        return view('tenant_'.tenant('tipo_negocio').'.compras.compra.create', compact('proveedor', 'metodo_pago', 'detalleCompra', 'almacen', 'producto', 'categoria'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction(); // Inicia una transacción para asegurar que ambos registros se completen

        try {
            $idUsuario = Auth::user()->id;

            // Registrar la compra
            $compra = new Compra();
            $compra->COM_TipoDocumento = $request->COM_TipoDocumento;
            $compra->COM_NumDocumento = $request->COM_NumDocumento;
            $compra->COM_TipoPago = $request->COM_TipoPago;
            $compra->MEP_Id = $request->MEP_Id;
            $compra->PROV_Id = $request->PROV_Id;
            $compra->USU_Id = $idUsuario;
            $compra->COM_Status = 1;
            $compra->save();

            $PRO_Id = $request->get('PRO_Id');
            $DEC_Cantidad = $request->get('DEC_Cantidad');
            $DEC_PrecioUnitario = $request->get('DEC_PrecioUnitario');
            $PrecioUnitarioV = $request->get('DEC_PrecioUnitarioV');
            $idalmacen = $request->get('idalmacen');

            $cont = 0;

            while ($cont < count($PRO_Id)) {
                $detalle = new DetalleCompra();
                $detalle->COM_Id = $compra->COM_Id;
                $detalle->PRO_Id = $PRO_Id[$cont];
                $detalle->ALM_Id = $idalmacen[$cont];
                $detalle->DCOM_Cantidad = $DEC_Cantidad[$cont];
                $detalle->DCOM_PrecioCompra = $DEC_PrecioUnitario[$cont];
                $detalle->DCOM_PrecioVenta = $PrecioUnitarioV[$cont];
                $detalle->DCOM_Item = $cont + 1;
                $detalle->save();

                $lote = new Lote();
                $lote->ALM_Id = $idalmacen[$cont];
                $lote->PRO_Id = $PRO_Id[$cont];
                $lote->LOT_TipoIngreso = "COMPRA";
                $lote->LOT_IdIngreso = $compra->COM_Id;
                $lote->LOT_CantidadReal = $DEC_Cantidad[$cont];
                $lote->LOT_CantidadIngreso = $DEC_Cantidad[$cont];
                $lote->LOT_PrecioCompra = $DEC_PrecioUnitario[$cont];
                $lote->LOT_PrecioVenta = $PrecioUnitarioV[$cont];
                $lote->save();

                //ACTUALIZA TODO LOS PRECIOS DE VENTA DEL ULTIMO LOTE PARA TODOS LOS LOTES POR PRODUCTO
                /* DB::table('lote_producto as lp')
                ->where('lp.PRO_Id','=',$PRO_Id[$cont])
                ->update(['lp.LOT_Precio_Venta_Unit' => $PrecioUnitarioV[$cont]]); */

                $producto = Producto::findOrFail($PRO_Id[$cont]);
                $producto->PRO_PrecioCompra = $DEC_PrecioUnitario[$cont];
                $producto->PRO_PrecioVenta = $PrecioUnitarioV[$cont];
                $producto->update();

                $cont = $cont + 1;
            }
            $movi = new Movimiento();
            $movi->tipo = "Entrada";
            $movi->idcv = $compra->COM_Id;
            $movi->save();

            DB::commit(); // Confirmar la transacción

            return response()->json(['success' => 'Compra y detalles registrados exitosamente!']);
        } catch (\Exception $e) {
            DB::rollBack(); // Revertir la transacción si hay un error
            return response()->json(['error' => 'Error al registrar la compra: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = Compra::with([
            'proveedor',
            'metodo_pago',
            'users',
            'detalle_compra.almacen',
            'detalle_compra.producto.categoria'
        ])->where('compra.COM_Id',$id)->first();
        return response()->json(['compra'=>$data]);
    }

    public function edit(string $id)
    {
        $proveedor = Proveedor::all();
        $metodo_pago = MetodoPago::all();
        $detalleCompra = DetalleCompra::all();
        $almacen = Almacen::all();
        $producto =  DB::table('producto as p')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.*', 'c.*')
            ->get();

        $categoria = Categoria::all();

        $data = Compra::with([
            'proveedor',
            'metodo_pago',
            'users',
            'detalle_compra.almacen',
            'detalle_compra.producto.categoria'
        ])->where('compra.COM_Id',$id)->first();

        return view('gestion.compra.edit', compact('data','proveedor', 'metodo_pago', 'detalleCompra', 'almacen', 'producto', 'categoria'));
    }

    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
