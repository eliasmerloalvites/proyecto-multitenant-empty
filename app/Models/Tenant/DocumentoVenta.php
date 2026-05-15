<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoVenta extends Model
{
    protected $table = 'documento_venta';
    protected $primaryKey='DOV_Id';
    public $timestamps=false;
    protected $fillable=[
        'DOV_Tipo',
        'DOV_TipoOriginal',
        'DOV_Serie',
        'DOV_Numero',
        'VEN_Id',
        'DOV_Estado',
        'DOV_Nombre',
        'DOV_IdRes',
        'DOV_StateToRes',
        'DOV_Pdf',
        'DOV_Anulado',
        'DOV_Vista',
    ];
    
    protected $guarded =[

    ];
}
