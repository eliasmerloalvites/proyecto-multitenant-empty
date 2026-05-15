<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gasto extends Model
{
    protected $table = 'gasto';
    protected $primaryKey='GAS_Id';
    public $timestamps=false;
    protected $fillable=[
        'PROV_Id',
        'MEP_Id',
        'TG_Id',
        'ALM_Id',
        'USU_Id',
        'GAS_Descripcion',
        'GAS_Monto',
        'GAS_Fecha',
        'TG_Comprobante',
        'TG_ComprobanteNum',
        'GAS_Afecta',
        'GAS_Documento',
        'GAS_Status'
    ];
    
    protected $guarded =[

    ];
}
