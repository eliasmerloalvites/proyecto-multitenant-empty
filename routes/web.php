<?php

use App\Http\Controllers\Central\ClientController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\PagoController;
use App\Http\Controllers\Central\PlanController;
use App\Http\Controllers\Central\RegistroController;
use App\Http\Controllers\Central\AuditLogController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Central\UserController;
use App\Http\Controllers\ConsultaDocumentoController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Database\Models\Domain;



Route::middleware([
  'web',
  'no-tenant',
  ])->group(function () {
  Route::get('/', [HomeController::class, 'inicio'])->name('central.inicio');
  Route::get('/login', [UserController::class, 'showlogin'])->name('central.login');
  Route::post('/login', [UserController::class, 'login'])->name('central.login.post');
  Route::get('/cancelarusuario', function () {
    return redirect()->route('central-usuarios.index')->with('datos', 'Acción Cancelada...!');
  })->name('central.usuario.cancelar');
  Route::post('/logout', [UserController::class, 'logout'])->name('central.logout');

  Route::get('/crear-empresa', [RegistroController::class, 'show'])->name('central.registro.show');
  Route::post('/crear-empresa', [RegistroController::class, 'store'])
    ->middleware('throttle:5,10') // máx. 5 intentos cada 10 minutos por IP
    ->name('central.registro.store');
  Route::get('/crear-empresa/verificar/{token}', [RegistroController::class, 'verificar'])->name('central.registro.verificar');

  

  Route::middleware(['auth:central'])->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('central.home');
    Route::get('/personal/getimagen', [ProfileController::class, 'getimagen'])->name('personal.getimagen');
    Route::resource('permiso', PermisoController::class);
    Route::resource('role', RoleController::class);
    Route::resource('usuario', UserController::class)->names([
            'index' => 'usuario.index',
            'create' => 'usuario.create',
            'store' => 'usuario.store',
            'edit' => 'usuario.edit',
            'update' => 'usuario.update',
            'destroy' => 'usuario.destroy',
            'show' => 'usuario.show'
        ]);
    Route::resource('admin/clients', ClientController::class)->names([
      'index' => 'admin.clients.index',
      'create' => 'admin.clients.create',
      'store' => 'admin.clients.store',
      'edit' => 'admin.clients.edit',
      'update' => 'admin.clients.update',
      'destroy' => 'admin.clients.destroy',
      'show' => 'admin.clients.show'
    ]);
    Route::patch('admin/clients/{client}/toggle-status', [ClientController::class, 'toggleStatus'])
      ->name('admin.clients.toggleStatus');

    Route::get('admin/cobros', [PagoController::class, 'index'])->name('admin.cobros.index');
    Route::post('admin/cobros/{client}', [PagoController::class, 'store'])->name('admin.cobros.store');
    Route::get('admin/cobros/{client}/historial', [PagoController::class, 'historial'])->name('admin.cobros.historial');

    Route::get('admin/planes', [PlanController::class, 'index'])->name('admin.planes.index');
    Route::put('admin/planes/{plan}', [PlanController::class, 'update'])->name('admin.planes.update');

    Route::get('admin/auditoria', [AuditLogController::class, 'index'])->name('admin.auditoria.index');
  });
});
Route::get('/__who', fn () => dd('CENTRAL', tenant()));
Route::get('/__central-debug', function () {
    dd([
        'tenant' => tenant(),
        'middleware' => app('router')->getRoutes()->getByName('central.inicio')->middleware(),
    ]);
});
