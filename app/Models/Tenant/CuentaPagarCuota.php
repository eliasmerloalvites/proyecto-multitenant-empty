<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

/**
 * Una cuota planificada dentro de un plan de pagos de una cuenta por
 * pagar. Exclusiva del vertical 'generico'.
 */
class CuentaPagarCuota extends Model
{
    protected $table = 'cuenta_pagar_cuota';
    protected $primaryKey = 'CXPC_Id';
    public $timestamps = true;

    /** Estados de CXPC_Estado. */
    const ESTADO_PENDIENTE = 1;
    const ESTADO_PAGADO = 2;

    protected $fillable = [
        'CXP_Id',
        'CXPC_Numero',
        'CXPC_MontoProgramado',
        'CXPC_FechaVencimiento',
        'CXPC_MontoAbonado',
        'CXPC_Estado',
    ];

    protected $casts = [
        'CXPC_FechaVencimiento' => 'date',
    ];

    public function cuentaPagar()
    {
        return $this->belongsTo(CuentaPagar::class, 'CXP_Id', 'CXP_Id');
    }

    public function abonos()
    {
        return $this->hasMany(CuentaPagarAbono::class, 'CXPC_Id', 'CXPC_Id');
    }
}
