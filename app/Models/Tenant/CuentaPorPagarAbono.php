<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CuentaPorPagarAbono extends Model
{
    protected $table = 'cuenta_por_pagar_abono';
    protected $primaryKey = 'CPPA_Id';
    protected $fillable = [
        'CPP_Id',
        'MEP_Id',
        'USU_Id',
        'CAJ_Id',
        'CS_Id',
        'CPPA_Monto',
        'CPPA_Observacion',
    ];

    public function cuentaPorPagar()
    {
        return $this->belongsTo(CuentaPorPagar::class, 'CPP_Id', 'CPP_Id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'MEP_Id', 'MEP_Id');
    }
}
