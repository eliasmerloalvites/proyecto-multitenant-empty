<?php

namespace App\Services\Tenant\Generico;

use App\Models\Tenant\CuentaCobrar;
use App\Models\Tenant\CuentaCobrarAbono;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Aplica un abono (pago parcial) a una cuenta por cobrar, exclusivo del
 * vertical 'generico'. Un solo lugar para la logica de "a que cuota le
 * toca este dinero", usado tanto por el adelanto inicial (registrado por
 * CuentaCobrarController::store) como por cualquier abono posterior
 * (CuentaCobrarAbonoController::store).
 *
 * Regla de aplicacion (FIFO): si la cuenta tiene un plan de cuotas fijo,
 * el monto se reparte empezando por la cuota pendiente mas antigua; si el
 * abono sobra respecto de esa cuota, el excedente sigue con la siguiente,
 * y asi sucesivamente. El cajero solo ingresa un monto, nunca elige una
 * cuota. Si la cuenta no tiene cuotas, el abono simplemente reduce el
 * saldo general (una sola fila, sin cuota asociada).
 */
class CuentaCobrarAbonoService
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    public function registrarAbono(
        CuentaCobrar $cuentaCobrar,
        float $monto,
        int $metodoPagoId,
        ?string $descripcion,
        int $usuarioId
    ): void {
        DB::transaction(function () use ($cuentaCobrar, $monto, $metodoPagoId, $descripcion, $usuarioId) {
            $restante = round($monto, 2);
            $ahora = Carbon::now('America/Lima');

            if ($cuentaCobrar->CXC_TieneCuotas) {
                $cuotas = $cuentaCobrar->cuotas()
                    ->where('CCC_Estado', \App\Models\Tenant\CuentaCobrarCuota::ESTADO_PENDIENTE)
                    ->orderBy('CCC_Numero')
                    ->get();

                foreach ($cuotas as $cuota) {
                    if ($restante <= self::TOLERANCIA) {
                        break;
                    }

                    $faltaCuota = round((float) $cuota->CCC_MontoProgramado - (float) $cuota->CCC_MontoAbonado, 2);

                    if ($faltaCuota <= self::TOLERANCIA) {
                        continue;
                    }

                    $aplicado = min($restante, $faltaCuota);

                    CuentaCobrarAbono::create([
                        'CXC_Id' => $cuentaCobrar->CXC_Id,
                        'CCC_Id' => $cuota->CCC_Id,
                        'MEP_Id' => $metodoPagoId,
                        'USU_Id' => $usuarioId,
                        'CCA_Monto' => $aplicado,
                        'CCA_Fecha' => $ahora,
                        'CCA_Descripcion' => $descripcion,
                    ]);

                    $cuota->CCC_MontoAbonado = round((float) $cuota->CCC_MontoAbonado + $aplicado, 2);

                    if ($cuota->CCC_MontoAbonado + self::TOLERANCIA >= $cuota->CCC_MontoProgramado) {
                        $cuota->CCC_Estado = \App\Models\Tenant\CuentaCobrarCuota::ESTADO_PAGADO;
                    }

                    $cuota->save();

                    $restante = round($restante - $aplicado, 2);
                }

                // Si sobro dinero (el cliente abono mas de lo que quedaba
                // en cuotas pendientes, p.ej. pago anticipado del total),
                // se registra igual como abono libre sin cuota, para que
                // el dinero nunca se pierda del historial.
                if ($restante > self::TOLERANCIA) {
                    CuentaCobrarAbono::create([
                        'CXC_Id' => $cuentaCobrar->CXC_Id,
                        'CCC_Id' => null,
                        'MEP_Id' => $metodoPagoId,
                        'USU_Id' => $usuarioId,
                        'CCA_Monto' => $restante,
                        'CCA_Fecha' => $ahora,
                        'CCA_Descripcion' => $descripcion,
                    ]);
                }
            } else {
                CuentaCobrarAbono::create([
                    'CXC_Id' => $cuentaCobrar->CXC_Id,
                    'CCC_Id' => null,
                    'MEP_Id' => $metodoPagoId,
                    'USU_Id' => $usuarioId,
                    'CCA_Monto' => $restante,
                    'CCA_Fecha' => $ahora,
                    'CCA_Descripcion' => $descripcion,
                ]);
            }

            // El encabezado siempre se recalcula desde una suma fresca de
            // los abonos reales, nunca decrementando el cache a ciegas.
            $totalAbonado = round(
                (float) $cuentaCobrar->abonos()->sum('CCA_Monto'),
                2
            );

            $cuentaCobrar->CXC_MontoAbonado = $totalAbonado;
            $cuentaCobrar->CXC_MontoPendiente = max(
                0,
                round((float) $cuentaCobrar->CXC_MontoTotal - $totalAbonado, 2)
            );
            $cuentaCobrar->CXC_Estado = $cuentaCobrar->CXC_MontoPendiente <= self::TOLERANCIA
                ? CuentaCobrar::ESTADO_PAGADO
                : CuentaCobrar::ESTADO_PENDIENTE;
            $cuentaCobrar->save();
        });
    }
}
