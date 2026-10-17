<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MglImagen extends Model
{
    protected $table = 'mgl_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGL_Id',
        'MGLI_Item',
        'MGLI_url',
        'MGLI_Thumb',
        'MGLI_Nombre',
        'MGLI_Peso',
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