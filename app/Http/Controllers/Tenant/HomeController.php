<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        return view('tenant_generico.menu.home');
    }
    public function inicio()
    {
        return view('welcome');
    }

    public function salir()
    {
        Auth::guard('tenant')->logout();
        return redirect()->route('tenant_generico.login');
    }
}
