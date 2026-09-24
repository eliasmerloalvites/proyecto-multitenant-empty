<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Models\Tenant\Compra;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Controlador de Compras propio de taller de motos. Hereda toda la logica
 * compartida de App\Http\Controllers\Tenant\CompraController y solo
 * sobreescribe el catalogo de "Nueva compra" para excluir los productos
 * marcados como Servicio (PRO_TipoProducto, columna exclusiva de
 * tallermoto): un servicio no tiene stock/lotes, asi que no tiene sentido
 * ofrecerlo como algo reabastecible. El binding en AppServiceProvider hace
 * que las rutas de Compras resuelvan a ESTA clase cuando el tenant es
 * 'tallermoto'.
 */
class CompraController extends \App\Http\Controllers\Tenant\CompraController
{
    protected function productosParaCompra()
    {
        return DB::table('producto as p')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.*', 'c.*')
            ->where('p.PRO_TipoProducto', 'PRODUCTO')
            ->get();
    }

    protected function validarProductosComprables(array $proIds): void
    {
        if (empty($proIds)) {
            return;
        }

        $servicios = DB::table('producto')
            ->whereIn('PRO_Id', $proIds)
            ->where('PRO_TipoProducto', 'SERVICIO')
            ->pluck('PRO_Nombre');

        if ($servicios->isNotEmpty()) {
            throw new Exception(
                'No se puede comprar/agregar stock a un Servicio: ' . $servicios->implode(', ') . '.'
            );
        }
    }

    /**
     * Compra al credito: crea la cuenta por pagar con el total real de la
     * compra (ya guardado en detalle_compra) y, si vino un monto inicial,
     * lo registra de una vez como el primer abono -- mismo mecanismo que
     * VentaController usa para el monto inicial de una venta al credito.
     */
    protected function registrarCreditoCompra(Request $request, Compra $compra): void
    {
        $fechaVencimiento = trim((string) $request->input('fecha_vencimiento'));

        if ($fechaVencimiento === '') {
            throw new Exception('Indica la fecha de vencimiento de la compra al credito.');
        }

        $montoTotal = (float) DB::table('detalle_compra')
            ->where('COM_Id', $compra->COM_Id)
            ->sum(DB::raw('DCOM_Cantidad * DCOM_PrecioCompra'));

        $montoInicial = round((float) $request->input('monto_inicial', 0), 2);
        $metodoPagoInicial = $request->input('metodo_pago_inicial');

        if ($montoInicial > 0) {
            $metodoValido = DB::table('metodo_pago')
                ->whereNotIn('MEP_Pago', ['Credito', 'Pago Mixto'])
                ->where('MEP_Id', $metodoPagoInicial)
                ->exists();

            if (! $metodoValido) {
                throw new Exception('Indica un metodo de pago valido para el monto inicial.');
            }

            if ($montoInicial > $montoTotal + 0.009) {
                throw new Exception('El monto inicial (S/ ' . number_format($montoInicial, 2) . ') no puede superar el total de la compra (S/ ' . number_format($montoTotal, 2) . ').');
            }
        }

        $montoFaltante = max(0, round($montoTotal - $montoInicial, 2));

        $cppId = DB::table('cuenta_por_pagar')->insertGetId([
            'COM_Id'               => $compra->COM_Id,
            'CPP_MontoTotal'       => $montoTotal,
            'CPP_MontoAbonado'     => $montoInicial,
            'CPP_MontoFaltante'    => $montoFaltante,
            'CPP_FechaEmision'     => now()->toDateString(),
            'CPP_FechaVencimiento' => $fechaVencimiento,
            'CPP_Estado'           => $montoFaltante <= 0.009 ? 'PAGADA' : 'PENDIENTE',
            'created_at'           => now(),
            'updated_at'           => now(),
        ]);

        if ($montoInicial > 0) {
            DB::table('cuenta_por_pagar_abono')->insert([
                'CPP_Id'           => $cppId,
                'MEP_Id'           => $metodoPagoInicial,
                'USU_Id'           => Auth::id(),
                'CAJ_Id'           => tenant_caja_activa_id(),
                'CS_Id'            => tenant_caja_sesion_activa_id(),
                'CPPA_Monto'       => $montoInicial,
                'CPPA_Observacion' => 'Monto inicial pagado al momento de la compra.',
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}
