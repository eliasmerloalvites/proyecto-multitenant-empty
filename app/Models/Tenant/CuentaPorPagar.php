<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class CuentaPorPagar extends Model
{
    protected $table = 'cuenta_por_pagar';
    protected $primaryKey = 'CPP_Id';
    protected $fillable = [
        'COM_Id',
        'CPP_MontoTotal',
        'CPP_MontoAbonado',
        'CPP_MontoFaltante',
        'CPP_FechaEmision',
        'CPP_FechaVencimiento',
        'CPP_Estado',
    ];

    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_PAGADA = 'PAGADA';

    public function compra()
    {
        return $this->belongsTo(Compra::class, 'COM_Id', 'COM_Id');
    }

    public function abonos()
    {
        return $this->hasMany(CuentaPorPagarAbono::class, 'CPP_Id', 'CPP_Id');
    }
}
