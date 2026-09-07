<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CuentaPorCobrar extends Model
{
    protected $table = 'cuenta_por_cobrar';
    protected $primaryKey = 'CPC_Id';
    protected $fillable = [
        'VEN_Id',
        'CPC_MontoTotal',
        'CPC_MontoAbonado',
        'CPC_MontoFaltante',
        'CPC_FechaEmision',
        'CPC_FechaVencimiento',
        'CPC_Estado',
    ];

    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_PAGADA = 'PAGADA';

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'VEN_Id', 'VEN_Id');
    }

    public function abonos()
    {
        return $this->hasMany(CuentaPorCobrarAbono::class, 'CPC_Id', 'CPC_Id');
    }
}
