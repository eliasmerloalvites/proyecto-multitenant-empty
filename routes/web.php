<?php

use App\Http\Controllers\Central\ClientController;
use App\Http\Controllers\Central\ComisionReporteController;
use App\Http\Controllers\Central\CulqiWebhookController;
use App\Http\Controllers\Central\EsquemaComisionController;
use App\Http\Controllers\Central\HomeController;
use App\Http\Controllers\Central\PagoController;
use App\Http\Controllers\Central\PlanController;
use App\Http\Controllers\Central\RegistroController;
use App\Http\Controllers\Central\AuditLogController;
use App\Http\Controllers\Central\VendedorController;
use App\Http\Controllers\Central\Vendedor\ClienteController as VendedorClienteController;
use App\Http\Controllers\Central\Vendedor\ComisionController as VendedorComisionController;
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

  Route::view('/terminos-y-condiciones', 'central.legal.terminos')->name('central.terminos');
  Route::view('/politica-de-privacidad', 'central.legal.privacidad')->name('central.privacidad');

  // Landing multi-página: cada ítem del menú carga su propia página (solo
  // esa sección), no un scroll dentro de una sola página larga. El footer
  // vive en el layout compartido, así que aparece en todas.
  Route::view('/soluciones', 'central.landing.pages.soluciones')->name('central.soluciones');
  Route::view('/planes', 'central.landing.pages.planes')->name('central.planes');
  Route::view('/clientes', 'central.landing.pages.clientes')->name('central.clientes');

  // Culqi llama a este endpoint server-a-server (sin sesión, sin CSRF —
  // ver bootstrap/app.php). No confía en el body: vuelve a consultar el
  // evento contra la API de Culqi antes de procesar nada (ver
  // CulqiWebhookController).
  Route::post('/webhooks/culqi', [CulqiWebhookController::class, 'handle'])->name('webhooks.culqi');



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

    // --- Vendedores comerciales y comisiones ---
    // A diferencia del resto del panel central (protegido solo por
    // auth:central + @can visual en el sidebar), acá sí se agrega
    // middleware can: en cada ruta: son datos financieros de otros
    // vendedores, no basta con ocultar el enlace del menú.
    Route::get('admin/vendedores', [VendedorController::class, 'index'])
      ->middleware('can:admin.vendedores.index')->name('admin.vendedores.index');
    Route::post('admin/vendedores', [VendedorController::class, 'store'])
      ->middleware('can:admin.vendedores.create')->name('admin.vendedores.store');
    Route::get('admin/vendedores/{vendedor}/edit', [VendedorController::class, 'edit'])
      ->middleware('can:admin.vendedores.edit')->name('admin.vendedores.edit');
    Route::put('admin/vendedores/{vendedor}', [VendedorController::class, 'update'])
      ->middleware('can:admin.vendedores.edit')->name('admin.vendedores.update');
    Route::delete('admin/vendedores/{vendedor}', [VendedorController::class, 'destroy'])
      ->middleware('can:admin.vendedores.destroy')->name('admin.vendedores.destroy');
    Route::post('admin/vendedores/{vendedor}/esquema', [EsquemaComisionController::class, 'store'])
      ->middleware('can:admin.vendedores.edit')->name('admin.vendedores.esquema.store');

    Route::get('admin/comisiones', [ComisionReporteController::class, 'index'])
      ->middleware('can:admin.comisiones.index')->name('admin.comisiones.index');
    Route::post('admin/comisiones/liquidar', [ComisionReporteController::class, 'liquidar'])
      ->middleware('can:admin.comisiones.liquidar')->name('admin.comisiones.liquidar');

    // --- Panel del vendedor (siempre acotado a "el vendedor logueado") ---
    Route::get('mi-panel/clientes', [VendedorClienteController::class, 'index'])
      ->middleware('can:vendedor.clientes.index')->name('vendedor.clientes.index');
    Route::post('mi-panel/clientes', [VendedorClienteController::class, 'store'])
      ->middleware('can:vendedor.clientes.create')->name('vendedor.clientes.store');
    Route::get('mi-panel/comisiones', [VendedorComisionController::class, 'index'])
      ->middleware('can:vendedor.comisiones.index')->name('vendedor.comisiones.index');
  });
});
Route::get('/__who', fn () => dd('CENTRAL', tenant()));
Route::get('/__central-debug', function () {
    dd([
        'tenant' => tenant(),
        'middleware' => app('router')->getRoutes()->getByName('central.inicio')->middleware(),
    ]);
});
