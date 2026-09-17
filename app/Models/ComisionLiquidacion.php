<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComisionLiquidacion extends Model
{
    protected $table = 'comisiones_liquidaciones';

    protected $fillable = [
        'vendedor_id',
        'periodo',
        'liquidado_por',
        'liquidado_en',
        'nota',
    ];

    protected $casts = [
        'liquidado_en' => 'datetime',
    ];

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    public function liquidadoPor()
    {
        return $this->belongsTo(Central\User::class, 'liquidado_por');
    }
}
