<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CuentaPagar;
use App\Models\Tenant\CuentaPagarAbono;
use App\Models\Tenant\CuentaPagarCuota;
use App\Services\Tenant\Generico\CuentaPagarAbonoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Registra abonos (pagos parciales al proveedor) posteriores contra una
 * cuenta por pagar ya existente, exclusivo del vertical 'generico'. La
 * aplicacion a cuotas (si las hay) es automatica via
 * CuentaPagarAbonoService.
 */
class CuentaPagarAbonoController extends Controller
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    public function store(Request $request, CuentaPagar $cuentaPagar)
    {
        $validated = $request->validate([
            'monto' => ['required', 'numeric', 'min:0.01'],
            'metodo_pago_id' => ['required', 'integer', 'exists:metodo_pago,MEP_Id'],
            'descripcion' => ['nullable', 'string', 'max:150'],
        ], [
            'monto.min' => 'El abono debe ser mayor a 0.',
        ]);

        $monto = round((float) $validated['monto'], 2);

        try {
            DB::transaction(function () use ($cuentaPagar, $monto, $validated) {
                // El saldo pendiente real se recalcula desde una suma
                // fresca de abonos, nunca desde el cache
                // CXP_MontoPendiente, para decidir si se acepta o
                // rechaza el abono.
                $totalAbonado = round((float) $cuentaPagar->abonos()->sum('CXPA_Monto'), 2);
                $saldoReal = round((float) $cuentaPagar->CXP_MontoTotal - $totalAbonado, 2);

                if ($monto > $saldoReal + self::TOLERANCIA) {
                    abort(422, 'El abono (S/ ' . number_format($monto, 2) .
                        ') excede el saldo pendiente (S/ ' . number_format(max($saldoReal, 0), 2) . ').');
                }

                $servicio = new CuentaPagarAbonoService();
                $servicio->registrarAbono(
                    $cuentaPagar,
                    $monto,
                    (int) $validated['metodo_pago_id'],
                    $validated['descripcion'] ?? null,
                    (int) Auth::id()
                );
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo registrar el abono.',
            ], 500);
        }

        return response()->json(['success' => 'Abono registrado correctamente.']);
    }

    /**
     * Anula un abono puntual. La deuda vuelve a la cuota que cubria (y al
     * saldo de la cuenta); ver CuentaPagarAbonoService::anularAbono().
     */
    public function anular(Request $request, CuentaPagar $cuentaPagar, CuentaPagarAbono $abono)
    {
        abort_unless((int) $abono->CXP_Id === (int) $cuentaPagar->CXP_Id, 404);

        $motivo = trim((string) $request->input('motivo'));

        if ($motivo === '') {
            return response()->json(['error' => 'Indica el motivo de la anulación.'], 422);
        }

        if ($abono->CXPA_Anulado) {
            return response()->json(['error' => 'Este abono ya está anulado.'], 422);
        }

        try {
            (new CuentaPagarAbonoService())->anularAbono($abono, $motivo, (int) Auth::id());
        } catch (\Throwable $e) {
            return response()->json(['error' => 'No se pudo anular el abono.'], 500);
        }

        return response()->json(['success' => 'Abono anulado. La deuda volvió a la cuenta.']);
    }

    /**
     * Anula el pago de una cuota completa: todos los abonos aplicados a ella
     * se anulan y la cuota vuelve a quedar pendiente por su monto.
     */
    public function anularPagoCuota(Request $request, CuentaPagar $cuentaPagar, CuentaPagarCuota $cuota)
    {
        abort_unless((int) $cuota->CXP_Id === (int) $cuentaPagar->CXP_Id, 404);

        $motivo = trim((string) $request->input('motivo'));

        if ($motivo === '') {
            return response()->json(['error' => 'Indica el motivo de la anulación.'], 422);
        }

        if ((float) $cuota->CXPC_MontoAbonado <= 0) {
            return response()->json(['error' => 'Esta cuota no tiene pagos que anular.'], 422);
        }

        try {
            $anulados = (new CuentaPagarAbonoService())->anularPagoDeCuota($cuota, $motivo, (int) Auth::id());
        } catch (\Throwable $e) {
            return response()->json(['error' => 'No se pudo anular el pago de la cuota.'], 500);
        }

        return response()->json([
            'success' => 'Pago de la cuota #' . $cuota->CXPC_Numero . ' anulado (' . $anulados . ' abono(s)). La deuda volvió a la cuota.',
        ]);
    }
}
