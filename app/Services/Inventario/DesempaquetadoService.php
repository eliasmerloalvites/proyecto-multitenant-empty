<?php

namespace App\Services\Inventario;

use App\Http\Controllers\Tenant\VentaController;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Desempaquetado de kits (ver la migracion create_desempaquetado_tables).
 * Toda la logica de stock vive aqui para que el controlador solo valide y
 * responda. Los errores para el usuario salen como RuntimeException.
 */
class DesempaquetadoService
{
    public const LOTE_INGRESO = 'DESEMPAQUETADO';
    public const LOTE_DEVOLUCION = 'DESEMPAQ_ANULADO';

    /** Receta guardada del kit: [['PRO_Id','PRO_Nombre','cantidad'], ...] */
    public function receta(int $kitId): array
    {
        return DB::table('producto_kit_componente as k')
            ->join('producto as p', 'p.PRO_Id', '=', 'k.PRO_IdComponente')
            ->where('k.PRO_IdKit', $kitId)
            ->orderBy('p.PRO_Nombre')
            ->get(['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_CodigoInterno', 'k.PKC_Cantidad as cantidad'])
            ->map(fn ($r) => ['PRO_Id' => (int) $r->PRO_Id, 'PRO_Nombre' => $r->PRO_Nombre, 'PRO_CodigoInterno' => $r->PRO_CodigoInterno, 'cantidad' => (float) $r->cantidad])
            ->all();
    }

