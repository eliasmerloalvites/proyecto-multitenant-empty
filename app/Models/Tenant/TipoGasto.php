<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoGasto extends Model
{
    protected $table = 'tipo_gasto';
    protected $primaryKey='TG_Id';
    public $timestamps=true;
    protected $fillable=[
        'TG_Descripcion',
    ];
    
    protected $guarded =[

    ];
}
