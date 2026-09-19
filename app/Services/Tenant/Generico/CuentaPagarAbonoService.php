<?php

namespace App\Services\Tenant\Generico;

use App\Models\Tenant\CuentaPagar;
use App\Models\Tenant\CuentaPagarAbono;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Aplica un abono (pago parcial al proveedor) a una cuenta por pagar,
 * exclusivo del vertical 'generico'. Un solo lugar para la logica de "a
 * que cuota le toca este dinero", usado tanto por el adelanto inicial
 * (registrado por CuentaPagarController::store) como por cualquier abono
 * posterior (CuentaPagarAbonoController::store).
 *
 * Regla de aplicacion (FIFO): si la cuenta tiene un plan de cuotas fijo,
 * el monto se reparte empezando por la cuota pendiente mas antigua; si el
 * abono sobra respecto de esa cuota, el excedente sigue con la siguiente,
 * y asi sucesivamente. Quien registra el abono solo ingresa un monto,
 * nunca elige una cuota. Si la cuenta no tiene cuotas, el abono
 * simplemente reduce el saldo general (una sola fila, sin cuota
 * asociada).
 */
class CuentaPagarAbonoService
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    public function registrarAbono(
        CuentaPagar $cuentaPagar,
        float $monto,
        int $metodoPagoId,
        ?string $descripcion,
        int $usuarioId
    ): void {
        DB::transaction(function () use ($cuentaPagar, $monto, $metodoPagoId, $descripcion, $usuarioId) {
            $restante = round($monto, 2);
            $ahora = Carbon::now('America/Lima');

            if ($cuentaPagar->CXP_TieneCuotas) {
                $cuotas = $cuentaPagar->cuotas()
                    ->where('CXPC_Estado', \App\Models\Tenant\CuentaPagarCuota::ESTADO_PENDIENTE)
                    ->orderBy('CXPC_Numero')
                    ->get();

                foreach ($cuotas as $cuota) {
                    if ($restante <= self::TOLERANCIA) {
                        break;
                    }

                    $faltaCuota = round((float) $cuota->CXPC_MontoProgramado - (float) $cuota->CXPC_MontoAbonado, 2);

                    if ($faltaCuota <= self::TOLERANCIA) {
                        continue;
                    }

                    $aplicado = min($restante, $faltaCuota);

                    CuentaPagarAbono::create([
                        'CXP_Id' => $cuentaPagar->CXP_Id,
                        'CXPC_Id' => $cuota->CXPC_Id,
                        'MEP_Id' => $metodoPagoId,
                        'USU_Id' => $usuarioId,
                        'CXPA_Monto' => $aplicado,
                        'CXPA_Fecha' => $ahora,
                        'CXPA_Descripcion' => $descripcion,
                    ]);

                    $cuota->CXPC_MontoAbonado = round((float) $cuota->CXPC_MontoAbonado + $aplicado, 2);

                    if ($cuota->CXPC_MontoAbonado + self::TOLERANCIA >= $cuota->CXPC_MontoProgramado) {
                        $cuota->CXPC_Estado = \App\Models\Tenant\CuentaPagarCuota::ESTADO_PAGADO;
                    }

                    $cuota->save();

                    $restante = round($restante - $aplicado, 2);
                }

                // Si sobro dinero (se abono mas de lo que quedaba en
                // cuotas pendientes, p.ej. pago anticipado del total), se
                // registra igual como abono libre sin cuota, para que el
                // dinero nunca se pierda del historial.
                if ($restante > self::TOLERANCIA) {
                    CuentaPagarAbono::create([
                        'CXP_Id' => $cuentaPagar->CXP_Id,
                        'CXPC_Id' => null,
                        'MEP_Id' => $metodoPagoId,
                        'USU_Id' => $usuarioId,
                        'CXPA_Monto' => $restante,
                        'CXPA_Fecha' => $ahora,
                        'CXPA_Descripcion' => $descripcion,
                    ]);
                }
            } else {
                CuentaPagarAbono::create([
                    'CXP_Id' => $cuentaPagar->CXP_Id,
                    'CXPC_Id' => null,
                    'MEP_Id' => $metodoPagoId,
                    'USU_Id' => $usuarioId,
                    'CXPA_Monto' => $restante,
                    'CXPA_Fecha' => $ahora,
                    'CXPA_Descripcion' => $descripcion,
                ]);
            }

            // El encabezado siempre se recalcula desde una suma fresca de
            // los abonos reales, nunca decrementando el cache a ciegas.
            $totalAbonado = round(
                (float) $cuentaPagar->abonos()->sum('CXPA_Monto'),
                2
            );

            $cuentaPagar->CXP_MontoAbonado = $totalAbonado;
            $cuentaPagar->CXP_MontoPendiente = max(
                0,
                round((float) $cuentaPagar->CXP_MontoTotal - $totalAbonado, 2)
            );
            $cuentaPagar->CXP_Estado = $cuentaPagar->CXP_MontoPendiente <= self::TOLERANCIA
                ? CuentaPagar::ESTADO_PAGADO
                : CuentaPagar::ESTADO_PENDIENTE;
            $cuentaPagar->save();
        });
    }
}
