<?php

use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\Auth\SesionController;
use App\Http\Controllers\Api\V1\CatalogoController;
use App\Http\Controllers\Api\V1\MotoController;
use App\Http\Controllers\Api\V1\OrdenController;
use App\Http\Controllers\Api\V1\PedidoController;
use App\Http\Controllers\Api\V1\PerfilController;
use App\Http\Controllers\Api\V1\ReservaController;
use App\Http\Controllers\Api\V1\TallerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API movil v1 - taller (tenant)
|--------------------------------------------------------------------------
| Prefijo /api/v1. El taller se identifica por el Host y ya esta validado
| (activo y con el modulo app_movil) cuando se llega aqui; ver
| TenancyServiceProvider::mapRoutes.
*/

// Sin sesion.
Route::get('taller', [TallerController::class, 'show'])->name('api.v1.taller');

Route::post('auth/otp/solicitar', [OtpController::class, 'solicitar'])
    ->middleware('throttle:app-movil-otp-solicitar')
    ->name('api.v1.auth.otp.solicitar');

Route::post('auth/otp/verificar', [OtpController::class, 'verificar'])
    ->middleware('throttle:app-movil-otp-verificar')
    ->name('api.v1.auth.otp.verificar');

// Con sesion (token Sanctum del cliente).
Route::middleware(['auth:cliente', 'api.cuenta', 'throttle:app-movil-api'])->group(function () {
    Route::post('auth/refresh', [SesionController::class, 'refresh'])->name('api.v1.auth.refresh');
    Route::post('auth/logout', [SesionController::class, 'logout'])->name('api.v1.auth.logout');
    Route::post('auth/logout-all', [SesionController::class, 'logoutAll'])->name('api.v1.auth.logout-all');

    Route::get('perfil', [PerfilController::class, 'show'])->name('api.v1.perfil.show');
    Route::patch('perfil', [PerfilController::class, 'update'])->name('api.v1.perfil.update');

    // Mi garaje. Las rutas fijas van antes que {moto}.
    Route::get('motos', [MotoController::class, 'index'])->name('api.v1.motos.index');
    Route::post('motos/actualizar', [MotoController::class, 'actualizar'])->middleware('throttle:app-movil-sincronizar')->name('api.v1.motos.actualizar');
    Route::post('motos/vincular-invitacion', [MotoController::class, 'vincularInvitacion'])->middleware('throttle:app-movil-invitacion')->name('api.v1.motos.vincular-invitacion');
    Route::post('motos/solicitar-vinculo', [MotoController::class, 'solicitarVinculo'])->middleware('throttle:app-movil-solicitud')->name('api.v1.motos.solicitar-vinculo');
    Route::get('motos/{moto}', [MotoController::class, 'show'])->whereNumber('moto')->name('api.v1.motos.show');
    Route::delete('motos/{moto}/vinculo', [MotoController::class, 'desvincular'])->whereNumber('moto')->name('api.v1.motos.desvincular');

    // Ordenes de servicio (historial y estado).
    Route::get('ordenes', [OrdenController::class, 'index'])->name('api.v1.ordenes.index');
    Route::get('ordenes/{ref}', [OrdenController::class, 'show'])->where('ref', '[a-z]{3}-[0-9]+')->name('api.v1.ordenes.show');
    Route::post('ordenes/{ref}/firmar', [OrdenController::class, 'firmar'])->where('ref', '[a-z]{3}-[0-9]+')->middleware('throttle:10,1')->name('api.v1.ordenes.firmar');
    Route::get('ordenes/{ref}/pdf', [OrdenController::class, 'pdf'])->where('ref', '[a-z]{3}-[0-9]+')->name('api.v1.ordenes.pdf');
    Route::get('ordenes/{ref}/fotos/{item}', [OrdenController::class, 'foto'])->where('ref', '[a-z]{3}-[0-9]+')->whereNumber('item')->name('api.v1.ordenes.foto');

    // Reservas. Las rutas fijas van antes que {id}.
    Route::get('reservas/sedes', [ReservaController::class, 'sedes'])->name('api.v1.reservas.sedes');
    Route::get('reservas/disponibilidad', [ReservaController::class, 'disponibilidad'])->name('api.v1.reservas.disponibilidad');
    Route::get('reservas', [ReservaController::class, 'index'])->name('api.v1.reservas.index');
    Route::post('reservas', [ReservaController::class, 'store'])->middleware('throttle:app-movil-reserva')->name('api.v1.reservas.store');
    Route::get('reservas/{id}', [ReservaController::class, 'show'])->whereNumber('id')->name('api.v1.reservas.show');
    Route::delete('reservas/{id}', [ReservaController::class, 'cancelar'])->whereNumber('id')->name('api.v1.reservas.cancelar');

    // Tienda: catalogo de productos y pedidos (pago y recojo en el taller).
    Route::get('catalogo/categorias', [CatalogoController::class, 'categorias'])->name('api.v1.catalogo.categorias');
    Route::get('catalogo/productos', [CatalogoController::class, 'productos'])->name('api.v1.catalogo.productos');
    Route::get('catalogo/productos/{producto}', [CatalogoController::class, 'show'])->whereNumber('producto')->name('api.v1.catalogo.show');
    Route::get('pedidos', [PedidoController::class, 'index'])->name('api.v1.pedidos.index');
    Route::post('pedidos', [PedidoController::class, 'store'])->middleware('throttle:app-movil-pedido')->name('api.v1.pedidos.store');
    Route::get('pedidos/{id}', [PedidoController::class, 'show'])->whereNumber('id')->name('api.v1.pedidos.show');
    Route::delete('pedidos/{id}', [PedidoController::class, 'cancelar'])->whereNumber('id')->name('api.v1.pedidos.cancelar');
});
