<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CuentaPagar;
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
}
