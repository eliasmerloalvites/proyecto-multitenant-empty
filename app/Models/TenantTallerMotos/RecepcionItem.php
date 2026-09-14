<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

class RecepcionItem extends Model
{
    protected $table = 'recepcion_item';
    protected $primaryKey = 'RIT_Id';

    const TIPO_BOOLEAN = 'BOOLEAN';
    const TIPO_STATUS = 'STATUS';
    const TIPO_INSPECTION = 'INSPECTION';
    const TIPO_SELECT = 'SELECT';
    const TIPO_TEXT = 'TEXT';

    const TIPOS_VALIDOS = [
        self::TIPO_BOOLEAN,
        self::TIPO_STATUS,
        self::TIPO_INSPECTION,
        self::TIPO_SELECT,
        self::TIPO_TEXT,
    ];

    protected $fillable = [
        'RCT_Id',
        'RIT_Codigo',
        'RIT_Etiqueta',
        'RIT_TipoCampo',
        'RIT_Opciones',
        'RIT_Orden',
        'RIT_Activo',
    ];

    protected $casts = [
        'RIT_Opciones' => 'array',
        'RIT_Activo' => 'boolean',
    ];

    public function categoria()
    {
        return $this->belongsTo(RecepcionCategoria::class, 'RCT_Id', 'RCT_Id');
    }

    public function scopeActivos($query)
    {
        return $query->where('RIT_Activo', true)->orderBy('RIT_Orden');
    }
}
