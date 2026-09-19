<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class TrasladoDetalle extends Model
{
    protected $table = 'traslado_detalle';
    protected $primaryKey = 'TRD_Id';
    protected $fillable = [
        'TRA_Id',
        'PRO_Id',
        'LOT_IdDestino',
        'TRD_Cantidad',
    ];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Id', 'PRO_Id');
    }
}
