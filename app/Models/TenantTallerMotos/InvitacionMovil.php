<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

/** Codigo de invitacion para vincular una moto (tabla invitacion_movil; solo guarda el hash). */
class InvitacionMovil extends Model
{
    protected $table = 'invitacion_movil';
    protected $primaryKey = 'INV_Id';

    protected $fillable = [
        'MOT_Id', 'INV_CodigoHash', 'INV_ExpiraEn', 'INV_UsadaEn', 'INV_UsadaPor', 'INV_CreadaPor', 'INV_RevocadaEn',
    ];

    protected $casts = [
        'INV_ExpiraEn' => 'datetime',
        'INV_UsadaEn' => 'datetime',
        'INV_RevocadaEn' => 'datetime',
    ];

    public function moto()
    {
        return $this->belongsTo(Moto::class, 'MOT_Id', 'MOT_Id');
    }

    public function estaVigente(): bool
    {
        return $this->INV_UsadaEn === null
            && $this->INV_RevocadaEn === null
            && $this->INV_ExpiraEn->isFuture();
    }
}
