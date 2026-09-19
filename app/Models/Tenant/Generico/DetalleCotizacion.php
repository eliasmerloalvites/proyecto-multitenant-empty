<?php

namespace App\Models\Tenant\Generico;

use App\Models\Tenant\Producto;
use Illuminate\Database\Eloquent\Model;

class DetalleCotizacion extends Model
{
    protected $table = 'detalle_cotizacion';
    public $incrementing = false;
    protected $primaryKey = 'DCOT_Item';
    public $timestamps = true;

    protected $fillable = [
        'COT_Id',
        'PRO_Id',
        'DCOT_Item',
        'DCOT_Cantidad',
        'DCOT_PrecioUnitario',
        'DCOT_Descuento',
    ];

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Id', 'PRO_Id');
    }
}
