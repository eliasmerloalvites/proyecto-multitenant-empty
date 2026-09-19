<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Models\Tenant\Almacen;
use App\Models\TenantTallerMotos\ProductoImagen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Ventas propio de taller de motos. Hereda toda la logica
 * compartida de App\Http\Controllers\Tenant\VentaController y solo
 * sobreescribe lo que depende de PRO_TipoProducto (columna exclusiva de
 * tallermoto, ver Models\Tenant\Producto y la migracion
 * add_tipo_producto_to_producto_table): un producto marcado como Servicio
 * nunca tiene lotes, asi que (a) siempre debe aparecer en el buscador del
 * POS aunque tenga 0 stock, y (b) siempre debe poder venderse sin stock. El
 * binding en AppServiceProvider hace que las rutas de Ventas resuelvan a
 * ESTA clase cuando el tenant es 'tallermoto'.
 */
class VentaController extends \App\Http\Controllers\Tenant\VentaController
{
    /**
     * Usado por store() (en la base) para decidir, por producto, si se
     * vende "sin stock" sin depender de ALM_PermitirVentaSinStock.
     */
    protected function productosSinControlDeStock(array $items): array
    {
        $idsCarrito = collect($items)->pluck('PRO_Id')->all();

        return DB::table('producto')
            ->whereIn('PRO_Id', $idsCarrito)
            ->where('PRO_TipoProducto', 'SERVICIO')
            ->pluck('PRO_Id')
            ->all();
    }

    public function getProductos(Request $request)
    {
        $idAlmacen = tenant_caja_activa_almacen_id() ?? 1;
        $permitirSinStock = (bool) (Almacen::find($idAlmacen)->ALM_PermitirVentaSinStock ?? false);

        $columnas = [
            'p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Descripcion', 'p.PRO_Imagen', 'p.CAT_Id', 'cat.CAT_Nombre',
            'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion', 'p.PRO_TipoProducto',
        ];
        $agrupar = [
            'p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Descripcion', 'p.PRO_Imagen', 'p.CAT_Id', 'cat.CAT_Nombre', 'p.PRO_PrecioVenta',
            'p.PRO_CodigoInterno', 'p.PRO_CodigoFabricacion', 'p.PRO_TipoProducto',
        ];

        $query = DB::table('producto as p')
            ->join('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->join('clase as cl', 'cl.CLA_Id', '=', 'cat.CLA_Id')
            ->leftJoin('lote as lt', function ($join) use ($idAlmacen) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')
                    ->where('lt.ALM_Id', '=', $idAlmacen);
            })
            ->select(array_merge($columnas, [
                DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as PRO_Cantidad'),
                DB::raw('COALESCE(MAX(lt.LOT_PrecioVenta), p.PRO_PrecioVenta) as PRO_PrecioBaseVenta'),
            ]))
            ->where('p.PRO_Status', 1);

        if ($request->categoria != 'all') {
            $query->where('p.CAT_Id', $request->categoria);
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $busqueda = '%' . $request->search . '%';
                $q->where('p.PRO_Nombre', 'like', $busqueda)
                    ->orWhere('p.PRO_CodigoInterno', 'like', $busqueda)
                    ->orWhere('p.PRO_CodigoFabricacion', 'like', $busqueda);
            });
        }

        $query->groupBy($agrupar);

        // Sin el permiso de la sede, solo se ofrece lo que tenga stock
        // disponible -- salvo un Servicio, que nunca tiene lotes y siempre
        // debe poder venderse.
        if (! $permitirSinStock) {
            $query->havingRaw("COALESCE(SUM(lt.LOT_CantidadReal), 0) > 0 OR MAX(p.PRO_TipoProducto) = 'SERVICIO'");
        }

        $productos = $query->paginate(20);

        // Galeria (hasta 4 fotos adicionales, sumadas a PRO_Imagen dan 5 en
        // total -- ver ProductoController::subirImagenGaleria()): se pide
        // aparte en vez de con JOIN para no multiplicar filas del GROUP BY
        // de arriba. Se usa PROI_url (no PROI_Thumb) porque esto alimenta
        // la previsualizacion grande del POS, no una miniatura.
        $idsPagina = $productos->getCollection()->pluck('PRO_Id')->all();
        $galeriasPorProducto = ProductoImagen::whereIn('PRO_Id', $idsPagina)
            ->orderBy('PROI_Item')
            ->get(['PRO_Id', 'PROI_url'])
            ->groupBy('PRO_Id');

        $productos->getCollection()->transform(function ($producto) use ($galeriasPorProducto) {
            $producto->galeria = ($galeriasPorProducto->get($producto->PRO_Id) ?? collect())
                ->pluck('PROI_url')
                ->values();

            return $producto;
        });

        return response()->json($productos);
    }
}
