<?php

namespace App\Models\Tenant\Generico;

use App\Models\Tenant\Almacen;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Venta;
use Illuminate\Database\Eloquent\Model;

/**
 * Cotizacion propia del vertical 'generico' (tabla/columnas distintas de
 * App\Models\Tenant\Cotizacion, que es la compartida/tallermoto): estado
 * numerico, COT_Total propio, relacion a DetalleCotizacion y a Almacen.
 * Ver migraciones database/migrations/tenant/generico/2026_09_10_020000_*.
 */
class Cotizacion extends Model
{
    protected $table = 'cotizacion';
    protected $primaryKey = 'COT_Id';
    public $timestamps = true;

    const ESTADO_ANULADA = 0;
    const ESTADO_PENDIENTE = 1;
    const ESTADO_APROBADA = 2;
    const ESTADO_RECHAZADA = 3;

    protected $fillable = [
        'CLI_Id',
        'ALM_Id',
        'USU_Id',
        'COT_Total',
        'COT_FechaVencimiento',
        'COT_Observaciones',
        'COT_Estado',
        'VEN_Id',
    ];

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CLI_Id', 'CLI_Id');
    }

    public function almacen()
    {
        return $this->belongsTo(Almacen::class, 'ALM_Id', 'ALM_Id');
    }

    public function detalles()
    {
        return $this->hasMany(\App\Models\Tenant\DetalleCotizacion::class, 'COT_Id', 'COT_Id');
    }

    /**
     * Venta en la que quedo convertida (ver
     * CotizacionController::marcarConvertida()). Null mientras siga
     * Pendiente o si nunca se convirtio.
     */
    public function venta()
    {
        return $this->belongsTo(Venta::class, 'VEN_Id', 'VEN_Id');
    }
}
