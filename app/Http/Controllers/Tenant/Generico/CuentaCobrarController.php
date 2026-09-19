<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Models\Tenant\CuentaCobrar;
use App\Models\Tenant\CuentaCobrarCuota;
use App\Services\Tenant\Generico\CuentaCobrarAbonoService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Modulo de "cuentas por cobrar" (ventas al credito), exclusivo del
 * vertical 'generico'. No reemplaza el guardado normal de la venta
 * (VentaController::store(), compartido con tallermoto y NO tocado): esto
 * solo crea el registro de credito y su plan de cuotas (opcional) despues
 * de que la venta ya se guardo con VEN_TipoPago = 2.
 */
class CuentaCobrarController extends Controller
{
    /** Tolerancia de redondeo (centimos) al comparar sumas de dinero. */
    const TOLERANCIA = 0.01;

    /**
     * Listado de cuentas por cobrar, con datos del cliente y un estado
     * "vencida" calculado al leer (nunca almacenado, asi nunca queda
     * desactualizado). Soporta filtro por rango de fechas de emision
     * (por defecto, el dia de hoy) y busqueda por cliente (nombre o
     * documento) — al buscar un cliente sin fechas se puede ver toda su
     * deuda acumulada, no solo la de hoy.
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

        [$cuentas, $resumen, $fechaDesde, $fechaHasta, $cliente] = $this->obtenerCuentasFiltradas($request, $esAjax);

        if ($esAjax) {
            return response()->json([
                'cuentas' => $cuentas->values(),
                'resumen' => $resumen,
            ]);
        }

        return view('tenant_generico.cuentas_cobrar.index', compact('cuentas', 'resumen', 'fechaDesde', 'fechaHasta', 'cliente'));
    }

    /**
     * Logica de filtrado compartida entre el listado (index, HTML/AJAX) y
     * el reporte PDF, para que ambos respondan exactamente igual a los
     * mismos filtros de fecha/cliente y nunca queden desincronizados.
     *
     * Devuelve [cuentas, resumen, fechaDesde, fechaHasta, cliente].
     */
    private function obtenerCuentasFiltradas(Request $request, bool $esAjax = false): array
    {
        $hoy = Carbon::now('America/Lima')->toDateString();

        // En la primera carga (no AJAX) se filtra por defecto al dia de
        // hoy, tal como se pidio; en llamadas AJAX posteriores el
        // navegador siempre manda explicitamente lo que quiere filtrar
        // (incluyendo "sin fechas" para ver todo el historico de un
        // cliente), asi que ahi no se aplica ningun default.
        $fechaDesde = $request->get('fecha_desde', $esAjax ? null : $hoy);
        $fechaHasta = $request->get('fecha_hasta', $esAjax ? null : $hoy);
        $cliente = trim((string) $request->get('cliente', ''));

        $query = DB::table('cuenta_cobrar as cxc')
            ->join('venta as v', 'v.VEN_Id', '=', 'cxc.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->select(
                'cxc.CXC_Id',
                'cxc.VEN_Id',
                'cxc.CXC_MontoTotal',
                'cxc.CXC_MontoAdelanto',
                'cxc.CXC_MontoAbonado',
                'cxc.CXC_MontoPendiente',
                'cxc.CXC_TieneCuotas',
                'cxc.CXC_FechaEmision',
                'cxc.CXC_FechaVencimiento',
                'cxc.CXC_Estado',
                'c.CLI_Nombre',
                'c.CLI_NumDocumento'
            );

        if ($fechaDesde) {
            $query->whereDate('cxc.CXC_FechaEmision', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('cxc.CXC_FechaEmision', '<=', $fechaHasta);
        }

        if ($cliente !== '') {
            $query->where(function ($q) use ($cliente) {
                $q->where('c.CLI_Nombre', 'like', '%' . $cliente . '%')
                  ->orWhere('c.CLI_NumDocumento', 'like', '%' . $cliente . '%');
            });
        }

        $cuentas = $query
            ->orderByRaw('cxc.CXC_Estado asc, cxc.CXC_FechaVencimiento asc')
            ->get()
            ->map(function ($cuenta) use ($hoy) {
                $cuenta->vencida = $cuenta->CXC_Estado == CuentaCobrar::ESTADO_PENDIENTE
                    && $cuenta->CXC_FechaVencimiento !== null
                    && $cuenta->CXC_FechaVencimiento < $hoy;

                return $cuenta;
            });

        $resumen = [
            'cantidad' => $cuentas->count(),
            'total' => round((float) $cuentas->sum('CXC_MontoTotal'), 2),
            'adelanto' => round((float) $cuentas->sum('CXC_MontoAdelanto'), 2),
            'abonado' => round((float) $cuentas->sum('CXC_MontoAbonado'), 2),
            'pendiente' => round((float) $cuentas->sum('CXC_MontoPendiente'), 2),
            'vencidas' => $cuentas->where('vencida', true)->count(),
        ];

        return [$cuentas, $resumen, $fechaDesde, $fechaHasta, $cliente];
    }

    /**
     * Reporte PDF de cuentas por cobrar, respetando los mismos filtros
     * (fecha_desde, fecha_hasta, cliente) que el listado en pantalla.
     * Muestra, por cada cuenta: el detalle de la venta/cliente, el total,
     * adelanto, abonado y pendiente, el plan de cuotas (si aplica) y el
     * historial completo de abonos (fecha, metodo, monto) — es decir,
     * "lo que debe y lo que ya pago" de forma clara y precisa, ademas del
     * resumen agregado de todo lo filtrado.
     */
    public function reportePdf(Request $request)
    {
        [$cuentas, $resumen, $fechaDesde, $fechaHasta, $cliente] = $this->obtenerCuentasFiltradas($request, false);

        $cxcIds = $cuentas->pluck('CXC_Id')->all();

        // Cuotas y abonos de todas las cuentas filtradas, en 2 consultas
        // (no N+1), agrupadas luego por CXC_Id en memoria.
        $cuotasPorCuenta = CuentaCobrarCuota::whereIn('CXC_Id', $cxcIds)
            ->orderBy('CCC_Numero')
            ->get()
            ->groupBy('CXC_Id');

        $abonosPorCuenta = DB::table('cuenta_cobrar_abono as cca')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cca.MEP_Id')
            ->whereIn('cca.CXC_Id', $cxcIds)
            ->select('cca.*', 'mp.MEP_Pago as metodo')
            ->orderBy('cca.CCA_Fecha')
            ->get()
            ->groupBy('CXC_Id');

        $cuentas = $cuentas->map(function ($cuenta) use ($cuotasPorCuenta, $abonosPorCuenta) {
            $cuenta->cuotas = $cuotasPorCuenta->get($cuenta->CXC_Id, collect());
            $cuenta->abonos = $abonosPorCuenta->get($cuenta->CXC_Id, collect());

            return $cuenta;
        });

        $fechaGeneracion = Carbon::now('America/Lima');

        $pdf = Pdf::loadView('tenant_generico.cuentas_cobrar.reporte_pdf', compact(
            'cuentas',
            'resumen',
            'fechaDesde',
            'fechaHasta',
            'cliente',
            'fechaGeneracion'
        ))->setPaper('a4', 'portrait');

        return $pdf->stream('cuentas-por-cobrar.pdf');
    }

    /**
     * Detalle de una cuenta: datos de venta/cliente, cuotas (si aplica) y
     * el historial completo de abonos.
     */
    public function show(CuentaCobrar $cuentaCobrar)
    {
        $venta = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('v.VEN_Id', $cuentaCobrar->VEN_Id)
            ->select('v.VEN_Id', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_Celular', 'v.created_at')
            ->first();

        $cuotas = $cuentaCobrar->cuotas()->get();

        $abonos = $cuentaCobrar->abonos()
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cuenta_cobrar_abono.MEP_Id')
            ->select('cuenta_cobrar_abono.*', 'mp.MEP_Pago as metodo')
            ->orderByDesc('cuenta_cobrar_abono.CCA_Fecha')
            ->get();

        $metodoPago = DB::table('metodo_pago')->where('MEP_Status', 1)->orderBy('MEP_Id')->get();

        return view('tenant_generico.cuentas_cobrar.show', compact('cuentaCobrar', 'venta', 'cuotas', 'abonos', 'metodoPago'));
    }

    /**
     * Atajo para llegar a la cuenta por cobrar de una venta a partir de su
     * VEN_Id (usado por el link "Ver cuenta" del listado general de
     * Ventas, que es compartido con tallermoto y no se toca: este metodo
     * vive aparte y solo redirige al detalle real).
     */
    public function showByVenta($ventaId)
    {
        $cuentaCobrar = CuentaCobrar::where('VEN_Id', $ventaId)->firstOrFail();

        return redirect()->route('tenant.ventas.cuentascobrar.show', ['cuentaCobrar' => $cuentaCobrar->CXC_Id]);
    }

    /**
     * Lista los VEN_Id que SI tienen una cuenta por cobrar. La usa el
     * listado general de Ventas (compartido con tallermoto, no se toca)
     * para decidir, del lado del navegador, en que filas mostrar el link
     * "Ver cuenta por cobrar" — el metodo de pago mostrado ("Mixto") no
     * alcanza para saberlo, porque "Mixto" tambien se usa en ventas al
     * contado con pago dividido que nunca generan cuenta por cobrar.
     */
    public function idsConCuenta()
    {
        return response()->json(CuentaCobrar::pluck('VEN_Id'));
    }

    /**
     * Crea la cuenta por cobrar (y su plan de cuotas, si se definio) para
     * una venta que ya se guardo al credito. Se llama justo despues de
     * que tenant.ventas.venta.store responda exitosamente.
     */
    public function store(Request $request)
    {
        // num_cuotas/frecuencia_dias van como 'nullable': cuando no se
        // definen cuotas, el navegador los manda vacios, y "required_if"
        // no es confiable aqui porque tiene_cuotas llega como "0"/"1" (no
        // como el string literal "true" que required_if compara). La
        // obligatoriedad real se valida a mano justo despues.
        $validated = $request->validate([
            'venta_id' => ['required', 'integer', 'exists:venta,VEN_Id'],
            'adelanto_pagos' => ['nullable', 'array'],
            'adelanto_pagos.*.metodo_pago_id' => ['required', 'integer', 'exists:metodo_pago,MEP_Id'],
            'adelanto_pagos.*.monto' => ['required', 'numeric', 'min:0.01'],
            'tiene_cuotas' => ['required', 'boolean'],
            'num_cuotas' => ['nullable', 'integer', 'min:1', 'max:60'],
            'frecuencia_dias' => ['nullable', 'integer', 'min:1', 'max:365'],
        ], [
            'venta_id.exists' => 'La venta indicada no existe.',
        ]);

        $tieneCuotasSolicitada = filter_var($validated['tiene_cuotas'], FILTER_VALIDATE_BOOLEAN);

        if ($tieneCuotasSolicitada && (empty($validated['num_cuotas']) || empty($validated['frecuencia_dias']))) {
            return response()->json([
                'error' => 'Debe indicar el número de cuotas y cada cuántos días vence cada una.',
            ], 422);
        }

        $ventaId = (int) $validated['venta_id'];
        $venta = DB::table('venta')->where('VEN_Id', $ventaId)->first();

        // Nunca se confia en que el front avise que esto es una venta al
        // credito: se relee de la base de datos.
        if (!$venta || (int) $venta->VEN_TipoPago !== 2) {
            return response()->json([
                'error' => 'Esta venta no esta marcada como venta al credito.',
            ], 422);
        }

        // El total real de la venta se recalcula desde detalle_venta,
        // nunca se confia en un total que venga del navegador (misma
        // formula que VentaPagoController).
        $montoTotal = round((float) DB::table('detalle_venta')
            ->where('VEN_Id', $ventaId)
            ->sum(DB::raw('(DEV_Cantidad * DEV_PrecioUnitario) - DEV_Descuento')), 2);

        // El adelanto ya quedo guardado por el store() de la venta
        // (VEN_Pagado); no se confia en ningun monto de adelanto que
        // venga en este request aparte.
        $adelanto = round((float) $venta->VEN_Pagado, 2);

        if ($adelanto > $montoTotal + self::TOLERANCIA) {
            return response()->json([
                'error' => 'El adelanto no puede ser mayor al total de la venta.',
            ], 422);
        }

        $adelantoPagos = $validated['adelanto_pagos'] ?? [];

        if ($adelanto > self::TOLERANCIA) {
            $sumaPagos = round(array_sum(array_map(fn ($p) => (float) $p['monto'], $adelantoPagos)), 2);

            if (abs($sumaPagos - $adelanto) > self::TOLERANCIA) {
                return response()->json([
                    'error' => 'La suma de los metodos del adelanto (S/ ' . number_format($sumaPagos, 2) .
                        ') no coincide con el adelanto guardado en la venta (S/ ' . number_format($adelanto, 2) . ').',
                ], 422);
            }
        }

        $tieneCuotas = $tieneCuotasSolicitada;
        $numCuotas = $tieneCuotas ? (int) $validated['num_cuotas'] : null;
        $frecuenciaDias = $tieneCuotas ? (int) $validated['frecuencia_dias'] : null;
        $montoPendiente = round($montoTotal - $adelanto, 2);

        try {
            $cuentaCobrar = DB::transaction(function () use (
                $ventaId,
                $venta,
                $montoTotal,
                $adelanto,
                $montoPendiente,
                $tieneCuotas,
                $numCuotas,
                $frecuenciaDias,
                $adelantoPagos
            ) {
                $existente = CuentaCobrar::where('VEN_Id', $ventaId)->first();

                if ($existente) {
                    // Si ya tiene abonos registrados, no se destruye el
                    // historial de pagos: se rechaza en vez de reemplazar.
                    if ($existente->abonos()->exists()) {
                        abort(409, 'Esta venta ya tiene una cuenta por cobrar con abonos registrados.');
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

                $cuentaCobrar = CuentaCobrar::create([
                    'VEN_Id' => $ventaId,
                    'USU_Id' => $venta->USU_Id,
                    'CXC_MontoTotal' => $montoTotal,
                    'CXC_MontoAdelanto' => $adelanto,
                    'CXC_MontoAbonado' => 0,
                    'CXC_MontoPendiente' => $montoPendiente,
                    'CXC_TieneCuotas' => $tieneCuotas,
                    'CXC_NumCuotas' => $numCuotas,
                    'CXC_FrecuenciaDias' => $frecuenciaDias,
                    'CXC_FechaEmision' => $fechaEmision,
                    'CXC_FechaVencimiento' => $fechaVencimiento,
                    'CXC_Estado' => $montoPendiente <= self::TOLERANCIA
                        ? CuentaCobrar::ESTADO_PAGADO
                        : CuentaCobrar::ESTADO_PENDIENTE,
                ]);

                if ($tieneCuotas) {
                    $montoBase = round($montoPendiente / $numCuotas, 2);
                    $acumulado = 0;

                    for ($i = 1; $i <= $numCuotas; $i++) {
                        $esUltima = $i === $numCuotas;
                        $montoCuota = $esUltima ? round($montoPendiente - $acumulado, 2) : $montoBase;
                        $acumulado = round($acumulado + $montoCuota, 2);

                        CuentaCobrarCuota::create([
                            'CXC_Id' => $cuentaCobrar->CXC_Id,
                            'CCC_Numero' => $i,
                            'CCC_MontoProgramado' => $montoCuota,
                            'CCC_FechaVencimiento' => Carbon::parse($fechaEmision)->addDays($i * $frecuenciaDias)->toDateString(),
                            'CCC_MontoAbonado' => 0,
                            'CCC_Estado' => CuentaCobrarCuota::ESTADO_PENDIENTE,
                        ]);
                    }
                }

                if (!empty($adelantoPagos)) {
                    $servicio = new CuentaCobrarAbonoService();

                    foreach ($adelantoPagos as $pago) {
                        $servicio->registrarAbono(
                            $cuentaCobrar,
                            (float) $pago['monto'],
                            (int) $pago['metodo_pago_id'],
                            'Adelanto al momento de la venta',
                            (int) $venta->USU_Id
                        );
                    }

                    $cuentaCobrar->refresh();
                }

                return $cuentaCobrar;
            });
        } catch (\Illuminate\Http\Exceptions\HttpResponseException $e) {
            throw $e;
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Throwable $e) {
            return response()->json([
                'error' => 'No se pudo registrar la cuenta por cobrar. La venta ya quedo registrada.',
            ], 500);
        }

        return response()->json([
            'success' => 'Cuenta por cobrar registrada correctamente.',
            'cuenta_cobrar_id' => $cuentaCobrar->CXC_Id,
        ]);
    }
}
