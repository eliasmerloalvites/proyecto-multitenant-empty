<?php

use App\Http\Controllers\Central\ClientController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\central\UserController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Database\Models\Domain;

Route::middleware([
  'web',
  'central',
  ])->group(function () {
  Route::get('/', [HomeController::class, 'inicio'])->name('inicio');
  Route::get('/login', [UserController::class, 'showlogin'])->name('central.login');
  Route::post('/login', [UserController::class, 'login'])->name('central.login.post');
  Route::get('/cancelarusuario', function () {
    return redirect()->route('central-usuarios.index')->with('datos', 'Acción Cancelada...!');
  })->name('central.usuario.cancelar');
  Route::post('/logout', [UserController::class, 'logout'])->name('central.logout');


  Route::middleware(['auth:central'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('central.home');
    // Route::get('/personal/getimagen', [ProfileController::class, 'getimagen'])->name('personal.getimagen');
    Route::resource('permiso', PermisoController::class);
    Route::resource('role', RoleController::class);
    Route::resource('central-usuarios', UserController::class);
    Route::resource('admin/clients', ClientController::class)->names([
      'index' => 'admin.clients.index',
      'create' => 'admin.clients.create',
      'store' => 'admin.clients.store',
      'edit' => 'admin.clients.edit',
      'update' => 'admin.clients.update',
      'destroy' => 'admin.clients.destroy',
      'show' => 'admin.clients.show'
    ]);
  });
});

Route::get('/debug-domains', function () {
    return response()->json([
        'Request Host' => request()->getHost(),
        'Domains in DB' => Domain::all()->pluck('domain'),
    ]);
});
