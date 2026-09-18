<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendedor extends Model
{
    protected $table = 'vendedores';

    protected $fillable = [
        'user_id',
        'codigo_referido',
        'estado',
    ];

    public function user()
    {
        return $this->belongsTo(Central\User::class, 'user_id');
    }

    public function esquemasComision()
    {
        return $this->hasMany(EsquemaComision::class);
    }

    public function clientes()
    {
        return $this->hasMany(ClienteVendedor::class);
    }

    /**
     * El esquema de % y meses que rige hoy para este vendedor — el que se
     * congela en cliente_vendedor cuando trae un cliente nuevo. Nunca hay
     * más de uno activo a la vez (EsquemaComisionController desactiva el
     * anterior al crear uno nuevo).
     */
    public function esquemaVigente(): ?EsquemaComision
    {
        return $this->esquemasComision()->where('activo', true)->first();
    }
}
