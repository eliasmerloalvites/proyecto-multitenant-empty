<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MpcDetalleReemplazo extends Model
{
    protected $table = 'mpc_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MPC_Id',
        'MPCD_Item',
        'MPCD_Descripcion',
        'MPC_Precio',
    ];

    protected $casts = [
        'MPC_Precio' => 'decimal:2',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoPreventivoCarburada::class,
            'MPC_Id',
            'MPC_Id'
        );
    }
}