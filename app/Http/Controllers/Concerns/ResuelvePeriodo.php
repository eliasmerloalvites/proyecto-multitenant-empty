<?php

namespace App\Http\Controllers\Concerns;

use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Filtro de periodo comun a todos los reportes del dueño: hoy / semana /
 * mes / personalizado. Un solo lugar para esta logica evita que cada
 * reporte nuevo la reimplemente (y posiblemente con bugs distintos).
 */
trait ResuelvePeriodo
{
    /**
     * @return array{0: Carbon, 1: Carbon} [$inicio, $fin]
     */
    protected function resolverPeriodo(Request $request, string $tz = 'America/Lima'): array
    {
        $periodo = $request->get('periodo', 'hoy');

        if ($periodo === 'personalizado' && $request->filled('fecha_inicio') && $request->filled('fecha_fin')) {
            return [
                Carbon::parse($request->fecha_inicio, $tz)->startOfDay(),
                Carbon::parse($request->fecha_fin, $tz)->endOfDay(),
            ];
        }

        $ahora = Carbon::now($tz);

        return match ($periodo) {
            'semana' => [$ahora->copy()->startOfWeek(), $ahora->copy()->endOfWeek()],
            'mes'    => [$ahora->copy()->startOfMonth(), $ahora->copy()->endOfMonth()],
            default  => [$ahora->copy()->startOfDay(), $ahora->copy()->endOfDay()],
        };
    }

    /**
     * El periodo inmediatamente anterior, de la misma duracion, para poder
     * mostrar "vs periodo anterior" sin importar si el periodo es de 1 dia
     * o de 45 (personalizado).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function periodoAnterior(Carbon $inicio, Carbon $fin): array
    {
        $duracionSegundos = $fin->diffInSeconds($inicio) + 1;

        return [
            $inicio->copy()->subSeconds($duracionSegundos),
            $inicio->copy()->subSecond(),
        ];
    }

    /**
     * Misma formula que usa el dashboard (HomeController::crecimientoPorcentual)
     * para que el % de variacion se lea igual en todo el sistema.
     */
    protected function crecimientoPorcentual(float $actual, float $anterior): float
    {
        if ($anterior <= 0.0) {
            return $actual > 0 ? 100.0 : 0.0;
        }

        return round((($actual - $anterior) / $anterior) * 100, 1);
    }
}
