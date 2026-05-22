<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table='turno';

    protected $primaryKey='TUR_Id';

    public $timestamps=true;
    protected $fillable =[
    	'TUR_Nombre',
    	'TUR_Descripcion',
    	'TUR_Estado',
    ];

    protected $guarded =[

    ];
}
