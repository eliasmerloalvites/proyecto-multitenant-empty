<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class EmpresaFacturacion extends Model
{
    protected $table = 'empresa_facturacion';

    protected $fillable = [
        // EMPRESA
        'tenant_id',
        'ruc',
        'razon_social',
        'nombre_comercial',

        // DIRECCION
        'ubigeo',
        'direccion',
        'departamento',
        'provincia',
        'distrito',
        'cod_local',

        // CONTACTO
        'telefono',
        'whatsapp',
        'correo',
        'web',

        // LOGOS
        'logo',
        'logo_pdf',

        // SOL
        'sol_usuario',
        'sol_password',

        // CERTIFICADO
        'certificado_ruta',
        'certificado_password',
        'certificado_vencimiento',

        // FACTURACION
        'ambiente',
        'proveedor_facturacion',
        'facturacion_electronica',

        // SERIES
        'serie_factura',
        'serie_boleta',
        'serie_nota_credito',
        'serie_nota_debito',

        // CONFIG
        'moneda',
        'decimales',
        'formato_pdf',

        // BRANDING
        'color_principal',
        'tipo_tema',
        'color_main', 'color_light', 'color_bg', 'color_card',

        // ESTADO
        'activo',
    ];

    protected $casts = [

        'activo' => 'boolean',

        'facturacion_electronica' => 'boolean',

        'certificado_vencimiento' => 'date',

        'decimales' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS
    |--------------------------------------------------------------------------
    */

    public function hexToRgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if (strlen($hex) == 3) {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        } else {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }

        return "$r, $g, $b";
    }

    public function getLogoUrlAttribute()
    {
        return $this->logo
            ? asset('storage/' . $this->logo)
            : null;
    }

    public function getLogoPdfUrlAttribute()
    {
        return $this->logo_pdf
            ? asset('storage/' . $this->logo_pdf)
            : null;
    }

    /*
    |--------------------------------------------------------------------------
    | HELPERS
    |--------------------------------------------------------------------------
    */

    public function esProduccion()
    {
        return $this->ambiente === 'produccion';
    }

    public function esBeta()
    {
        return $this->ambiente === 'beta';
    }

    public function certificadoVencido()
    {
        if (!$this->certificado_vencimiento) {
            return false;
        }

        return now()->greaterThan($this->certificado_vencimiento);
    }
}
