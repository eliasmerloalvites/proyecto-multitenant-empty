<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'tenant_id',
        'razon_social',
        'ruc',
        'email',
        'tipo_negocio',
        'billing_day',
        'trial_ends_at',
        'domain_id',
        'status',
        'next_payment_date',
    ];

    protected $casts = [
        'billing_day' => 'integer',
        'trial_ends_at' => 'date',
        'next_payment_date' => 'date',
    ];

    public function enPeriodoDePrueba(Carbon $hoy): bool
    {
        return $this->trial_ends_at !== null && $hoy->copy()->startOfDay()->lt($this->trial_ends_at->copy()->startOfDay());
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class);
    }

    public function notificacionesCobro()
    {
        return $this->hasMany(CobroNotificacion::class);
    }

    /**
     * Fecha en la que cae billing_day dentro del ciclo (mes) de $hoy, sin
     * saltar al mes siguiente aunque ya haya pasado. Sirve para saber si el
     * ciclo ACTUAL está vencido. Respeta meses cortos (ej. día 31 en
     * febrero cae en el día 28/29).
     */
    public function fechaCicloActual(Carbon $hoy): Carbon
    {
        $mes = $hoy->copy()->startOfMonth();

        return $mes->copy()->addDays(min($this->billing_day, $mes->daysInMonth) - 1);
    }

    /**
     * Próxima fecha de cobro a partir de $hoy: la de este mes si billing_day
     * aún no pasó, o la del mes siguiente si ya pasó.
     */
    public function proximaFechaCobro(Carbon $hoy): Carbon
    {
        $fecha = $this->fechaCicloActual($hoy);

        if ($fecha->lt($hoy->copy()->startOfDay())) {
            $mesSiguiente = $hoy->copy()->addMonthNoOverflow()->startOfMonth();
            $fecha = $mesSiguiente->copy()->addDays(min($this->billing_day, $mesSiguiente->daysInMonth) - 1);
        }

        return $fecha;
    }

    /**
     * Estado de cobro del ciclo actual (mes de $hoy):
     * - 'en_prueba': todavía dentro de los días de prueba gratis, no se
     *   evalúa vencimiento ni se cobra nada.
     * - 'pagado': ya existe un Pago con periodo = mes de $hoy.
     * - 'vencido': billing_day de este mes ya pasó y no está pagado.
     * - 'por_vencer': billing_day de este mes cae dentro de los próximos 7 días.
     * - 'pendiente': billing_day de este mes aún no llega (más de 7 días).
     */
    public function estadoCicloActual(Carbon $hoy): string
    {
        if ($this->enPeriodoDePrueba($hoy)) {
            return 'en_prueba';
        }

        $periodo = $hoy->format('Y-m');

        if ($this->relationLoaded('pagos')) {
            $pagado = $this->pagos->contains('periodo', $periodo);
        } else {
            $pagado = $this->pagos()->where('periodo', $periodo)->exists();
        }

        if ($pagado) {
            return 'pagado';
        }

        $fechaCiclo = $this->fechaCicloActual($hoy);

        if ($fechaCiclo->lt($hoy->copy()->startOfDay())) {
            return 'vencido';
        }

        if ($fechaCiclo->lte($hoy->copy()->addDays(7)->endOfDay())) {
            return 'por_vencer';
        }

        return 'pendiente';
    }
}
