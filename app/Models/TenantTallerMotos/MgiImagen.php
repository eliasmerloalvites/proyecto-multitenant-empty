<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class MgiImagen extends Model
{
    protected $table = 'mgi_imagen';
    public $incrementing = false;
    public $timestamps = false;
    protected $primaryKey = null;
    protected $fillable = [
        'MGI_Id',
        'MGII_Item',
        'MGII_url',
        'MGII_Thumb',
        'MGII_Nombre',
        'MGII_Peso',
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