<?php

namespace App\Http\Controllers\TenantTallerMotos;

use Exception;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Compras propio de taller de motos. Hereda toda la logica
 * compartida de App\Http\Controllers\Tenant\CompraController y solo
 * sobreescribe el catalogo de "Nueva compra" para excluir los productos
 * marcados como Servicio (PRO_TipoProducto, columna exclusiva de
 * tallermoto): un servicio no tiene stock/lotes, asi que no tiene sentido
 * ofrecerlo como algo reabastecible. El binding en AppServiceProvider hace
 * que las rutas de Compras resuelvan a ESTA clase cuando el tenant es
 * 'tallermoto'.
 */
class CompraController extends \App\Http\Controllers\Tenant\CompraController
{
    protected function productosParaCompra()
    {
        return DB::table('producto as p')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.*', 'c.*')
            ->where('p.PRO_TipoProducto', 'PRODUCTO')
            ->get();
    }

    protected function validarProductosComprables(array $proIds): void
    {
        if (empty($proIds)) {
            return;
        }

        $servicios = DB::table('producto')
            ->whereIn('PRO_Id', $proIds)
            ->where('PRO_TipoProducto', 'SERVICIO')
            ->pluck('PRO_Nombre');

        if ($servicios->isNotEmpty()) {
            throw new Exception(
                'No se puede comprar/agregar stock a un Servicio: ' . $servicios->implode(', ') . '.'
            );
        }
    }
}
