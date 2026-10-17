<?php

namespace App\Services\AppMovil;

use App\Models\TenantTallerMotos\PedidoMovil;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Catalogo de la tienda de la app: los mismos productos que la web publica
 * del taller ("Mostrar en catalogo"), solo los de tipo PRODUCTO (los
 * servicios no se piden por la app). La disponibilidad es por sede: stock de
 * sus lotes menos lo que ya esta apartado por pedidos confirmados de la app.
 * Al cliente nunca se le muestra la cantidad exacta.
 */
class CatalogoMovilService
{
    public const DISPONIBLE = 'DISPONIBLE';
    public const ULTIMAS = 'ULTIMAS';
    public const AGOTADO = 'AGOTADO';

    /** Tope de unidades por producto en un pedido. */
    public const MAX_POR_PRODUCTO = 20;

    /** Sede por defecto: la principal activa (o la primera activa). */
    public function sedePorDefecto(): ?int
    {
        return DB::table('almacen')->where('ALM_Status', 1)
            ->orderByDesc('ALM_EsPrincipal')->orderBy('ALM_Id')
            ->value('ALM_Id');
    }

    public function sedeValida(?int $sedeId): ?int
    {
        if (! $sedeId) {
            return $this->sedePorDefecto();
        }

        return DB::table('almacen')->where('ALM_Id', $sedeId)->where('ALM_Status', 1)->exists() ? $sedeId : null;
    }

    public function sedes(): Collection
    {
        return DB::table('almacen')->where('ALM_Status', 1)
            ->orderByDesc('ALM_EsPrincipal')->orderBy('ALM_NombreAlmacen')
            ->get(['ALM_Id as id', 'ALM_NombreAlmacen as nombre', 'ALM_Direccion as direccion']);
    }

    /** Consulta base: productos publicados en el catalogo. */
    private function base()
    {
        return DB::table('producto as p')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->where('p.PRO_Status', 1)
            ->where('p.PRO_MostrarCatalogo', 1)
            ->where(fn ($q) => $q->where('p.PRO_TipoProducto', 'PRODUCTO')->orWhereNull('p.PRO_TipoProducto'));
    }

    public function categorias(): Collection
    {
        return $this->base()
            ->select('c.CAT_Id as id', 'c.CAT_Nombre as nombre', DB::raw('COUNT(*) as productos'))
            ->groupBy('c.CAT_Id', 'c.CAT_Nombre')
            ->orderBy('c.CAT_Nombre')
            ->get();
    }

    /**
     * @return array{filas: Collection, siguiente: ?int, total: int}
     */
    public function productos(int $sedeId, ?int $categoriaId, ?string $buscar, int $offset, int $limite): array
    {
        $q = $this->base()
            ->when($categoriaId, fn ($q) => $q->where('p.CAT_Id', $categoriaId))
            ->when($buscar, function ($q) use ($buscar) {
                $termino = '%' . mb_strtolower(trim($buscar)) . '%';
                $q->where(fn ($w) => $w->whereRaw('LOWER(p.PRO_Nombre) LIKE ?', [$termino])
                    ->orWhereRaw('LOWER(p.PRO_Marca) LIKE ?', [$termino])
                    ->orWhereRaw('LOWER(p.PRO_CodigoInterno) LIKE ?', [$termino])
                    ->orWhereRaw('LOWER(p.PRO_CodigoFabricacion) LIKE ?', [$termino]));
            });

        $total = (clone $q)->count();
        $filas = $q->orderBy('p.PRO_Nombre')
            ->offset($offset)->limit($limite)
            ->get(['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Marca', 'p.PRO_PrecioVenta', 'p.PRO_Imagen', 'p.PRO_StockMinimo', 'c.CAT_Id', 'c.CAT_Nombre']);

        $disponible = $this->disponibles($sedeId, $filas->pluck('PRO_Id')->all());

        return [
            'filas' => $filas->map(fn ($p) => $this->resumen($p, $disponible[$p->PRO_Id] ?? 0)),
            'siguiente' => $offset + $limite < $total ? $offset + $limite : null,
            'total' => $total,
        ];
    }

