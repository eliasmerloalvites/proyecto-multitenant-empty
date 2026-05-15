<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleVenta extends Model
{
    protected $table = 'detalle_venta';
    protected $primaryKey='DEV_Item';
    public $timestamps=false;
    protected $fillable=[
        'VEN_Id',
        'PRO_Id',
        'DEV_Cantidad',
        'DEV_PrecioUnitario',
        'LOT_Id',
        'DEV_Descuento',
    ];
    
    protected $guarded =[

    ];
}
