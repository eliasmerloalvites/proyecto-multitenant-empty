<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Services\Inventario\DesempaquetadoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Inventario > Desempaquetar Kits: abre kits para vender sus piezas por
 * separado (ver DesempaquetadoService y la migracion del modulo).
 */
class DesempaquetadoController extends Controller
{
    public function __construct(private DesempaquetadoService $servicio)
    {
        // Las tablas del modulo solo existen en talleres de motos.
        abort_unless(tenant('tipo_negocio') === 'tallermoto', 404);
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('desempaquetado as d')
                ->join('almacen as a', 'a.ALM_Id', '=', 'd.ALM_Id')
                ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
                ->join('users as u', 'u.id', '=', 'd.USU_Id')
                ->select(
                    'd.DES_Id', 'd.DES_Cantidad', 'd.DES_Estado', 'd.created_at',
                    'a.ALM_NombreAlmacen as almacen', 'p.PRO_Nombre as kit', 'u.name as usuario',
                    DB::raw('(SELECT COUNT(*) FROM desempaquetado_detalle x WHERE x.DES_Id = d.DES_Id) as componentes')
                );

            return datatables()::of($data)
                ->addColumn('numero', fn ($r) => self::numero($r->DES_Id))
                ->editColumn('DES_Cantidad', fn ($r) => rtrim(rtrim((string) $r->DES_Cantidad, '0'), '.'))
                ->addColumn('estado', fn ($r) => $r->DES_Estado === 'ANULADO'
                    ? '<span class="badge badge-dark">ANULADO</span>'
                    : '<span class="badge badge-success">ACTIVO</span>')
                ->addColumn('action', function ($r) {
                    $btn = '<a href="javascript:void(0)" data-id="' . $r->DES_Id . '" class="btn btn-info btn-sm verDesempaquetado" title="Ver"><i class="fa fa-eye"></i></a>';
                    if ($r->DES_Estado === 'ACTIVO' && Auth::user()->can('tenant.inventario.producto.create')) {
                        $btn .= ' <a href="javascript:void(0)" data-id="' . $r->DES_Id . '" class="btn btn-danger btn-sm anularDesempaquetado" title="Anular"><i class="fa fa-undo"></i></a>';
                    }

                    return $btn;
                })
                ->filterColumn('kit', fn ($q, $k) => $q->where('p.PRO_Nombre', 'like', "%{$k}%"))
                ->filterColumn('almacen', fn ($q, $k) => $q->where('a.ALM_NombreAlmacen', 'like', "%{$k}%"))
                ->filterColumn('usuario', fn ($q, $k) => $q->where('u.name', 'like', "%{$k}%"))
                ->rawColumns(['estado', 'action'])
                ->make(true);
        }

        $almacenes = DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get();

        return view('tenant_tallermoto.inventario.desempaquetado.index', compact('almacenes'));
    }

    /**
     * Buscador de productos con su stock en el almacen. `kits=1` solo trae
     * los que tienen stock (lo que se puede abrir); sin el, cualquier
     * producto valido como componente.
     */
    public function productos(Request $request)
    {
        $request->validate(['ALM_Id' => 'required|integer|exists:almacen,ALM_Id']);

        $productos = DB::table('producto as p')
            ->leftJoin('lote as lt', function ($join) use ($request) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')->where('lt.ALM_Id', '=', $request->ALM_Id);
            })
            ->select('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion', DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as stock'),
                DB::raw('(SELECT COUNT(*) FROM producto_kit_componente k WHERE k.PRO_IdKit = p.PRO_Id) as tiene_receta'))
            ->where('p.PRO_Status', 1)
            ->where(fn ($q) => $q->where('p.PRO_TipoProducto', 'PRODUCTO')->orWhereNull('p.PRO_TipoProducto'))
            ->when($request->filled('search'), function ($q) use ($request) {
                $b = '%' . $request->search . '%';
                $q->where(fn ($qq) => $qq->where('p.PRO_Nombre', 'like', $b)->orWhere('p.PRO_CodigoInterno', 'like', $b)->orWhere('p.PRO_CodigoFabricacion', 'like', $b));
            })
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion')
            ->when($request->boolean('kits'), fn ($q) => $q->havingRaw('COALESCE(SUM(lt.LOT_CantidadReal), 0) >= 1')->orderByDesc('tiene_receta'))
            ->orderBy('p.PRO_Nombre')
            ->limit(30)
            ->get();

        return response()->json($productos);
    }

    public function receta(string $kit)
    {
        return response()->json(['componentes' => $this->servicio->receta((int) $kit)]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'PRO_Id' => 'required|integer|exists:producto,PRO_Id',
            'cantidad' => 'required|integer|min:1|max:10000',
            'observacion' => 'nullable|string|max:500',
            'guardar_receta' => 'nullable|boolean',
            'componentes' => 'required|array|min:1|max:100',
            'componentes.*.PRO_Id' => 'required|integer',
            'componentes.*.cantidad' => 'required|numeric|min:0.01|max:100000',
        ]);

        try {
            $id = $this->servicio->desempaquetar(
                (int) $request->ALM_Id,
                (int) $request->PRO_Id,
                (float) $request->cantidad,
                $request->input('componentes'),
                $request->input('observacion'),
                (int) Auth::id(),
                $request->boolean('guardar_receta'),
            );
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => 'Desempaquetado ' . self::numero($id) . ' registrado.', 'DES_Id' => $id]);
    }

    public function show(string $id)
    {
        $des = DB::table('desempaquetado as d')
            ->join('almacen as a', 'a.ALM_Id', '=', 'd.ALM_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('users as u', 'u.id', '=', 'd.USU_Id')
            ->leftJoin('users as ua', 'ua.id', '=', 'd.USU_Id_Anula')
            ->where('d.DES_Id', $id)
            ->first(['d.*', 'a.ALM_NombreAlmacen as almacen', 'p.PRO_Nombre as kit', 'u.name as usuario', 'ua.name as usuario_anula']);

        if (! $des) {
            return response()->json(['error' => 'Desempaquetado no encontrado.'], 404);
        }

        $des->numero = self::numero($des->DES_Id);

        $detalle = DB::table('desempaquetado_detalle as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->leftJoin('lote as l', 'l.LOT_Id', '=', 'd.LOT_Id')
            ->where('d.DES_Id', $id)
            ->orderBy('p.PRO_Nombre')
            ->get(['p.PRO_Nombre', 'd.DSD_CantidadPorKit', 'd.DSD_Cantidad', 'd.DSD_PrecioCompra', 'd.LOT_Id', 'l.LOT_CantidadReal as disponible_lote']);

        return response()->json(['desempaquetado' => $des, 'detalle' => $detalle]);
    }

    public function anular(Request $request, string $id)
    {
        $request->validate(['motivo' => 'required|string|min:3|max:255']);

        try {
            $this->servicio->anular((int) $id, $request->motivo, (int) Auth::id());
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => 'Desempaquetado anulado: el kit volvió al stock.']);
    }

    public static function numero(int $id): string
    {
        return 'DES-' . str_pad((string) $id, 5, '0', STR_PAD_LEFT);
    }
}
