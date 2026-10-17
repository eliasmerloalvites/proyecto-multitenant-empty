<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

/** Moto identificada por su placa normalizada (tabla moto, base del tenant). */
class Moto extends Model
{
    protected $table = 'moto';
    protected $primaryKey = 'MOT_Id';

    protected $fillable = ['MOT_Placa', 'MOT_Descripcion', 'MOT_Estado'];

    public function vinculos()
    {
        return $this->hasMany(CuentaMovilMoto::class, 'MOT_Id', 'MOT_Id');
    }
}
