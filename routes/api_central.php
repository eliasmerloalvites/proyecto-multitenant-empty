<?php

use App\Http\Controllers\Api\V1\Central\DirectorioController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API movil v1 - dominio central
|--------------------------------------------------------------------------
| Se registra solo en los dominios centrales (ver TenancyServiceProvider).
| Prefijo /api/v1. Aqui no hay tenant inicializado.
*/

Route::get('talleres/{codigo}', [DirectorioController::class, 'show'])
    ->middleware('throttle:app-movil-directorio')
    ->name('api.v1.directorio.show');
