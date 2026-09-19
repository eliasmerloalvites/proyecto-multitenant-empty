<?php

namespace App\Http\Controllers\TenantTallerMotos;

/**
 * Controlador de Productos propio de taller de motos. Hereda de
 * App\Http\Controllers\Tenant\ProductoController (base compartida, que ya
 * trae los guard-flags tenantTieneCodigosProducto()/tenantTieneGaleriaProducto()/
 * tenantTieneCatalogoWeb() para las columnas/tablas exclusivas de este
 * vertical). El binding en AppServiceProvider hace que las rutas de
 * Inventario > Producto resuelvan a ESTA clase cuando el tenant es
 * 'tallermoto', asi que a partir de ahora se puede sobreescribir aqui
 * cualquier metodo sin afectar a generico.
 */
class ProductoController extends \App\Http\Controllers\Tenant\ProductoController
{
}
