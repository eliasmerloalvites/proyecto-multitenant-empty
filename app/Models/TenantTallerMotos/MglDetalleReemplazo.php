<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MglDetalleReemplazo extends Model
{
    protected $table = 'mgl_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGL_Id',
        'MGLD_Item',
        'MGLD_Descripcion',
        'MGL_Precio',
    ];

    protected $casts = [
        'MGL_Precio' => 'decimal:2',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoGarantiaLifan::class,
            'MGL_Id',
            'MGL_Id'
        );
    }
}