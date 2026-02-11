<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
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
        return view('welcome',compact('tenantid'));
    }

    public function salir()
    {
        Auth::guard('central')->logout();
        return redirect()->route('central.login');
    }
}
