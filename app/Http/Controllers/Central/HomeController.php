<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class HomeController extends Controller
{
    public function index()
    {
        // dd(Permission::where('name','admin.clients.index')->first()->guard_name);

        return view('central.menu.home');
    }
    public function inicio()
    {
        $tenantid = null;
        if (tenant() !== null) {
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            $dataProductos = [];
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            if ($plan == 'start') {
                $colorview = $empresa->tipo_tema ?? 'dark';
                return view('tenant_' . $tiponegocio . '.welcome', compact('tenantid', 'plan', 'tiponegocio', 'empresa', 'colorview'));
            }

            // basic, plus y empresarial comparten la web completa (multi-página).
            $colorview = $empresa->tipo_tema ?? 'dark';

            if (tenant_has_module('productos')) {
                // Plus/Empresarial: Query Base para Productos con Lotes acumulados
                $queryProductos = DB::table('producto as pd')
                    ->join('categoria as ct', 'pd.CAT_Id', '=', 'ct.CAT_Id')
                    ->join('lote as lt', 'pd.PRO_Id', '=', 'lt.PRO_Id')
                    ->select(
                        'pd.PRO_Id',
                        'pd.PRO_Nombre',
                        'pd.PRO_Descripcion',
                        'pd.PRO_Marca',
                        'pd.PRO_PrecioVenta',
                        'pd.PRO_Imagen',
                        'ct.CAT_Id',
                        'ct.CAT_Nombre',
                        DB::raw('SUM(lt.LOT_CantidadReal) as cantidad_total')
                    )
                    ->groupBy(
                        'pd.PRO_Id',
                        'pd.PRO_Nombre',
                        'pd.PRO_Descripcion',
                        'pd.PRO_Marca',
                        'pd.PRO_PrecioVenta',
                        'pd.PRO_Imagen',
                        'ct.CAT_Id',
                        'ct.CAT_Nombre'
                    );

                // Paginación de 12 en 12 productos (Mantiene la query con string del buscador si aplica)
                $dataProductos = $queryProductos->paginate(4)->withQueryString();
            } else {
                // Basic: sin catálogo de productos habilitado.
                $dataProductos = null;
            }
            return view('tenant_' . $tiponegocio . '.landing.index', compact('tenantid', 'empresa', 'plan', 'tiponegocio', 'colorview', 'dataProductos'));
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function salir()
    {
        Auth::guard('central')->logout();
        return redirect()->route('central.login');
    }
}
