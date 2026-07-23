<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if(tenant() !== null){
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            
		    $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            if($plan == 'start'){
                $colorview = $empresa->tipo_tema;
                return view('tenant_'.$tiponegocio.'.welcome',compact('tenantid', 'plan','empresa', 'colorview'));
            }else if($plan == 'basic'){
                $colorview = $empresa->tipo_tema;
                //dd($empresa->color_bg);
                return view('tenant_'.$tiponegocio.'.landing.index',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome',compact('tenantid'));
        }
        
    }

    public function salir()
    {
        Auth::guard('central')->logout();
        return redirect()->route('central.login');
    }
}
