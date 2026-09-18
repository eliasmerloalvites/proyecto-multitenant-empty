<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EsquemaComision extends Model
{
    protected $table = 'esquemas_comision';

    protected $fillable = [
        'vendedor_id',
        'porcentaje',
        'meses_duracion',
        'activo',
        'vigente_desde',
    ];

    protected $casts = [
        'porcentaje' => 'decimal:2',
        'meses_duracion' => 'integer',
        'activo' => 'boolean',
        'vigente_desde' => 'datetime',
    ];

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }
}
