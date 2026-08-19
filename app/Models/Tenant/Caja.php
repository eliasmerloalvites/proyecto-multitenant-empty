<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

class Caja extends Model
{
    protected $table = 'caja';
    protected $primaryKey = 'CAJ_Id';

    protected $fillable = [
        'ALM_Id',
        'CAJ_Nombre',
        'CAJ_MontoApertura',
        'CAJ_Status',
        'CAJ_ProgramacionActiva',
        'CAJ_HoraApertura',
        'CAJ_HoraCierre',
    ];

    protected $casts = [
        'CAJ_MontoApertura' => 'decimal:2',
        'CAJ_Status' => 'boolean',
        'CAJ_ProgramacionActiva' => 'boolean',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'ALM_Id', 'ALM_Id');
    }

    public function sesiones()
    {
        return $this->hasMany(CajaSesion::class, 'CAJ_Id', 'CAJ_Id');
    }

    public function sesionAbierta()
    {
        return $this->hasOne(CajaSesion::class, 'CAJ_Id', 'CAJ_Id')->where('CS_Estado', 'abierta');
    }
}
