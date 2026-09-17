<?php

namespace App\Models;

use Carbon\Carbon;

class ClienteVendedor extends \Illuminate\Database\Eloquent\Model
{
    protected $table = 'cliente_vendedor';

    protected $fillable = [
        'client_id',
        'vendedor_id',
        'esquema_comision_id',
        'porcentaje_congelado',
        'meses_congelado',
        'referido_en',
    ];

    protected $casts = [
        'porcentaje_congelado' => 'decimal:2',
        'meses_congelado' => 'integer',
        'referido_en' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendedor()
    {
        return $this->belongsTo(Vendedor::class);
    }

    public function esquemaComision()
    {
        return $this->belongsTo(EsquemaComision::class);
    }

    /**
     * ¿El $periodo ('YYYY-MM') de un pago cae dentro de la ventana de
     * comisión de este cliente? La ventana arranca en su PRIMER pago real
     * (no en el alta — hay días de prueba gratis antes) y dura
     * meses_congelado meses. Se recalcula siempre desde `pagos` (comparación
     * lexicográfica = cronológica porque el formato es fijo 'YYYY-MM'), así
     * nunca puede desincronizarse — no hay contador mutable que actualizar.
     */
    public function periodoDentroDeVentana(string $periodo): bool
    {
        $primerPeriodo = Pago::where('client_id', $this->client_id)->min('periodo');

        if ($primerPeriodo === null) {
            return false;
        }

        $cutoff = Carbon::createFromFormat('Y-m', $primerPeriodo)
            ->addMonthsNoOverflow($this->meses_congelado - 1)
            ->format('Y-m');

        return $periodo >= $primerPeriodo && $periodo <= $cutoff;
    }
}
