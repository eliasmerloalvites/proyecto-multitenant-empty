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
        'vendedor_id',
        'expira_en',
        'verificado_en',
        'estado',
        'error_mensaje',
        'tenant_domain',
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

    public function estaProcesando(): bool
    {
        return $this->estado === 'procesando';
    }

    public function estaCompletado(): bool
    {
        return $this->estado === 'completado';
    }

    public function tieneError(): bool
    {
        return $this->estado === 'error';
    }
}
