<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;

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
        return $this->hasMany(DetalleCotizacion::class, 'COT_Id', 'COT_Id');
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
