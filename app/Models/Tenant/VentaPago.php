<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class VentaPago extends Model
{
    protected $table = 'venta_pago';
    protected $primaryKey = 'VNP_Id';
    protected $fillable = [
        'VEN_Id',
        'MEP_Id',
        'VNP_Monto',
    ];

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'MEP_Id', 'MEP_Id');
    }
}
