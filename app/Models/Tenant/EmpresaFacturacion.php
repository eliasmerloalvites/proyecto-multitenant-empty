<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmpresaFacturacion extends Model
{
    /* Tipos de documento internos, tal como se guardan en documento_venta. */
    const TIPO_BOLETA  = 'BOL';
    const TIPO_FACTURA = 'FAC';

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
        'logo_portada1',
        'logo_portada2',

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

    public function getLogoPortada1UrlAttribute()
    {
        return $this->logo_portada1
            ? asset('storage/' . $this->logo_portada1)
            : null;
    }

    public function getLogoPortada2UrlAttribute()
    {
        return $this->logo_portada2
            ? asset('storage/' . $this->logo_portada2)
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

    /**
     * Ruta absoluta del certificado en disco.
     *
     * En base de datos se guarda la URL publica que devuelve Storage::url()
     * (por ejemplo /storage/generico/abc/empresa/certificados/uuid.pem), no una
     * ruta de disco. Aqui se traduce a la ruta real del disco 'public'.
     */
    public function rutaCertificado(): ?string
    {
        if (!$this->certificado_ruta) {
            return null;
        }

        $relativa = ltrim(
            preg_replace('#^/?storage/#', '', $this->certificado_ruta),
            '/'
        );

        $absoluta = Storage::disk('public')->path($relativa);

        return is_file($absoluta) ? $absoluta : null;
    }

    /**
     * Extension del certificado, que la API necesita para saber como leerlo.
     */
    public function extensionCertificado(): string
    {
        return strtolower(
            pathinfo($this->certificado_ruta ?? '', PATHINFO_EXTENSION) ?: 'pem'
        );
    }

    /**
     * Modo que espera la API facturadora a partir del ambiente configurado.
     */
    public function modoApi(): string
    {
        return $this->esProduccion() ? 'PRODUCCION' : 'BETA';
    }

    /**
     * Campos sin los cuales SUNAT no acepta un comprobante.
     *
     * El domicilio (ubigeo, direccion, departamento, provincia, distrito), el
     * codigo de local y las series NO estan aqui: viven en la sede, porque
     * cada establecimiento anexo tiene su propia direccion y su propia
     * numeracion. Los valida la sede desde donde se emite.
     */
    const CAMPOS_REQUERIDOS = [
        'ruc'          => 'el RUC de la empresa',
        'razon_social' => 'la razon social',
        'sol_usuario'  => 'el usuario SOL',
        'sol_password' => 'la clave SOL',
    ];

    /**
     * Motivos por los que esta empresa todavia no puede facturar, redactados
     * para que los lea quien atiende la caja.
     * Devuelve un arreglo vacio cuando la configuracion esta completa.
     *
     * @return string[]
     */
    public function problemasDeConfiguracion(): array
    {
        $problemas = [];

        if (!$this->facturacion_electronica) {
            $problemas[] = 'La facturacion electronica esta desactivada.';
        }

        $faltantes = [];
        foreach (self::CAMPOS_REQUERIDOS as $campo => $etiqueta) {
            if (blank($this->{$campo})) {
                $faltantes[] = $etiqueta;
            }
        }

        if ($faltantes) {
            $problemas[] = 'Faltan los datos de la empresa: ' . implode(', ', $faltantes) . '.';
        }

        if (!$this->certificado_ruta) {
            $problemas[] = 'Falta cargar el certificado digital.';
        } elseif (!$this->rutaCertificado()) {
            $problemas[] = 'El certificado digital figura cargado pero el archivo no esta en el servidor; vuelve a subirlo.';
        } elseif ($this->certificadoVencido()) {
            $problemas[] = 'El certificado digital vencio el ' .
                $this->certificado_vencimiento->format('d/m/Y') . '.';
        }

        return $problemas;
    }

    /**
     * true cuando la empresa tiene todo lo necesario para emitir boletas y
     * facturas electronicas.
     */
    public function puedeFacturar(): bool
    {
        return $this->problemasDeConfiguracion() === [];
    }

    /**
     * Datos de facturacion del tenant activo, o null si aun no se crearon.
     */
    public static function delTenantActual(): ?self
    {
        return static::where('tenant_id', tenant('id'))->first();
    }
}
