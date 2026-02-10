<?php

declare(strict_types=1);

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/test-tenant', function () {
        return response()->json([
            'tenant_id' => tenant('id'),
            'database' => config('database.connections.tenant.database'),
            'host' => request()->getHost()
        ]);
    });
    
    Route::get('/tenant/login', [UserController::class, 'showlogin'])->name('tenant.login');
    Route::post('/tenant/login', [UserController::class, 'login'])->name('tenant.login.post');
    Route::get('/tenant/cancelarusuario', function () {
        return redirect()->route('tenant-usuarios.index')->with('datos', 'Acción Cancelada...!');
    })->name('tenant.usuario.cancelar');
    Route::post('/tenant/logout', [UserController::class, 'logout'])->name('tenant.logout');

    Route::middleware(['auth:tenant'])->group(function(){  
        Route::get('/tenant/home', [HomeController::class,'index'])->name('tenant.home');
        Route::get('/tenant/personal/getimagen', [ProfileController::class, 'getimagen'])->name('tenant.personal.getimagen');
    });
});
