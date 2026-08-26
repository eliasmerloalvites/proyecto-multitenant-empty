<?php

namespace App\Services\Ventas;

use Illuminate\Support\Facades\DB;

/**
 * Crea un producto "al vuelo" para vender algo que no esta en el catalogo
 * (un servicio, un cargo especial, un producto todavia no registrado):
 * un Producto minimo mas un Lote con exactamente la cantidad a vender, para
 * que la venta se registre igual que cualquier otra sin depender de que la
 * sede tenga activado "vender sin stock".
 *
 * Se agrupan todos bajo la categoria/clase "Varios" para no ensuciar el
 * catalogo de categorias real del negocio. La usan tanto el punto de venta
 * tradicional como el tablero de Ventas por Bahia.
 */
class ItemRapidoService
{
    public function crear(string $nombre, float $cantidad, float $precio, int $idAlmacen): array
    {
        $clase = DB::table('clase')->where('CLA_Nombre', 'Varios')->first();
        if (!$clase) {
            $claseId = DB::table('clase')->insertGetId([
                'CLA_Nombre' => 'Varios',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $clase = (object) ['CLA_Id' => $claseId];
        }

        $categoria = DB::table('categoria')->where('CAT_Nombre', 'Varios')->where('CLA_Id', $clase->CLA_Id)->first();
        if (!$categoria) {
            $categoriaId = DB::table('categoria')->insertGetId([
                'CAT_Nombre' => 'Varios',
                'CLA_Id' => $clase->CLA_Id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $categoria = (object) ['CAT_Id' => $categoriaId];
        }

        $productoId = DB::table('producto')->insertGetId([
            'PRO_Nombre' => $nombre,
            'PRO_Descripcion' => 'Item agregado manualmente en el punto de venta.',
            'PRO_PrecioCompra' => 0,
            'PRO_PrecioVenta' => $precio,
            'CAT_Id' => $categoria->CAT_Id,
            'PRO_Status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('lote')->insert([
            'ALM_Id' => $idAlmacen,
            'PRO_Id' => $productoId,
            'LOT_TipoIngreso' => 'ITEM_RAPIDO',
            'LOT_IdIngreso' => 0,
            'LOT_CantidadReal' => $cantidad,
            'LOT_CantidadIngreso' => $cantidad,
            'LOT_PrecioCompra' => 0,
            'LOT_PrecioVenta' => $precio,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return [
            'PRO_Id' => $productoId,
            'PRO_Nombre' => $nombre,
            'PRO_Descripcion' => 'Item agregado manualmente',
            'PRO_Imagen' => null,
            'CAT_Id' => $categoria->CAT_Id,
            'PRO_Cantidad' => $cantidad,
            'PRO_PrecioBaseVenta' => $precio,
        ];
    }
}
