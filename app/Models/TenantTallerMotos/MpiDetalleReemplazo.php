<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MpiDetalleReemplazo extends Model
{
    protected $table = 'mpi_detalle_reemplazo';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MPI_Id',
        'MPID_Item',
        'MPID_Descripcion',
        'MPI_Precio',
    ];

    protected $casts = [
        'MPI_Precio' => 'decimal:2',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoPreventivoInyectada::class,
            'MPI_Id',
            'MPI_Id'
        );
    }
}