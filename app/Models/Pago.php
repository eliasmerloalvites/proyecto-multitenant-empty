<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $fillable = [
        'client_id',
        'monto',
        'periodo',
        'fecha_pago',
        'metodo_pago',
        'nota',
        'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function registradoPor()
    {
        return $this->belongsTo(Central\User::class, 'registrado_por');
    }
}
