<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CuentaCobrar;
use App\Services\Tenant\Generico\CuentaCobrarAbonoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Registra abonos (pagos parciales) posteriores contra una cuenta por
 * cobrar ya existente, exclusivo del vertical 'generico'. La aplicacion a
 * cuotas (si las hay) es automatica via CuentaCobrarAbonoService.
 */
class CuentaCobrarAbonoController extends Controller
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    public function store(Request $request, CuentaCobrar $cuentaCobrar)
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
            DB::transaction(function () use ($cuentaCobrar, $monto, $validated) {
                // El saldo pendiente real se recalcula desde una suma
                // fresca de abonos, nunca desde el cache CXC_MontoPendiente,
                // para decidir si se acepta o rechaza el abono.
                $totalAbonado = round((float) $cuentaCobrar->abonos()->sum('CCA_Monto'), 2);
                $saldoReal = round((float) $cuentaCobrar->CXC_MontoTotal - $totalAbonado, 2);

                if ($monto > $saldoReal + self::TOLERANCIA) {
                    abort(422, 'El abono (S/ ' . number_format($monto, 2) .
                        ') excede el saldo pendiente (S/ ' . number_format(max($saldoReal, 0), 2) . ').');
                }

                $servicio = new CuentaCobrarAbonoService();
                $servicio->registrarAbono(
                    $cuentaCobrar,
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
