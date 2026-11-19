<?php

namespace App\Services\Tenant\Generico;

use App\Models\Tenant\CuentaCobrar;
use App\Models\Tenant\CuentaCobrarAbono;
use App\Models\Tenant\CuentaCobrarCuota;
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

            $this->recalcularEncabezado($cuentaCobrar);
        });
    }

    /**
     * Anula un abono ya registrado. La deuda vuelve a donde estaba: si el
     * abono pagaba (total o parcialmente) una cuota, esa cuota recupera su
     * saldo y, si ya figuraba como pagada, vuelve a pendiente; el saldo de
     * la cuenta sube en el monto anulado. El abono no se borra: queda
     * marcado como anulado con motivo, usuario y fecha.
     */
    public function anularAbono(CuentaCobrarAbono $abono, string $motivo, int $usuarioId): void
    {
        DB::transaction(function () use ($abono, $motivo, $usuarioId) {
            $this->anularAbonoSinTransaccion($abono, $motivo, $usuarioId);

            $cuenta = CuentaCobrar::lockForUpdate()->findOrFail($abono->CXC_Id);
            $this->recalcularEncabezado($cuenta);
        });
    }

    /**
     * Anula el pago de una cuota: anula todos los abonos vigentes que se
     * aplicaron a ella, y la cuota queda pendiente por su monto completo.
     * Los abonos de las demas cuotas no se tocan.
     *
     * @return int cantidad de abonos anulados
     */
    public function anularPagoDeCuota(CuentaCobrarCuota $cuota, string $motivo, int $usuarioId): int
    {
        return DB::transaction(function () use ($cuota, $motivo, $usuarioId) {
            $abonos = CuentaCobrarAbono::where('CCC_Id', $cuota->CCC_Id)
                ->where('CCA_Anulado', false)
                ->lockForUpdate()
                ->get();

            foreach ($abonos as $abono) {
                $this->anularAbonoSinTransaccion($abono, $motivo, $usuarioId);
            }

            $cuenta = CuentaCobrar::lockForUpdate()->findOrFail($cuota->CXC_Id);
            $this->recalcularEncabezado($cuenta);

            return $abonos->count();
        });
    }

    private function anularAbonoSinTransaccion(CuentaCobrarAbono $abono, string $motivo, int $usuarioId): void
    {
        $abono->CCA_Anulado = true;
        $abono->CCA_FechaAnulacion = Carbon::now('America/Lima');
        $abono->CCA_UsuarioAnulacion = $usuarioId;
        $abono->CCA_MotivoAnulacion = mb_substr($motivo, 0, 150);
        $abono->save();

        if ($abono->CCC_Id) {
            $cuota = CuentaCobrarCuota::lockForUpdate()->find($abono->CCC_Id);

            if ($cuota) {
                // Se recalcula desde los abonos vigentes que le quedan, no
                // restando a ciegas, para que nunca se desfase.
                $abonado = round((float) CuentaCobrarAbono::where('CCC_Id', $cuota->CCC_Id)
                    ->where('CCA_Anulado', false)
                    ->sum('CCA_Monto'), 2);

                $cuota->CCC_MontoAbonado = $abonado;
                $cuota->CCC_Estado = $abonado + self::TOLERANCIA >= (float) $cuota->CCC_MontoProgramado
                    ? CuentaCobrarCuota::ESTADO_PAGADO
                    : CuentaCobrarCuota::ESTADO_PENDIENTE;
                $cuota->save();
            }
        }
    }

    /**
     * El encabezado siempre se recalcula desde una suma fresca de los
     * abonos vigentes, nunca decrementando el cache a ciegas.
     */
    private function recalcularEncabezado(CuentaCobrar $cuentaCobrar): void
    {
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
    }
}
