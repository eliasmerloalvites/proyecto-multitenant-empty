<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class RecepcionCategoria extends Model
{
    protected $table = 'recepcion_categoria';
    protected $primaryKey = 'RCT_Id';

    const GRUPO_INVENTARIO = 'INVENTARIO';
    const GRUPO_INSPECCION = 'INSPECCION';

    protected $fillable = [
        'RCT_Nombre',
        'RCT_Grupo',
        'RCT_Orden',
        'RCT_Activo',
    ];

    protected $casts = [
        'RCT_Activo' => 'boolean',
    ];

    public function items()
    {
        return $this->hasMany(RecepcionItem::class, 'RCT_Id', 'RCT_Id')->orderBy('RIT_Orden');
    }

    public function scopeActivas($query)
    {
        return $query->where('RCT_Activo', true)->orderBy('RCT_Orden');
    }
}
