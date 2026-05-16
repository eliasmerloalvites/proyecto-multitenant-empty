<?php

declare(strict_types=1);

use App\Http\Controllers\ConsultaDocumentoController;
use App\Http\Controllers\Tenant\PermisoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\AlmacenController;
use App\Http\Controllers\Tenant\CategoriaController;
use App\Http\Controllers\Tenant\RoleController;
use App\Http\Controllers\Tenant\ClaseController;
use App\Http\Controllers\Tenant\ClienteController;
use App\Http\Controllers\Tenant\CompraController;
use App\Http\Controllers\Tenant\GastoController;
use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\MetodoPagoController;
use App\Http\Controllers\Tenant\ProductoController;
use App\Http\Controllers\Tenant\ProveedorController;
use App\Http\Controllers\Tenant\TipoGastoController;
use App\Http\Controllers\Tenant\UserController;
use App\Http\Controllers\Tenant\VentaController;
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
    
    Route::get('/', [HomeController::class, 'inicio'])->name('tenant.inicio');
    
    
    Route::get('/tenant/login', [UserController::class, 'showlogin'])->name('tenant.login');
    Route::post('/tenant/login', [UserController::class, 'login'])->name('tenant.login.post');
    Route::get('/tenant/seguridad/cancelarusuario',   function () {
        return redirect()->route('tenant.seguridad.usuarios.index')->with('datos', 'Acción Cancelada...!');
    })->name('tenant.seguridad.usuario.cancelar');
    Route::post('/tenant/logout', [UserController::class, 'logout'])->name('tenant.logout');

    
