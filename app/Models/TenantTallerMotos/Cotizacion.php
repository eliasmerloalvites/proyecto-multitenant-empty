<?php

namespace App\Models\TenantTallerMotos;

use App\Models\Tenant\Cliente;
use App\Models\Tenant\Venta;
use Illuminate\Database\Eloquent\Model;

/**
 * Cotizacion: presupuesto para un cliente que aun no descuenta stock ni
 * genera comprobante. Solo existe en la tabla `cotizacion` de tallermoto
 * (con RES_Id, columna ausente en generico) — la migracion
 * tenant/tallermoto/2026_09_09_100000_create_cotizacion_table.php no tiene
 * equivalente en tenant/generico, asi que este modelo es exclusivo de este
 * vertical.
 *
 * El vertical 'generico' tiene su PROPIA tabla/columnas de cotizacion
 * (COT_Total, COT_Observaciones, estado numerico), con su propio modelo en
 * App\Models\Tenant\Generico\Cotizacion. No son intercambiables: cada
 * tenant usa la migracion de su propio vertical (tenant/tallermoto o
 * tenant/generico), asi que nunca conviven en la misma base de datos.
 */
class Cotizacion extends Model
{
    protected $table = 'cotizacion';
    protected $primaryKey = 'COT_Id';

    const ESTADO_PENDIENTE = 'PENDIENTE';
    const ESTADO_APROBADA = 'APROBADA';
    const ESTADO_RECHAZADA = 'RECHAZADA';

    protected $fillable = [
        'CLI_Id',
        'ALM_Id',
        'USU_Id',
        'VEN_Id',
        'RES_Id',
        'COT_Estado',
        'COT_FechaVencimiento',
        'COT_Observacion',
        'COT_Pdf',
    ];

    protected $casts = [
        'COT_FechaVencimiento' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(CotizacionItem::class, 'COT_Id', 'COT_Id');
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_Id', 'CLI_Id');
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'VEN_Id', 'VEN_Id');
    }

    public function estaPendiente(): bool
    {
        return $this->COT_Estado === self::ESTADO_PENDIENTE;
    }

    public function estaVencida(): bool
    {
        return $this->estaPendiente()
            && $this->COT_FechaVencimiento !== null
            && $this->COT_FechaVencimiento->lt(now()->startOfDay());
    }

    /**
     * Estado a mostrar: a diferencia de PENDIENTE/APROBADA/RECHAZADA (que se
     * guardan), VENCIDA es derivado en el momento a partir de la fecha de
     * vencimiento, para no tener que correr un job que la vaya actualizando.
     */
    public function estadoMostrar(): string
    {
        return $this->estaVencida() ? 'VENCIDA' : $this->COT_Estado;
    }

    public function subtotal(): float
    {
        return (float) $this->items->sum(
            fn ($item) => ($item->COI_Cantidad * $item->COI_PrecioUnitario) - $item->COI_Descuento
        );
    }
}
