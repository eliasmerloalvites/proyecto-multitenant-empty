<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class AjusteDetalle extends Model
{
    protected $table = 'ajuste_detalle';
    protected $primaryKey = 'AJD_Id';
    protected $fillable = [
        'AJU_Id',
        'PRO_Id',
        'LOT_Id',
        'AJD_Tipo',
        'AJD_Cantidad',
    ];

    const TIPO_INCREMENTO = 'INCREMENTO';
    const TIPO_DECREMENTO = 'DECREMENTO';

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'PRO_Id', 'PRO_Id');
    }
}
