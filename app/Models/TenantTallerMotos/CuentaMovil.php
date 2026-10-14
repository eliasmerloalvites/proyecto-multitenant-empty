<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Cuenta de un cliente final en la app movil (tabla cuenta_movil, base del
 * tenant). Se autentica con un token Sanctum (guard "cliente"); no tiene
 * contrasena: su identidad es el celular verificado por codigo.
 */
class CuentaMovil extends Authenticatable
{
    use HasApiTokens;

    public const ESTADO_ACTIVA = 'ACTIVA';
    public const ESTADO_BLOQUEADA = 'BLOQUEADA';

    protected $table = 'cuenta_movil';
    protected $primaryKey = 'CMV_Id';

    protected $fillable = [
        'CMV_Celular',
        'CMV_Nombre',
        'CMV_Correo',
        'CMV_VerificadoEn',
        'CMV_Estado',
        'CMV_UltimoAcceso',
    ];

    protected $casts = [
        'CMV_VerificadoEn' => 'datetime',
        'CMV_UltimoAcceso' => 'datetime',
    ];

    public function estaActiva(): bool
    {
        return $this->CMV_Estado === self::ESTADO_ACTIVA;
    }
}
