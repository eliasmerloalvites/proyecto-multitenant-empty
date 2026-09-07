<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrarAbono extends Model
{
    protected $table = 'cuenta_por_cobrar_abono';
    protected $primaryKey = 'CPA_Id';
    protected $fillable = [
        'CPC_Id',
        'MEP_Id',
        'USU_Id',
        'CAJ_Id',
        'CS_Id',
        'CPA_Monto',
        'CPA_Observacion',
    ];

    public function cuentaPorCobrar()
    {
        return $this->belongsTo(CuentaPorCobrar::class, 'CPC_Id', 'CPC_Id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'MEP_Id', 'MEP_Id');
    }
}
