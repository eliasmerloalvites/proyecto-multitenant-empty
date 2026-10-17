<?php

namespace App\Models\TenantTallerMotos;

use Illuminate\Database\Eloquent\Model;

/** Pedido hecho desde la app (ver migracion create_pedido_movil_tables). */
class PedidoMovil extends Model
{
    protected $table = 'pedido_movil';
    protected $primaryKey = 'PED_Id';

    public const PENDIENTE = 'PENDIENTE';
    public const CONFIRMADO = 'CONFIRMADO';
    public const LISTO = 'LISTO';
    public const ENTREGADO = 'ENTREGADO';
    public const CANCELADO = 'CANCELADO';

    /** Estados en los que el stock queda apartado para el cliente. */
    public const ESTADOS_QUE_APARTAN = [self::CONFIRMADO, self::LISTO];

    protected $fillable = [
        'CMV_Id',
        'ALM_Id',
        'PED_Estado',
        'PED_Total',
        'PED_Nota',
        'PED_CanceladoPor',
        'PED_MotivoCancelacion',
        'VEN_Id',
        'USU_Id_Atiende',
        'PED_ConfirmadoEn',
        'PED_ListoEn',
        'PED_EntregadoEn',
        'PED_CanceladoEn',
    ];

    protected $casts = [
        'PED_Total' => 'decimal:2',
        'PED_ConfirmadoEn' => 'datetime',
        'PED_ListoEn' => 'datetime',
        'PED_EntregadoEn' => 'datetime',
        'PED_CanceladoEn' => 'datetime',
    ];

    public function items()
    {
        return $this->hasMany(PedidoMovilItem::class, 'PED_Id', 'PED_Id');
    }

    public function cuenta()
    {
        return $this->belongsTo(CuentaMovil::class, 'CMV_Id', 'CMV_Id');
    }

    /** Numero visible del pedido: PED-000123. */
    public function numero(): string
    {
        return 'PED-' . str_pad((string) $this->PED_Id, 6, '0', STR_PAD_LEFT);
    }

    public function estaAbierto(): bool
    {
        return in_array($this->PED_Estado, [self::PENDIENTE, self::CONFIRMADO, self::LISTO], true);
    }
}
