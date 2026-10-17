<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Support\Collection;

/**
 * Lo que la app puede mostrar de un taller SIN iniciar sesion: marca y
 * contacto publico. Es una lista blanca a proposito: EmpresaFacturacion
 * guarda tambien credenciales SUNAT y del certificado, y nunca deben salir
 * por esta via (el modelo ademas las oculta con $hidden).
 */
class TallerPublico
{
    /**
     * @param  string|null  $base  Origen (https://host) para convertir rutas relativas
     *                              (/storage/...) en URL absolutas. Si es null, usa el de la peticion.
     */
    public static function datos(?EmpresaFacturacion $empresa, Collection $sedes, ?string $base = null): array
    {
        return [
            'nombre' => $empresa?->nombre_comercial ?: $empresa?->razon_social,
            'logo' => self::url($empresa?->logo, $base),
            'tema' => [
                'modo' => $empresa?->tipo_tema ?: 'light',
                'color_principal' => $empresa?->color_main,
                'color_secundario' => $empresa?->color_light,
                'fondo' => $empresa?->color_bg,
                'tarjeta' => $empresa?->color_card,
            ],
            'contacto' => [
                'telefono' => $empresa?->telefono,
                'whatsapp' => $empresa?->whatsapp,
                'correo' => $empresa?->correo,
                'web' => $empresa?->web,
                'direccion' => $empresa?->direccion,
            ],
            'sedes' => $sedes->map(fn ($s) => [
                'id' => $s->ALM_Id,
                'nombre' => $s->ALM_NombreAlmacen,
                'direccion' => $s->ALM_Direccion,
                'referencia' => $s->ALM_Referencia,
                'telefono' => $s->ALM_Celular ?: $s->ALM_Telefono,
                'latitud' => $s->ALM_Latitud,
                'longitud' => $s->ALM_Longitud,
            ])->values()->all(),
        ];
    }

    private static function url(?string $ruta, ?string $base): ?string
    {
        if (! $ruta) {
            return null;
        }

        if (preg_match('#^https?://#i', $ruta)) {
            return $ruta;
        }

        return rtrim($base ?? request()->getSchemeAndHttpHost(), '/') . '/' . ltrim($ruta, '/');
    }
}
