<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

/** Pago adelantado sobre una cuenta de bahia abierta (ver migracion create_bahia_cuenta_adelanto). */
class BahiaCuentaAdelanto extends Model
{
    protected $table = 'bahia_cuenta_adelanto';
    protected $primaryKey = 'BCA_Id';

    protected $fillable = [
        'BCT_Id',
        'MEP_Id',
        'USU_Id',
        'CAJ_Id',
        'CS_Id',
        'BCA_Monto',
        'BCA_Observacion',
    ];

    protected $casts = [
        'BCA_Monto' => 'decimal:2',
    ];

    public function cuenta()
    {
        return $this->belongsTo(BahiaCuenta::class, 'BCT_Id', 'BCT_Id');
    }
}
