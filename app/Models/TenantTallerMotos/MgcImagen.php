<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MgcImagen extends Model
{
    protected $table = 'mgc_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGC_Id',
        'MGCI_Item',
        'MGCI_url',
        'MGCI_Thumb',
        'MGCI_Nombre',
        'MGCI_Peso',
    ];

    public function mantenimiento()
    {
        return $this->belongsTo(
            MantenimientoGeneralCarburada::class,
            'MGC_Id',
            'MGC_Id'
        );
    }
}