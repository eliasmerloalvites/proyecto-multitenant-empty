<?php

namespace App\Http\Controllers\Tenant\Generico;

/**
 * Controlador de Productos propio de generico. Hereda de
 * App\Http\Controllers\Tenant\ProductoController (base compartida). El
 * binding en AppServiceProvider hace que las rutas de Inventario > Producto
 * resuelvan a ESTA clase cuando el tenant es 'generico', asi que a partir de
 * ahora se puede sobreescribir aqui cualquier metodo sin afectar a taller de
 * motos.
 */
class ProductoController extends \App\Http\Controllers\Tenant\ProductoController
{
}
