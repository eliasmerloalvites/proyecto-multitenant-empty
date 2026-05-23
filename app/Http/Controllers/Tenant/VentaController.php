<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarVentaSunatJob;
use App\Models\Tenant\Venta;
use App\Models\Tenant\DetalleVenta;
use App\Models\Tenant\DocumentoVenta;
use App\Models\Tenant\Lote;
use App\Models\Tenant\Movimiento;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Spatie\Browsershot\Browsershot;
use App\Services\Facturacion\SunatService;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $sunatService;

    public function __construct(
        SunatService $sunatService
    ){
        $this->sunatService = $sunatService;
    }

    public function index(Request $request)
    {
        $mytime = Carbon::now('America/Lima');
        $fecha = $mytime->toDateString();

        if ($request->ajax()) {
            $data = DB::table('detalle_venta as dv')
                ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
                ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
                ->join('users as u', 'u.id', '=', 'v.USU_Id')
                ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
                ->select('dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'dov.DOV_Numero', 'dov.DOV_Serie', 'dov.DOV_Estado as estadoDocVenta', 'v.VEN_Id', 'mp.MEP_Pago', 'u.name as empleado', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_Id', 'a.ALM_NombreAlmacen', 'u.id as EMP_Codigo', 'v.VEN_TipoPago as tipopago', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'))
                ->groupBy('dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'dov.DOV_Numero', 'dov.DOV_Serie', 'dov.DOV_Estado', 'v.VEN_Id', 'mp.MEP_Pago', 'u.name', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_Id', 'a.ALM_NombreAlmacen', 'u.id', 'v.VEN_TipoPago', 'v.created_at')
                ->where(DB::raw('DATE(v.created_at)'), '>=', ($fecha))
                ->where(DB::raw('DATE(v.created_at)'), '<=', ($fecha))
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('importe', function ($row) {
                    $btn = 'S/ ' . number_format($row->total_venta - $row->total_descuento, 2);
                    return $btn;
                })
                ->addColumn('fecha', function ($row) {
                    $btn = $row->fechaVenta . " " . $row->fechaVentaT;
                    return $btn;
                })
                ->addColumn('documento', function ($row) {
                    $btn = $row->DOV_Serie . " - " . $row->DOV_Numero;
                    return $btn;
                })
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->VEN_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editVenta" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->VEN_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteVenta"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->VEN_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeVenta"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->addColumn('ticket', function ($row) {
                    $btn = '<a title="TICKET" target="_blank" href="/tenant/ventas/venta/' . $row->VEN_Id . '/ticket"  data-original-title="Ver" class="btn btn-primary btn-sm printVenta"><i class="fa fa-print" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->addColumn('pdf', function ($row) {
                    $btn = '<a title="PDF" target="_blank" href="/tenant/ventas/venta/' . $row->VEN_Id . '/pdf"  data-original-title="Ver" class="btn btn-primary btn-sm printVenta"><i class="fa fa-file-pdf" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->addColumn('whatsapp', function ($row) {
                    $btn = '<a title="WHATSAPP" target="_blank" href="/tenant/ventas/venta/' . $row->VEN_Id . '/ticket-imagen"  data-original-title="Ver" class="btn btn-success btn-sm envioWhatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i></a>';

                    return $btn;
                })

                ->rawColumns(['action1', 'action2', 'action3', 'ticket', 'pdf', 'whatsapp'])
                ->make(true);
        }

        return view('tenant_'.tenant('tipo_negocio').'.ventas.venta.index');
    }

    public function filtro(Request $request, $fecharange)
    {
        $fec    = explode(" - ", $fecharange);

        $fechaini = $fec[0];
        $fechafin = $fec[1];

        if ($request->ajax()) {
            $data = DB::table('detalle_venta as dv')
                ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
                ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
                ->join('users as u', 'u.id', '=', 'v.USU_Id')
                ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
                ->select('dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'dov.DOV_Numero', 'dov.DOV_Serie', 'dov.DOV_Estado as estadoDocVenta', 'v.VEN_Id', 'mp.MEP_Pago', 'u.name as empleado', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_Id', 'a.ALM_NombreAlmacen', 'u.id as EMP_Codigo', 'v.VEN_TipoPago as tipopago', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'))
                ->groupBy('dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'dov.DOV_Numero', 'dov.DOV_Serie', 'dov.DOV_Estado', 'v.VEN_Id', 'mp.MEP_Pago', 'u.name', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_Id', 'a.ALM_NombreAlmacen', 'u.id', 'v.VEN_TipoPago', 'v.created_at')
                ->where(DB::raw('DATE(v.created_at)'), '>=', ($fechaini))
                ->where(DB::raw('DATE(v.created_at)'), '<=', ($fechafin))
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('importe', function ($row) {
                    $btn = 'S/ ' . number_format($row->total_venta - $row->total_descuento, 2);
                    return $btn;
                })
                ->addColumn('fecha', function ($row) {
                    $btn = $row->fechaVenta . " " . $row->fechaVentaT;
                    return $btn;
                })
                ->addColumn('documento', function ($row) {
                    $btn = $row->DOV_Serie . " - " . $row->DOV_Numero;
                    return $btn;
                })
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->VEN_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editVenta" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->VEN_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteVenta"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->VEN_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeVenta"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->addColumn('ticket', function ($row) {
                    $btn = '<a title="TICKET" target="_blank" href="/tenant/ventas/venta/' . $row->VEN_Id . '/ticket"  data-original-title="Ver" class="btn btn-primary btn-sm printVenta"><i class="fa fa-print" aria-hidden="true"></i></a>';

                    return $btn;
                })

                ->rawColumns(['action1', 'action2', 'action3', 'ticket'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clase = DB::table('clase')->orderBy('CLA_Nombre', 'asc')->get();
        $categoria = DB::table('categoria')->orderBy('CAT_Nombre', 'asc')->get();
        $clientes = DB::table('cliente')->orderBy('CLI_NumDocumento', 'asc')->get();
        $metodo_pago = DB::table('metodo_pago')->orderBy('MEP_Pago', 'asc')->get();


        $lotesuni = DB::table('lote as lt')
            ->join('almacen as a', 'a.ALM_Id', '=', 'lt.ALM_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'lt.PRO_Id')
            ->join('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->join('clase as cl', 'cl.CLA_Id', '=', 'cat.CLA_Id')
            ->select('a.ALM_Id', 'p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Descripcion', 'p.PRO_Imagen', 'p.CAT_Id as CATEGORIA', 'cl.CLA_Nombre as Clase', 'cat.CAT_Nombre as Clase', DB::raw('sum(lt.LOT_CantidadReal) as PRO_Cantidad'), DB::raw('max(lt.LOT_PrecioVenta) as PRO_PrecioBaseVenta'), DB::raw('max(lt.LOT_PrecioCompra)'), 'a.ALM_Id', 'a.ALM_Nombre', DB::raw('SUM(lt.LOT_CantidadReal) as LOT_CantidadReal'))
            ->where('lt.LOT_CantidadReal', '>', 0)
            ->where('p.PRO_Status', '=', 1)
            ->groupBy('a.ALM_Id', 'p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Descripcion', 'p.PRO_Imagen', 'p.PRO_Marca', 'p.CAT_Id', 'cat.CAT_Nombre', 'cl.CLA_Nombre', 'a.ALM_Id', 'a.ALM_Nombre')
            ->get();

        return view('tenant_'.tenant('tipo_negocio').'.ventas.venta.create', compact('clase', 'categoria', 'clientes', 'metodo_pago', 'lotesuni'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        try {
            DB::beginTransaction();

            $mytime = Carbon::now('America/Lima');
            $fechaactual = $mytime->toDateString();
            $horaactual = $mytime->toTimeString();

            $idAlmacen = 1;
            $idUsuario = $request->get('USU_Id');
            $idCliente = $request->get('selectedIdCliente');

            $venta = new Venta;
            $venta->VEN_TipoPago = $request->get('VEN_TipoPago');
            $venta->VEN_Vuelto = $request->get('VEN_Vuelto');
            $venta->VEN_Pagado = $request->get('VEN_Pagado');
            $venta->MEP_Id = $request->get('selectedIdMetodoPago');
            $venta->USU_Id = $idUsuario;
            $venta->CLI_Id = $idCliente;
            $venta->ALM_Id = $idAlmacen;
            $venta->VEN_FechaEnvio = $fechaactual . ' ' . $horaactual;
            $venta->save();

            $VentaTipo = $request->get('VentaTipo');

            if ($VentaTipo == "VENTA") {
                // $folio = self::CrearDocumentoDetalle($CLI_Cod, $venta->VEN_Id, $idEmpleado, $idubicacion->UBI_Id, $idalmacen1, $CLI_Cod, 2, $documentodescuento);
            } else if ($VentaTipo == "LIBRE") {
                $DocumentoVenta = self::CrearDocumentoDetalleVentaLibre($venta->VEN_Id, $idAlmacen);
            }

            $PRO_Id = $request->get('PRO_Id');
            $DEV_Cantidad = $request->get('DEV_Cantidad');
            $DEV_PrecioUnitario = $request->get('DEV_PrecioUnitario');
            $DEV_Descuento = $request->get('DEV_Descuento');

            $cont = 0;
            $item = 0;
            while ($cont < count($PRO_Id)) {

                $rdst = self::ReducirStock($PRO_Id[$cont], $DEV_Cantidad[$cont], $idAlmacen);

                for ($i = 0; $i < count($rdst); $i = $i + 2) {
                    $detalle = new DetalleVenta();
                    $detalle->VEN_Id = $venta->VEN_Id;
                    $detalle->DEV_Item = $item + 1;
                    $detalle->PRO_Id = $PRO_Id[$cont];
                    $detalle->DEV_Cantidad = $rdst[$i + 1];
                    $detalle->DEV_PrecioUnitario = $DEV_PrecioUnitario[$cont];
                    $detalle->LOT_Id = $rdst[$i];
                    $detalle->DEV_Descuento = (($DEV_PrecioUnitario[$cont] * $rdst[$i + 1]) / ($DEV_PrecioUnitario[$cont] * $DEV_Cantidad[$cont]) * ($DEV_Descuento[$cont]));
                    $detalle->save();
                    $item = $item + 1;
                }

                $cont = $cont + 1;
            }

            $movi = new Movimiento();
            $movi->tipo = "Salida";
            $movi->idcv = $venta->VEN_Id;
            $movi->save();

            $ventagenerado = DB::table('documento_venta as dov')
                ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
                ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
                ->join('users as u', 'u.id', '=', 'v.USU_Id')
                ->select('dov.*', 'v.*', 'mp.*', 'a.*', 'u.*')
                ->where('v.VEN_Id', '=', $venta->VEN_Id)
                ->orderby('dov.DOV_Id', 'desc')
                ->first();

            DB::commit();
            $this->sunatService->enviarVenta( $venta->VEN_Id );
            //EnviarVentaSunatJob::dispatch($venta->VEN_Id,tenant('id'),tenant('tipo_negocio'));
            /* $responseSunat = $this->sunatService->enviarVenta($venta->VEN_Id);
            self::ticketImagen($venta->VEN_Id); */

        } catch (Exception $e) {
            DB::rollback();
            $e->getMessage();
            return response()->json(['error' => $e->getMessage(),]);
        }

        return response()->json(['success' => 'Venta Registrado Exitosamente!', compact('ventagenerado')]);
    }

    function ticket(string $tenant_id, string $idventa)
    {
        $ventae = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $idventa)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        $datosalmacen = DB::table('almacen')
            ->where('ALM_Id', '=', $ventae->ALM_Id)
            ->first();

        $Subtotal = 0.00;
        $igv = 0.00;
        $UbiDoc = "";
        $numDocu = 0;
        $codi = "";
        if ($ventae) {
            $Subtotal = ($ventae->total_venta) / 1.18;
            $igv = $ventae->total_venta - $Subtotal;
            $igv = round($igv, 2);
            $Subtotal = round($Subtotal, 2);

            $codi = $ventae->fechaVenta . "| " . $datosalmacen->ALM_Ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
            $Total = round($ventae->total_venta - $ventae->total_descuento, 2);

            $UbiDoc = $ventae->serDoc;
            $numDocu = $ventae->numDoc;
        }

        $detallese = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Nombre as articulo', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $idventa)
            ->groupBy('p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        $calificarventa = DB::table('venta as v')
            ->select('v.VEN_TipoPago')
            ->where('v.VEN_Id', '=', $idventa)
            ->first();


        if ($calificarventa->VEN_TipoPago == 2) {
            $datosdecuenta = DB::table('cuentas_por_cobrar as cc')
                ->select('cc.CPC_Frecuencia', DB::raw('date(cc.CPC_FechaEmision) AS FECHAEMISION'), 'cc.CPC_MontoAbonado', 'cc.CPC_MontoFaltante', 'cc.CPC_FechaVencimiento', 'cc.CPC_Descripcion')
                ->where('cc.VEN_Id', '=', $idventa)
                ->first();
            //$descripcion = 
        } else {
            $datosdecuenta = 0;
        }
        $NumDoc = self::IndiceNumeroDocumentoVenta($numDocu);
        $Total = round($ventae->total_venta - $ventae->total_descuento, 2);
        $x = str_replace(',', '.', $Total);
        $LetrasTotal = self::numletras($x);

        $generaimagen = false;

        return view('tenant_'.tenant('tipo_negocio').'/ventas/venta/ticket/ticketventa9cm', compact('ventae', 'detallese', 'Subtotal', 'igv', 'codi', 'UbiDoc', 'NumDoc', 'datosalmacen', 'calificarventa', 'datosdecuenta', 'LetrasTotal','generaimagen'));
    }

    function pdf(string $tenant_id, string $idventa)
    {
        $ventae = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $idventa)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        $datosalmacen = DB::table('almacen')
            ->where('ALM_Id', '=', $ventae->ALM_Id)
            ->first();

        $Subtotal = 0.00;
        $igv = 0.00;
        $UbiDoc = "";
        $numDocu = 0;
        $codi = "";
        if ($ventae) {
            $Subtotal = ($ventae->total_venta) / 1.18;
            $igv = $ventae->total_venta - $Subtotal;
            $igv = round($igv, 2);
            $Subtotal = round($Subtotal, 2);

            $codi = $ventae->fechaVenta . "| " . $datosalmacen->ALM_Ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
            $Total = round($ventae->total_venta - $ventae->total_descuento, 2);

            $UbiDoc = $ventae->serDoc;
            $numDocu = $ventae->numDoc;
        }

        $detallese = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Nombre as articulo', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $idventa)
            ->groupBy('p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        $calificarventa = DB::table('venta as v')
            ->select('v.VEN_TipoPago')
            ->where('v.VEN_Id', '=', $idventa)
            ->first();


        if ($calificarventa->VEN_TipoPago == 2) {
            $datosdecuenta = DB::table('cuentas_por_cobrar as cc')
                ->select('cc.CPC_Frecuencia', DB::raw('date(cc.CPC_FechaEmision) AS FECHAEMISION'), 'cc.CPC_MontoAbonado', 'cc.CPC_MontoFaltante', 'cc.CPC_FechaVencimiento', 'cc.CPC_Descripcion')
                ->where('cc.VEN_Id', '=', $idventa)
                ->first();
            //$descripcion = 
        } else {
            $datosdecuenta = 0;
        }
        $NumDoc = self::IndiceNumeroDocumentoVenta($numDocu);
        $Total = round($ventae->total_venta - $ventae->total_descuento, 2);
        $x = str_replace(',', '.', $Total);
        $LetrasTotal = self::numletras($x);

        $generaimagen = false;

        return view('tenant_'.tenant('tipo_negocio').'/ventas/venta/ticket/ticket_A4', compact('ventae', 'detallese', 'Subtotal', 'igv', 'codi', 'UbiDoc', 'NumDoc', 'datosalmacen', 'calificarventa', 'datosdecuenta', 'LetrasTotal','generaimagen'));
    }

    public static function ticketImagen($idventa)
    {
        $ventae = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $idventa)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        $datosalmacen = DB::table('almacen')
            ->where('ALM_Id', '=', $ventae->ALM_Id)
            ->first();

        $Subtotal = 0.00;
        $igv = 0.00;
        $UbiDoc = "";
        $numDocu = 0;
        $codi = "";
        if ($ventae) {
            $Subtotal = ($ventae->total_venta) / 1.18;
            $igv = $ventae->total_venta - $Subtotal;
            $igv = round($igv, 2);
            $Subtotal = round($Subtotal, 2);

            $codi = $ventae->fechaVenta . "| " . $datosalmacen->ALM_Ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
            $Total = round($ventae->total_venta - $ventae->total_descuento, 2);

            $UbiDoc = $ventae->serDoc;
            $numDocu = $ventae->numDoc;
        }

        $detallese = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Nombre as articulo', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $idventa)
            ->groupBy('p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        $calificarventa = DB::table('venta as v')
            ->select('v.VEN_TipoPago')
            ->where('v.VEN_Id', '=', $idventa)
            ->first();


        if ($calificarventa->VEN_TipoPago == 2) {
            $datosdecuenta = DB::table('cuentas_por_cobrar as cc')
                ->select('cc.CPC_Frecuencia', DB::raw('date(cc.CPC_FechaEmision) AS FECHAEMISION'), 'cc.CPC_MontoAbonado', 'cc.CPC_MontoFaltante', 'cc.CPC_FechaVencimiento', 'cc.CPC_Descripcion')
                ->where('cc.VEN_Id', '=', $idventa)
                ->first();
            //$descripcion = 
        } else {
            $datosdecuenta = 0;
        }
        $NumDoc = self::IndiceNumeroDocumentoVenta($numDocu);
        $Total = round($ventae->total_venta - $ventae->total_descuento, 2);
        $x = str_replace(',', '.', $Total);
        $LetrasTotal = self::numletras($x);

        $generaimagen = true;

        $html = view(
            'tenant_generico/ventas/venta/ticket/ticket_A4',
            compact(
                'ventae',
                'detallese',
                'Subtotal',
                'igv',
                'codi',
                'UbiDoc',
                'NumDoc',
                'datosalmacen',
                'calificarventa',
                'datosdecuenta',
                'LetrasTotal',
                'generaimagen'
            )
        )->render();

        $ubicacionNegocio = "";
        $id = null;
        if (tenant()) {
            // Estás en el contexto de un TENANT
            $id = tenant('id');
            $ubicacionNegocio = tenant('tipo_negocio');
        }

        $path = public_path('storage/' .$ubicacionNegocio . '/' .$id . '/archivos/tickets/');
        if (!file_exists($path)) {

            mkdir($path, 0777, true);
        }
        $fileName = $ventae->pdf .'.png';
        $rutaCompleta = $path . $fileName;

        Browsershot::html($html)
        ->timeout(120)
        ->windowSize(900, 1200)
        ->save($rutaCompleta);

        /* Browsershot::html($html)
    ->format('A4')
    ->pdf(); */

    }




    /**
     * Display the specified resource.
     */
    public function show(string $tenant_id, string $id)
    {
        $venta = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
            ->select('dov.DOV_Id as codigoDoc', 'dov.DOV_Nombre as nombre', 'dov.DOV_Pdf as pdf', 'v.VEN_Id as codigoVenta', 'mp.MEP_Pago', 'dov.DOV_Tipo as tipoDoc', 'dov.DOV_Numero as numDoc', 'dov.DOV_Serie as serDoc', 'c.CLI_Nombre as cliente', 'c.CLI_Celular as celular', 'c.CLI_NumDocumento as clienteNumero', 'c.CLI_TipoDocumento as tipoDocumento', 'c.CLI_Direccion as clienteDireccion', 'a.ALM_Id', 'u.id as EMP_Codigo', 'u.name as empleado', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), 'dov.DOV_Estado as estadoDocVenta', DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'), 'v.VEN_TipoPago as tipopago', 'a.ALM_Id as ubica')
            ->where('v.VEN_Id', '=', $id)
            ->groupBy('dov.DOV_Id', 'dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'mp.MEP_Pago', 'u.name', 'c.CLI_Nombre', 'v.VEN_Id', 'dov.DOV_Numero', 'dov.DOV_Estado', 'v.created_at', 'v.VEN_TipoPago', 'c.CLI_Celular', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'c.CLI_Direccion', 'a.ALM_Id', 'u.id', 'dov.DOV_Serie')
            ->distinct()
            ->first();

        $detalle = DB::table('detalle_venta as d')
            ->join('producto as p', 'p.PRO_Id', '=', 'd.PRO_Id')
            ->join('categoria as c', 'c.CAT_Id', '=', 'p.CAT_Id')
            ->select('p.PRO_Id', 'p.PRO_Nombre', 'c.CAT_Nombre as categoria', DB::raw('SUM(d.DEV_Cantidad ) as cantidad'), DB::raw('(d.DEV_PrecioUnitario ) as precio_venta'), DB::raw('(d.DEV_Descuento ) as descuento'), DB::raw('CAST(SUM(d.DEV_Cantidad*(d.DEV_PrecioUnitario)) as decimal(10,2)) as subtotal'))
            ->where('d.VEN_Id', '=', $id)
            ->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'c.CAT_Nombre', 'd.DEV_PrecioUnitario', 'd.DEV_Descuento')
            ->get();

        return response()->json(['venta' => $venta, 'detalle' => $detalle]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $tenant_id, string $id)
    {
        $Venta = Venta::find($id);
        return response()->json(['data' => $Venta]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $tenant_id, string $id)
    {
        $Venta = Venta::find($id);
        $Venta->PRO_Nombre = $request->PRO_Nombre;
        $Venta->PRO_Descripcion = $request->PRO_Descripcion;
        $Venta->PRO_PrecioCompra = $request->PRO_PrecioCompra;
        $Venta->PRO_PrecioVenta = $request->PRO_PrecioVenta;
        $Venta->PRO_Marca = $request->PRO_Marca;
        $Venta->PRO_Status = $request->PRO_Status ?? 1;
        $Venta->CAT_Id = $request->CAT_Id;
        $Venta->update();

        return response()->json(['success' => 'Venta Editado Exitosamente.', compact('Venta')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $tenant_id, string $id)
    {
        $Venta = Venta::find($id);
        $Venta->delete();
        return response()->json(['success' => 'Venta Eliminado Exitosamente.']);
    }



    public static function ReducirStock($pro, $can, $alm)
    {
        $lot = DB::table('lote')
            ->select('LOT_Id', 'PRO_Id', 'LOT_CantidadReal')
            ->where('PRO_Id', '=', $pro)
            ->where('ALM_Id', '=', $alm)
            ->orderBy('created_at', 'asc')
            ->get();

        $canre = $can;
        $conte = 0;
        $pasar = 0;
        $lotid = [];
        $sumlot = 0;
        $idfin;
        $conta = 0;
        foreach ($lot as $lo) {
            $sumlot = $sumlot + $lo->LOT_CantidadReal;
            $conta++;
            if ($lo->LOT_CantidadReal > 0) {
                $lotid[$conte] = $lo->LOT_Id;

                $lotes = Lote::findOrFail($lo->LOT_Id);
                $stock =  $lotes->LOT_CantidadReal - $can;

                if ($stock < 0) {
                    $can = - ($stock);
                    $stock = 0;
                    $pasar = 1;
                    $lotid[$conte + 1] = $lotes->LOT_CantidadReal;
                } else {
                    $pasar = 0;
                    $lotid[$conte + 1] = $can;
                    $can = 0;
                }

                $lotes->LOT_CantidadReal = $stock;
                $lotes->update();
                $conte = $conte + 2;
                if ($pasar == 0) {
                    return $lotid;
                }
            }
            $idfin = $lo->LOT_Id;
        }

        if ($sumlot == 0) {
            $lotid[0] = $idfin;
            $lotid[1] = $canre;
            return $lotid;
        }
    }

    public static  function CrearDocumentoDetalleVentaLibre($idventa, $idalmacen)
    {
        $correlativoPRO = 1;
        $numPro = self::creaFolioPro($idalmacen, $correlativoPRO);
        $serPro = self::IndiceDocumentVentaP($idalmacen);

        $ultimoCNumero = DB::table('documento_venta as dov')
            ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
            ->select('dov.DOV_Id', 'dov.DOV_Numero')
            ->where('dov.DOV_Tipo', "=", 'PRO')
            ->where('v.ALM_Id', '=', $idalmacen)
            ->orderby('dov.DOV_Id', 'desc')
            ->first();


        if ($ultimoCNumero != null) {
            if ((int)$ultimoCNumero->DOV_Numero == $numPro) {
                throw new Exception('Numero de Proforma ya existe');
            }
        }

        $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $i = false;
        do {
            $cod_rndon = self::generate_string($permitted_chars, 20);
            $afiCodigo = DocumentoVenta::where('DOV_Pdf', $cod_rndon)->first();
            if ($afiCodigo) {
                $i = false;
            } else {
                $i = true;
            }
        } while (!$i);

        $documento = new DocumentoVenta;
        $documento->DOV_Tipo = 'PRO';
        $documento->DOV_TipoOriginal = 'PRO';
        $documento->DOV_Serie = $serPro;
        $documento->DOV_Numero = $numPro;
        $documento->DOV_Nombre = $serPro.'-'.$numPro;
        $documento->VEN_Id = $idventa;
        $documento->DOV_Estado = 'ACTIVADO';
        $documento->DOV_StateToRes = 0;
        $documento->DOV_Pdf = $cod_rndon;
        $documento->DOV_Vista = 1;
        $documento->save();

        return $documento;
    }

    public static function creaFolioPro($idalmacen, $correlativo)
    {
        $id = 0;
        $corre = $correlativo;

        $idPro = DB::table('documento_venta as dov')
            ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
            ->select(DB::raw('count(dov.DOV_Numero)'))
            ->where('dov.DOV_TipoOriginal', "=", 'PRO')
            ->where('v.ALM_Id', '=', $idalmacen)
            ->first();

        if ($idPro != '') {
            $con = 0;
            foreach ($idPro as $idpr) {
                if ($con == 0) {
                    $id = $idpr;
                }
                $con++;
            }
        }

        if ($id == "" or $id == null or $id == 0) {
            return $corre;
        } else {
            return $corre + $id;
        }
    }

    public static function IndiceDocumentVentaP($Num)
    {

        $newNum = '';
        if (($Num / 100) > 1) {
            return 'P' . $Num;
        } elseif (($Num / 10) > 1) {
            $newNum = 'P0' . $Num;
            return $newNum;
        } else {
            $newNum = 'P00' . $Num;
            return $newNum;
        }
    }

    public static function generate_string($input, $strength = 20)
    {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 0; $i < $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    public static function IndiceNumeroDocumentoVenta($Num)
    {

        $newNum = '';

        if (($Num / 10000000) > 1) {

            return '' . $Num;
        } elseif (($Num / 1000000) > 1) {

            $newNum = '0' . $Num;

            return $newNum;
        } elseif (($Num / 100000) > 1) {

            $newNum = '00' . $Num;

            return $newNum;
        } elseif (($Num / 10000) > 1) {

            $newNum = '000' . $Num;

            return $newNum;
        } elseif (($Num / 1000) > 1) {

            $newNum = '0000' . $Num;

            return $newNum;
        } elseif (($Num / 100) > 1) {

            $newNum = '00000' . $Num;

            return $newNum;
        } elseif (($Num / 10) > 1) {

            $newNum = '000000' . $Num;

            return $newNum;
        } else {

            $newNum = '0000000' . $Num;

            return $newNum;
        }
    }

    public static function numletras($numero)
    {

        $tempnum = explode('.', $numero);



        if ($tempnum[0] !== "") {

            $numf = self::milmillon($tempnum[0]);

            if ($numf == "UNO") {

                $numf = substr($numf, 0, -1);
            }



            $TextEnd = $numf . ' CON ';

            //$TextEnd .= $_nommoneda.' CON '; 

        }

        if ($tempnum[0] == "" || $tempnum[0] >= 100) {

            $tempnum[0] = "0";
        }

        if (empty($tempnum[1])) //empty: Determina si una variable es considerada vacía. Una variable se considera vacía si no existe o si su valor es igual a FALSE. empty() no genera una advertencia si la variable no existe.

        {

            $TextEnd .= "00/100 SOLES";
        } else {

            $TextEnd .= $tempnum[1];

            $TextEnd .= "/100 SOLES";
        }



        return $TextEnd;
    }

    public static function unidad($numuero)
    {

        switch ($numuero) {

            case 9: {

                    $numu = "NUEVE";

                    break;
                }

            case 8: {

                    $numu = "OCHO";

                    break;
                }

            case 7: {

                    $numu = "SIETE";

                    break;
                }

            case 6: {

                    $numu = "SEIS";

                    break;
                }

            case 5: {

                    $numu = "CINCO";

                    break;
                }

            case 4: {

                    $numu = "CUATRO";

                    break;
                }

            case 3: {

                    $numu = "TRES";

                    break;
                }

            case 2: {

                    $numu = "DOS";

                    break;
                }

            case 1: {

                    $numu = "UNO";

                    break;
                }

            case 0: {

                    $numu = "";

                    break;
                }
        }

        return $numu;
    }



    public static function decena($numdero)
    {



        if ($numdero >= 90 && $numdero <= 99) {

            $numd = "NOVENTA ";

            if ($numdero > 90)

                $numd = $numd . "Y " . (self::unidad($numdero - 90));
        } else if ($numdero >= 80 && $numdero <= 89) {

            $numd = "OCHENTA ";

            if ($numdero > 80)

                $numd = $numd . "Y " . (self::unidad($numdero - 80));
        } else if ($numdero >= 70 && $numdero <= 79) {

            $numd = "SETENTA ";

            if ($numdero > 70)

                $numd = $numd . "Y " . (self::unidad($numdero - 70));
        } else if ($numdero >= 60 && $numdero <= 69) {

            $numd = "SESENTA ";

            if ($numdero > 60)

                $numd = $numd . "Y " . (self::unidad($numdero - 60));
        } else if ($numdero >= 50 && $numdero <= 59) {

            $numd = "CINCUENTA ";

            if ($numdero > 50)

                $numd = $numd . "Y " . (self::unidad($numdero - 50));
        } else if ($numdero >= 40 && $numdero <= 49) {

            $numd = "CUARENTA ";

            if ($numdero > 40)

                $numd = $numd . "Y " . (self::unidad($numdero - 40));
        } else if ($numdero >= 30 && $numdero <= 39) {

            $numd = "TREINTA ";

            if ($numdero > 30)

                $numd = $numd . "Y " . (self::unidad($numdero - 30));
        } else if ($numdero >= 20 && $numdero <= 29) {

            if ($numdero == 20)

                $numd = "VEINTE ";

            else

                $numd = "VEINTI" . (self::unidad($numdero - 20));
        } else if ($numdero >= 10 && $numdero <= 19) {

            switch ($numdero) {

                case 10: {

                        $numd = "DIEZ ";

                        break;
                    }

                case 11: {

                        $numd = "ONCE ";

                        break;
                    }

                case 12: {

                        $numd = "DOCE ";

                        break;
                    }

                case 13: {

                        $numd = "TRECE ";

                        break;
                    }

                case 14: {

                        $numd = "CATORCE ";

                        break;
                    }

                case 15: {

                        $numd = "QUINCE ";

                        break;
                    }

                case 16: {

                        $numd = "DIECISEIS ";

                        break;
                    }

                case 17: {

                        $numd = "DIECISIETE ";

                        break;
                    }

                case 18: {

                        $numd = "DIECIOCHO ";

                        break;
                    }

                case 19: {

                        $numd = "DIECINUEVE ";

                        break;
                    }
            }
        } else

            $numd = self::unidad($numdero);

        return $numd;
    }



    public static function centena($numc)
    {

        if ($numc >= 100) {

            if ($numc >= 900 && $numc <= 999) {

                $numce = "NOVECIENTOS ";

                if ($numc > 900)

                    $numce = $numce . (self::decena($numc - 900));
            } else if ($numc >= 800 && $numc <= 899) {

                $numce = "OCHOCIENTOS ";

                if ($numc > 800)

                    $numce = $numce . (self::decena($numc - 800));
            } else if ($numc >= 700 && $numc <= 799) {

                $numce = "SETECIENTOS ";

                if ($numc > 700)

                    $numce = $numce . (self::decena($numc - 700));
            } else if ($numc >= 600 && $numc <= 699) {

                $numce = "SEISCIENTOS ";

                if ($numc > 600)

                    $numce = $numce . (self::decena($numc - 600));
            } else if ($numc >= 500 && $numc <= 599) {

                $numce = "QUINIENTOS ";

                if ($numc > 500)

                    $numce = $numce . (self::decena($numc - 500));
            } else if ($numc >= 400 && $numc <= 499) {

                $numce = "CUATROCIENTOS ";

                if ($numc > 400)

                    $numce = $numce . (self::decena($numc - 400));
            } else if ($numc >= 300 && $numc <= 399) {

                $numce = "TRESCIENTOS ";

                if ($numc > 300)

                    $numce = $numce . (self::decena($numc - 300));
            } else if ($numc >= 200 && $numc <= 299) {

                $numce = "DOSCIENTOS ";

                if ($numc > 200)

                    $numce = $numce . (self::decena($numc - 200));
            } else if ($numc >= 100 && $numc <= 199) {

                if ($numc == 100)

                    $numce = "CIEN ";

                else

                    $numce = "CIENTO " . (self::decena($numc - 100));
            }
        } else

            $numce = self::decena($numc);



        return $numce;
    }



    public static function miles($nummero)
    {

        if ($nummero >= 1000 && $nummero < 2000) {

            $numm = "MIL " . (self::centena($nummero % 1000));
        }

        if ($nummero >= 2000 && $nummero < 10000) {

            $numm = self::unidad(Floor($nummero / 1000)) . " MIL " . (self::centena($nummero % 1000));
        }

        if ($nummero < 1000)

            $numm = self::centena($nummero);



        return $numm;
    }



    public static function decmiles($numdmero)
    {

        if ($numdmero == 10000)

            $numde = "DIEZ MIL";

        if ($numdmero > 10000 && $numdmero < 20000) {

            $numde = self::decena(Floor($numdmero / 1000)) . "MIL " . (self::centena($numdmero % 1000));
        }

        if ($numdmero >= 20000 && $numdmero < 100000) {

            $numde = self::decena(Floor($numdmero / 1000)) . " MIL " . (self::miles($numdmero % 1000));
        }

        if ($numdmero < 10000)

            $numde = self::miles($numdmero);



        return $numde;
    }



    public static function cienmiles($numcmero)
    {

        if ($numcmero == 100000)

            $num_letracm = "CIEN MIL";

        if ($numcmero >= 100000 && $numcmero < 1000000) {

            $num_letracm = self::centena(Floor($numcmero / 1000)) . " MIL " . (self::centena($numcmero % 1000));
        }

        if ($numcmero < 100000)

            $num_letracm = self::decmiles($numcmero);

        return $num_letracm;
    }



    public static function millon($nummiero)
    {

        if ($nummiero >= 1000000 && $nummiero < 2000000) {

            $num_letramm = "UN MILLON " . (self::cienmiles($nummiero % 1000000));
        }

        if ($nummiero >= 2000000 && $nummiero < 10000000) {

            $num_letramm = self::unidad(Floor($nummiero / 1000000)) . " MILLONES " . (self::cienmiles($nummiero % 1000000));
        }

        if ($nummiero < 1000000)

            $num_letramm = self::cienmiles($nummiero);



        return $num_letramm;
    }



    public static function decmillon($numerodm)
    {

        if ($numerodm == 10000000)

            $num_letradmm = "DIEZ MILLONES";

        if ($numerodm > 10000000 && $numerodm < 20000000) {

            $num_letradmm = self::decena(Floor($numerodm / 1000000)) . "MILLONES " . (self::cienmiles($numerodm % 1000000));
        }

        if ($numerodm >= 20000000 && $numerodm < 100000000) {

            $num_letradmm = self::decena(Floor($numerodm / 1000000)) . " MILLONES " . (self::millon($numerodm % 1000000));
        }

        if ($numerodm < 10000000)

            $num_letradmm = self::millon($numerodm);



        return $num_letradmm;
    }



    public static function cienmillon($numcmeros)
    {

        if ($numcmeros == 100000000)

            $num_letracms = "CIEN MILLONES";

        if ($numcmeros >= 100000000 && $numcmeros < 1000000000) {

            $num_letracms = self::centena(Floor($numcmeros / 1000000)) . " MILLONES " . (self::millon($numcmeros % 1000000));
        }

        if ($numcmeros < 100000000)

            $num_letracms = self::decmillon($numcmeros);

        return $num_letracms;
    }



    public static function milmillon($nummierod)
    {

        if ($nummierod >= 1000000000 && $nummierod < 2000000000) {

            $num_letrammd = "MIL " . (self::cienmillon($nummierod % 1000000000));
        }

        if ($nummierod >= 2000000000 && $nummierod < 10000000000) {

            $num_letrammd = self::unidad(Floor($nummierod / 1000000000)) . " MIL " . (self::cienmillon($nummierod % 1000000000));
        }

        if ($nummierod < 1000000000)

            $num_letrammd = self::cienmillon($nummierod);



        return $num_letrammd;
    }
}
