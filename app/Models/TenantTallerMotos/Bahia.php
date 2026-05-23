<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class Bahia extends Model
{
    protected $table='bahia';

    protected $primaryKey='BAH_Id';

    public $timestamps=true;
    protected $fillable =[
    	'USU_Id',
    	'ALM_Id',
    	'BAH_Nombre',
    	'BAH_Estado'
    ];

    protected $guarded =[

    ];
}
