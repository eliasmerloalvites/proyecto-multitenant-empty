<?php

use App\Http\Controllers\Api\V1\Auth\OtpController;
use App\Http\Controllers\Api\V1\Auth\SesionController;
use App\Http\Controllers\Api\V1\PerfilController;
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
});
