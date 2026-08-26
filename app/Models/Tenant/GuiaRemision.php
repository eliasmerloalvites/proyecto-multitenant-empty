<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class GuiaRemision extends Model
{
    /** Unico motivo soportado por ahora: entrega de productos ya vendidos. */
    const MOTIVO_VENTA = '01';

    /** Modalidad de traslado, catalogo 18 de SUNAT. */
    const MODO_PUBLICO  = '01';
    const MODO_PRIVADO  = '02';

    protected $table = 'guia_remision';
    protected $primaryKey = 'GRM_Id';
    public $timestamps = true;

    protected $fillable = [
        'GRM_Serie',
        'GRM_Numero',
        'VEN_Id',

        'GRM_MotivoTraslado',
        'GRM_DesMotivo',
        'GRM_FechaTraslado',
        'GRM_PesoTotal',
        'GRM_UndPeso',

        'GRM_UbigeoPartida',
        'GRM_DireccionPartida',
        'GRM_UbigeoLlegada',
        'GRM_DireccionLlegada',

        'GRM_ModoTransporte',

        'GRM_TransportistaTipoDoc',
        'GRM_TransportistaNumero',
        'GRM_TransportistaRazonSocial',

        'GRM_VehiculoPlaca',

        'GRM_ConductorTipoDoc',
        'GRM_ConductorNumero',
        'GRM_ConductorNombres',
        'GRM_ConductorApellidos',
        'GRM_ConductorLicencia',

        'GRM_Estado',
        'GRM_EstadoSunat',
        'GRM_Ticket',
        'GRM_CodigoSunat',
        'GRM_DescripcionSunat',
        'GRM_ResponseSunat',
        'GRM_Anulado',
        'GRM_IntentosSunat',
        'GRM_FechaEnvioSunat',
        'GRM_FechaRespuestaSunat',
    ];

    protected $casts = [
        'GRM_FechaTraslado'       => 'datetime',
        'GRM_PesoTotal'           => 'float',
        'GRM_Anulado'             => 'boolean',
        'GRM_FechaEnvioSunat'     => 'datetime',
        'GRM_FechaRespuestaSunat' => 'datetime',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'VEN_Id', 'VEN_Id');
    }

    public function getGRMNombreAttribute(): string
    {
        return $this->GRM_Serie . '-' . str_pad((string) $this->GRM_Numero, 8, '0', STR_PAD_LEFT);
    }
}
