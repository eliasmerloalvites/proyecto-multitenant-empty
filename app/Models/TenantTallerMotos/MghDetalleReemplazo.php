<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MghDetalleReemplazo extends Model
{
    protected $table = 'mgh_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGH_Id',
        'MGHD_Item',
        'MGHD_Descripcion',
        'MGH_Precio',
    ];

    protected $casts = [
        'MGH_Precio' => 'decimal:2',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoGarantiaHero::class,
            'MGH_Id',
            'MGH_Id'
        );
    }
}