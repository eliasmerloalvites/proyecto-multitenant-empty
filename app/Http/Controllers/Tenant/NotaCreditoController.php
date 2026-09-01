<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarVentaSunatJob;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\DetalleVenta;
use App\Models\Tenant\DocumentoVenta;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\Lote;
use App\Models\Tenant\Venta;
use App\Services\Facturacion\DocumentoVentaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Emite una nota de credito que afecta una boleta o factura ya aceptada.
 *
 * Reutiliza el mismo mecanismo que boleta/factura: crea una venta y su
 * detalle con los items que se acreditan, un documento_venta propio (con su
 * propia serie/correlativo) y despacha el mismo job de envio a SUNAT.
 */
class NotaCreditoController extends Controller
{
    protected $documentoVentaService;

    public function __construct(DocumentoVentaService $documentoVentaService)
    {
        $this->documentoVentaService = $documentoVentaService;
    }

    /**
     * Lista las notas de credito emitidas, mas recientes primero.
     */
    public function index(Request $request)
    {
        $notas = DB::table('documento_venta as dov')
            ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->where('dov.DOV_Tipo', EmpresaFacturacion::TIPO_NOTA_CREDITO)
            ->select(
                'dov.DOV_Id',
                'v.VEN_Id',
                'dov.DOV_Nombre',
                'dov.DOV_Estado',
                'dov.DOV_CodMotivo',
                'dov.DOV_DesMotivo',
                'dov.DOV_TipoDocAfectado',
                'dov.DOV_NumDocAfectado',
                'c.CLI_Nombre',
                DB::raw('CAST(sum(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) as decimal(10,2)) as total'),
                DB::raw('date(v.created_at) as fecha')
            )
            ->groupBy(
                'dov.DOV_Id', 'v.VEN_Id', 'dov.DOV_Nombre', 'dov.DOV_Estado', 'dov.DOV_CodMotivo',
                'dov.DOV_DesMotivo', 'dov.DOV_TipoDocAfectado', 'dov.DOV_NumDocAfectado',
                'c.CLI_Nombre', 'v.created_at'
            )
            ->when($request->filled('estado'), fn ($q) => $q->where('dov.DOV_Estado', $request->input('estado')))
            ->when($request->filled('cod_motivo'), fn ($q) => $q->where('dov.DOV_CodMotivo', $request->input('cod_motivo')))
            ->when($request->filled('fecha_inicio'), fn ($q) => $q->where(DB::raw('date(v.created_at)'), '>=', $request->input('fecha_inicio')))
            ->when($request->filled('fecha_fin'), fn ($q) => $q->where(DB::raw('date(v.created_at)'), '<=', $request->input('fecha_fin')))
            ->when($request->filled('cliente'), function ($q) use ($request) {
                $busqueda = $request->input('cliente');
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('c.CLI_Nombre', 'like', '%' . $busqueda . '%')
                       ->orWhere('c.CLI_NumDocumento', 'like', '%' . $busqueda . '%');
                });
            })
            ->orderByDesc('dov.DOV_Id')
            ->get();

        return view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.notas-credito',
            [
                'notas' => $notas,
                'motivos' => DocumentoVentaService::MOTIVOS_NOTA_CREDITO,
                'filtros' => $request->only(['estado', 'cod_motivo', 'fecha_inicio', 'fecha_fin', 'cliente']),
            ]
        );
    }

    /**
     * Formulario: que items del comprobante original se acreditan y por que.
     */
    public function create($ventaId)
    {
        $original = $this->documentoOriginal($ventaId);

        $venta = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->where('v.VEN_Id', $ventaId)
            ->select('v.ALM_Id', 'v.CLI_Id', 'v.MEP_Id', 'c.CLI_Nombre', 'c.CLI_NumDocumento',
                     'c.CLI_TipoDocumento', 'a.ALM_NombreAlmacen')
            ->first();

        $items = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $ventaId)
            ->select('dv.DEV_Item', 'dv.PRO_Id', 'p.PRO_Nombre', 'dv.DEV_Cantidad', 'dv.DEV_PrecioUnitario', 'dv.LOT_Id')
            ->get();

        $sede = Almacen::find($venta->ALM_Id);
        $origenLabel = $original->DOV_Tipo === EmpresaFacturacion::TIPO_FACTURA ? 'factura' : 'boleta';
        $problemasSede = array_filter([
            !$sede->serieNotaCreditoPara($original->DOV_Tipo)
                ? 'A "' . $sede->ALM_NombreAlmacen . '" le falta la serie de nota de credito para ' . $origenLabel . '.'
                : null,
        ]);

        return view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.nota-credito',
            [
                'ventaId'       => $ventaId,
                'documento'     => $original,
                'venta'         => $venta,
                'items'         => $items,
                'motivos'       => DocumentoVentaService::MOTIVOS_NOTA_CREDITO,
                'problemasSede' => $problemasSede,
            ]
        );
    }

    /**
     * Crea la nota de credito y despacha su envio a SUNAT.
     */
    public function store(Request $request, $ventaId)
    {
        $original = $this->documentoOriginal($ventaId);

        $codMotivo = (string) $request->input('cod_motivo');
        $desMotivo = trim((string) $request->input('des_motivo'));
        $devolverStock = $request->boolean('devolver_stock');

        if (!isset(DocumentoVentaService::MOTIVOS_NOTA_CREDITO[$codMotivo])) {
            return response()->json(['success' => false, 'error' => 'Selecciona un motivo valido.'], 422);
        }

        if ($desMotivo === '') {
            $desMotivo = DocumentoVentaService::MOTIVOS_NOTA_CREDITO[$codMotivo];
        }

        $ventaOriginal = DB::table('venta')->where('VEN_Id', $ventaId)->first();

        // Se indexa por DEV_Item (no por PRO_Id): la venta original
        // puede tener el mismo producto en dos lineas distintas si la
        // cantidad pedida se dividio entre dos lotes al vender.
        $itemsOriginales = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $ventaId)
            ->select('dv.DEV_Item', 'dv.PRO_Id', 'p.PRO_Nombre', 'dv.DEV_Cantidad', 'dv.DEV_PrecioUnitario', 'dv.LOT_Id')
            ->get()
            ->keyBy('DEV_Item');

        $incluir     = (array) $request->input('incluir', []);
        $cantidades  = (array) $request->input('cantidad', []);

        // Arma la lista de items a acreditar, validando cada cantidad contra
        // lo que realmente tiene esa linea del comprobante original.
        $itemsCreditados = [];
        foreach ($incluir as $devItem => $marcado) {
            if (!$marcado) {
                continue;
            }

            $devItem = (int) $devItem;

            if (!isset($itemsOriginales[$devItem])) {
                continue;
            }

            $cantidad = (float) ($cantidades[$devItem] ?? 0);

            if ($cantidad <= 0) {
                continue;
            }

            $orig = clone $itemsOriginales[$devItem];

            if ($cantidad > (float) $orig->DEV_Cantidad) {
                return response()->json([
                    'success' => false,
                    'error'   => 'No puedes acreditar mas de ' . $orig->DEV_Cantidad . ' de "' . $orig->PRO_Nombre . '".',
                ], 422);
            }

            $orig->DEV_Cantidad = $cantidad;
            $itemsCreditados[] = $orig;
        }

        if (empty($itemsCreditados)) {
            return response()->json(['success' => false, 'error' => 'Selecciona al menos un producto a acreditar.'], 422);
        }

        DB::beginTransaction();
        try {
            // Venta "cabecera" de la nota: mismo cliente y sede que el
            // comprobante original, sin cobro real asociado.
            $venta = new Venta;
            $venta->CLI_Id  = $ventaOriginal->CLI_Id;
            $venta->ALM_Id  = $ventaOriginal->ALM_Id;
            $venta->MEP_Id  = $ventaOriginal->MEP_Id;
            $venta->USU_Id  = Auth::user()->id;
            $venta->VEN_TipoPago = 1;
            $venta->VEN_FechaEnvio = now();
            $venta->save();

            $item = 1;
            foreach ($itemsCreditados as $linea) {
                $detalle = new DetalleVenta();
                $detalle->VEN_Id = $venta->VEN_Id;
                $detalle->DEV_Item = $item++;
                $detalle->PRO_Id = $linea->PRO_Id;
                $detalle->DEV_Cantidad = $linea->DEV_Cantidad;
                $detalle->DEV_PrecioUnitario = $linea->DEV_PrecioUnitario;
                $detalle->LOT_Id = $linea->LOT_Id;
                $detalle->DEV_Descuento = 0;
                $detalle->save();

                if ($devolverStock) {
                    Lote::devolver($linea->LOT_Id, $linea->DEV_Cantidad);
                }
            }

            $sede = Almacen::find($ventaOriginal->ALM_Id);

            $documento = $this->documentoVentaService->crearNotaCredito(
                $venta->VEN_Id,
                $original,
                $sede,
                $codMotivo,
                $desMotivo
            );

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }

        EnviarVentaSunatJob::dispatch($venta->VEN_Id, tenant('id'), tenant('tipo_negocio'));

        return response()->json([
            'success'   => true,
            'documento' => $documento->DOV_Nombre,
            'venta_id'  => $venta->VEN_Id,
        ]);
    }

    /**
     * Aumenta el stock del lote de origen; si ya no existe, no revienta la
     * nota por eso, solo lo deja constando para revisarlo a mano.
     */


    private function documentoOriginal($ventaId): DocumentoVenta
    {
        $documento = DocumentoVenta::where('VEN_Id', $ventaId)->first();

        if (!$documento) {
            throw new RuntimeException("La venta $ventaId no tiene documento asociado.");
        }

        if (!in_array($documento->DOV_Tipo, [EmpresaFacturacion::TIPO_BOLETA, EmpresaFacturacion::TIPO_FACTURA], true)) {
            throw new RuntimeException('Solo se puede emitir una nota de credito sobre una boleta o una factura.');
        }

        return $documento;
    }
}
