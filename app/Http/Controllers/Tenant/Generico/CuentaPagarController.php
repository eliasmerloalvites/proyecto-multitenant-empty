<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CuentaPagar;
use App\Models\Tenant\CuentaPagarCuota;
use App\Services\Tenant\Generico\CuentaPagarAbonoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Modulo de "cuentas por pagar" (compras al credito a proveedores),
 * exclusivo del vertical 'generico'. No reemplaza el guardado normal de
 * la compra (CompraController::store(), compartido con tallermoto y NO
 * tocado salvo el agregado de 'compra_id' a su respuesta): esto solo crea
 * el registro de deuda y su plan de cuotas (opcional) despues de que la
 * compra ya se guardo con COM_TipoPago = 'Credito'.
 */
class CuentaPagarController extends Controller
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    /**
     * Listado de cuentas por pagar, con datos del proveedor y un estado
     * "vencida" calculado al leer (nunca almacenado, asi nunca queda
     * desactualizado). Soporta filtro por rango de fechas de emision (por
     * defecto, el dia de hoy) y busqueda por proveedor (razon social o
     * documento) — al buscar un proveedor sin fechas se puede ver toda la
     * deuda acumulada con el, no solo la de hoy.
     *
     * En la carga inicial de la pagina devuelve la vista completa; en
     * peticiones AJAX (los filtros del propio listado) devuelve JSON con
     * las filas y un resumen agregado, para que las tarjetas de resumen y
     * la tabla (paginada del lado del navegador) se actualicen sin
     * recargar la pagina.
     */
    public function index(Request $request)
    {
        $esAjax = $request->ajax() || $request->wantsJson();

        [$cuentas, $resumen, $fechaDesde, $fechaHasta, $proveedor] = $this->obtenerCuentasFiltradas($request, $esAjax);

        if ($esAjax) {
            return response()->json([
                'cuentas' => $cuentas->values(),
                'resumen' => $resumen,
            ]);
        }

        return view('tenant_generico.cuentas_pagar.index', compact('cuentas', 'resumen', 'fechaDesde', 'fechaHasta', 'proveedor'));
    }

    /**
     * Logica de filtrado compartida entre el listado (index, HTML/AJAX) y
     * el reporte PDF, para que ambos respondan exactamente igual a los
     * mismos filtros de fecha/proveedor y nunca queden desincronizados.
     *
     * Devuelve [cuentas, resumen, fechaDesde, fechaHasta, proveedor].
     */
    private function obtenerCuentasFiltradas(Request $request, bool $esAjax = false): array
    {
        $hoy = Carbon::now('America/Lima')->toDateString();

        // En la primera carga (no AJAX) se filtra por defecto al dia de
        // hoy; en llamadas AJAX posteriores el navegador siempre manda
        // explicitamente lo que quiere filtrar (incluyendo "sin fechas"
        // para ver todo el historico de un proveedor), asi que ahi no se
        // aplica ningun default.
        $fechaDesde = $request->get('fecha_desde', $esAjax ? null : $hoy);
        $fechaHasta = $request->get('fecha_hasta', $esAjax ? null : $hoy);
        $proveedor = trim((string) $request->get('proveedor', ''));

        $query = DB::table('cuenta_pagar as cxp')
            ->join('compra as c', 'c.COM_Id', '=', 'cxp.COM_Id')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
            ->select(
                'cxp.CXP_Id',
                'cxp.COM_Id',
                'cxp.CXP_MontoTotal',
                'cxp.CXP_MontoAdelanto',
                'cxp.CXP_MontoAbonado',
                'cxp.CXP_MontoPendiente',
                'cxp.CXP_TieneCuotas',
                'cxp.CXP_FechaEmision',
                'cxp.CXP_FechaVencimiento',
                'cxp.CXP_Estado',
                'p.PROV_RazonSocial',
                'p.PROV_NumDocumento'
            );

        if ($fechaDesde) {
            $query->whereDate('cxp.CXP_FechaEmision', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('cxp.CXP_FechaEmision', '<=', $fechaHasta);
        }

        if ($proveedor !== '') {
            $query->where(function ($q) use ($proveedor) {
                $q->where('p.PROV_RazonSocial', 'like', '%' . $proveedor . '%')
                  ->orWhere('p.PROV_NumDocumento', 'like', '%' . $proveedor . '%');
            });
        }

        $cuentas = $query
            ->orderByRaw('cxp.CXP_Estado asc, cxp.CXP_FechaVencimiento asc')
            ->get()
            ->map(function ($cuenta) use ($hoy) {
                $cuenta->vencida = $cuenta->CXP_Estado == CuentaPagar::ESTADO_PENDIENTE
                    && $cuenta->CXP_FechaVencimiento !== null
                    && $cuenta->CXP_FechaVencimiento < $hoy;

                return $cuenta;
            });

        $resumen = [
            'cantidad' => $cuentas->count(),
            'total' => round((float) $cuentas->sum('CXP_MontoTotal'), 2),
            'adelanto' => round((float) $cuentas->sum('CXP_MontoAdelanto'), 2),
            'abonado' => round((float) $cuentas->sum('CXP_MontoAbonado'), 2),
            'pendiente' => round((float) $cuentas->sum('CXP_MontoPendiente'), 2),
            'vencidas' => $cuentas->where('vencida', true)->count(),
        ];

        return [$cuentas, $resumen, $fechaDesde, $fechaHasta, $proveedor];
    }

    /**
     * Reporte PDF de cuentas por pagar, respetando los mismos filtros
     * (fecha_desde, fecha_hasta, proveedor) que el listado en pantalla.
     * Muestra, por cada cuenta: el detalle de la compra/proveedor, el
     * total, adelanto, abonado y pendiente, el plan de cuotas (si aplica)
     * y el historial completo de abonos (fecha, metodo, monto) — es
     * decir, "lo que se compro y lo que ya se pago" de forma clara y
     * precisa, ademas del resumen agregado de todo lo filtrado.
     */
    public function reportePdf(Request $request)
    {
        [$cuentas, $resumen, $fechaDesde, $fechaHasta, $proveedor] = $this->obtenerCuentasFiltradas($request, false);

        $cxpIds = $cuentas->pluck('CXP_Id')->all();

        // Cuotas y abonos de todas las cuentas filtradas, en 2 consultas
        // (no N+1), agrupadas luego por CXP_Id en memoria.
        $cuotasPorCuenta = CuentaPagarCuota::whereIn('CXP_Id', $cxpIds)
            ->orderBy('CXPC_Numero')
            ->get()
            ->groupBy('CXP_Id');

        $abonosPorCuenta = DB::table('cuenta_pagar_abono as cxpa')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cxpa.MEP_Id')
            ->whereIn('cxpa.CXP_Id', $cxpIds)
            ->select('cxpa.*', 'mp.MEP_Pago as metodo')
            ->orderBy('cxpa.CXPA_Fecha')
            ->get()
            ->groupBy('CXP_Id');

        $cuentas = $cuentas->map(function ($cuenta) use ($cuotasPorCuenta, $abonosPorCuenta) {
            $cuenta->cuotas = $cuotasPorCuenta->get($cuenta->CXP_Id, collect());
            $cuenta->abonos = $abonosPorCuenta->get($cuenta->CXP_Id, collect());

            return $cuenta;
        });

        $fechaGeneracion = Carbon::now('America/Lima');

        $pdf = Pdf::loadView('tenant_generico.cuentas_pagar.reporte_pdf', compact(
            'cuentas',
            'resumen',
            'fechaDesde',
            'fechaHasta',
            'proveedor',
            'fechaGeneracion'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('cuentas-por-pagar.pdf');
    }

    /**
     * Detalle de una cuenta: datos de compra/proveedor, cuotas (si
     * aplica) y el historial completo de abonos.
     */
    public function show(CuentaPagar $cuentaPagar)
    {
        $compra = DB::table('compra as c')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
            ->where('c.COM_Id', $cuentaPagar->COM_Id)
            ->select('c.COM_Id', 'p.PROV_RazonSocial', 'p.PROV_NumDocumento', 'p.PROV_Celular', 'c.created_at')
            ->first();

        $cuotas = $cuentaPagar->cuotas()->get();

        $abonos = $cuentaPagar->abonos()
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cuenta_pagar_abono.MEP_Id')
            ->select('cuenta_pagar_abono.*', 'mp.MEP_Pago as metodo')
            ->orderByDesc('cuenta_pagar_abono.CXPA_Fecha')
            ->get();

        $metodoPago = DB::table('metodo_pago')->where('MEP_Status', 1)->orderBy('MEP_Id')->get();

        return view('tenant_generico.cuentas_pagar.show', compact('cuentaPagar', 'compra', 'cuotas', 'abonos', 'metodoPago'));
    }

    /**
     * Atajo para llegar a la cuenta por pagar de una compra a partir de
     * su COM_Id (usado por el link "Ver cuenta por pagar" del listado
     * general de Compras, que es compartido con tallermoto y no se toca:
     * este metodo vive aparte y solo redirige al detalle real).
     */
    public function showByCompra($compraId)
    {
        $cuentaPagar = CuentaPagar::where('COM_Id', $compraId)->firstOrFail();

        return redirect()->route('tenant.compras.cuentaspagar.show', ['cuentaPagar' => $cuentaPagar->CXP_Id]);
    }

    /**
     * Crea la cuenta por pagar (y su plan de cuotas, si se definio) para
     * una compra que ya se guardo al credito. Se llama justo despues de
     * que tenant.compras.compra.store responda exitosamente.
     */
    public function store(Request $request)
    {
        // num_cuotas/frecuencia_dias van como 'nullable': cuando no se
        // definen cuotas, el navegador los manda vacios, y "required_if"
        // no es confiable aqui porque tiene_cuotas llega como "0"/"1" (no
        // como el string literal "true" que required_if compara). La
        // obligatoriedad real se valida a mano justo despues.
        $validated = $request->validate([
            'compra_id' => ['required', 'integer', 'exists:compra,COM_Id'],
            'adelanto' => ['nullable', 'numeric', 'min:0'],
            'metodo_pago_id' => ['nullable', 'integer', 'exists:metodo_pago,MEP_Id'],
            'tiene_cuotas' => ['required', 'boolean'],
            'num_cuotas' => ['nullable', 'integer', 'min:1', 'max:60'],
            'frecuencia_dias' => ['nullable', 'integer', 'min:1', 'max:365'],
        ], [
            'compra_id.exists' => 'La compra indicada no existe.',
        ]);

        $tieneCuotasSolicitada = filter_var($validated['tiene_cuotas'], FILTER_VALIDATE_BOOLEAN);

        if ($tieneCuotasSolicitada && (empty($validated['num_cuotas']) || empty($validated['frecuencia_dias']))) {
            return response()->json([
                'error' => 'Debe indicar el número de cuotas y cada cuántos días vence cada una.',
            ], 422);
        }

        $compraId = (int) $validated['compra_id'];
        $compra = DB::table('compra')->where('COM_Id', $compraId)->first();

        // Nunca se confia en que el front avise que esto es una compra al
        // credito: se relee de la base de datos.
        if (!$compra || $compra->COM_TipoPago !== 'Credito') {
            return response()->json([
                'error' => 'Esta compra no esta marcada como compra al crédito.',
            ], 422);
        }

        // El total real de la compra se recalcula desde detalle_compra,
        // nunca se confia en un total que venga del navegador. No existe
        // campo de descuento en detalle_compra (a diferencia de
        // detalle_venta), asi que la formula es mas simple.
        $montoTotal = round((float) DB::table('detalle_compra')
            ->where('COM_Id', $compraId)
            ->sum(DB::raw('DCOM_Cantidad * DCOM_PrecioCompra')), 2);

        // A diferencia de venta (que ya guarda VEN_Pagado), compra no
        // tiene ningun campo de adelanto: el monto viene directo de este
        // request y solo se valida contra el total recalculado.
        $adelanto = round((float) ($validated['adelanto'] ?? 0), 2);

        if ($adelanto > $montoTotal + self::TOLERANCIA) {
            return response()->json([
                'error' => 'El adelanto no puede ser mayor al total de la compra.',
            ], 422);
        }

        if ($adelanto > self::TOLERANCIA && empty($validated['metodo_pago_id'])) {
            return response()->json([
                'error' => 'Debe indicar el método de pago del adelanto.',
            ], 422);
        }

        $tieneCuotas = $tieneCuotasSolicitada;
        $numCuotas = $tieneCuotas ? (int) $validated['num_cuotas'] : null;
        $frecuenciaDias = $tieneCuotas ? (int) $validated['frecuencia_dias'] : null;
        $montoPendiente = round($montoTotal - $adelanto, 2);

        try {
            $cuentaPagar = DB::transaction(function () use (
                $compraId,
                $compra,
                $montoTotal,
                $adelanto,
                $montoPendiente,
                $tieneCuotas,
                $numCuotas,
                $frecuenciaDias,
                $validated
            ) {
                $existente = CuentaPagar::where('COM_Id', $compraId)->first();

                if ($existente) {
                    // Si ya tiene abonos registrados, no se destruye el
                    // historial de pagos: se rechaza en vez de reemplazar.
                    if ($existente->abonos()->exists()) {
                        abort(409, 'Esta compra ya tiene una cuenta por pagar con abonos registrados.');
                    }

                    $existente->delete(); // cascada limpia cuotas/abonos (no deberia haber abonos aqui)
                }

                $fechaEmision = Carbon::now('America/Lima')->toDateString();
                $fechaVencimiento = null;

                if ($tieneCuotas) {
                    $fechaVencimiento = Carbon::parse($fechaEmision)
                        ->addDays($numCuotas * $frecuenciaDias)
                        ->toDateString();
                }

                $cuentaPagar = CuentaPagar::create([
                    'COM_Id' => $compraId,
                    'USU_Id' => $compra->USU_Id,
                    'CXP_MontoTotal' => $montoTotal,
                    'CXP_MontoAdelanto' => $adelanto,
                    'CXP_MontoAbonado' => 0,
                    'CXP_MontoPendiente' => $montoPendiente,
                    'CXP_TieneCuotas' => $tieneCuotas,
                    'CXP_NumCuotas' => $numCuotas,
                    'CXP_FrecuenciaDias' => $frecuenciaDias,
                    'CXP_FechaEmision' => $fechaEmision,
                    'CXP_FechaVencimiento' => $fechaVencimiento,
                    'CXP_Estado' => $montoPendiente <= self::TOLERANCIA
                        ? CuentaPagar::ESTADO_PAGADO
                        : CuentaPagar::ESTADO_PENDIENTE,
                ]);

                if ($tieneCuotas) {
                    $montoBase = round($montoPendiente / $numCuotas, 2);
                    $acumulado = 0;

                    for ($i = 1; $i <= $numCuotas; $i++) {
                        $esUltima = $i === $numCuotas;
                        $montoCuota = $esUltima ? round($montoPendiente - $acumulado, 2) : $montoBase;
                        $acumulado = round($acumulado + $montoCuota, 2);

                        CuentaPagarCuota::create([
                            'CXP_Id' => $cuentaPagar->CXP_Id,
                            'CXPC_Numero' => $i,
                            'CXPC_MontoProgramado' => $montoCuota,
                            'CXPC_FechaVencimiento' => Carbon::parse($fechaEmision)->addDays($i * $frecuenciaDias)->toDateString(),
                            'CXPC_MontoAbonado' => 0,
                            'CXPC_Estado' => CuentaPagarCuota::ESTADO_PENDIENTE,
                        ]);
                    }
                }

                if ($adelanto > self::TOLERANCIA) {
                    $servicio = new CuentaPagarAbonoService();

                    $servicio->registrarAbono(
                        $cuentaPagar,
                        $adelanto,
                        (int) $validated['metodo_pago_id'],
                        'Adelanto al momento de la compra',
                        (int) $compra->USU_Id
                    );

                    $cuentaPagar->refresh();
                }

                return $cuentaPagar;
            });
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo registrar la cuenta por pagar. La compra ya quedo registrada.',
            ], 500);
        }

        return response()->json([
            'success' => 'Cuenta por pagar registrada correctamente.',
            'cuenta_pagar_id' => $cuentaPagar->CXP_Id,
        ]);
    }
}
