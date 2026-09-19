<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

/**
 * Un abono (pago parcial) real entregado a un proveedor contra una cuenta
 * por pagar, opcionalmente contra una cuota especifica. Exclusiva del
 * vertical 'generico'.
 */
class CuentaPagarAbono extends Model
{
    protected $table = 'cuenta_pagar_abono';
    protected $primaryKey = 'CXPA_Id';
    public $timestamps = true;

    protected $fillable = [
        'CXP_Id',
        'CXPC_Id',
        'MEP_Id',
        'USU_Id',
        'CXPA_Monto',
        'CXPA_Fecha',
        'CXPA_Descripcion',
    ];

    protected $casts = [
        'CXPA_Fecha' => 'datetime',
    ];

    public function cuentaPagar()
    {
        return $this->belongsTo(CuentaPagar::class, 'CXP_Id', 'CXP_Id');
    }

    public function cuota()
    {
        return $this->belongsTo(CuentaPagarCuota::class, 'CXPC_Id', 'CXPC_Id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'MEP_Id', 'MEP_Id');
    }
}
