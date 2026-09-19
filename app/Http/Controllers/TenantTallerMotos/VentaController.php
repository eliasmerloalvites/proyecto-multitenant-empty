<?php

namespace App\Http\Controllers\TenantTallerMotos;

/**
 * Controlador de Ventas propio de taller de motos. Por ahora hereda toda la
 * logica de App\Http\Controllers\Tenant\VentaController (que sigue siendo la
 * base con la logica compartida, incluidos sus metodos estaticos usados por
 * otras clases como CotizacionController/VentaComprobanteController). El
 * binding en AppServiceProvider hace que las rutas de Ventas resuelvan a
 * ESTA clase cuando el tenant es 'tallermoto', asi que a partir de ahora se
 * puede sobreescribir aqui cualquier metodo (create(), store(), etc.) sin
 * afectar a generico.
 */
class VentaController extends \App\Http\Controllers\Tenant\VentaController
{
}
