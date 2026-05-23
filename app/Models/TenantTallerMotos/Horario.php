<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table='horario';

    protected $primaryKey='HOR_Id';

    public $timestamps=true;
    protected $fillable =[
    	'ALM_Id',
    	'TUR_Id',
    	'HOR_Dia',
    	'HOR_Detalle',
    	'HOR_Estado'
    ];

    protected $guarded =[

    ];
}
