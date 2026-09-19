<?php

namespace App\Models\Tenant\Generico;

use App\Models\Tenant\MetodoPago;
use App\Models\Tenant\Venta;
use Illuminate\Database\Eloquent\Model;

/**
 * Detalle de pago de una venta: una fila por cada metodo usado. Exclusivo
 * de 'generico' (tabla con columnas VPG_*; el vertical tallermoto tiene su
 * propia venta_pago con columnas VNP_*, ver App\Models\Tenant\VentaPago).
 *
 * La suma de VPG_Monto de una misma VEN_Id siempre debe ser exactamente el
 * total de esa venta (ya con el vuelto de la linea en efectivo descontado);
 * de ese invariante depende el arqueo de caja en
 * App\Http\Controllers\Tenant\Generico\CajaSesionReporteController.
 */
class VentaPago extends Model
{
    protected $table = 'venta_pago';
    protected $primaryKey = 'VPG_Id';
    public $timestamps = true;

    protected $fillable = [
        'VEN_Id',
        'MEP_Id',
        'VPG_Monto',
    ];

    protected $casts = [
        'VPG_Monto' => 'decimal:2',
    ];

    public function venta()
    {
        return $this->belongsTo(Venta::class, 'VEN_Id', 'VEN_Id');
    }

    public function metodoPago()
    {
        return $this->belongsTo(MetodoPago::class, 'MEP_Id', 'MEP_Id');
    }
}
