<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MavImagen extends Model
{
    protected $table = 'mav_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MAV_Id',
        'MAVI_Item',
        'MAVI_url',
        'MAVI_Nombre',
        'MAVI_Peso',
        'MAVI_Thumb'
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoActividadVariada::class,
            'MAV_Id',
            'MAV_Id'
        );
    }
}