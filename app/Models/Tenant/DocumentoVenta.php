<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoVenta extends Model
{
    protected $table = 'documento_venta';
    protected $primaryKey='DOV_Id';
    public $timestamps=true;
    protected $fillable=[
        'DOV_Tipo',
        'DOV_TipoOriginal',
        'DOV_Serie',
        'DOV_Numero',
        'DOV_Nombre',
        'DOV_Estado',
        'DOV_EstadoSunat',
        'DOV_CodigoSunat',
        'DOV_DescripcionSunat',
        'DOV_ResponseSunat',
        'DOV_Hash',
        'DOV_Pdf',
        'DOV_Anulado',
        'DOV_IntentosSunat',
        'DOV_Vista',
        'DOV_FechaEnvioSunat',
        'DOV_FechaRespuestaSunat',
        'DOV_IdRes',
        'DOV_StateToRes',
        'VEN_Id',
    ];
    
    protected $guarded =[

    ];
}
