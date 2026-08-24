<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdenPago extends Model
{
    protected $table = 'orden_pagos';

    protected $fillable = [
        'client_id',
        'periodo',
        'culqi_order_id',
        'order_number',
        'monto',
        'estado',
        'payment_code',
        'qr_url',
        'expires_at',
        'paid_at',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