Route::get('/consulta', [ConsultaDocumentoController::class,'index'])->name('consulta');
Route::get('/consultardni/{id}', [ConsultaDocumentoController::class,'buscarDni'] )->name('consultar.reniec');
Route::get('/consultasunat', [ConsultaDocumentoController::class,'indexsunsat'] )->name('consultarsunat');
Route::get('/consultarruc/{id}', [ConsultaDocumentoController::class,'buscarRuc'] )->name('consultar.sunat');

    Route::middleware(['auth:tenant'])->group(function(){  
        Route::get('/tenant/home', [HomeController::class,'index'])->name('tenant.home');
        Route::get('/tenant/personal/getimagen', [ProfileController::class, 'getimagen'])->name('tenant.personal.getimagen');
        
        Route::resource('/tenant/ventas/venta', VentaController::class)->names([
            'index' => 'tenant.ventas.venta.index',
            'create' => 'tenant.ventas.venta.create',
            'store' => 'tenant.ventas.venta.store',
            'edit' => 'tenant.ventas.venta.edit',
            'update' => 'tenant.ventas.venta.update',
            'destroy' => 'tenant.ventas.venta.destroy',
            'show' => 'tenant.ventas.venta.show'
        ])->parameters([
            'venta' => 'venta'
        ]);

        Route::resource('/tenant/compras/compra', CompraController::class)->names([
            'index' => 'tenant.compras.compra.index',
            'create' => 'tenant.compras.compra.create',
            'store' => 'tenant.compras.compra.store',
            'edit' => 'tenant.compras.compra.edit',
            'update' => 'tenant.compras.compra.update',
            'destroy' => 'tenant.compras.compra.destroy',
            'show' => 'tenant.compras.compra.show'
        ])->parameters([
            'compra' => 'compra'
        ]);


        Route::resource('/tenant/ventas/cliente', ClienteController::class)->names([
            'index' => 'tenant.ventas.cliente.index',
            'create' => 'tenant.ventas.cliente.create',
            'store' => 'tenant.ventas.cliente.store',
            'edit' => 'tenant.ventas.cliente.edit',
            'update' => 'tenant.ventas.cliente.update',
            'destroy' => 'tenant.ventas.cliente.destroy',
            'show' => 'tenant.ventas.cliente.show'
        ])->parameters([
            'cliente' => 'cliente'
        ]);
        
        Route::get('/tenant/ventas/venta/{id}/ticket', [VentaController::class,'ticket'] )->name('tenant.ventas.venta.ticket');
        Route::get('/tenant/ventas/venta/{id}/pdf', [VentaController::class,'pdf'] )->name('tenant.ventas.venta.pdf');
        Route::get('/tenant/ventas/venta/filtro/{filtro}', [VentaController::class,'filtro'] )->name('tenant.ventas.venta.filtro');
        Route::get('/tenant/ventas/venta/{id}/ticket-imagen',[VentaController::class, 'ticketImagen'])->name('tenant.ventas.venta.ticket-imagen');

        Route::resource('/tenant/compras/proveedor', ProveedorController::class)->names([
            'index' => 'tenant.compras.proveedor.index',
            'create' => 'tenant.compras.proveedor.create',
            'store' => 'tenant.compras.proveedor.store',
            'edit' => 'tenant.compras.proveedor.edit',
            'update' => 'tenant.compras.proveedor.update',
            'destroy' => 'tenant.compras.proveedor.destroy',
            'show' => 'tenant.compras.proveedor.show'
        ])->parameters([
            'proveedor' => 'proveedor'
        ]);

        
        Route::resource('/tenant/ventas/metodopago', MetodoPagoController::class)->names([
            'index' => 'tenant.ventas.metodopago.index',
            'create' => 'tenant.ventas.metodopago.create',
            'store' => 'tenant.ventas.metodopago.store',
            'edit' => 'tenant.ventas.metodopago.edit',
            'update' => 'tenant.ventas.metodopago.update',
            'destroy' => 'tenant.ventas.metodopago.destroy',
            'show' => 'tenant.ventas.metodopago.show'
        ])->parameters([
            'metodopago' => 'metodopago'
        ]);

        Route::resource('/tenant/compras/gasto', GastoController::class)->names([
            'index' => 'tenant.compras.gasto.index',
            'create' => 'tenant.compras.gasto.create',
            'store' => 'tenant.compras.gasto.store',
            'edit' => 'tenant.compras.gasto.edit',
            'update' => 'tenant.compras.gasto.update',
            'destroy' => 'tenant.compras.gasto.destroy',
            'show' => 'tenant.compras.gasto.show'
        ])->parameters([
            'gasto' => 'gasto'
        ]);
        
        Route::resource('/tenant/compras/tipogasto', TipoGastoController::class)->names([
            'index' => 'tenant.compras.tipogasto.index',
            'create' => 'tenant.compras.tipogasto.create',
            'store' => 'tenant.compras.tipogasto.store',
            'edit' => 'tenant.compras.tipogasto.edit',
            'update' => 'tenant.compras.tipogasto.update',
            'destroy' => 'tenant.compras.tipogasto.destroy',
            'show' => 'tenant.compras.tipogasto.show'
        ])->parameters([
            'tipogasto' => 'tipogasto'
        ]);
        
        Route::get('/tenant/inventario/controlinventario', [ProductoController::class,'controlinventario'] )->name('tenant.inventario.controlinventario.index');
        Route::get('/tenant/inventario/controlinventario/{producto}', [ProductoController::class,'lotes'] )->name('tenant.inventario.controlinventario.lotes');
        Route::resource('/tenant/inventario/producto', ProductoController::class)->names([
            'index' => 'tenant.inventario.producto.index',
            'create' => 'tenant.inventario.producto.create',
            'store' => 'tenant.inventario.producto.store',
            'edit' => 'tenant.inventario.producto.edit',
            'update' => 'tenant.inventario.producto.update',
            'destroy' => 'tenant.inventario.producto.destroy',
            'show' => 'tenant.inventario.producto.show',
        ])->parameters([
            'producto' => 'producto'
        ]);

        Route::resource('/tenant/inventario/categoria', CategoriaController::class)->names([
            'index' => 'tenant.inventario.categoria.index',
            'create' => 'tenant.inventario.categoria.create',
            'store' => 'tenant.inventario.categoria.store',
            'edit' => 'tenant.inventario.categoria.edit',
            'update' => 'tenant.inventario.categoria.update',
            'destroy' => 'tenant.inventario.categoria.destroy',
            'show' => 'tenant.inventario.categoria.show'
        ])->parameters([
            'categoria' => 'categoria'
        ]);

        Route::resource('/tenant/inventario/clase', ClaseController::class)->names([
            'index' => 'tenant.inventario.clase.index',
            'create' => 'tenant.inventario.clase.create',
            'store' => 'tenant.inventario.clase.store',
            'edit' => 'tenant.inventario.clase.edit',
            'update' => 'tenant.inventario.clase.update',
            'destroy' => 'tenant.inventario.clase.destroy',
            'show' => 'tenant.inventario.clase.show'
        ]);

        Route::resource('/tenant/inventario/almacen', AlmacenController::class)->names([
            'index' => 'tenant.inventario.almacen.index',
            'create' => 'tenant.inventario.almacen.create',
            'store' => 'tenant.inventario.almacen.store',
            'edit' => 'tenant.inventario.almacen.edit',
            'update' => 'tenant.inventario.almacen.update',
            'destroy' => 'tenant.inventario.almacen.destroy',
            'show' => 'tenant.inventario.almacen.show'
        ])->parameters([
            'almacen' => 'almacen'
        ]);

        Route::resource('/tenant/seguridad/usuario', UserController::class)->names([
            'index' => 'tenant.seguridad.usuario.index',
            'create' => 'tenant.seguridad.usuario.create',
            'store' => 'tenant.seguridad.usuario.store',
            'edit' => 'tenant.seguridad.usuario.edit',
            'update' => 'tenant.seguridad.usuario.update',
            'destroy' => 'tenant.seguridad.usuario.destroy',
            'show' => 'tenant.seguridad.usuario.show'
        ]);
        Route::resource('/tenant/seguridad/permiso', PermisoController::class)->names([
            'index' => 'tenant.seguridad.permiso.index',
            'create' => 'tenant.seguridad.permiso.create',
            'store' => 'tenant.seguridad.permiso.store',
            'edit' => 'tenant.seguridad.permiso.edit',
            'update' => 'tenant.seguridad.permiso.update',
            'destroy' => 'tenant.seguridad.permiso.destroy',
            'show' => 'tenant.seguridad.permiso.show'
        ]);
        Route::resource('/tenant/seguridad/rol', RoleController::class)->names([
            'index' => 'tenant.seguridad.role.index',
            'create' => 'tenant.seguridad.role.create',
            'store' => 'tenant.seguridad.role.store',
            'edit' => 'tenant.seguridad.role.edit',
            'update' => 'tenant.seguridad.role.update',
            'destroy' => 'tenant.seguridad.role.destroy',
            'show' => 'tenant.seguridad.role.show'
        ]);

    });
    
});

Route::get('/__debug', function () {
    dd([
        'host' => request()->getHost(),
        'tenant' => tenant(),
    ]);
});
Route::get('/__who', fn () => dd('TENANT', tenant()));

// Route::get('/test-tenant', function () {
//         return response()->json([
//             'tenant_id' => tenant('id'),
//             'database' => config('database.connections.tenant.database'),
//             'host' => request()->getHost()
//         ]);
//     }); 
