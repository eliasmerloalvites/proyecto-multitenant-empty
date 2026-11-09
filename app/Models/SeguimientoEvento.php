<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\CentralConnection;

class SeguimientoEvento extends Model
{
    use CentralConnection;

    protected $table = 'seguimiento_eventos';

    public $timestamps = false;

    protected $fillable = ['client_id', 'tipo', 'asunto', 'detalle', 'clave', 'user_id', 'user_nombre', 'created_at'];

    protected $casts = ['created_at' => 'datetime'];
}
