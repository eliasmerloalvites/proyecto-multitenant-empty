<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacen';
    protected $primaryKey = 'ALM_Id';
    
    // Cambiado a true ya que la migración incluye $table->timestamps()
    public $timestamps = true;

    protected $fillable = [
        'EMP_Id',
        'ALM_NombreAlmacen',
        'ALM_CodigoSunat',
        'ALM_EsPrincipal',
        'ALM_Direccion',
        'ALM_Departamento',
        'ALM_Provincia',
        'ALM_Distrito',
        'ALM_Ubigeo',
        'ALM_Referencia',
        'ALM_Latitud',
        'ALM_Longitud',
        'ALM_Celular',
        'ALM_Encargado',
        'ALM_Telefono',
        'ALM_Email',
        'ALM_SerieFactura',
        'ALM_SerieBoleta',
        'ALM_SerieNotaCredito',
        'ALM_SerieNotaDebito',
        'ALM_SerieGuiaRemision',
        'ALM_SerieNotaVenta',
        'ALM_PermitirVentaSinStock',
        'ALM_Status',
    ];

    /**
     * Conversión automática de tipos de datos.
     */
    protected $casts = [
        'ALM_EsPrincipal'           => 'boolean',
        'ALM_PermitirVentaSinStock' => 'boolean',
        'ALM_Status'                => 'boolean',
    ];

    /* =========================================================================
     | RELACIONES
     | ========================================================================= */

    /**
     * Relación con la Empresa / Tenant a la que pertenece el almacén.
     */
    public function empresa()
    {
        return $this->belongsTo(EmpresaFacturacion::class, 'EMP_Id', 'id');
    }

    /* =========================================================================
     | SCOPES (Filtros comunes)
     | ========================================================================= */

    /**
     * Scope para obtener solo almacenes activos.
     * Ejemplo: Almacen::activos()->get();
     */
    public function scopeActivos($query)
    {
        return $query->where('ALM_Status', true);
    }

    /**
     * Scope para obtener la sede principal.
     * Ejemplo: Almacen::principal()->first();
     */
    public function scopePrincipal($query)
    {
        return $query->where('ALM_EsPrincipal', true);
    }

    /* =========================================================================
     | FACTURACION ELECTRONICA
     |
     | Para SUNAT cada sede es un establecimiento anexo: tiene su domicilio,
     | su codigo de local y su propia numeracion. Por eso el comprobante toma
     | estos datos de la sede donde se hizo la venta, no de la empresa.
     | ========================================================================= */

    /** Campos de la sede sin los cuales SUNAT no acepta el comprobante. */
    const CAMPOS_REQUERIDOS = [
        'ALM_Ubigeo'       => 'el ubigeo',
        'ALM_Direccion'    => 'la direccion',
        'ALM_Departamento' => 'el departamento',
        'ALM_Provincia'    => 'la provincia',
        'ALM_Distrito'     => 'el distrito',
    ];

    /** Tipo de documento interno => columna con su serie. */
    const SERIES = [
        EmpresaFacturacion::TIPO_BOLETA        => 'ALM_SerieBoleta',
        EmpresaFacturacion::TIPO_FACTURA       => 'ALM_SerieFactura',
        EmpresaFacturacion::TIPO_NOTA_CREDITO  => 'ALM_SerieNotaCredito',
        EmpresaFacturacion::TIPO_GUIA_REMISION => 'ALM_SerieGuiaRemision',
    ];

    /**
     * Serie configurada en esta sede para un tipo de documento (BOL / FAC).
     */
    public function seriePara(string $tipo): ?string
    {
        $columna = self::SERIES[$tipo]
            ?? throw new \InvalidArgumentException("Tipo de documento no soportado: $tipo");

        $serie = trim((string) $this->{$columna});

        return $serie !== '' ? strtoupper($serie) : null;
    }

    /**
     * Que le falta a esta sede para poder emitir comprobantes electronicos.
     *
     * @return string[]
     */
    public function problemasDeConfiguracion(): array
    {
        $problemas = [];
        $nombre = $this->ALM_NombreAlmacen ?: 'la sede';

        $faltantes = [];
        foreach (self::CAMPOS_REQUERIDOS as $campo => $etiqueta) {
            if (blank($this->{$campo})) {
                $faltantes[] = $etiqueta;
            }
        }

        if ($faltantes) {
            $problemas[] = 'A "' . $nombre . '" le falta ' . implode(', ', $faltantes) . '.';
        }

        $sinSerie = [];
        foreach (['Boleta' => EmpresaFacturacion::TIPO_BOLETA, 'Factura' => EmpresaFacturacion::TIPO_FACTURA] as $label => $tipo) {
            if (!$this->seriePara($tipo)) {
                $sinSerie[] = $label;
            }
        }

        if ($sinSerie) {
            $problemas[] = 'A "' . $nombre . '" le falta la serie de ' . implode(' y ', $sinSerie) . '.';
        }

        return $problemas;
    }
}