<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class RecepcionObservacion extends Model
{
    protected $table = 'recepcion_observacion';
    protected $primaryKey = 'ROB_Id';

    protected $fillable = [
        'MTO_Tabla',
        'MTO_Id',
        'RCT_Id',
        'ROB_Texto',
    ];

    public function categoria()
    {
        return $this->belongsTo(RecepcionCategoria::class, 'RCT_Id', 'RCT_Id');
    }

    public function scopeDeMantenimiento($query, string $tabla, int $id)
    {
        return $query->where('MTO_Tabla', $tabla)->where('MTO_Id', $id);
    }
}
