<?php

namespace App\Http\Controllers\Tenant\Generico;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Tenant\VentaController;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\Generico\Cotizacion;
use App\Models\Tenant\DetalleCotizacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cotizaciones, exclusivo de generico. Es igual a una venta en la
 * experiencia (mismo catalogo de productos, mismo cliente) pero NO
 * descuenta stock -- no crea ni toca ninguna fila de 'lote'. Por eso no
 * llama a nada de VentaController salvo los endpoints de solo lectura/alta
 * de clientes y el catalogo de productos, que son de proposito general y no
 * se tocan.
 */
class CotizacionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::table('cotizacion as cot')
                ->leftJoin('cliente as cli', 'cli.CLI_Id', '=', 'cot.CLI_Id')
                ->join('almacen as alm', 'alm.ALM_Id', '=', 'cot.ALM_Id')
                ->select(
                    'cot.COT_Id',
                    'cot.COT_Total',
                    'cot.COT_Estado',
                    'cot.COT_FechaVencimiento',
                    'cot.VEN_Id',
                    'cot.created_at',
                    DB::raw("COALESCE(cli.CLI_Nombre, 'Sin cliente') as CLI_Nombre"),
                    'alm.ALM_NombreAlmacen'
                )
                ->orderByDesc('cot.COT_Id');

            return datatables()::of($query)
                ->addColumn('action', function ($row) {
                    $btns = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->COT_Id .
                        '" data-original-title="Ver" class="btn btn-info btn-sm eyeCotizacion"><i class="fa fa-eye"></i></a> ';

                    // "Editar" y "Convertir a Venta" solo tienen sentido
                    // mientras esta Pendiente (ver motivoNoEditable()): una
                    // vez Aprobada/Rechazada/Anulada queda como registro
                    // historico.
                    if ((int) $row->COT_Estado === Cotizacion::ESTADO_PENDIENTE) {
                        $btns .= '<a href="' . tenant_url('tenant.ventas.cotizacion.edit.generico', ['cotizacion' => $row->COT_Id]) . '" data-toggle="tooltip"' .
                            ' data-original-title="Editar" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i></a> ';

                        $btns .= '<a href="' . tenant_url('tenant.ventas.venta.create') . '?desde_cotizacion=' . $row->COT_Id . '" data-toggle="tooltip"' .
                            ' data-original-title="Convertir a Venta" class="btn btn-success btn-sm"><i class="fa fa-cash-register"></i></a> ';
                    }

                    $btns .= '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->COT_Id .
                        '" data-original-title="PDF" class="btn btn-sm pdfCotizacion" style="background:#6C3BFF;color:#fff;"><i class="fa fa-file-pdf"></i></a> ' .
                        '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->COT_Id .
                        '" data-original-title="Anular" class="btn btn-danger btn-sm anularCotizacion"><i class="fa fa-ban"></i></a>';

                    return $btns;
                })
                ->editColumn('COT_Estado', function ($row) {
                    // Si ya se convirtio en una venta (ver
                    // marcarConvertida()), se distingue de un simple
                    // "Aprobada" manual y queda enlazada a esa venta.
                    if ((int) $row->COT_Estado === Cotizacion::ESTADO_APROBADA && $row->VEN_Id) {
                        return '<span class="cot-badge cot-badge-aprobada" title="Venta #' . $row->VEN_Id . '">' .
                            'Convertida (Venta #' . $row->VEN_Id . ')</span>';
                    }

                    return match ((int) $row->COT_Estado) {
                        1 => '<span class="cot-badge cot-badge-pendiente">Pendiente</span>',
                        2 => '<span class="cot-badge cot-badge-aprobada">Aprobada</span>',
                        3 => '<span class="cot-badge cot-badge-rechazada">Rechazada</span>',
                        default => '<span class="cot-badge cot-badge-anulada">Anulada</span>',
                    };
                })
                ->rawColumns(['action', 'COT_Estado'])
                ->make(true);
        }

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.index');
    }

    public function create()
    {
        $almacen = Almacen::activos()->get();
        $almacenCajaActiva = function_exists('tenant_caja_activa_almacen_id') ? tenant_caja_activa_almacen_id() : null;

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.create', compact('almacen', 'almacenCajaActiva'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'CLI_Id' => 'nullable|integer|exists:cliente,CLI_Id',
            'COT_FechaVencimiento' => 'nullable|date',
            'COT_Observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.PRO_Id' => 'required|integer|exists:producto,PRO_Id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.descuento' => 'nullable|numeric|min:0',
        ]);

        $cotizacion = DB::transaction(function () use ($request) {
            $total = 0;

            foreach ($request->items as $item) {
                $total += ($item['cantidad'] * $item['precio_unitario']) - ($item['descuento'] ?? 0);
            }

            $cotizacion = Cotizacion::create([
                'CLI_Id' => $request->CLI_Id,
                'ALM_Id' => $request->ALM_Id,
                'USU_Id' => Auth::id(),
                'COT_Total' => round($total, 2),
                'COT_FechaVencimiento' => $request->COT_FechaVencimiento,
                'COT_Observaciones' => $request->COT_Observaciones,
                'COT_Estado' => Cotizacion::ESTADO_PENDIENTE,
            ]);

            $item_num = 1;

            foreach ($request->items as $item) {
                DetalleCotizacion::create([
                    'COT_Id' => $cotizacion->COT_Id,
                    'PRO_Id' => $item['PRO_Id'],
                    'DCOT_Item' => $item_num,
                    'DCOT_Cantidad' => $item['cantidad'],
                    'DCOT_PrecioUnitario' => $item['precio_unitario'],
                    'DCOT_Descuento' => $item['descuento'] ?? 0,
                ]);

                $item_num++;
            }

            return $cotizacion;
        });

        return response()->json([
            'success' => 'Cotización registrada exitosamente.',
            'cotizacion_id' => $cotizacion->COT_Id,
        ]);
    }

    /**
     * Solo se puede editar mientras este Pendiente: una vez que se
     * Aprobo/Rechazo/Anulo, es un registro historico y no deberia poder
     * cambiar de contenido (por ejemplo, una vez Aprobada podria haberse
     * "convertido" en referencia de una venta, aunque hoy no se enlacen).
     * Devuelve null si se puede editar, o el motivo por el que no.
     */
    private function motivoNoEditable($cotizacion): ?string
    {
        if (!$cotizacion) {
            return 'La cotización indicada no existe.';
        }

        if ((int) $cotizacion->COT_Estado !== Cotizacion::ESTADO_PENDIENTE) {
            return 'Solo se puede editar una cotización mientras esté Pendiente.';
        }

        return null;
    }

    public function edit(string $id)
    {
        $cotizacion = DB::table('cotizacion')->where('COT_Id', $id)->first();

        if ($motivo = $this->motivoNoEditable($cotizacion)) {
            abort($cotizacion ? 422 : 404, $motivo);
        }

        $cliente = $cotizacion->CLI_Id
            ? DB::table('cliente')->where('CLI_Id', $cotizacion->CLI_Id)->first()
            : null;

        $detalles = DB::table('detalle_cotizacion as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->select('d.*', 'p.PRO_Nombre')
            ->where('d.COT_Id', $id)
            ->orderBy('d.DCOT_Item')
            ->get();

        $almacen = Almacen::activos()->get();

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.edit', compact(
            'cotizacion',
            'cliente',
            'detalles',
            'almacen'
        ));
    }

    public function update(Request $request, string $id)
    {
        $cotizacion = Cotizacion::find($id);

        if ($motivo = $this->motivoNoEditable($cotizacion)) {
            return response()->json(['error' => $motivo], $cotizacion ? 422 : 404);
        }

        $request->validate([
            'ALM_Id' => 'required|integer|exists:almacen,ALM_Id',
            'CLI_Id' => 'nullable|integer|exists:cliente,CLI_Id',
            'COT_FechaVencimiento' => 'nullable|date',
            'COT_Observaciones' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.PRO_Id' => 'required|integer|exists:producto,PRO_Id',
            'items.*.cantidad' => 'required|numeric|min:0.01',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.descuento' => 'nullable|numeric|min:0',
        ]);

        try {
            DB::transaction(function () use ($request, $cotizacion) {
                // Se revalida el estado DENTRO de la transaccion (con lock),
                // por si dos pestañas editan/anulan la misma cotizacion a la
                // vez: la segunda en llegar no debe pisar una anulacion que
                // ocurrio en el medio.
                $actual = DB::table('cotizacion')->where('COT_Id', $cotizacion->COT_Id)->lockForUpdate()->first();

                if ($motivo = $this->motivoNoEditable($actual)) {
                    // Aborta la transaccion (se revierte sola al salir con
                    // una excepcion) y se recoge afuera para responder JSON.
                    throw new \RuntimeException($motivo);
                }

                $total = 0;

                foreach ($request->items as $item) {
                    $total += ($item['cantidad'] * $item['precio_unitario']) - ($item['descuento'] ?? 0);
                }

                $cotizacion->CLI_Id = $request->CLI_Id;
                $cotizacion->ALM_Id = $request->ALM_Id;
                $cotizacion->COT_Total = round($total, 2);
                $cotizacion->COT_FechaVencimiento = $request->COT_FechaVencimiento;
                $cotizacion->COT_Observaciones = $request->COT_Observaciones;
                $cotizacion->save();

                // Se reemplaza el detalle completo en vez de "diffear" items
                // agregados/editados/quitados: es mas simple y, como una
                // cotizacion nunca mueve stock, no hay nada que revertir al
                // borrar las filas viejas.
                DetalleCotizacion::where('COT_Id', $cotizacion->COT_Id)->delete();

                $item_num = 1;

                foreach ($request->items as $item) {
                    DetalleCotizacion::create([
                        'COT_Id' => $cotizacion->COT_Id,
                        'PRO_Id' => $item['PRO_Id'],
                        'DCOT_Item' => $item_num,
                        'DCOT_Cantidad' => $item['cantidad'],
                        'DCOT_PrecioUnitario' => $item['precio_unitario'],
                        'DCOT_Descuento' => $item['descuento'] ?? 0,
                    ]);

                    $item_num++;
                }
            });
        } catch (\RuntimeException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        }

        return response()->json(['success' => 'Cotización actualizada exitosamente.']);
    }

    public function show(string $id)
    {
        $cotizacion = DB::table('cotizacion as cot')
            ->leftJoin('cliente as cli', 'cli.CLI_Id', '=', 'cot.CLI_Id')
            ->join('almacen as alm', 'alm.ALM_Id', '=', 'cot.ALM_Id')
            ->select('cot.*', 'cli.CLI_Nombre', 'cli.CLI_NumDocumento', 'cli.CLI_Celular', 'alm.ALM_NombreAlmacen')
            ->where('cot.COT_Id', $id)
            ->first();

        if (!$cotizacion) {
            return response()->json(['error' => 'La cotización indicada no existe.'], 404);
        }

        $detalles = DB::table('detalle_cotizacion as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->select('d.*', 'p.PRO_Nombre')
            ->where('d.COT_Id', $id)
            ->orderBy('d.DCOT_Item')
            ->get();

        return response()->json(['cotizacion' => $cotizacion, 'detalles' => $detalles]);
    }

    /**
     * PDF de una cotizacion individual, para enviarle al cliente. Se muestra
     * como una pagina HTML con el mismo diseño (y el mismo boton "Descargar
     * PDF" via window.print()) que ya usa el comprobante de Ventas
     * (ticket_A4.blade.php), en vez de generarla con dompdf: ese diseño usa
     * flexbox/grid, que dompdf no soporta bien, mientras que el navegador
     * real si, e imprimir/guardar como PDF desde ahi da el mismo resultado.
     */
    public function pdf(string $id)
    {
        $cotizacion = DB::table('cotizacion as cot')
            ->leftJoin('cliente as cli', 'cli.CLI_Id', '=', 'cot.CLI_Id')
            ->join('almacen as alm', 'alm.ALM_Id', '=', 'cot.ALM_Id')
            ->select(
                'cot.*',
                'cli.CLI_Nombre',
                'cli.CLI_NumDocumento',
                'cli.CLI_TipoDocumento',
                'cli.CLI_Celular',
                'cli.CLI_Direccion',
                'alm.ALM_NombreAlmacen',
                'alm.ALM_Direccion'
            )
            ->where('cot.COT_Id', $id)
            ->first();

        if (!$cotizacion) {
            abort(404, 'La cotización indicada no existe.');
        }

        $detalles = DB::table('detalle_cotizacion as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->select(
                'd.*',
                'p.PRO_Nombre',
                'p.PRO_Descripcion',
                DB::raw('(d.DCOT_Cantidad * d.DCOT_PrecioUnitario - d.DCOT_Descuento) as subtotal')
            )
            ->where('d.COT_Id', $id)
            ->orderBy('d.DCOT_Item')
            ->get();

        // Mismos datos de la empresa/almacen (RUC, razon social, logo,
        // direccion) que usa el comprobante de Ventas -- se unen igual
        // (almacen + empresa_facturacion), en vez de solo los 2 campos de
        // almacen que traia antes esta consulta.
        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $cotizacion->ALM_Id)
            ->first();

        $totalDescuento = round((float) $detalles->sum('DCOT_Descuento'), 2);
        $Subtotal = round($cotizacion->COT_Total / 1.18, 2);
        $igv = round($cotizacion->COT_Total - $Subtotal, 2);

        // Reusa el conversor de numero-a-letras que ya usa el comprobante de
        // Ventas (metodo publico y estatico, de solo lectura: no se modifica
        // VentaController).
        $LetrasTotal = VentaController::numletras(str_replace(',', '.', round($cotizacion->COT_Total, 2)));

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cotizacion.pdf', compact(
            'cotizacion',
            'detalles',
            'datosalmacen',
            'Subtotal',
            'igv',
            'totalDescuento',
            'LetrasTotal'
        ));
    }

    /**
     * "Eliminar" una cotizacion es solo marcarla Anulada (COT_Estado = 0):
     * nunca movio stock, asi que no hay nada que revertir, pero se conserva
     * para que quede el historial de que existio.
     */
    public function anular(string $id)
    {
        $cotizacion = Cotizacion::find($id);

        if (!$cotizacion) {
            return response()->json(['error' => 'La cotización indicada no existe.'], 404);
        }

        $cotizacion->COT_Estado = Cotizacion::ESTADO_ANULADA;
        $cotizacion->save();

        return response()->json(['success' => 'Cotización anulada correctamente.']);
    }

    /**
     * "Convertir a Venta": el boton de Cotizaciones no crea la venta el
     * mismo -- solo manda al cajero a la pantalla de Ventas
     * (create.blade.php, exclusiva de generico) con el carrito precargado
     * via JS, donde puede seguir agregando/quitando/modificando productos
     * con total libertad. Recien cuando esa venta se registra de verdad
     * (VentaController::store(), compartido, sin tocar) esta pantalla llama
     * aqui para dejar la cotizacion marcada como Aprobada y enlazada a la
     * venta resultante -- trazabilidad nada mas, no participa en ningun
     * calculo de stock ni de dinero.
     *
     * Igual que guardarDetallePago()/guardarCuentaCobrar() en Ventas, esto
     * NUNCA debe poder invalidar la venta si falla: para cuando esto se
     * llama, la venta ya quedo registrada.
     */
    public function marcarConvertida(Request $request, string $id)
    {
        $request->validate([
            'venta_id' => 'required|integer|exists:venta,VEN_Id',
        ]);

        $cotizacion = Cotizacion::find($id);

        if (!$cotizacion) {
            return response()->json(['error' => 'La cotización indicada no existe.'], 404);
        }

        // Idempotente: un reintento de red (o un doble click que ya se
        // proceso) no debe pisar nada ni devolver error.
        if ((int) $cotizacion->COT_Estado === Cotizacion::ESTADO_APROBADA && $cotizacion->VEN_Id) {
            return response()->json(['success' => 'La cotización ya estaba marcada como convertida.']);
        }

        $cotizacion->COT_Estado = Cotizacion::ESTADO_APROBADA;
        $cotizacion->VEN_Id = $request->venta_id;
        $cotizacion->save();

        return response()->json(['success' => 'Cotización marcada como convertida a venta.']);
    }
}
