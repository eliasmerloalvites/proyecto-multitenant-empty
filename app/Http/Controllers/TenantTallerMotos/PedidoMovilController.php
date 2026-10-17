<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\PedidoMovil;
use App\Services\AppMovil\PedidosMovilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Panel del taller para los pedidos que hacen los clientes desde la app:
 * confirmar (aparta el stock), marcar listo para recoger, cancelar y cobrar
 * (abre el punto de venta con el pedido precargado).
 */
class PedidoMovilController extends Controller
{
    public const PESTANAS = [
        'pendientes' => [PedidoMovil::PENDIENTE],
        'en-curso' => [PedidoMovil::CONFIRMADO, PedidoMovil::LISTO],
        'entregados' => [PedidoMovil::ENTREGADO],
        'cancelados' => [PedidoMovil::CANCELADO],
    ];

    public function __construct(private PedidosMovilService $pedidos)
    {
    }

    public function index(Request $request)
    {
        $pestana = array_key_exists($request->query('estado'), self::PESTANAS) ? $request->query('estado') : 'pendientes';

        $conteos = PedidoMovil::select('PED_Estado', DB::raw('COUNT(*) as n'))->groupBy('PED_Estado')->pluck('n', 'PED_Estado');
        $porPestana = collect(self::PESTANAS)->map(fn ($estados) => (int) collect($estados)->sum(fn ($e) => $conteos[$e] ?? 0));

        $sedes = DB::table('almacen')->pluck('ALM_NombreAlmacen', 'ALM_Id');

        $pedidos = PedidoMovil::with(['items', 'cuenta'])
            ->whereIn('PED_Estado', self::PESTANAS[$pestana])
            ->orderBy($pestana === 'pendientes' ? 'PED_Id' : 'updated_at', $pestana === 'pendientes' ? 'asc' : 'desc')
            ->limit(200)
            ->get()
            ->map(fn ($p) => (object) [
                'id' => $p->PED_Id,
                'numero' => $p->numero(),
                'estado' => $p->PED_Estado,
                'fecha' => $p->created_at,
                'cliente' => $p->cuenta?->CMV_Nombre ?: 'Cliente de la app',
                'celular' => AppMovilController::celularLegible((string) $p->cuenta?->CMV_Celular),
                'sede' => $sedes[$p->ALM_Id] ?? '—',
                'total' => (float) $p->PED_Total,
                'nota' => $p->PED_Nota,
                'motivo' => $p->PED_MotivoCancelacion,
                'cancelado_por' => $p->PED_CanceladoPor,
                'venta' => $p->VEN_Id,
                'items' => $p->items->map(fn ($i) => [
                    'nombre' => $i->PDI_Nombre,
                    'cantidad' => (float) $i->PDI_Cantidad,
                    'precio' => (float) $i->PDI_Precio,
                    'subtotal' => (float) $i->PDI_Subtotal,
                ])->values(),
            ]);

        return view('tenant_tallermoto.appmovil.pedidos', compact('pedidos', 'pestana', 'porPestana'));
    }

    public function confirmar(int $id)
    {
        return $this->accion(fn () => $this->pedidos->confirmar(PedidoMovil::findOrFail($id), (int) Auth::guard('tenant')->id()),
            'Pedido confirmado: el stock quedó apartado para el cliente.');
    }

    public function listo(int $id)
    {
        return $this->accion(fn () => $this->pedidos->marcarListo(PedidoMovil::findOrFail($id)),
            'Pedido marcado como listo para recoger.');
    }

    public function cancelar(Request $request, int $id)
    {
        $motivo = $request->validate(['motivo' => 'nullable|string|max:250'])['motivo'] ?? null;

        return $this->accion(fn () => $this->pedidos->cancelarPorTaller(PedidoMovil::findOrFail($id), $motivo, (int) Auth::guard('tenant')->id()),
            'Pedido cancelado.');
    }

    /** Lleva al punto de venta con el pedido precargado (ver VentaController::create). */
    public function cobrar(int $id)
    {
        $pedido = PedidoMovil::findOrFail($id);

        if (! in_array($pedido->PED_Estado, PedidoMovil::ESTADOS_QUE_APARTAN, true)) {
            return back()->with('error', 'Solo se puede cobrar un pedido confirmado o listo para recoger.');
        }

        return redirect()->route('tenant.ventas.venta.create', ['pedido_movil' => $pedido->PED_Id]);
    }

    private function accion(callable $hacer, string $exito)
    {
        try {
            $hacer();
        } catch (RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => $exito]);
    }
}
