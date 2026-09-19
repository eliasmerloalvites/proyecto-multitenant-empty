<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Ajuste;
use App\Models\Tenant\AjusteDetalle;
use App\Models\Tenant\Producto;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Ajuste de inventario: corrige el stock de un producto en una sede sin
 * que venga de una compra, venta o traslado (merma, rotura, vencimiento,
 * diferencia de conteo fisico, etc).
 */
class AjusteController extends Controller
{
    const MOTIVOS = ['MERMA', 'ROTURA', 'VENCIMIENTO', 'CONTEO_FISICO', 'OTRO'];

    /**
     * Guarda de servidor para store(): la base no rechaza nada;
     * TenantTallerMotos\AjusteController la sobreescribe para rechazar
     * Servicios (nunca deben terminar con un lote/stock).
     */
    protected function validarProductosAjustables(array $proIds): void
    {
        //
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('ajuste as a')
                ->join('almacen as al', 'a.ALM_Id', '=', 'al.ALM_Id')
                ->join('users as u', 'a.USU_Id', '=', 'u.id')
                ->select(
                    'a.AJU_Id',
                    'a.AJU_Motivo',
                    'a.AJU_Observacion',
                    'a.created_at',
                    'al.ALM_NombreAlmacen as almacen',
                    'u.name as usuario',
                    DB::raw('(SELECT COUNT(*) FROM ajuste_detalle ad WHERE ad.AJU_Id = a.AJU_Id) as items')
                )
                ->orderBy('a.AJU_Id', 'desc')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->AJU_Id . '" data-original-title="Ver" class="btn btn-info btn-sm verAjuste"><i class="fa fa-eye" aria-hidden="true"></i></a>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $almacenes = DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.inventario.ajuste.index', compact('almacenes'));
    }

    /**
     * Productos activos con su stock actual en el almacen indicado, para el
     * selector del formulario de ajuste. A diferencia del traslado, aqui se
     * listan todos los productos (incluso sin stock), porque un ajuste
     * tambien sirve para ingresar stock encontrado que nunca se registro.
     */
    public function productos(Request $request)
    {
        $request->validate(['ALM_Id' => 'required|integer|exists:almacen,ALM_Id']);

        // PRO_CodigoInterno/PRO_CodigoFabricacion solo existen en tallermoto
        // (ver ProductoController::tenantTieneCodigosProducto()): este
        // controlador es compartido con generico.
        $tieneCodigos = tenant('tipo_negocio') === 'tallermoto';

        $columnas = ['p.PRO_Id', 'p.PRO_Nombre'];
        $agrupar = ['p.PRO_Id', 'p.PRO_Nombre'];
        if ($tieneCodigos) {
            $columnas[] = 'p.PRO_CodigoInterno';
            $columnas[] = 'p.PRO_CodigoFabricacion';
            $agrupar[] = 'p.PRO_CodigoInterno';
            $agrupar[] = 'p.PRO_CodigoFabricacion';
        }

        $productos = DB::table('producto as p')
            ->leftJoin('lote as lt', function ($join) use ($request) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')
                    ->where('lt.ALM_Id', '=', $request->ALM_Id);
            })
            ->select(array_merge($columnas, [DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as stock')]))
            ->where('p.PRO_Status', 1)
            ->when($request->filled('search'), function ($q) use ($request, $tieneCodigos) {
                $busqueda = '%' . $request->search . '%';
                $q->where(function ($qq) use ($busqueda, $tieneCodigos) {
                    $qq->where('p.PRO_Nombre', 'like', $busqueda);
                    if ($tieneCodigos) {
                        $qq->orWhere('p.PRO_CodigoInterno', 'like', $busqueda)
                            ->orWhere('p.PRO_CodigoFabricacion', 'like', $busqueda);
                    }
                });
            })
            ->groupBy($agrupar)
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
            'ALM_Id'         => 'required|integer|exists:almacen,ALM_Id',
            'AJU_Motivo'     => 'required|string|in:' . implode(',', self::MOTIVOS),
            'AJU_Observacion' => 'nullable|string',
            'PRO_Id'         => 'required|array|min:1',
            'PRO_Id.*'       => 'required|integer|exists:producto,PRO_Id',
            'AJD_Tipo'       => 'required|array|min:1',
            'AJD_Tipo.*'     => 'required|string|in:INCREMENTO,DECREMENTO',
            'AJD_Cantidad'   => 'required|array|min:1',
            'AJD_Cantidad.*' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $proIds = $request->get('PRO_Id');
            $tipos = $request->get('AJD_Tipo');
            $cantidades = $request->get('AJD_Cantidad');

            if (count($proIds) !== count($tipos) || count($proIds) !== count($cantidades)) {
                throw new Exception('La lista de productos, tipos y cantidades no coincide.');
            }

            // El selector del formulario ya excluye lo que no se pueda
            // ajustar (ver productos()), pero eso es solo del lado del
            // cliente: esto vuelve a validarlo aqui por si el PRO_Id llega
            // igual por otra via.
            $this->validarProductosAjustables($proIds);

            $ajuste = new Ajuste();
            $ajuste->ALM_Id = $request->ALM_Id;
            $ajuste->USU_Id = Auth::id();
            $ajuste->AJU_Motivo = $request->AJU_Motivo;
            $ajuste->AJU_Observacion = $request->AJU_Observacion;
            $ajuste->save();

            foreach ($proIds as $i => $proId) {
                $tipo = $tipos[$i];
                $cantidad = (float) $cantidades[$i];
                $loteId = null;

                if ($tipo === AjusteDetalle::TIPO_INCREMENTO) {
                    $producto = Producto::findOrFail($proId);

                    $loteId = DB::table('lote')->insertGetId([
                        'ALM_Id' => $request->ALM_Id,
                        'PRO_Id' => $proId,
                        'LOT_TipoIngreso' => 'AJUSTE',
                        'LOT_IdIngreso' => $ajuste->AJU_Id,
                        'LOT_CantidadReal' => $cantidad,
                        'LOT_CantidadIngreso' => $cantidad,
                        'LOT_PrecioCompra' => $producto->PRO_PrecioCompra,
                        'LOT_PrecioVenta' => $producto->PRO_PrecioVenta,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } else {
                    // Descuenta FIFO, igual que una venta o un traslado. Sin
                    // "permitir sin stock": un ajuste no puede dejar la sede
                    // en negativo, solo puede corregir hasta lo que hay.
                    VentaController::ReducirStock($proId, $cantidad, $request->ALM_Id, false);
                }

                $detalle = new AjusteDetalle();
                $detalle->AJU_Id = $ajuste->AJU_Id;
                $detalle->PRO_Id = $proId;
                $detalle->LOT_Id = $loteId;
                $detalle->AJD_Tipo = $tipo;
                $detalle->AJD_Cantidad = $cantidad;
                $detalle->save();
            }

            DB::commit();

            return response()->json(['success' => 'Ajuste registrado exitosamente!', 'AJU_Id' => $ajuste->AJU_Id]);
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
        $ajuste = DB::table('ajuste as a')
            ->join('almacen as al', 'a.ALM_Id', '=', 'al.ALM_Id')
            ->join('users as u', 'a.USU_Id', '=', 'u.id')
            ->select(
                'a.AJU_Id',
                'a.AJU_Motivo',
                'a.AJU_Observacion',
                'a.created_at',
                'al.ALM_NombreAlmacen as almacen',
                'u.name as usuario'
            )
            ->where('a.AJU_Id', $id)
            ->first();

        if (!$ajuste) {
            return response()->json(['error' => 'Ajuste no encontrado.'], 404);
        }

        $detalle = DB::table('ajuste_detalle as ad')
            ->join('producto as p', 'ad.PRO_Id', '=', 'p.PRO_Id')
            ->select('p.PRO_Nombre', 'ad.AJD_Tipo', 'ad.AJD_Cantidad')
            ->where('ad.AJU_Id', $id)
            ->get();

        return response()->json(['ajuste' => $ajuste, 'detalle' => $detalle]);
    }
}
