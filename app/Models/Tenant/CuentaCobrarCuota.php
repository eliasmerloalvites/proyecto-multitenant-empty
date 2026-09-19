<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

/**
 * Una cuota planificada dentro de un plan de pagos de una cuenta por
 * cobrar. Exclusiva del vertical 'generico'.
 */
class CuentaCobrarCuota extends Model
{
    protected $table = 'cuenta_cobrar_cuota';
    protected $primaryKey = 'CCC_Id';
    public $timestamps = true;

    /** Estados de CCC_Estado. */
    const ESTADO_PENDIENTE = 1;
    const ESTADO_PAGADO = 2;

    protected $fillable = [
        'CXC_Id',
        'CCC_Numero',
        'CCC_MontoProgramado',
        'CCC_FechaVencimiento',
        'CCC_MontoAbonado',
        'CCC_Estado',
    ];

    protected $casts = [
        'CCC_FechaVencimiento' => 'date',
    ];

    public function cuentaCobrar()
    {
        return $this->belongsTo(CuentaCobrar::class, 'CXC_Id', 'CXC_Id');
    }

    public function abonos()
    {
        return $this->hasMany(CuentaCobrarAbono::class, 'CCC_Id', 'CCC_Id');
    }
}
