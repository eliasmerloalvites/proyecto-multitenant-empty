<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class BahiaCuenta extends Model
{
    protected $table = 'bahia_cuenta';
    protected $primaryKey = 'BCT_Id';

    const ESTADO_ABIERTA = 'ABIERTA';
    const ESTADO_CERRADA = 'CERRADA';
    const ESTADO_CERRADA_SIN_COBRO = 'CERRADA_SIN_COBRO';

    protected $fillable = [
        'RES_Id',
        'ALM_Id',
        'BAH_Id',
        'VEN_Id',
        'BCT_Estado',
        'USU_Id_Abre',
        'BCT_AbiertoEn',
        'BCT_CerradoEn',
    ];

    protected $casts = [
        'BCT_AbiertoEn' => 'datetime',
        'BCT_CerradoEn' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(BahiaCuentaItem::class, 'BCT_Id', 'BCT_Id');
    }

    public function reservacion()
    {
        return $this->belongsTo(Reservacion::class, 'RES_Id', 'RES_Id');
    }

    public function estaAbierta(): bool
    {
        return $this->BCT_Estado === self::ESTADO_ABIERTA;
    }

    public function total(): float
    {
        return (float) $this->items->sum(fn ($item) => $item->BCI_Cantidad * $item->BCI_PrecioUnitario);
    }
}
