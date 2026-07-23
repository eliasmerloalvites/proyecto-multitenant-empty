<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MgiDetalleReemplazo extends Model
{
    protected $table = 'mgi_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGI_Id',
        'MGID_Item',
        'MGID_Descripcion',
        'MGI_Precio',
    ];

    protected $casts = [
        'MGI_Precio' => 'decimal:2',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoGeneralInyectada::class,
            'MGI_Id',
            'MGI_Id'
        );
    }
}