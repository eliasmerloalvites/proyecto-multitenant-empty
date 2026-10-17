<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

/** Vinculo cuenta de la app <-> moto (tabla cuenta_movil_moto). */
class CuentaMovilMoto extends Model
{
    public const PENDIENTE = 'PENDIENTE';
    public const VERIFICADA = 'VERIFICADA';
    public const RECHAZADA = 'RECHAZADA';
    public const REVOCADA = 'REVOCADA';

    public const METODO_TELEFONO = 'TELEFONO';
    public const METODO_INVITACION = 'INVITACION';
    public const METODO_APROBACION = 'APROBACION';

    public const ALCANCE_PROPIOS = 'REGISTROS_PROPIOS';
    public const ALCANCE_COMPLETO = 'COMPLETO';

    protected $table = 'cuenta_movil_moto';
    protected $primaryKey = 'CMM_Id';

    protected $fillable = [
        'CMV_Id', 'MOT_Id', 'CMM_Estado', 'CMM_Metodo', 'CMM_Alcance',
        'CMM_VerificadaPor', 'CMM_VerificadaEn', 'CMM_Nota',
    ];

    protected $casts = ['CMM_VerificadaEn' => 'datetime'];

    public function cuenta()
    {
        return $this->belongsTo(CuentaMovil::class, 'CMV_Id', 'CMV_Id');
    }

    public function moto()
    {
        return $this->belongsTo(Moto::class, 'MOT_Id', 'MOT_Id');
    }

    public function esVerificada(): bool
    {
        return $this->CMM_Estado === self::VERIFICADA;
    }
}
