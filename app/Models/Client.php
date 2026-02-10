<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'tenant_id',
        'razon_social',
        'ruc',
        'tipo_negocio',
        'billing_day',
        'status',
    ];
}
