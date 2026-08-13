<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CobroNotificacion extends Model
{
    protected $table = 'cobro_notificaciones';

    protected $fillable = [
        'client_id',
        'periodo',
        'tipo',
        'enviado_en',
    ];

    protected $casts = [
        'enviado_en' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
