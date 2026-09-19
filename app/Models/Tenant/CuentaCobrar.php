<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

/**
 * Cabecera de "cuenta por cobrar" (venta al credito), exclusiva del
 * vertical 'generico'.
 */
class CuentaCobrar extends Model
{
    protected $table = 'cuenta_cobrar';
    protected $primaryKey = 'CXC_Id';
    public $timestamps = true;

    /** Estados de CXC_Estado. */
    const ESTADO_PENDIENTE = 1;
    const ESTADO_PAGADO = 2;

    protected $fillable = [
        'VEN_Id',
        'USU_Id',
        'CXC_MontoTotal',
        'CXC_MontoAdelanto',
        'CXC_MontoAbonado',
        'CXC_MontoPendiente',
        'CXC_TieneCuotas',
        'CXC_NumCuotas',
        'CXC_FrecuenciaDias',
        'CXC_FechaEmision',
        'CXC_FechaVencimiento',
        'CXC_Estado',
    ];

    protected $casts = [
        'CXC_TieneCuotas' => 'boolean',
        'CXC_FechaEmision' => 'date',
        'CXC_FechaVencimiento' => 'date',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'VEN_Id', 'VEN_Id');
    }

    public function cuotas()
    {
        return $this->hasMany(CuentaCobrarCuota::class, 'CXC_Id', 'CXC_Id')
            ->orderBy('CCC_Numero');
    }

    public function abonos()
    {
        return $this->hasMany(CuentaCobrarAbono::class, 'CXC_Id', 'CXC_Id')
            ->orderBy('CCA_Fecha');
    }
}