    public function detalle(int $sedeId, int $productoId): ?array
    {
        $p = $this->base()->where('p.PRO_Id', $productoId)
            ->first(['p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Descripcion', 'p.PRO_Marca', 'p.PRO_PrecioVenta', 'p.PRO_Imagen', 'p.PRO_StockMinimo', 'c.CAT_Id', 'c.CAT_Nombre']);

        if (! $p) {
            return null;
        }

        $disponible = $this->disponibles($sedeId, [$p->PRO_Id])[$p->PRO_Id] ?? 0;

        // Galeria: imagen principal + las fotos adicionales del producto.
        $fotos = collect();
        if ($p->PRO_Imagen) {
            $fotos->push($this->urlImagenPrincipal($p->PRO_Imagen));
        }
        foreach (DB::table('producto_imagen')->where('PRO_Id', $p->PRO_Id)->orderBy('PROI_Item')->pluck('PROI_url') as $url) {
            $fotos->push(url($url));
        }

        return $this->resumen($p, $disponible) + [
            'descripcion' => $p->PRO_Descripcion ? trim($p->PRO_Descripcion) : null,
            'fotos' => $fotos->unique()->values()->all(),
            'maximo' => (int) min(self::MAX_POR_PRODUCTO, max(0, floor($disponible))),
        ];
    }

    /**
     * Unidades que se pueden vender por la app en una sede: stock de sus lotes
     * menos lo apartado por pedidos confirmados (o listos) todavia no cobrados.
     * $excluirPedido sirve al confirmar un pedido: no se descuenta a si mismo.
     *
     * @return array<int, float> [PRO_Id => unidades]
     */
    public function disponibles(int $sedeId, array $productoIds, ?int $excluirPedido = null): array
    {
        if (! $productoIds) {
            return [];
        }

        $stock = DB::table('lote')->where('ALM_Id', $sedeId)->whereIn('PRO_Id', $productoIds)
            ->groupBy('PRO_Id')
            ->selectRaw('PRO_Id, SUM(LOT_CantidadReal) as total')
            ->pluck('total', 'PRO_Id');

        $apartado = DB::table('pedido_movil_item as i')
            ->join('pedido_movil as p', 'p.PED_Id', '=', 'i.PED_Id')
            ->where('p.ALM_Id', $sedeId)
            ->whereIn('p.PED_Estado', PedidoMovil::ESTADOS_QUE_APARTAN)
            ->when($excluirPedido, fn ($q) => $q->where('p.PED_Id', '!=', $excluirPedido))
            ->whereIn('i.PRO_Id', $productoIds)
            ->groupBy('i.PRO_Id')
            ->selectRaw('i.PRO_Id as producto, SUM(i.PDI_Cantidad) as total')
            ->pluck('total', 'producto');

        $resultado = [];
        foreach ($productoIds as $id) {
            $resultado[$id] = max(0, (float) ($stock[$id] ?? 0) - (float) ($apartado[$id] ?? 0));
        }

        return $resultado;
    }

    public static function estadoDisponibilidad(float $disponible, ?float $stockMinimo): string
    {
        if ($disponible < 1) {
            return self::AGOTADO;
        }

        return $disponible <= max(3, (float) $stockMinimo) ? self::ULTIMAS : self::DISPONIBLE;
    }

    private function resumen(object $p, float $disponible): array
    {
        return [
            'id' => (int) $p->PRO_Id,
            'nombre' => trim($p->PRO_Nombre),
            'marca' => $p->PRO_Marca ? trim($p->PRO_Marca) : null,
            'precio' => number_format((float) $p->PRO_PrecioVenta, 2, '.', ''),
            'imagen' => $p->PRO_Imagen ? $this->urlImagenPrincipal($p->PRO_Imagen) : null,
            'categoria' => ['id' => (int) $p->CAT_Id, 'nombre' => $p->CAT_Nombre],
            'disponibilidad' => self::estadoDisponibilidad($disponible, $p->PRO_StockMinimo),
        ];
    }

    private function urlImagenPrincipal(string $archivo): string
    {
        return url('/storage/' . tenant('tipo_negocio') . '/' . tenant('id') . '/archivos/producto/' . $archivo);
    }
}
