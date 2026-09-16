<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class RecepcionRespuesta extends Model
{
    protected $table = 'recepcion_respuesta';
    protected $primaryKey = 'RRP_Id';

    protected $fillable = [
        'MTO_Tabla',
        'MTO_Id',
        'RIT_Id',
        'RRP_Valor',
    ];

    public function item()
    {
        return $this->belongsTo(RecepcionItem::class, 'RIT_Id', 'RIT_Id');
    }

    public function scopeDeMantenimiento($query, string $tabla, int $id)
    {
        return $query->where('MTO_Tabla', $tabla)->where('MTO_Id', $id);
    }
}
