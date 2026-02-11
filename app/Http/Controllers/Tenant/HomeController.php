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
        return view('tenant_generico.menu.home');
    }

    
    public function inicio()
    {
        if(tenant() !== null){
            $tenantid = tenant('id');
        } else {
            $tenantid = null;
        }
        return view('welcome', compact('tenantid'));
    }
    public function salir()
    {
        Auth::guard('tenant')->logout();
        return redirect()->route('tenant_generico.login');
    }
}
