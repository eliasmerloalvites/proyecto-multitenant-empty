<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Concerns\ResuelvePeriodo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * Seccion propia para el perfil Contador (ver rol "Contador" en
 * RoleAndPermissionSeeder): solo lectura/descarga de la informacion que un
 * contador necesita para su trabajo -- nada de crear/editar/eliminar nada
 * del negocio. Exclusivo de tallermoto (nadie pidio esto para generico).
 *
 * IGV: se calcula igual que en SunatService::mapearItems() -- 18% "por
 * dentro" del total cobrado (valor_venta = total / 1.18), no un porcentaje
 * aplicado aparte, para que el desglose de este reporte coincida centavo a
 * centavo con lo que ya se declaro ante SUNAT en cada comprobante.
 */
class ContabilidadController extends Controller
{
    use ResuelvePeriodo;

    private const IGV = 0.18;

    public function index()
    {
        return view('tenant_tallermoto.contabilidad.index');
    }

    /**
     * Libro de Ventas: boletas y facturas emitidas (no anuladas), con el
     * desglose base imponible / IGV / total que se sube al PLE de SUNAT.
     * Las notas de credito (NCR) y las notas de venta internas sin
     * comprobante (PRO) no forman parte del libro de ventas formal, asi
     * que se excluyen.
     */
    public function libroVentas(Request $request)
    {
        [$inicio, $fin] = $this->resolverPeriodo($request);

        $filas = $this->consultaLibroVentas($inicio, $fin);

        if ($request->get('export') === 'xlsx') {
            return $this->exportarExcel(
                'libro_ventas_' . $inicio->toDateString() . '_' . $fin->toDateString() . '.xlsx',
                ['Fecha', 'Tipo', 'Serie-Numero', 'Cliente', 'Doc. Cliente', 'Base Imponible', 'IGV', 'Total', 'Estado SUNAT'],
                $filas->map(fn ($f) => [
                    $f->fecha, $f->tipo_doc, $f->comprobante, $f->cliente, $f->doc_cliente,
                    $f->base_imponible, $f->igv, $f->total, $f->estado_sunat,
                ])
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
                'filas' => $filas,
                'totales' => [
                    'base_imponible' => round($filas->sum('base_imponible'), 2),
                    'igv' => round($filas->sum('igv'), 2),
                    'total' => round($filas->sum('total'), 2),
                ],
            ]);
        }

