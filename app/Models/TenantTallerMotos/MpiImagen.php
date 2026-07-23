<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MpiImagen extends Model
{
    protected $table = 'mpi_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MPI_Id',
        'MPII_Item',
        'MPII_url',
        'MPII_Thumb',
        'MPII_Nombre',
        'MPII_Peso',
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