<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\PedidoMovil;
use App\Services\AppMovil\CatalogoMovilService;
use App\Services\AppMovil\PedidosMovilService;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/** Pedidos de la cuenta: se pagan y recogen en el taller. Un pedido ajeno responde 404. */
class PedidoController extends Controller
{
    private const ETIQUETAS = [
        PedidoMovil::PENDIENTE => 'Esperando confirmación del taller',
        PedidoMovil::CONFIRMADO => 'Confirmado, lo están preparando',
        PedidoMovil::LISTO => 'Listo para recoger',
        PedidoMovil::ENTREGADO => 'Entregado',
        PedidoMovil::CANCELADO => 'Cancelado',
    ];

    public function __construct(private PedidosMovilService $pedidos, private CatalogoMovilService $catalogo)
    {
    }

    public function index(Request $request)
    {
        $d = $request->validate(['estado' => 'nullable|in:activos,todos']);
        $soloActivos = ($d['estado'] ?? 'todos') === 'activos';

        $lista = PedidoMovil::withCount('items')
            ->where('CMV_Id', $request->user('cliente')->CMV_Id)
            ->when($soloActivos, fn ($q) => $q->whereIn('PED_Estado', [PedidoMovil::PENDIENTE, PedidoMovil::CONFIRMADO, PedidoMovil::LISTO]))
            ->orderByDesc('PED_Id')
            ->limit(50)
            ->get();

        return ApiRespuesta::ok($lista->map(fn ($p) => $this->resumen($p))->all());
    }

    public function show(Request $request, int $id)
    {
        $pedido = $this->delaCuenta($request, $id);

        return $pedido ? ApiRespuesta::ok($this->detalle($pedido)) : $this->noEncontrado();
    }

    public function store(Request $request)
    {
        if (! tenant_has_module('productos')) {
            return ApiRespuesta::error('TIENDA_NO_DISPONIBLE', 'Este taller no tiene tienda en la app.', 404);
        }

        $d = $request->validate([
            'sede' => 'required|integer|min:1',
            'nota' => 'nullable|string|max:250',
            'items' => 'required|array|min:1|max:' . PedidosMovilService::MAX_ITEMS,
            'items.*.producto_id' => 'required|integer|min:1',
            'items.*.cantidad' => 'required|integer|min:1|max:' . CatalogoMovilService::MAX_POR_PRODUCTO,
        ]);

        $sede = $this->catalogo->sedeValida((int) $d['sede']);
        if (! $sede) {
            return ApiRespuesta::error('SEDE_INVALIDA', 'Esa sede no está disponible.', 422);
        }

        try {
            $pedido = $this->pedidos->crear($request->user('cliente'), $sede, $d['items'], $d['nota'] ?? null);
        } catch (RuntimeException $e) {
            return ApiRespuesta::error('PEDIDO_INVALIDO', $e->getMessage(), 422);
        }

        return ApiRespuesta::ok($this->detalle($pedido), 201);
    }

    public function cancelar(Request $request, int $id)
    {
        $pedido = $this->delaCuenta($request, $id);
        if (! $pedido) {
            return $this->noEncontrado();
        }

        try {
            $this->pedidos->cancelarPorCliente($pedido);
        } catch (RuntimeException $e) {
            return ApiRespuesta::error('NO_CANCELABLE', $e->getMessage(), 409);
        }

        return ApiRespuesta::ok($this->detalle($pedido->fresh()));
    }

    private function delaCuenta(Request $request, int $id): ?PedidoMovil
    {
        return PedidoMovil::with('items')->where('CMV_Id', $request->user('cliente')->CMV_Id)->find($id);
    }

    private function resumen(PedidoMovil $p): array
    {
        return [
            'id' => $p->PED_Id,
            'numero' => $p->numero(),
            'estado' => $p->PED_Estado,
            'estado_texto' => self::ETIQUETAS[$p->PED_Estado] ?? $p->PED_Estado,
            'total' => number_format((float) $p->PED_Total, 2, '.', ''),
            'productos' => (int) ($p->items_count ?? $p->items->count()),
            'fecha' => $p->created_at?->toIso8601String(),
            'sede' => DB::table('almacen')->where('ALM_Id', $p->ALM_Id)->value('ALM_NombreAlmacen'),
        ];
    }

    private function detalle(PedidoMovil $p): array
    {
        $sede = DB::table('almacen')->where('ALM_Id', $p->ALM_Id)->first(['ALM_NombreAlmacen', 'ALM_Direccion']);
        $imagenes = DB::table('producto')->whereIn('PRO_Id', $p->items->pluck('PRO_Id'))->pluck('PRO_Imagen', 'PRO_Id');

        return $this->resumen($p) + [
            'sede_direccion' => $sede->ALM_Direccion ?? null,
            'nota' => $p->PED_Nota,
            'motivo_cancelacion' => $p->PED_MotivoCancelacion,
            'cancelado_por' => $p->PED_CanceladoPor,
            'cancelable' => $p->PED_Estado === PedidoMovil::PENDIENTE,
            'pasos' => [
                'pedido' => $p->created_at?->toIso8601String(),
                'confirmado' => $p->PED_ConfirmadoEn?->toIso8601String(),
                'listo' => $p->PED_ListoEn?->toIso8601String(),
                'entregado' => $p->PED_EntregadoEn?->toIso8601String(),
                'cancelado' => $p->PED_CanceladoEn?->toIso8601String(),
            ],
            'items' => $p->items->map(fn ($i) => [
                'producto_id' => (int) $i->PRO_Id,
                'nombre' => $i->PDI_Nombre,
                'precio' => number_format((float) $i->PDI_Precio, 2, '.', ''),
                'cantidad' => (int) $i->PDI_Cantidad,
                'subtotal' => number_format((float) $i->PDI_Subtotal, 2, '.', ''),
                'imagen' => ! empty($imagenes[$i->PRO_Id])
                    ? url('/storage/' . tenant('tipo_negocio') . '/' . tenant('id') . '/archivos/producto/' . $imagenes[$i->PRO_Id])
                    : null,
            ])->values()->all(),
        ];
    }

    private function noEncontrado()
    {
        return ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos ese pedido.', 404);
    }
}
