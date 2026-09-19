<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Cliente;
use App\Models\Tenant\Cotizacion;
use App\Models\Tenant\CotizacionItem;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Browsershot\Browsershot;

/**
 * Cotizaciones: presupuesto para un cliente que todavia no toca stock ni
 * genera comprobante. Al aprobarse, redirige al punto de venta ya existente
 * con el carrito precargado (mismo mecanismo que "Ventas por Bahia" via
 * cuenta_bahia) -- el cobro en si (comprobante, stock, pagos) lo sigue
 * haciendo VentaController::store(), sin duplicar esa logica aqui.
 */
class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Cotizacion::with(['cliente', 'items'])
                ->orderByDesc('COT_Id');

            if ($request->filled('estado') && $request->input('estado') !== 'TODAS') {
                if ($request->input('estado') === 'VENCIDA') {
                    $query->where('COT_Estado', Cotizacion::ESTADO_PENDIENTE)
                        ->whereDate('COT_FechaVencimiento', '<', now()->toDateString());
                } else {
                    $query->where('COT_Estado', $request->input('estado'));
                    if ($request->input('estado') === Cotizacion::ESTADO_PENDIENTE) {
                        $query->where(function ($q) {
                            $q->whereNull('COT_FechaVencimiento')
                              ->orWhereDate('COT_FechaVencimiento', '>=', now()->toDateString());
                        });
                    }
                }
            }

            $cotizaciones = $query->get();

            return datatables()::of($cotizaciones)
                ->addIndexColumn()
                ->addColumn('numero', fn ($row) => 'COT-' . str_pad($row->COT_Id, 5, '0', STR_PAD_LEFT))
                ->addColumn('cliente', fn ($row) => $row->cliente->CLI_Nombre ?? '—')
                ->addColumn('fecha', fn ($row) => $row->created_at->format('d/m/Y H:i'))
                ->addColumn('vencimiento', fn ($row) => $row->COT_FechaVencimiento ? $row->COT_FechaVencimiento->format('d/m/Y') : '—')
                ->addColumn('total', fn ($row) => 'S/ ' . number_format($row->subtotal(), 2))
                ->addColumn('estado', function ($row) {
                    $badges = [
                        'PENDIENTE' => 'badge-warning',
                        'APROBADA' => 'badge-success',
                        'RECHAZADA' => 'badge-danger',
                        'VENCIDA' => 'badge-secondary',
                    ];
                    $estado = $row->estadoMostrar();
                    return '<span class="badge ' . ($badges[$estado] ?? 'badge-light') . '">' . $estado . '</span>';
                })
                ->addColumn('acciones', function ($row) {
                    $html = '<div class="btn-group">';
                    $html .= '<a href="' . tenant_url('tenant.ventas.cotizacion.show', ['cotizacion' => $row->COT_Id]) . '" class="btn btn-warning btn-sm" title="Ver"><i class="fa fa-eye"></i></a>';

                    if ($row->estaPendiente() && !$row->estaVencida()) {
                        $html .= '<a href="' . tenant_url('tenant.ventas.cotizacion.edit', ['cotizacion' => $row->COT_Id]) . '" class="btn btn-primary btn-sm" title="Editar"><i class="fa fa-edit"></i></a>';
                        $html .= '<button type="button" class="btn btn-success btn-sm aprobarCotizacion" data-id="' . $row->COT_Id . '" title="Aprobar y generar venta"><i class="fa fa-check"></i></button>';
                        $html .= '<button type="button" class="btn btn-danger btn-sm rechazarCotizacion" data-id="' . $row->COT_Id . '" title="Rechazar"><i class="fa fa-times"></i></button>';
                    }

                    if (!$row->estaPendiente() || $row->estaVencida()) {
                        // No aprobada aun (o vencida): se puede eliminar sin perder nada real.
                        if ($row->COT_Estado !== Cotizacion::ESTADO_APROBADA) {
                            $html .= '<button type="button" class="btn btn-outline-danger btn-sm eliminarCotizacion" data-id="' . $row->COT_Id . '" title="Eliminar"><i class="fa fa-trash"></i></button>';
                        }
                    }

                    $html .= '<button type="button" class="btn btn-info btn-sm whatsappCotizacion" data-id="' . $row->COT_Id . '" title="Enviar por WhatsApp"><i class="fab fa-whatsapp"></i></button>';
                    $html .= '</div>';
                    return $html;
                })
                ->rawColumns(['estado', 'acciones'])
                ->make(true);
        }

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.index');
    }

    /**
     * Formulario del carrito. Puede abrirse en blanco, o con ?res_id= (solo
     * tallermoto) para precargar cliente/moto desde una reserva en curso
     * (Gestion de Proceso / Ventas por Bahia).
     */
    public function create(Request $request)
    {
        $clientes = DB::table('cliente')->orderBy('CLI_Nombre', 'asc')->get();

        $prefillCliente = null;
        $reservaId = null;

        if ($request->filled('res_id') && tenant('tipo_negocio') === 'tallermoto') {
            $reserva = DB::table('reservacion')->where('RES_Id', $request->input('res_id'))->first();

            if ($reserva) {
                $reservaId = $reserva->RES_Id;
                $prefillCliente = [
                    'nombre' => $reserva->RES_Cliente,
                    'celular' => $reserva->RES_Celular,
                    'moto' => $reserva->RES_Moto ?? null,
                    'placa' => $reserva->RES_Placa ?? null,
                ];

                $celular = trim((string) $reserva->RES_Celular);
                if ($celular !== '') {
                    $clienteMatch = Cliente::where('CLI_Celular', $celular)->first();
                    if ($clienteMatch) {
                        $prefillCliente['cliente_id'] = $clienteMatch->CLI_Id;
                        $prefillCliente['documento'] = $clienteMatch->CLI_NumDocumento;
                    }
                }
            }
        }

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.create', [
            'clientes' => $clientes,
            'prefillCliente' => $prefillCliente,
            'reservaId' => $reservaId,
            'cotizacion' => null,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|integer',
            'fecha_vencimiento' => 'nullable|date',
            'observacion' => 'nullable|string|max:500',
            'res_id' => 'nullable|integer',
            'items' => 'required|array|min:1',
            'items.*.pro_id' => 'nullable|integer',
            'items.*.nombre' => 'nullable|string|max:150',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio' => 'required|numeric|min:0',
            'items.*.descuento' => 'nullable|numeric|min:0',
        ]);

        $idAlmacen = tenant_caja_activa_almacen_id() ?? 1;

        $cotizacion = DB::transaction(function () use ($validated, $idAlmacen, $request) {
            $cotizacion = Cotizacion::create([
                'CLI_Id' => $validated['cliente_id'],
                'ALM_Id' => $idAlmacen,
                'USU_Id' => Auth::id(),
                'RES_Id' => tenant('tipo_negocio') === 'tallermoto' ? ($validated['res_id'] ?? null) : null,
                'COT_Estado' => Cotizacion::ESTADO_PENDIENTE,
                'COT_FechaVencimiento' => $validated['fecha_vencimiento'] ?? null,
                'COT_Observacion' => $validated['observacion'] ?? null,
                'COT_Pdf' => $this->codigoPublicoUnico(),
            ]);

            $this->guardarItems($cotizacion, $validated['items']);

            return $cotizacion;
        });

        return response()->json(['success' => true, 'cotizacion_id' => $cotizacion->COT_Id]);
    }

    public function show(string $id)
    {
        $cotizacion = Cotizacion::with(['items.producto', 'cliente'])->findOrFail($id);

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.show', [
            'cotizacion' => $cotizacion,
        ]);
    }

    public function edit(string $id)
    {
        $cotizacion = Cotizacion::with(['items.producto', 'cliente'])->findOrFail($id);

        if (!$cotizacion->estaPendiente() || $cotizacion->estaVencida()) {
            return redirect()
                ->route('tenant.ventas.cotizacion.show', $id)
                ->with('error', 'Esta cotización ya no se puede editar.');
        }

        $clientes = DB::table('cliente')->orderBy('CLI_Nombre', 'asc')->get();

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.create', [
            'clientes' => $clientes,
            'prefillCliente' => null,
            'reservaId' => $cotizacion->RES_Id ?? null,
            'cotizacion' => $cotizacion,
        ]);
    }

    public function update(Request $request, string $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        if (!$cotizacion->estaPendiente() || $cotizacion->estaVencida()) {
            return response()->json(['success' => false, 'message' => 'Esta cotización ya no se puede editar.'], 422);
        }

        $validated = $request->validate([
            'cliente_id' => 'required|integer',
            'fecha_vencimiento' => 'nullable|date',
            'observacion' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.pro_id' => 'nullable|integer',
            'items.*.nombre' => 'nullable|string|max:150',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio' => 'required|numeric|min:0',
            'items.*.descuento' => 'nullable|numeric|min:0',
        ]);

        DB::transaction(function () use ($cotizacion, $validated) {
            $cotizacion->update([
                'CLI_Id' => $validated['cliente_id'],
                'COT_FechaVencimiento' => $validated['fecha_vencimiento'] ?? null,
                'COT_Observacion' => $validated['observacion'] ?? null,
            ]);

            $cotizacion->items()->delete();
            $this->guardarItems($cotizacion, $validated['items']);
        });

        // El PDF ya generado (si alguien lo vio o lo compartio antes de esta
        // edicion) quedaria con los items/precios viejos: se borra para que
        // la proxima vez que se pida (ver o compartir) se regenere solo,
        // con los datos ya actualizados.
        self::eliminarPdfCacheado($cotizacion);

        return response()->json(['success' => true, 'cotizacion_id' => $cotizacion->COT_Id]);
    }

    /**
     * Borra el PDF cacheado de una cotizacion en disco, si existe. No borra
     * el registro ni el codigo (COT_Pdf) — la proxima vez que se pida el PDF
     * (ver o compartir), generarPdf()/compartir() lo regeneran solos, porque
     * ambos ya chequean is_file() antes de servir.
     */
    private static function eliminarPdfCacheado(Cotizacion $cotizacion): void
    {
        $rutaCompleta = self::rutaPdf(tenant('tipo_negocio'), tenant('id'), $cotizacion->COT_Pdf);

        if (is_file($rutaCompleta)) {
            unlink($rutaCompleta);
        }
    }

    public function destroy(string $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        if ($cotizacion->COT_Estado === Cotizacion::ESTADO_APROBADA) {
            return response()->json(['success' => false, 'message' => 'Esta cotización ya fue aprobada y generó una venta; no se puede eliminar.'], 422);
        }

        $cotizacion->delete();

        return response()->json(['success' => true]);
    }

    public function rechazar(string $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        if (!$cotizacion->estaPendiente()) {
            return response()->json(['success' => false, 'message' => 'Esta cotización ya no está pendiente.'], 422);
        }

        $cotizacion->update(['COT_Estado' => Cotizacion::ESTADO_RECHAZADA]);

        return response()->json(['success' => true]);
    }

    /**
     * "Aprobar" no genera la venta aqui mismo: lleva al punto de venta ya
     * existente con el carrito precargado (igual que "Cobrar" en Ventas por
     * Bahia). El cobro real (comprobante, stock, pagos) lo termina de hacer
     * VentaController::store(), que al recibir cotizacion_id marca esta
     * cotizacion como APROBADA y la enlaza a la venta creada.
     */
    public function aprobar(string $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);

        if (!$cotizacion->estaPendiente()) {
            return back()->with('error', 'Esta cotización ya no está pendiente.');
        }

        if ($cotizacion->estaVencida()) {
            return back()->with('error', 'Esta cotización venció; edítala para actualizar la fecha antes de aprobarla.');
        }

        return redirect()->route('tenant.ventas.venta.create', ['cotizacion' => $cotizacion->COT_Id]);
    }

    private function guardarItems(Cotizacion $cotizacion, array $items): void
    {
        foreach ($items as $item) {
            CotizacionItem::create([
                'COT_Id' => $cotizacion->COT_Id,
                'PRO_Id' => $item['pro_id'] ?? null,
                'COI_Nombre' => empty($item['pro_id']) ? ($item['nombre'] ?? null) : null,
                'COI_Cantidad' => $item['cantidad'],
                'COI_PrecioUnitario' => $item['precio'],
                'COI_Descuento' => $item['descuento'] ?? 0,
            ]);
        }
    }

    private function codigoPublicoUnico(): string
    {
        $alfabeto = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

        do {
            $codigo = '';
            for ($i = 0; $i < 20; $i++) {
                $codigo .= $alfabeto[random_int(0, strlen($alfabeto) - 1)];
            }
        } while (Cotizacion::where('COT_Pdf', $codigo)->exists());

        return $codigo;
    }

    /**
     * Ruta fisica donde vive (o vivira) el PDF compartible de una cotizacion.
     * Mismo mecanismo que VentaController::rutaTicketPdf().
     */
    private static function rutaPdf(string $tipoNegocio, ?string $tenantId, string $codigo): string
    {
        $path = public_path('storage/' . $tipoNegocio . '/' . $tenantId . '/archivos/cotizaciones/');
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        return $path . $codigo . '.pdf';
    }

    private static function generarPdf(Cotizacion $cotizacion): string
    {
        $cotizacion->loadMissing(['items.producto', 'cliente']);

        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        $almacen = DB::table('almacen')->where('ALM_Id', $cotizacion->ALM_Id)->first();

        $html = view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.pdf',
            compact('cotizacion', 'empresa', 'almacen')
        )->render();

        $tipoNegocio = tenant('tipo_negocio');
        $tenantId = tenant('id');
        $rutaCompleta = self::rutaPdf($tipoNegocio, $tenantId, $cotizacion->COT_Pdf);

        if (!is_file($rutaCompleta)) {
            $browsershot = Browsershot::html($html)
                ->timeout(120)
                ->format('A4')
                ->showBackground()
                ->noSandbox()
                ->setNodeEnv(['HOME' => sys_get_temp_dir()]);

            if ($chromePath = config('services.puppeteer.executable_path')) {
                $browsershot->setChromePath($chromePath);
            }

            $browsershot->save($rutaCompleta);
        }

        return route('tenant.ventas.cotizacion.compartir', $cotizacion->COT_Pdf);
    }

    public function pdf(string $id)
    {
        $cotizacion = Cotizacion::findOrFail($id);
        $url = self::generarPdf($cotizacion);

        return redirect($url);
    }

    public function whatsapp(string $id)
    {
        try {
            $cotizacion = Cotizacion::with('cliente')->findOrFail($id);
            $url = self::generarPdf($cotizacion);

            return response()->json([
                'success' => true,
                'url' => $url,
                'numero' => 'COT-' . str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT),
                'celular' => $cotizacion->cliente->CLI_Celular ?? '',
                'cliente' => $cotizacion->cliente->CLI_Nombre ?? '',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'descripcion' => 'No se pudo generar el PDF de la cotización: ' . $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Entrega el PDF por una URL corta/opaca (COT_Pdf, no el COT_Id), igual
     * que compartirTicket() en VentaController. Publica: sin middleware de
     * auth, para que el cliente pueda abrirla desde el link de WhatsApp.
     */
    public function compartir(string $codigo)
    {
        $cotizacion = Cotizacion::where('COT_Pdf', $codigo)->first();

        if (!$cotizacion) {
            abort(404);
        }

        $rutaCompleta = self::rutaPdf(tenant('tipo_negocio'), tenant('id'), $codigo);

        if (!is_file($rutaCompleta)) {
            self::generarPdf($cotizacion);
        }

        if (!is_file($rutaCompleta)) {
            abort(404);
        }

        $nombreArchivo = 'Cotizacion-' . str_pad($cotizacion->COT_Id, 5, '0', STR_PAD_LEFT) . '.pdf';

        return response()->file($rutaCompleta, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $nombreArchivo . '"',
        ]);
    }
}
