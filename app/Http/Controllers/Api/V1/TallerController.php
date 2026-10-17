<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\TallerPublico;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use App\Support\ApiRespuesta;

/** Marca y contacto publico del taller al que se conecta la app (sin sesion). */
class TallerController extends Controller
{
    public function show()
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        $sedes = Almacen::where('ALM_Status', 1)->orderBy('ALM_EsPrincipal', 'desc')->orderBy('ALM_NombreAlmacen')->get();

        return ApiRespuesta::ok(TallerPublico::datos($empresa, $sedes));
    }
}
