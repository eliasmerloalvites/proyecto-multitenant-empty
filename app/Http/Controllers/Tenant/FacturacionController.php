<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\OrdenPago;
use App\Services\CulqiOrderService;
use Carbon\Carbon;

class FacturacionController extends Controller
{
    public function __construct(private CulqiOrderService $culqiOrders)
    {
    }

    /**
     * "Mi Facturación": lo que ve el dueño del negocio (no el staff de
     * Kael) sobre su propia suscripción — plan, próximo cobro, historial
     * de pagos y el botón para pagar si tiene algo pendiente. Usa el mismo
     * Client/estadoCicloActual() que ya usa cobros:procesar del lado
     * central, para que ambos lados nunca digan cosas distintas.
     */
    public function index()
    {
        $client = Client::where('tenant_id', tenant('id'))->firstOrFail();
        $hoy = Carbon::now('America/Lima');
        $periodo = $hoy->format('Y-m');

        $estadoCiclo = $client->estadoCicloActual($hoy);
        $fechaCobro = $client->fechaCicloActual($hoy);
        $planConfig = saas_plans_config(tenant('tipo_negocio'));
        $montoEsperado = $client->montoEsperado($planConfig, tenant('plan'));

        $ordenPendiente = null;
        if (in_array($estadoCiclo, ['por_vencer', 'vencido'], true)) {
            $ordenPendiente = OrdenPago::where('client_id', $client->id)
                ->where('periodo', $periodo)
                ->where('estado', 'pending')
                ->first();
        }

        $historial = $client->pagos()->orderByDesc('fecha_pago')->limit(24)->get();

        return view('tenant_' . tenant('tipo_negocio') . '.facturacion.index', [
            'client' => $client,
            'estadoCiclo' => $estadoCiclo,
            'fechaCobro' => $fechaCobro,
            'montoEsperado' => $montoEsperado,
            'ordenPendiente' => $ordenPendiente,
            'historial' => $historial,
        ]);
    }

    /**
     * Genera (o reutiliza) la orden de Culqi del ciclo actual y manda al
     * cliente directo a pagar. GET simple en vez de un botón + AJAX: es la
     * forma más directa de "un clic y te vas a pagar".
     */
    public function pagar()
    {
        $client = Client::where('tenant_id', tenant('id'))->firstOrFail();
        $hoy = Carbon::now('America/Lima');
        $periodo = $hoy->format('Y-m');
        $planConfig = saas_plans_config(tenant('tipo_negocio'));
        $monto = $client->montoEsperado($planConfig, tenant('plan'));

        try {
            $orden = $this->culqiOrders->ordenParaCicloActual($client, $periodo, $monto);
        } catch (\Throwable $e) {
            report($e);

            return back()->with('error', 'No se pudo generar el link de pago. Intenta de nuevo en unos minutos o contáctanos.');
        }

        if (! $orden || ! $orden->qr_url) {
            return back()->with('error', 'No se pudo generar el link de pago. Contáctanos para regularizar tu cuenta.');
        }

        return redirect()->away($orden->qr_url);
    }
}
