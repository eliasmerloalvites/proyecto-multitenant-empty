<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class Reservacion extends Model
{
    protected $table='reservacion';

    protected $primaryKey='RES_Id';

    public $timestamps=false;
    protected $fillable =[
    	'TUR_Id',
    	'ALM_Id',
    	'BAH_Id',
    	'RES_Moto',
    	'RES_Cliente',
    	'RES_Celular',
    	'RES_Detalle',
    	'RES_Adicional',
    	'RES_FechaProgramada',
    	'RES_State',
    	'RES_Estado',
    ];

    protected $guarded =[

    ];
}
