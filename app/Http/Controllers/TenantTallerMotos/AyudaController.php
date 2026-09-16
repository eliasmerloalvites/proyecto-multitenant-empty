<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;

/**
 * Centro de Ayuda: manual de usuario in-app, explica paso a paso cada
 * modulo del sistema. Es una sola vista con contenido estatico (no depende
 * de datos del tenant), visible para cualquier usuario autenticado —
 * el objetivo es que cualquiera, sin importar su rol, pueda entender que
 * hace el sistema y como usarlo, no solo quien tiene permisos de admin.
 */
class AyudaController extends Controller
{
    public function index()
    {
        return view('tenant_tallermoto.ayuda.index');
    }
}
