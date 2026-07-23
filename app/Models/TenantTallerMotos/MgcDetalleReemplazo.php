<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MgcDetalleReemplazo extends Model
{
    protected $table = 'mgc_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGC_Id',
        'MGCD_Item',
        'MGCD_Descripcion',
        'MGC_Precio',
    ];

    protected $casts = [
        'MGC_Precio' => 'decimal:2',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoGeneralInyectada::class,
            'MGC_Id',
            'MGC_Id'
        );
    }
}