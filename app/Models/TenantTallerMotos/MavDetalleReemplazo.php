<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MavDetalleReemplazo extends Model
{
    protected $table = 'mav_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MAV_Id',
        'MAVD_Item',
        'MAVD_Descripcion',
        'MAV_Precio',
    ];

    protected $casts = [
        'MAV_Precio' => 'decimal:2',
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