<?php

namespace App\Services\AppMovil;

use App\Models\TenantTallerMotos\CuentaMovil;
use App\Models\TenantTallerMotos\PedidoMovil;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Ciclo de vida de los pedidos de la app (pago y recojo en el taller):
 *
 *   PENDIENTE  -> lo hizo el cliente; todavia no aparta stock.
 *   CONFIRMADO -> el taller lo acepto; el stock queda apartado.
 *   LISTO      -> preparado para recoger (sigue apartado).
 *   ENTREGADO  -> se cobro en el punto de venta (VEN_Id); el stock ya salio con la venta.
 *   CANCELADO  -> por el cliente (solo si estaba PENDIENTE) o por el taller.
 *
 * Los errores que debe ver la persona se lanzan como RuntimeException con un
 * mensaje listo para mostrar.
 */
class PedidosMovilService
{
    /** Pedidos sin terminar que puede tener una cuenta a la vez. */
    public const MAX_ABIERTOS = 3;

    public const MAX_ITEMS = 30;

    public function __construct(private CatalogoMovilService $catalogo)
    {
    }

    /**
     * @param  array<int, array{producto_id: int, cantidad: int}>  $items
     */
    public function crear(CuentaMovil $cuenta, int $sedeId, array $items, ?string $nota): PedidoMovil
    {
        $abiertos = PedidoMovil::where('CMV_Id', $cuenta->CMV_Id)
            ->whereIn('PED_Estado', [PedidoMovil::PENDIENTE, PedidoMovil::CONFIRMADO, PedidoMovil::LISTO])
            ->count();

        if ($abiertos >= self::MAX_ABIERTOS) {
            throw new RuntimeException('Ya tienes ' . self::MAX_ABIERTOS . ' pedidos en curso. Recoge o cancela alguno antes de hacer otro.');
        }

        // Une cantidades repetidas del mismo producto.
        $cantidades = [];
        foreach ($items as $item) {
            $id = (int) $item['producto_id'];
            $cantidades[$id] = ($cantidades[$id] ?? 0) + (int) $item['cantidad'];
        }

        if (count($cantidades) > self::MAX_ITEMS) {
            throw new RuntimeException('Un pedido puede tener como maximo ' . self::MAX_ITEMS . ' productos distintos.');
        }

        return DB::transaction(function () use ($cuenta, $sedeId, $cantidades, $nota) {
            $filas = [];
            $total = 0.0;

            foreach ($cantidades as $productoId => $cantidad) {
                $producto = $this->catalogo->detalle($sedeId, $productoId);

                if (! $producto) {
                    throw new RuntimeException('Uno de los productos ya no esta disponible en el catalogo. Actualiza tu carrito.');
                }
                if ($cantidad > CatalogoMovilService::MAX_POR_PRODUCTO) {
                    throw new RuntimeException("Puedes pedir hasta " . CatalogoMovilService::MAX_POR_PRODUCTO . " unidades de {$producto['nombre']}.");
                }
                if ($cantidad > $producto['maximo']) {
                    throw new RuntimeException($producto['maximo'] < 1
                        ? "{$producto['nombre']} esta agotado en esta sede."
                        : "Solo quedan {$producto['maximo']} unidades de {$producto['nombre']} en esta sede.");
                }

                // Precio de hoy, siempre leido de la base (nunca del celular).
                $precio = (float) $producto['precio'];
                $subtotal = round($precio * $cantidad, 2);
                $total += $subtotal;

                $filas[] = [
                    'PRO_Id' => $productoId,
                    'PDI_Nombre' => $producto['nombre'],
                    'PDI_Precio' => $precio,
                    'PDI_Cantidad' => $cantidad,
                    'PDI_Subtotal' => $subtotal,
                ];
            }

            $pedido = PedidoMovil::create([
                'CMV_Id' => $cuenta->CMV_Id,
                'ALM_Id' => $sedeId,
                'PED_Estado' => PedidoMovil::PENDIENTE,
                'PED_Total' => round($total, 2),
                'PED_Nota' => $nota ? trim($nota) : null,
            ]);
            $pedido->items()->createMany($filas);

            return $pedido->load('items');
        });
    }

    public function cancelarPorCliente(PedidoMovil $pedido): void
    {
        if ($pedido->PED_Estado !== PedidoMovil::PENDIENTE) {
            throw new RuntimeException('El taller ya acepto este pedido. Para cancelarlo comunicate con el taller.');
        }

        $pedido->update([
            'PED_Estado' => PedidoMovil::CANCELADO,
            'PED_CanceladoPor' => 'CLIENTE',
            'PED_CanceladoEn' => now(),
        ]);
    }

    /** El taller acepta el pedido: verifica que haya stock y lo aparta. */
    public function confirmar(PedidoMovil $pedido, int $usuarioId): void
    {
        DB::transaction(function () use ($pedido, $usuarioId) {
            $pedido = PedidoMovil::with('items')->lockForUpdate()->findOrFail($pedido->PED_Id);

            if ($pedido->PED_Estado !== PedidoMovil::PENDIENTE) {
                throw new RuntimeException('Solo se puede confirmar un pedido pendiente.');
            }

            $disponible = $this->catalogo->disponibles((int) $pedido->ALM_Id, $pedido->items->pluck('PRO_Id')->all(), $pedido->PED_Id);
            $faltan = $pedido->items->filter(fn ($i) => (float) $i->PDI_Cantidad > ($disponible[$i->PRO_Id] ?? 0));

            if ($faltan->isNotEmpty()) {
                throw new RuntimeException('No hay stock suficiente en esta sede para: '
                    . $faltan->map(fn ($i) => $i->PDI_Nombre . ' (hay ' . floor($disponible[$i->PRO_Id] ?? 0) . ')')->implode(', ')
                    . '. Ingresa mercaderia o cancela el pedido.');
            }

            $pedido->update([
                'PED_Estado' => PedidoMovil::CONFIRMADO,
                'PED_ConfirmadoEn' => now(),
                'USU_Id_Atiende' => $usuarioId,
            ]);
        });
    }

    public function marcarListo(PedidoMovil $pedido): void
    {
        if ($pedido->PED_Estado !== PedidoMovil::CONFIRMADO) {
            throw new RuntimeException('Primero hay que confirmar el pedido.');
        }

        $pedido->update(['PED_Estado' => PedidoMovil::LISTO, 'PED_ListoEn' => now()]);
    }

    public function cancelarPorTaller(PedidoMovil $pedido, ?string $motivo, int $usuarioId): void
    {
        if (! $pedido->estaAbierto()) {
            throw new RuntimeException('Este pedido ya esta cerrado.');
        }

        $pedido->update([
            'PED_Estado' => PedidoMovil::CANCELADO,
            'PED_CanceladoPor' => 'TALLER',
            'PED_MotivoCancelacion' => $motivo ? trim($motivo) : null,
            'PED_CanceladoEn' => now(),
            'USU_Id_Atiende' => $usuarioId,
        ]);
    }

    /** Lo llama VentaController al cobrar el pedido en el punto de venta. */
    public function marcarEntregado(int $pedidoId, int $ventaId): void
    {
        PedidoMovil::where('PED_Id', $pedidoId)
            ->whereIn('PED_Estado', [PedidoMovil::CONFIRMADO, PedidoMovil::LISTO])
            ->update([
                'PED_Estado' => PedidoMovil::ENTREGADO,
                'VEN_Id' => $ventaId,
                'PED_EntregadoEn' => now(),
            ]);
    }
}
