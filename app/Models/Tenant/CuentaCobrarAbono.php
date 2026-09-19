<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

/**
 * Un abono (pago parcial) real aplicado contra una cuenta por cobrar,
 * opcionalmente contra una cuota especifica. Exclusiva del vertical
 * 'generico'.
 */
class CuentaCobrarAbono extends Model
{
    protected $table = 'cuenta_cobrar_abono';
    protected $primaryKey = 'CCA_Id';
    public $timestamps = true;

    protected $fillable = [
        'CXC_Id',
        'CCC_Id',
        'MEP_Id',
        'USU_Id',
        'CCA_Monto',
        'CCA_Fecha',
        'CCA_Descripcion',
    ];

    protected $casts = [
        'CCA_Fecha' => 'datetime',
    ];

    public function cuentaCobrar()
    {
        return $this->belongsTo(CuentaCobrar::class, 'CXC_Id', 'CXC_Id');
    }

    public function cuota()
    {
        return $this->belongsTo(CuentaCobrarCuota::class, 'CCC_Id', 'CCC_Id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'MEP_Id', 'MEP_Id');
    }
}
