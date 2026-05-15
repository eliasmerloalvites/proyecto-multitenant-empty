<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lote extends Model
{
    protected $table = 'lote';
    protected $primaryKey='LOT_Id';
    public $timestamps=true;
    protected $fillable=[
        'ALM_Id',
        'PRO_Id',
        'LOT_TipoIngreso',
        'LOT_IdIngreso',
        'LOT_CantidadReal',
        'LOT_CantidadIngreso',
        'LOT_PrecioCompra',
        'LOT_PrecioVenta',
    ];
    
    protected $guarded =[

    ];
}
