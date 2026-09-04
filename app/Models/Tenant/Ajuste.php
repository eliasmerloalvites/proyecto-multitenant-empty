<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Ajuste extends Model
{
    protected $table = 'ajuste';
    protected $primaryKey = 'AJU_Id';
    protected $fillable = [
        'ALM_Id',
        'USU_Id',
        'AJU_Motivo',
        'AJU_Observacion',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'ALM_Id', 'ALM_Id');
    }

    public function detalle()
    {
        return $this->hasMany(AjusteDetalle::class, 'AJU_Id', 'AJU_Id');
    }
}
