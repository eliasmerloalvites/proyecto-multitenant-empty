<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MpcImagen extends Model
{
    protected $table = 'mpc_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MPC_Id',
        'MPCI_Item',
        'MPCI_url',
        'MPCI_Thumb',
        'MPCI_Nombre',
        'MPCI_Peso',
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