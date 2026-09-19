<?php

namespace App\Http\Controllers\Tenant\Generico;

/**
 * Controlador de Ventas propio de generico. Por ahora hereda toda la logica
 * de App\Http\Controllers\Tenant\VentaController (base compartida). El
 * binding en AppServiceProvider hace que las rutas de Ventas resuelvan a
 * ESTA clase cuando el tenant es 'generico', asi que a partir de ahora se
 * puede sobreescribir aqui cualquier metodo sin afectar a taller de motos.
 */
class VentaController extends \App\Http\Controllers\Tenant\VentaController
{
}
