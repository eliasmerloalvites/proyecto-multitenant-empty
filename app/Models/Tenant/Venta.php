<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    protected $table = 'venta';
    protected $primaryKey='VEN_Id';
    public $timestamps=true;
    protected $fillable=[
        'VEN_TipoPago',
        'VEN_Vuelto',
        'VEN_Pagado',
        'MEP_Id',
        'USU_Id',
        'CLI_Id',
        'ALM_Id',
        'VEN_Status',
        'VEN_FechaEnvio',
    ];
    
    protected $guarded =[

    ];
}
