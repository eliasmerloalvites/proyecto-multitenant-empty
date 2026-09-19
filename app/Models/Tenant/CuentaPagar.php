<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

/**
 * Cabecera de "cuenta por pagar" (compra al credito a un proveedor),
 * exclusiva del vertical 'generico'.
 */
class CuentaPagar extends Model
{
    protected $table = 'cuenta_pagar';
    protected $primaryKey = 'CXP_Id';
    public $timestamps = true;

    /** Estados de CXP_Estado. */
    const ESTADO_PENDIENTE = 1;
    const ESTADO_PAGADO = 2;

    protected $fillable = [
        'COM_Id',
        'USU_Id',
        'CXP_MontoTotal',
        'CXP_MontoAdelanto',
        'CXP_MontoAbonado',
        'CXP_MontoPendiente',
        'CXP_TieneCuotas',
        'CXP_NumCuotas',
        'CXP_FrecuenciaDias',
        'CXP_FechaEmision',
        'CXP_FechaVencimiento',
        'CXP_Estado',
    ];

    protected $casts = [
        'CXP_TieneCuotas' => 'boolean',
        'CXP_FechaEmision' => 'date',
        'CXP_FechaVencimiento' => 'date',
    ];

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'COM_Id', 'COM_Id');
    }

    public function cuotas()
    {
        return $this->hasMany(CuentaPagarCuota::class, 'CXP_Id', 'CXP_Id')
            ->orderBy('CXPC_Numero');
    }

    public function abonos()
    {
        return $this->hasMany(CuentaPagarAbono::class, 'CXP_Id', 'CXP_Id')
            ->orderBy('CXPA_Fecha');
    }
}