        return view('tenant_tallermoto.contabilidad.libro-ventas');
    }

    private function consultaLibroVentas($inicio, $fin)
    {
        return DB::table('venta as v')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->leftJoin('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            // El total real de la venta sale del detalle (DEV_PrecioUnitario
            // ya viene neto de descuento), no de VEN_Pagado: en una venta al
            // credito VEN_Pagado queda en 0 aunque la venta si tenga total.
            ->leftJoin('detalle_venta as dv', 'dv.VEN_Id', '=', 'v.VEN_Id')
            ->whereIn('dov.DOV_Tipo', ['BOL', 'FAC'])
            ->where('dov.DOV_Anulado', 0)
            ->whereBetween('v.created_at', [$inicio, $fin])
            ->groupBy(
                'v.VEN_Id', 'v.created_at', 'dov.DOV_Tipo', 'dov.DOV_Serie', 'dov.DOV_Numero',
                'dov.DOV_EstadoSunat', 'c.CLI_Nombre', 'c.CLI_NumDocumento'
            )
            ->orderBy('v.created_at')
            ->select(
                'v.VEN_Id',
                DB::raw('SUM(dv.DEV_Cantidad * dv.DEV_PrecioUnitario) as total'),
                'v.created_at as fecha_raw',
                'dov.DOV_Tipo as tipo_doc',
                'dov.DOV_Serie',
                'dov.DOV_Numero',
                'dov.DOV_EstadoSunat as estado_sunat',
                'c.CLI_Nombre as cliente',
                'c.CLI_NumDocumento as doc_cliente'
            )
            ->get()
            ->map(function ($v) {
                $total = round((float) $v->total, 2);
                $baseImponible = round($total / (1 + self::IGV), 2);
                $igv = round($total - $baseImponible, 2);

                return (object) [
                    'fecha' => \Illuminate\Support\Carbon::parse($v->fecha_raw)->format('d/m/Y'),
                    'tipo_doc' => $v->tipo_doc === 'FAC' ? 'Factura' : 'Boleta',
                    'comprobante' => $v->DOV_Serie . '-' . str_pad((string) $v->DOV_Numero, 6, '0', STR_PAD_LEFT),
                    'cliente' => $v->cliente ?: 'Cliente Genérico',
                    'doc_cliente' => $v->doc_cliente ?: '-',
                    'base_imponible' => $baseImponible,
                    'igv' => $igv,
                    'total' => $total,
                    'estado_sunat' => $v->estado_sunat ?? '-',
                ];
            });
    }

    /**
     * Libro de Compras: compras registradas (no anuladas), con el mismo
     * desglose de IGV -- solo tiene sentido para compras con Factura
     * (COM_TipoDocumento), que es el unico comprobante que da derecho a
     * credito fiscal; una Boleta/Recibo de compra se lista igual pero sin
     * desglose de IGV (base imponible = total, IGV = 0), porque asi es
     * como el contador debe tratarla ante SUNAT.
     */
    public function libroCompras(Request $request)
    {
        [$inicio, $fin] = $this->resolverPeriodo($request);

        $filas = $this->consultaLibroCompras($inicio, $fin);

        if ($request->get('export') === 'xlsx') {
            return $this->exportarExcel(
                'libro_compras_' . $inicio->toDateString() . '_' . $fin->toDateString() . '.xlsx',
                ['Fecha', 'Tipo Doc.', 'N° Documento', 'Proveedor', 'Base Imponible', 'IGV', 'Total'],
                $filas->map(fn ($f) => [
                    $f->fecha, $f->tipo_doc, $f->num_documento, $f->proveedor,
                    $f->base_imponible, $f->igv, $f->total,
                ])
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
                'filas' => $filas,
                'totales' => [
                    'base_imponible' => round($filas->sum('base_imponible'), 2),
                    'igv' => round($filas->sum('igv'), 2),
                    'total' => round($filas->sum('total'), 2),
                ],
            ]);
        }

        return view('tenant_tallermoto.contabilidad.libro-compras');
    }

    private function consultaLibroCompras($inicio, $fin)
    {
        return DB::table('compra as c')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
            ->leftJoin('detalle_compra as dc', 'dc.COM_Id', '=', 'c.COM_Id')
            ->where('c.COM_Status', 1)
            ->whereBetween('c.created_at', [$inicio, $fin])
            ->groupBy('c.COM_Id', 'c.COM_TipoDocumento', 'c.COM_NumDocumento', 'c.created_at', 'p.PROV_RazonSocial')
            ->orderBy('c.created_at')
            ->select(
                'c.COM_Id',
                'c.COM_TipoDocumento as tipo_doc',
                'c.COM_NumDocumento as num_documento',
                'c.created_at as fecha_raw',
                'p.PROV_RazonSocial as proveedor',
                DB::raw('SUM(dc.DCOM_Cantidad * dc.DCOM_PrecioCompra) as total')
            )
            ->get()
            ->map(function ($c) {
                $total = round((float) $c->total, 2);
                $tieneCreditoFiscal = mb_strtoupper((string) $c->tipo_doc) === 'FACTURA';
                $baseImponible = $tieneCreditoFiscal ? round($total / (1 + self::IGV), 2) : $total;
                $igv = $tieneCreditoFiscal ? round($total - $baseImponible, 2) : 0.0;

                return (object) [
                    'fecha' => \Illuminate\Support\Carbon::parse($c->fecha_raw)->format('d/m/Y'),
                    'tipo_doc' => $c->tipo_doc,
                    'num_documento' => $c->num_documento,
                    'proveedor' => $c->proveedor,
                    'base_imponible' => $baseImponible,
                    'igv' => $igv,
                    'total' => $total,
                ];
            });
    }

    /**
     * Registro de gastos operativos (alquiler, servicios, planilla, etc.),
     * con su tipo y metodo de pago -- no llevan IGV desglosado porque
     * gasto.GAS_Monto no distingue si el comprobante fue con o sin IGV.
     */
    public function gastos(Request $request)
    {
        [$inicio, $fin] = $this->resolverPeriodo($request);

        $filas = DB::table('gasto as g')
            ->join('tipo_gasto as tg', 'tg.TG_Id', '=', 'g.TG_Id')
            ->leftJoin('proveedor as p', 'p.PROV_Id', '=', 'g.PROV_Id')
            ->leftJoin('metodo_pago as mp', 'mp.MEP_Id', '=', 'g.MEP_Id')
            ->where('g.GAS_Status', 1)
            ->whereBetween('g.GAS_Fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->orderBy('g.GAS_Fecha')
            ->select(
                'g.GAS_Fecha as fecha',
                'g.GAS_Descripcion as descripcion',
                'g.GAS_Monto as monto',
                'tg.TG_Descripcion as tipo',
                'p.PROV_RazonSocial as proveedor',
                'mp.MEP_Pago as metodo_pago'
            )
            ->get()
            ->map(function ($g) {
                $g->monto = round((float) $g->monto, 2);
                $g->proveedor = $g->proveedor ?: '-';
                $g->metodo_pago = $g->metodo_pago ?: '-';

                return $g;
            });

        if ($request->get('export') === 'xlsx') {
            return $this->exportarExcel(
                'gastos_' . $inicio->toDateString() . '_' . $fin->toDateString() . '.xlsx',
                ['Fecha', 'Tipo', 'Descripción', 'Proveedor', 'Método de Pago', 'Monto'],
                $filas->map(fn ($f) => [
                    $f->fecha, $f->tipo, $f->descripcion, $f->proveedor, $f->metodo_pago, $f->monto,
                ])
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
                'filas' => $filas,
                'totales' => ['monto' => round($filas->sum('monto'), 2)],
            ]);
        }

        return view('tenant_tallermoto.contabilidad.gastos');
    }

    /**
     * Resumen para conciliar caja: sesiones cerradas en el periodo (con su
     * diferencia de cuadre) y el saldo pendiente de cuentas por cobrar a
     * clientes (ventas al credito).
     */
    public function resumenCaja(Request $request)
    {
        [$inicio, $fin] = $this->resolverPeriodo($request);

        $sesiones = DB::table('caja_sesion as cs')
            ->join('caja as c', 'c.CAJ_Id', '=', 'cs.CAJ_Id')
            ->leftJoin('users as u', 'u.id', '=', 'cs.USU_Id_Cierre')
            ->where('cs.CS_Estado', 'cerrada')
            ->whereBetween('cs.CS_FechaCierre', [$inicio, $fin])
            ->orderBy('cs.CS_FechaCierre')
            ->select(
                'c.CAJ_Nombre as caja',
                'cs.CS_FechaApertura as apertura',
                'cs.CS_FechaCierre as cierre',
                'cs.CS_MontoApertura as monto_apertura',
                'cs.CS_MontoEsperado as monto_esperado',
                'cs.CS_MontoReal as monto_real',
                'cs.CS_Diferencia as diferencia',
                'u.name as cerrado_por'
            )
            ->get()
            ->map(function ($s) {
                foreach (['monto_apertura', 'monto_esperado', 'monto_real', 'diferencia'] as $campo) {
                    $s->$campo = round((float) $s->$campo, 2);
                }
                $s->apertura = \Illuminate\Support\Carbon::parse($s->apertura)->format('d/m/Y H:i');
                $s->cierre = \Illuminate\Support\Carbon::parse($s->cierre)->format('d/m/Y H:i');
                $s->cerrado_por = $s->cerrado_por ?: '-';

                return $s;
            });

        $cuentasPorCobrar = DB::table('cuenta_por_cobrar as cpc')
            ->join('venta as v', 'v.VEN_Id', '=', 'cpc.VEN_Id')
            ->leftJoin('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->where('cpc.CPC_Estado', 'PENDIENTE')
            ->orderBy('cpc.CPC_FechaVencimiento')
            ->select(
                'cpc.CPC_Id',
                'c.CLI_Nombre as cliente',
                'cpc.CPC_MontoTotal as monto_total',
                'cpc.CPC_MontoAbonado as monto_abonado',
                'cpc.CPC_MontoFaltante as monto_faltante',
                'cpc.CPC_FechaVencimiento as vencimiento'
            )
            ->get()
            ->map(function ($c) {
                foreach (['monto_total', 'monto_abonado', 'monto_faltante'] as $campo) {
                    $c->$campo = round((float) $c->$campo, 2);
                }
                $c->cliente = $c->cliente ?: 'Cliente Genérico';
                $c->vencimiento = \Illuminate\Support\Carbon::parse($c->vencimiento)->format('d/m/Y');

                return $c;
            });

        if ($request->get('export') === 'xlsx') {
            return $this->exportarExcelMultihoja(
                'resumen_caja_' . $inicio->toDateString() . '_' . $fin->toDateString() . '.xlsx',
                [
                    'Sesiones de Caja' => [
                        ['Caja', 'Apertura', 'Cierre', 'Monto Apertura', 'Monto Esperado', 'Monto Real', 'Diferencia', 'Cerrado por'],
                        ...$sesiones->map(fn ($s) => [
                            $s->caja, $s->apertura, $s->cierre, $s->monto_apertura, $s->monto_esperado, $s->monto_real, $s->diferencia, $s->cerrado_por,
                        ])->all(),
                    ],
                    'Cuentas por Cobrar' => [
                        ['Cliente', 'Monto Total', 'Abonado', 'Pendiente', 'Vencimiento'],
                        ...$cuentasPorCobrar->map(fn ($c) => [
                            $c->cliente, $c->monto_total, $c->monto_abonado, $c->monto_faltante, $c->vencimiento,
                        ])->all(),
                    ],
                ]
            );
        }

        if ($request->ajax()) {
            return response()->json([
                'periodo' => ['inicio' => $inicio->toDateString(), 'fin' => $fin->toDateString()],
                'sesiones' => $sesiones,
                'cuentasPorCobrar' => $cuentasPorCobrar,
                'totales' => [
                    'diferencia' => round($sesiones->sum('diferencia'), 2),
                    'pendiente_cobrar' => round($cuentasPorCobrar->sum('monto_faltante'), 2),
                ],
            ]);
        }

        return view('tenant_tallermoto.contabilidad.resumen-caja');
    }

    /**
     * Excel de una sola hoja: $filas es una Collection de arrays ya en el
     * orden de $encabezados.
     */
    private function exportarExcel(string $nombreArchivo, array $encabezados, $filas)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray($encabezados, null, 'A1');
        $ultimaColumna = Coordinate::stringFromColumnIndex(count($encabezados));
        $sheet->getStyle("A1:{$ultimaColumna}1")->getFont()->setBold(true);
        foreach (range('A', $ultimaColumna) as $col) {
            $sheet->getColumnDimension($col)->setWidth(18);
        }

        $sheet->fromArray($filas->all(), null, 'A2');

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * Excel con una hoja por cada entrada de $hojas: nombre => filas (la
     * primera fila de cada una ya es su encabezado).
     */
    private function exportarExcelMultihoja(string $nombreArchivo, array $hojas)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->removeSheetByIndex(0);

        foreach ($hojas as $nombreHoja => $filas) {
            $sheet = $spreadsheet->createSheet();
            $sheet->setTitle($nombreHoja);
            $sheet->fromArray($filas, null, 'A1');
            $columnas = count($filas[0] ?? []);
            if ($columnas > 0) {
                $ultimaColumna = Coordinate::stringFromColumnIndex($columnas);
                $sheet->getStyle("A1:{$ultimaColumna}1")->getFont()->setBold(true);
                foreach (range('A', $ultimaColumna) as $col) {
                    $sheet->getColumnDimension($col)->setWidth(18);
                }
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $nombreArchivo, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
