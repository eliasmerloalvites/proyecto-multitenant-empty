<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReflectionFunction;

class HomeController extends Controller
{
    public function index()
    {
        
        $tenantid = tenant('id');
        $tiponegocio = tenant('tipo_negocio');
        return view('tenant_'.$tiponegocio.'.menu.home');
    }

    
    public function inicio()
    {
        if(tenant() !== null){
            $tenantid = tenant('id');
            $tiponegocio = tenant('tipo_negocio');
            $plan = tenant('plan');
            if($plan == 'start'){
                return view('tenant_'.$tiponegocio.'.welcome',compact('tenantid', 'plan'));
            }else if($plan == 'basic'){
                return view('tenant_'.$tiponegocio.'.landing',compact('tenantid', 'plan'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }
    public function salir()
    {
        Auth::guard('tenant')->logout();
        $tenantName = str_replace(tenant()->tipo_negocio . '_','',tenant()->id);
        return redirect()->route('tenant.login',['tenant' => $tenantName]);
    }
}
