<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MghImagen extends Model
{
    protected $table = 'mgh_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGH_Id',
        'MGHI_Item',
        'MGHI_url',
        'MGHI_Thumb',
        'MGHI_Nombre',
        'MGHI_Peso',
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