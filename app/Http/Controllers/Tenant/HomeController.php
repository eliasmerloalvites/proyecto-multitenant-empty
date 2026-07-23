<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Tenant\EmpresaFacturacion;
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
            $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
            if($plan == 'start'){
                $colorview = $empresa->tipo_tema;
                return view('tenant_'.$tiponegocio.'.welcome',compact('tenantid', 'plan','empresa', 'colorview'));
            }else if($plan == 'basic'){
                $colorview = $empresa->tipo_tema;
                return view('tenant_'.$tiponegocio.'.landing.index',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function servicios()
    {
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
                return view('tenant_'.$tiponegocio.'.landing.page.servicio',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function reservar()
    {
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
                return view('tenant_'.$tiponegocio.'.landing.page.reservar',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function historial()
    {
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
                return view('tenant_'.$tiponegocio.'.landing.page.historial',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function catalogo()
    {
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
                return view('tenant_'.$tiponegocio.'.landing.page.catalogo',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }

    public function nosotros()
    {
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
                return view('tenant_'.$tiponegocio.'.landing.page.nosotros',compact('tenantid','empresa', 'plan', 'colorview'));
            }
        } else {
            $tenantid = null;
            return view('welcome', compact('tenantid'));
        }
    }
    public function contacto()
    {
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
                return view('tenant_'.$tiponegocio.'.landing.page.contacto',compact('tenantid','empresa', 'plan', 'colorview'));
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
