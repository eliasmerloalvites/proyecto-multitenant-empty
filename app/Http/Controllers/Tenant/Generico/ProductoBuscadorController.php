<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Busqueda de productos para selects tipo "select2 AJAX", exclusiva de
 * generico. Antes, la pagina de Compras (compra/create.blade.php) cargaba
 * TODOS los productos de una vez en un <select> (un @foreach en la vista
 * con una opcion por producto): con un catalogo de varios miles de
 * productos eso es lentisimo/puede colgar el navegador.
 *
 * Este endpoint pagina y filtra por texto en la base de datos (igual que ya
 * hace VentaController::getProductos() para el catalogo de Ventas), asi que
 * el select solo pide y dibuja los productos que el usuario esta buscando,
 * no el catalogo completo. No se toco getProductos() porque ese es
 * compartido con tallermoto; ademas Compras necesita ver productos SIN
 * stock (para poder reabastecerlos) y el precio de compra, cosas que ese
 * endpoint (pensado para vender) no expone.
 */
class ProductoBuscadorController extends Controller
{
    public function compra(Request $request)
    {
        $search = trim((string) $request->get('search', ''));
        $page = max(1, (int) $request->get('page', 1));
        $porPagina = 20;

        $query = DB::table('producto as p')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_PrecioCompra', 'p.PRO_PrecioVenta', 'c.CAT_Nombre')
            ->where('p.PRO_Status', 1);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('p.PRO_Nombre', 'like', '%' . $search . '%')
                    ->orWhere('c.CAT_Nombre', 'like', '%' . $search . '%');
            });
        }

        $total = (clone $query)->count();

        $productos = $query
            ->orderBy('p.PRO_Nombre')
            ->forPage($page, $porPagina)
            ->get();

        $results = $productos->map(function ($p) {
            return [
                // Mismo formato "id_precioCompra_precioVenta" que ya
                // esperaba el JS de compra/create.blade.php, para no
                // tener que tocar el resto de esa logica.
                'id' => $p->PRO_Id . '_' . $p->PRO_PrecioCompra . '_' . $p->PRO_PrecioVenta,
                'text' => $p->CAT_Nombre . ' - ' . $p->PRO_Nombre,
            ];
        });

        return response()->json([
            'results' => $results,
            'pagination' => [
                'more' => ($page * $porPagina) < $total,
            ],
        ]);
    }
}
