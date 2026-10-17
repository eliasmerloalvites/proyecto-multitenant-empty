<?php

namespace App\Http\Resources\Api\V1;

use App\Models\TenantTallerMotos\CuentaMovil;
use App\Services\AppMovil\NormalizadorCelular;

/** Datos de la cuenta que la app puede ver de si misma (celular enmascarado). */
class CuentaPublica
{
    public static function datos(CuentaMovil $cuenta): array
    {
        return [
            'id' => $cuenta->CMV_Id,
            'celular' => NormalizadorCelular::enmascarar($cuenta->CMV_Celular),
            'nombre' => $cuenta->CMV_Nombre,
            'correo' => $cuenta->CMV_Correo,
            'verificada' => $cuenta->CMV_VerificadoEn !== null,
        ];
    }
}
