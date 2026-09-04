<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Producto;
use App\Models\Tenant\Traslado;
use App\Models\Tenant\TrasladoDetalle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Traslado de stock entre sedes/almacenes: descuenta el stock de cada
 * producto en la sede origen (FIFO, igual que una venta) y lo ingresa como
 * un lote nuevo en la sede destino.
 */
class TrasladoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('traslado as t')
                ->join('almacen as ao', 't.ALM_OrigenId', '=', 'ao.ALM_Id')
                ->join('almacen as ad', 't.ALM_DestinoId', '=', 'ad.ALM_Id')
                ->join('users as u', 't.USU_Id', '=', 'u.id')
                ->select(
                    't.TRA_Id',
                    't.TRA_Observacion',
                    't.created_at',
                    'ao.ALM_NombreAlmacen as origen',
                    'ad.ALM_NombreAlmacen as destino',
                    'u.name as usuario',
                    DB::raw('(SELECT COUNT(*) FROM traslado_detalle td WHERE td.TRA_Id = t.TRA_Id) as items')
                )
                ->orderBy('t.TRA_Id', 'desc')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->TRA_Id . '" data-original-title="Ver" class="btn btn-info btn-sm verTraslado"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $almacenes = DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.inventario.traslado.index', compact('almacenes'));
    }

    /**
     * Productos con stock disponible en el almacen indicado, para el
     * selector del formulario de traslado.
     */
    public function stockPorAlmacen(Request $request)
    {
        $request->validate(['ALM_Id' => 'required|integer|exists:almacen,ALM_Id']);

        $productos = DB::table('producto as p')
            ->join('lote as lt', 'lt.PRO_Id', '=', 'p.PRO_Id')
            ->select('p.PRO_Id', 'p.PRO_Nombre', DB::raw('SUM(lt.LOT_CantidadReal) as stock'))
            ->where('lt.ALM_Id', $request->ALM_Id)
            ->where('p.PRO_Status', 1)
            ->when($request->filled('search'), fn ($q) => $q->where('p.PRO_Nombre', 'like', '%' . $request->search . '%'))
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre')
            ->havingRaw('SUM(lt.LOT_CantidadReal) > 0')
            ->orderBy('p.PRO_Nombre')
            ->limit(30)
            ->get();

        return response()->json($productos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'ALM_OrigenId'  => 'required|integer|exists:almacen,ALM_Id',
            'ALM_DestinoId' => 'required|integer|different:ALM_OrigenId|exists:almacen,ALM_Id',
            'PRO_Id'        => 'required|array|min:1',
            'PRO_Id.*'      => 'required|integer|exists:producto,PRO_Id',
            'TRD_Cantidad'  => 'required|array|min:1',
            'TRD_Cantidad.*' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $proIds = $request->get('PRO_Id');
            $cantidades = $request->get('TRD_Cantidad');

            if (count($proIds) !== count($cantidades)) {
                throw new Exception('La lista de productos y cantidades no coincide.');
            }

            $traslado = new Traslado();
            $traslado->ALM_OrigenId = $request->ALM_OrigenId;
            $traslado->ALM_DestinoId = $request->ALM_DestinoId;
            $traslado->USU_Id = Auth::id();
            $traslado->TRA_Observacion = $request->TRA_Observacion;
            $traslado->save();

            foreach ($proIds as $i => $proId) {
                $cantidad = (float) $cantidades[$i];

                // Descuenta de la sede origen, lote por lote (FIFO), igual
                // que una venta. Sin "permitir sin stock": un traslado no
                // puede dejar la sede origen en negativo.
                VentaController::ReducirStock($proId, $cantidad, $request->ALM_OrigenId, false);

                $producto = Producto::findOrFail($proId);

                $loteDestinoId = DB::table('lote')->insertGetId([
                    'ALM_Id' => $request->ALM_DestinoId,
                    'PRO_Id' => $proId,
                    'LOT_TipoIngreso' => 'TRASLADO',
                    'LOT_IdIngreso' => $traslado->TRA_Id,
                    'LOT_CantidadReal' => $cantidad,
                    'LOT_CantidadIngreso' => $cantidad,
                    'LOT_PrecioCompra' => $producto->PRO_PrecioCompra,
                    'LOT_PrecioVenta' => $producto->PRO_PrecioVenta,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $detalle = new TrasladoDetalle();
                $detalle->TRA_Id = $traslado->TRA_Id;
                $detalle->PRO_Id = $proId;
                $detalle->LOT_IdDestino = $loteDestinoId;
                $detalle->TRD_Cantidad = $cantidad;
                $detalle->save();
            }

            DB::commit();

            return response()->json(['success' => 'Traslado registrado exitosamente!', 'TRA_Id' => $traslado->TRA_Id]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $traslado = DB::table('traslado as t')
            ->join('almacen as ao', 't.ALM_OrigenId', '=', 'ao.ALM_Id')
            ->join('almacen as ad', 't.ALM_DestinoId', '=', 'ad.ALM_Id')
            ->join('users as u', 't.USU_Id', '=', 'u.id')
            ->select(
                't.TRA_Id',
                't.TRA_Observacion',
                't.created_at',
                'ao.ALM_NombreAlmacen as origen',
                'ad.ALM_NombreAlmacen as destino',
                'u.name as usuario'
            )
            ->where('t.TRA_Id', $id)
            ->first();

        if (!$traslado) {
            return response()->json(['error' => 'Traslado no encontrado.'], 404);
        }

        $detalle = DB::table('traslado_detalle as td')
            ->join('producto as p', 'td.PRO_Id', '=', 'p.PRO_Id')
            ->select('p.PRO_Nombre', 'td.TRD_Cantidad')
            ->where('td.TRA_Id', $id)
            ->get();

        return response()->json(['traslado' => $traslado, 'detalle' => $detalle]);
    }
}
