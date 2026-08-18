<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistroVerificacion extends Model
{
    protected $table = 'registro_verificaciones';

    protected $fillable = [
        'token',
        'razon_social',
        'ruc',
        'email',
        'password',
        'subdomain',
        'tipo_negocio',
        'plan',
        'expira_en',
        'verificado_en',
    ];

    protected $casts = [
        'expira_en' => 'datetime',
        'verificado_en' => 'datetime',
    ];

    public function estaVencido(): bool
    {
        return $this->expira_en->isPast();
    }

    public function estaVerificado(): bool
    {
        return $this->verificado_en !== null;
    }
}