    /**
     * Abre $cantidad kits en el almacen e ingresa sus componentes.
     *
     * @param  array  $componentes  [['PRO_Id' => int, 'cantidad' => float (por kit)], ...]
     */
    public function desempaquetar(int $almacenId, int $kitId, float $cantidad, array $componentes, ?string $observacion, int $usuarioId, bool $guardarReceta): int
    {
        if ($cantidad <= 0 || floor($cantidad) != $cantidad) {
            throw new RuntimeException('La cantidad de kits a desempaquetar debe ser un número entero mayor a 0.');
        }

        $componentes = $this->normalizarComponentes($kitId, $componentes);

        return DB::transaction(function () use ($almacenId, $kitId, $cantidad, $componentes, $observacion, $usuarioId, $guardarReceta) {
            // Bloquea los lotes del kit en esta sede para que dos usuarios no
            // abran el mismo stock a la vez.
            $stockKit = (float) DB::table('lote')->where('PRO_Id', $kitId)->where('ALM_Id', $almacenId)->lockForUpdate()->sum('LOT_CantidadReal');

            if ($stockKit < $cantidad) {
                throw new RuntimeException('No hay stock suficiente del kit en este almacén (disponible: ' . $this->numero($stockKit) . ').');
            }

            $desId = DB::table('desempaquetado')->insertGetId([
                'ALM_Id' => $almacenId,
                'PRO_Id' => $kitId,
                'USU_Id' => $usuarioId,
                'DES_Cantidad' => $cantidad,
                'DES_Observacion' => $observacion,
                'DES_Estado' => 'ACTIVO',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Sale el kit (FIFO, sin permitir negativo).
            VentaController::ReducirStock($kitId, $cantidad, $almacenId, false);

            $productos = DB::table('producto')->whereIn('PRO_Id', array_column($componentes, 'PRO_Id'))->get()->keyBy('PRO_Id');

            foreach ($componentes as $c) {
                $p = $productos[$c['PRO_Id']];
                $total = round($c['cantidad'] * $cantidad, 2);

                // Entra cada componente con su costo actual.
                $loteId = DB::table('lote')->insertGetId([
                    'ALM_Id' => $almacenId,
                    'PRO_Id' => $c['PRO_Id'],
                    'LOT_TipoIngreso' => self::LOTE_INGRESO,
                    'LOT_IdIngreso' => $desId,
                    'LOT_CantidadReal' => $total,
                    'LOT_CantidadIngreso' => $total,
                    'LOT_PrecioCompra' => (float) $p->PRO_PrecioCompra,
                    'LOT_PrecioVenta' => (float) $p->PRO_PrecioVenta,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                DB::table('desempaquetado_detalle')->insert([
                    'DES_Id' => $desId,
                    'PRO_Id' => $c['PRO_Id'],
                    'LOT_Id' => $loteId,
                    'DSD_CantidadPorKit' => $c['cantidad'],
                    'DSD_Cantidad' => $total,
                    'DSD_PrecioCompra' => (float) $p->PRO_PrecioCompra,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            if ($guardarReceta) {
                $this->guardarReceta($kitId, $componentes);
            }

            return $desId;
        });
    }

    /**
     * Revierte un desempaquetado: solo si ningun componente se movio
     * (vendido, trasladado o ajustado) desde que entro.
     */
    public function anular(int $desId, string $motivo, int $usuarioId): void
    {
        DB::transaction(function () use ($desId, $motivo, $usuarioId) {
            $des = DB::table('desempaquetado')->where('DES_Id', $desId)->lockForUpdate()->first();

            if (! $des) {
                throw new RuntimeException('Desempaquetado no encontrado.');
            }
            if ($des->DES_Estado !== 'ACTIVO') {
                throw new RuntimeException('Este desempaquetado ya fue anulado.');
            }

            $detalle = DB::table('desempaquetado_detalle as d')
                ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
                ->leftJoin('lote as l', 'l.LOT_Id', '=', 'd.LOT_Id')
                ->where('d.DES_Id', $desId)
                ->lockForUpdate()
                ->get(['d.DSD_Id', 'd.LOT_Id', 'd.DSD_Cantidad', 'p.PRO_Nombre', 'l.LOT_CantidadReal']);

            $usados = $detalle->filter(fn ($d) => ! $d->LOT_Id || abs((float) $d->LOT_CantidadReal - (float) $d->DSD_Cantidad) > 0.009);
            if ($usados->isNotEmpty()) {
                throw new RuntimeException('No se puede anular: ya se usaron unidades de ' . $usados->pluck('PRO_Nombre')->implode(', ') . '. Si hace falta corregir, usa un ajuste de inventario.');
            }

            // Salen los componentes (el lote queda en 0 para conservar la historia).
            foreach ($detalle as $d) {
                DB::table('lote')->where('LOT_Id', $d->LOT_Id)->update(['LOT_CantidadReal' => 0, 'updated_at' => now()]);
            }

            // Vuelve el kit con el costo que tenia su ultimo lote.
            $ultimo = DB::table('lote')->where('PRO_Id', $des->PRO_Id)->where('ALM_Id', $des->ALM_Id)->orderByDesc('LOT_Id')->first();
            $kit = DB::table('producto')->where('PRO_Id', $des->PRO_Id)->first();

            $loteDevolucion = DB::table('lote')->insertGetId([
                'ALM_Id' => $des->ALM_Id,
                'PRO_Id' => $des->PRO_Id,
                'LOT_TipoIngreso' => self::LOTE_DEVOLUCION,
                'LOT_IdIngreso' => $desId,
                'LOT_CantidadReal' => $des->DES_Cantidad,
                'LOT_CantidadIngreso' => $des->DES_Cantidad,
                'LOT_PrecioCompra' => $ultimo ? (float) $ultimo->LOT_PrecioCompra : (float) $kit->PRO_PrecioCompra,
                'LOT_PrecioVenta' => $ultimo ? (float) $ultimo->LOT_PrecioVenta : (float) $kit->PRO_PrecioVenta,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('desempaquetado')->where('DES_Id', $desId)->update([
                'DES_Estado' => 'ANULADO',
                'LOT_IdDevolucion' => $loteDevolucion,
                'USU_Id_Anula' => $usuarioId,
                'DES_MotivoAnulacion' => $motivo,
                'DES_AnuladoEn' => now(),
                'updated_at' => now(),
            ]);
        });
    }

    public function guardarReceta(int $kitId, array $componentes): void
    {
        $componentes = $this->normalizarComponentes($kitId, $componentes);

        DB::table('producto_kit_componente')->where('PRO_IdKit', $kitId)->delete();
        foreach ($componentes as $c) {
            DB::table('producto_kit_componente')->insert([
                'PRO_IdKit' => $kitId,
                'PRO_IdComponente' => $c['PRO_Id'],
                'PKC_Cantidad' => $c['cantidad'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /** Valida y agrupa los componentes (sin repetidos, sin el propio kit, sin servicios). */
    private function normalizarComponentes(int $kitId, array $componentes): array
    {
        $agrupados = [];
        foreach ($componentes as $c) {
            $id = (int) ($c['PRO_Id'] ?? 0);
            $cant = round((float) ($c['cantidad'] ?? 0), 2);
            if ($id <= 0 || $cant <= 0) {
                throw new RuntimeException('Cada componente necesita un producto y una cantidad mayor a 0.');
            }
            if ($id === $kitId) {
                throw new RuntimeException('El kit no puede ser componente de sí mismo.');
            }
            $agrupados[$id] = ($agrupados[$id] ?? 0) + $cant;
        }

        if (empty($agrupados)) {
            throw new RuntimeException('Agrega al menos un componente del kit.');
        }

        $validos = DB::table('producto')
            ->whereIn('PRO_Id', array_keys($agrupados))
            ->where('PRO_Status', 1)
            ->where(fn ($q) => $q->where('PRO_TipoProducto', 'PRODUCTO')->orWhereNull('PRO_TipoProducto'))
            ->pluck('PRO_Id')
            ->map(fn ($v) => (int) $v)
            ->all();

        if (count($validos) !== count($agrupados)) {
            throw new RuntimeException('Algún componente no existe, está inactivo o es un servicio.');
        }

        return array_map(fn ($id, $cant) => ['PRO_Id' => $id, 'cantidad' => $cant], array_keys($agrupados), array_values($agrupados));
    }

    private function numero(float $n): string
    {
        return rtrim(rtrim(number_format($n, 2, '.', ''), '0'), '.');
    }
}
