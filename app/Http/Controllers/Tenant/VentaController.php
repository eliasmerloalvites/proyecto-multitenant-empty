<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarVentaSunatJob;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\Cliente;
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
use App\Models\Tenant\EmpresaFacturacion;
use App\Services\Facturacion\DocumentoVentaService;
use App\Services\Facturacion\SunatService;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    protected $sunatService;
    protected $documentoVentaService;

    public function __construct(
        SunatService $sunatService,
        DocumentoVentaService $documentoVentaService
    ) {
        $this->sunatService = $sunatService;
        $this->documentoVentaService = $documentoVentaService;
    }

    /**
     * Datos de facturacion del tenant actual.
     */
    private function empresaFacturacion(): EmpresaFacturacion
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        if (!$empresa) {
            throw new Exception(
                'Esta empresa todavia no tiene configurados sus datos de facturacion electronica.'
            );
        }

        return $empresa;
    }

    /**
     * Arma la consulta de ventas aplicando todos los filtros del listado:
     * rango de fechas, estado SUNAT, tipo de comprobante, anulado/baja,
     * almacen, metodo de pago y cliente (nombre o numero de documento).
     */
    private function consultaVentas(Request $request)
    {
        $query = DB::table('detalle_venta as dv')
            ->join('venta as v', 'v.VEN_Id', '=', 'dv.VEN_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'v.MEP_Id')
            ->join('users as u', 'u.id', '=', 'v.USU_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->select('dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'dov.DOV_Numero', 'dov.DOV_Serie', 'dov.DOV_Estado as estadoDocVenta', 'dov.DOV_Anulado', 'dov.DOV_EstadoBaja', 'v.VEN_Id', 'mp.MEP_Id', 'mp.MEP_Pago', 'u.name as empleado', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_Id', 'a.ALM_NombreAlmacen', 'u.id as EMP_Codigo', 'v.VEN_TipoPago as tipopago', DB::raw('CAST(sum((dv.DEV_Cantidad*dv.DEV_PrecioUnitario) ) as decimal(10,2)) as total_venta'), DB::raw('CAST(sum(dv.DEV_Descuento) as decimal(10,2)) as total_descuento'), DB::raw('date(v.created_at) AS fechaVenta'), DB::raw('time(v.created_at) AS fechaVentaT'))
            ->groupBy('dov.DOV_Nombre', 'dov.DOV_Pdf', 'dov.DOV_Tipo', 'dov.DOV_Numero', 'dov.DOV_Serie', 'dov.DOV_Estado', 'dov.DOV_Anulado', 'dov.DOV_EstadoBaja', 'v.VEN_Id', 'mp.MEP_Id', 'mp.MEP_Pago', 'u.name', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_Id', 'a.ALM_NombreAlmacen', 'u.id', 'v.VEN_TipoPago', 'v.created_at')
            ->when($request->filled('fecha_inicio'), fn ($q) => $q->where(DB::raw('DATE(v.created_at)'), '>=', $request->input('fecha_inicio')))
            ->when($request->filled('fecha_fin'), fn ($q) => $q->where(DB::raw('DATE(v.created_at)'), '<=', $request->input('fecha_fin')))
            ->when($request->filled('estado'), fn ($q) => $q->where('dov.DOV_Estado', $request->input('estado')))
            ->when($request->filled('tipo'), fn ($q) => $q->where('dov.DOV_Tipo', $request->input('tipo')))
            ->when($request->filled('anulado'), fn ($q) => $q->where('dov.DOV_Anulado', $request->input('anulado')))
            ->when($request->filled('almacen_id'), fn ($q) => $q->where('a.ALM_Id', $request->input('almacen_id')))
            ->when($request->filled('metodo_pago_id'), fn ($q) => $q->where('mp.MEP_Id', $request->input('metodo_pago_id')))
            ->when($request->filled('cliente'), function ($q) use ($request) {
                $busqueda = $request->input('cliente');
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('c.CLI_Nombre', 'like', '%' . $busqueda . '%')
                       ->orWhere('c.CLI_NumDocumento', 'like', '%' . $busqueda . '%');
                });
            });

        return $query;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = $this->consultaVentas($request)->get();

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
                    $btn = '<button type="button" title="Enviar ticket por WhatsApp" data-id="' . $row->VEN_Id . '" class="btn btn-success btn-sm envioWhatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i></button>';

                    return $btn;
                })
                ->addColumn('sunat', fn ($row) => $this->columnaSunat($row))

                ->rawColumns(['action1', 'action2', 'action3', 'ticket', 'pdf', 'whatsapp', 'sunat'])
                ->make(true);
        }

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.venta.index', [
            // La columna de SUNAT solo tiene sentido si ya hay certificado.
            'mostrarSunat' => tenant_tiene_certificado(),
            'almacenes' => DB::table('almacen')->orderBy('ALM_NombreAlmacen')->get(),
            'metodosPago' => DB::table('metodo_pago')->orderBy('MEP_Pago')->get(),
        ]);
    }

    /**
     * Contenido de la columna SUNAT: el estado del comprobante y las acciones
     * para revisarlo o reintentar el envio.
     */
    private function columnaSunat($row): string
    {
        // Las notas de venta no van a SUNAT.
        if (!in_array($row->DOV_Tipo, ['BOL', 'FAC', 'NCR'], true)) {
            return '<span class="text-muted">&mdash;</span>';
        }

        $estados = [
            'ACEPTADO'  => ['success', 'Aceptado por SUNAT'],
            'OBSERVADO' => ['warning', 'Aceptado con observaciones'],
            'RECHAZADO' => ['danger',  'Rechazado por SUNAT'],
            'ERROR'     => ['danger',  'No se pudo enviar'],
            'PENDIENTE' => ['secondary', 'Aun no enviado'],
        ];

        [$color, $titulo] = $estados[$row->estadoDocVenta] ?? ['secondary', $row->estadoDocVenta];

        $id = $row->VEN_Id;
        $anulado = (bool) $row->DOV_Anulado;
        $bajaEnCurso = $row->DOV_EstadoBaja === 'PENDIENTE';

        // OBSERVADO tambien significa que SUNAT lo acepto, solo que con
        // observaciones: reenviarlo lo duplicaria.
        $yaEnSunat = in_array($row->estadoDocVenta, ComprobanteSunatController::ESTADOS_EN_SUNAT, true);

        $html = '<span class="badge badge-' . $color . '" title="' . e($titulo) . '">'
              . e($row->estadoDocVenta) . '</span> ';

        if ($anulado) {
            $html .= '<span class="badge badge-dark" title="El comprobante fue anulado ante SUNAT">ANULADO</span> ';
        } elseif ($bajaEnCurso) {
            $html .= '<span class="badge badge-info" title="La anulacion esta en tramite en SUNAT">BAJA EN TRAMITE</span> ';
        }

        $html .= '<div class="btn-group btn-group-sm ml-1" role="group">';

        // XML y CDR solo existen si el comprobante llego a SUNAT.
        if ($yaEnSunat) {
            $html .= '<a class="btn btn-outline-info btn-sm" title="Descargar XML" href="/tenant/ventas/venta/' . $id . '/sunat/xml"><i class="fa fa-file-code"></i></a>';
            $html .= '<a class="btn btn-outline-info btn-sm" title="Descargar CDR (zip)" href="/tenant/ventas/venta/' . $id . '/sunat/cdr"><i class="fa fa-file-archive"></i></a>';
        }

        $html .= '<button type="button" class="btn btn-outline-primary btn-sm sunatConsultar" data-id="' . $id . '" title="Consultar estado en SUNAT"><i class="fa fa-search"></i></button>';

        if (!$yaEnSunat && !$anulado) {
            $html .= '<button type="button" class="btn btn-outline-warning btn-sm sunatReenviar" data-id="' . $id . '" title="Reintentar envio a SUNAT"><i class="fa fa-paper-plane"></i></button>';
        }

        // Solo boleta/factura ya aceptadas y no anuladas pueden generar una
        // nota de credito (no se le puede emitir una nota a otra nota).
        if (in_array($row->DOV_Tipo, ['BOL', 'FAC'], true) && $yaEnSunat && !$anulado && !$bajaEnCurso) {
            $html .= '<a class="btn btn-outline-secondary btn-sm" title="Emitir nota de credito" href="/tenant/ventas/venta/' . $id . '/nota-credito"><i class="fa fa-undo"></i></a>';
        }

        // Guia de remision: entrega de los productos de esta venta.
        if (in_array($row->DOV_Tipo, ['BOL', 'FAC'], true) && $yaEnSunat && !$anulado && !$bajaEnCurso) {
            $html .= '<a class="btn btn-outline-secondary btn-sm" title="Emitir guia de remision" href="/tenant/ventas/venta/' . $id . '/guia-remision"><i class="fa fa-truck"></i></a>';
        }

        // Anular: solo sobre un comprobante aceptado, que no este ya anulado
        // ni con una baja en curso (evita pedir dos veces lo mismo).
        if ($yaEnSunat && !$anulado && !$bajaEnCurso) {
            $html .= '<button type="button" class="btn btn-outline-danger btn-sm anularComprobante" data-id="' . $id . '" data-tipo="' . $row->DOV_Tipo . '" title="Anular este comprobante"><i class="fa fa-ban"></i></button>';
        }

        // Mientras la baja este en tramite, se ofrece consultarla aparte
        // (no comparte boton con "Consultar estado en SUNAT", que consulta
        // el ENVIO del comprobante, no la BAJA).
        if ($bajaEnCurso) {
            $html .= '<button type="button" class="btn btn-outline-info btn-sm bajaConsultar" data-id="' . $id . '" title="Consultar resultado de la anulacion"><i class="fa fa-history"></i></button>';
        }

        $html .= '</div>';

        return $html;
    }

    public function filtro(Request $request, $fecharange)
    {
        $fec    = explode(" - ", $fecharange);

        $request->merge([
            'fecha_inicio' => $request->input('fecha_inicio', $fec[0] ?? null),
            'fecha_fin'    => $request->input('fecha_fin', $fec[1] ?? null),
        ]);

        if ($request->ajax()) {
            $data = $this->consultaVentas($request)->get();

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
                    $btn = '<button type="button" title="Enviar ticket por WhatsApp" data-id="' . $row->VEN_Id . '" class="btn btn-success btn-sm envioWhatsapp"><i class="fab fa-whatsapp" aria-hidden="true"></i></button>';

                    return $btn;
                })
                ->addColumn('sunat', fn ($row) => $this->columnaSunat($row))

                ->rawColumns(['action1', 'action2', 'action3', 'ticket', 'pdf', 'whatsapp', 'sunat'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (tenant_requiere_apertura_caja()) {
            $cajasCerradas = \App\Models\Tenant\Caja::where('CAJ_Status', 1)->whereDoesntHave('sesionAbierta')->get();

            return view('tenant_' . tenant('tipo_negocio') . '.partials.caja-requerida', [
                'accion' => 'una venta',
                'cajasCerradas' => $cajasCerradas,
            ]);
        }

        $clase = DB::table('clase')->orderBy('CLA_Nombre', 'asc')->get();
        $categoria = DB::table('categoria')->orderBy('CAT_Nombre', 'asc')->get();
        $clientes = DB::table('cliente')->orderBy('CLI_NumDocumento', 'asc')->get();
        $metodo_pago = DB::table('metodo_pago')->orderBy('MEP_Pago', 'asc')->get();

        // Si faltan datos de facturacion, el punto de venta esconde Boleta y
        // Factura y explica que hay que completar.
        $problemasFacturacion = tenant_problemas_facturacion();
        $puedeFacturar = $problemasFacturacion === [];

        // En el ambiente de pruebas de SUNAT el comprobante no tiene validez,
        // asi que no se ofrece para imprimir.
        $facturacionEnPruebas = tenant_facturacion_en_pruebas();

        // Si se viene desde el tablero de "Ventas por Bahia" (cuenta_bahia en
        // la URL), se precarga el carrito con lo que ya se le cargo a la
        // cuenta y el cliente/moto de la reserva, para no volver a escribirlo.
        $cuentaBahiaId = null;
        $prefillCarrito = [];
        $prefillCliente = null;

        if ($request->filled('cuenta_bahia')) {
            $cuenta = \App\Models\TenantTallerMotos\BahiaCuenta::with(['items', 'reservacion'])
                ->find($request->input('cuenta_bahia'));

            if ($cuenta && $cuenta->estaAbierta()) {
                $cuentaBahiaId = $cuenta->BCT_Id;

                $productos = DB::table('producto')->whereIn('PRO_Id', $cuenta->items->pluck('PRO_Id'))
                    ->pluck('PRO_Nombre', 'PRO_Id');

                $prefillCarrito = $cuenta->items->map(fn ($item) => [
                    'PRO_Id' => $item->PRO_Id,
                    'PRO_Nombre' => $productos[$item->PRO_Id] ?? ('Producto #' . $item->PRO_Id),
                    'PRO_PrecioBaseVenta' => $item->BCI_PrecioUnitario,
                    'quantity' => $item->BCI_Cantidad,
                ])->values();

                if ($cuenta->reservacion) {
                    $prefillCliente = [
                        'nombre' => $cuenta->reservacion->RES_Cliente,
                        'celular' => $cuenta->reservacion->RES_Celular,
                        'moto' => $cuenta->reservacion->RES_Moto,
                        'placa' => $cuenta->reservacion->RES_Placa,
                    ];
                }
            }
        }

        return view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.create',
            compact(
                'clase',
                'categoria',
                'clientes',
                'metodo_pago',
                'puedeFacturar',
                'problemasFacturacion',
                'facturacionEnPruebas',
                'cuentaBahiaId',
                'prefillCarrito',
                'prefillCliente'
            )
        );
    }

    /**
     * Item "al vuelo" para vender algo que no esta en el catalogo (un
     * servicio, un cargo especial, un producto que todavia no se registro):
     * crea un Producto minimo y un Lote con exactamente la cantidad que se
     * va a vender, para que la venta se registre igual que cualquier otra
     * (sin depender de que la sede tenga activado "vender sin stock").
     * Se agrupan todos bajo la categoria/clase "Varios" para no ensuciar
     * el catalogo de categorias real del negocio.
     */
    public function crearProductoRapido(Request $request, \App\Services\Ventas\ItemRapidoService $itemRapidoService)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'precio' => 'required|numeric|min:0',
            'cantidad' => 'required|numeric|min:0.01',
        ]);

        $idAlmacen = tenant_caja_activa_almacen_id() ?? 1;

        return response()->json($itemRapidoService->crear(
            $validated['nombre'],
            (float) $validated['cantidad'],
            (float) $validated['precio'],
            $idAlmacen
        ));
    }

    public function getProductos(Request $request)
    {
        // La sede activa decide si el catalogo puede ofrecer productos sin
        // stock (LEFT JOIN a lote, incluye los que nunca tuvieron lote ahi)
        // o si se sigue ocultando todo lo que no tenga cantidad disponible
        // (comportamiento de siempre).
        $idAlmacen = tenant_caja_activa_almacen_id() ?? 1;
        $permitirSinStock = (bool) (Almacen::find($idAlmacen)->ALM_PermitirVentaSinStock ?? false);

        $query = DB::table('producto as p')
            ->join('categoria as cat', 'cat.CAT_Id', '=', 'p.CAT_Id')
            ->join('clase as cl', 'cl.CLA_Id', '=', 'cat.CLA_Id')
            ->leftJoin('lote as lt', function ($join) use ($idAlmacen) {
                $join->on('lt.PRO_Id', '=', 'p.PRO_Id')
                    ->where('lt.ALM_Id', '=', $idAlmacen);
            })
            ->select(
                'p.PRO_Id',
                'p.PRO_Nombre',
                'p.PRO_Descripcion',
                'p.PRO_Imagen',
                'p.CAT_Id',
                DB::raw('COALESCE(SUM(lt.LOT_CantidadReal), 0) as PRO_Cantidad'),
                DB::raw('COALESCE(MAX(lt.LOT_PrecioVenta), p.PRO_PrecioVenta) as PRO_PrecioBaseVenta')
            )
            ->where('p.PRO_Status', 1);

        // FILTRO CATEGORIA
        if ($request->categoria != 'all') {
            $query->where('p.CAT_Id', $request->categoria);
        }

        // BUSQUEDA
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    'p.PRO_Nombre',
                    'like',
                    '%' . $request->search . '%'
                );
            });
        }

        $query->groupBy('p.PRO_Id', 'p.PRO_Nombre', 'p.PRO_Descripcion', 'p.PRO_Imagen', 'p.CAT_Id', 'p.PRO_PrecioVenta');

        // Sin el permiso de la sede, se mantiene el filtro de siempre: solo
        // lo que tenga stock disponible en esta sede.
        if (! $permitirSinStock) {
            $query->havingRaw('COALESCE(SUM(lt.LOT_CantidadReal), 0) > 0');
        }

        $productos = $query->paginate(20);

        return response()->json($productos);
    }

    public function searchClientes(Request $request)
    {
        $search = $request->search;
        $clientes = DB::table('cliente')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('CLI_Nombre', 'like', "%{$search}%")
                        ->orWhere('CLI_NumDocumento', 'like', "%{$search}%")
                        ->orWhere('CLI_Celular', 'like', "%{$search}%");
                });
            })
            ->orderBy('CLI_Nombre', 'asc')
            ->limit(20)
            ->get();

        return response()->json($clientes);
    }

    public function createCliente(Request $request)
    {
        $cliente = new Cliente();
        $cliente->CLI_Nombre = $request->get('CLI_Nombre');
        $cliente->CLI_TipoDocumento = $request->get('CLI_TipoDocumento');
        $cliente->CLI_NumDocumento = $request->get('CLI_NumDocumento');
        $cliente->CLI_Celular = $request->get('CLI_Celular');
        $cliente->CLI_Direccion = $request->get('CLI_Direccion');
        $cliente->save();

        return response()->json($cliente);
    }

    public function storeauxiliar(Request $request)
    {

        DB::beginTransaction();

        try {

            // ==========================
            // TOTAL
            // ==========================

            $total = 0;

            foreach ($request->productos as $item) {

                $total +=
                    $item['quantity']
                    *
                    $item['PRO_PrecioBaseVenta'];
            }

            // ==========================
            // VENTA
            // ==========================

            $ventaId = DB::table('venta')
                ->insertGetId([

                    'CLI_Id' =>
                    $request->cliente_id,

                    'VEN_Comprobante' =>
                    $request->comprobante,

                    'VEN_MetodoPago' =>
                    $request->metodo_pago,

                    'VEN_Total' =>
                    $total,

                    'VEN_PagoRecibido' =>
                    $request->pago_recibido,

                    'VEN_Vuelto' =>
                    $request->vuelto,

                    'VEN_Observacion' =>
                    $request->observacion,

                    'created_at' => now(),
                    'updated_at' => now()

                ]);

            // ==========================
            // DETALLE
            // ==========================

            foreach ($request->productos as $item) {

                DB::table('detalle_venta')
                    ->insert([

                        'VEN_Id' => $ventaId,

                        'PRO_Id' =>
                        $item['PRO_Id'],

                        'DEV_Cantidad' =>
                        $item['quantity'],

                        'DEV_Precio' =>
                        $item['PRO_PrecioBaseVenta'],

                        'DEV_Subtotal' =>
                        $item['quantity']
                            *
                            $item['PRO_PrecioBaseVenta'],

                        'created_at' => now(),
                        'updated_at' => now()

                    ]);

                // ======================
                // DESCONTAR STOCK
                // ======================

                DB::table('lote')

                    ->where(
                        'PRO_Id',
                        $item['PRO_Id']
                    )

                    ->decrement(
                        'LOT_CantidadReal',
                        $item['quantity']
                    );
            }

            DB::commit();

            return response()->json([

                'success' => true,

                'venta_id' => $ventaId

            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([

                'success' => false,

                'message' => $e->getMessage()

            ], 500);
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if (tenant_requiere_apertura_caja()) {
            return response()->json(['error' => 'No hay ninguna caja aperturada. Abre una caja antes de registrar la venta.'], 422);
        }

        DB::beginTransaction();
        try {

            $mytime = Carbon::now('America/Lima');
            $fechaactual = $mytime->toDateString();
            $horaactual = $mytime->toTimeString();

            // Antes estaba fijo en 1 (siempre descontaba del primer
            // almacén, sin importar la sede/caja real). Ahora usa el
            // almacén ligado a la caja con la que se está operando, y solo
            // cae a 1 si el tenant no usa cajas todavía.
            $idAlmacen = tenant_caja_activa_almacen_id() ?? 1;
            $idUsuario = Auth::user()->id;
            $idCliente = $request->get('cliente_id') ? $request->get('cliente_id') : 1;

            $venta = new Venta;
            // El punto de venta cobra siempre al contado (pide pago recibido y
            // calcula vuelto). Antes no se enviaba nada y quedaba en null, con
            // lo que el ticket lo imprimia como CREDITO. 1 = contado.
            $venta->VEN_TipoPago = $request->get('VEN_TipoPago', 1) ?: 1;
            $venta->VEN_Vuelto = $request->get('vuelto');
            $venta->VEN_Pagado = $request->get('pago_recibido');
            $venta->MEP_Id = $request->get('metodo_pago');
            $venta->USU_Id = $idUsuario;
            $venta->CLI_Id = $idCliente;
            $venta->ALM_Id = $idAlmacen;
            $venta->CAJ_Id = tenant_caja_activa_id();
            $venta->CS_Id = tenant_caja_sesion_activa_id();
            $venta->VEN_FechaEnvio = $fechaactual . ' ' . $horaactual;
            $venta->save();

            $VentaTipo = $request->get('comprobante');

            if ($VentaTipo == "VENTA") {
                // $folio = self::CrearDocumentoDetalle($CLI_Cod, $venta->VEN_Id, $idEmpleado, $idubicacion->UBI_Id, $idalmacen1, $CLI_Cod, 2, $documentodescuento);
            } else if ($VentaTipo == "NOTA") {
                $DocumentoVenta = self::CrearDocumentoDetalleVentaLibre($venta->VEN_Id, $idAlmacen);
            } else if ($this->documentoVentaService->esComprobanteElectronico($VentaTipo)) {
                // La interfaz esconde Boleta y Factura cuando faltan datos, pero
                // la peticion puede llegar igual: se vuelve a comprobar aqui.
                $problemas = tenant_problemas_facturacion();

                if ($problemas) {
                    throw new Exception(
                        'No se puede emitir ' . strtolower($VentaTipo) . ' electronica. ' .
                        implode(' ', $problemas)
                    );
                }

                // Boleta y factura llevan su propia serie y correlativo, tomados
                // de la configuracion de facturacion de la empresa.
                $DocumentoVenta = $this->documentoVentaService->crear(
                    $venta->VEN_Id,
                    $VentaTipo,
                    $this->empresaFacturacion()
                );
            }

            $permitirSinStock = (bool) (Almacen::find($idAlmacen)->ALM_PermitirVentaSinStock ?? false);

            $cont = 0;
            $it = 0;
            foreach ($request->productos as $item) {

                $rdst = self::ReducirStock($item['PRO_Id'], $item['quantity'], $idAlmacen, $permitirSinStock);

                for ($i = 0; $i < count($rdst); $i = $i + 2) {
                    $detalle = new DetalleVenta();
                    $detalle->VEN_Id = $venta->VEN_Id;
                    $detalle->DEV_Item = $it + 1;
                    $detalle->PRO_Id = $item['PRO_Id'];
                    $detalle->DEV_Cantidad = $rdst[$i + 1];
                    $detalle->DEV_PrecioUnitario = $item['PRO_PrecioBaseVenta'];
                    $detalle->LOT_Id = $rdst[$i];
                    $detalle->DEV_Descuento = 0;
                    $detalle->save();
                    $it = $it + 1;
                }

                $cont = $cont + 1;
            }

            $movi = new Movimiento();
            $movi->tipo = "Salida";
            $movi->idcv = $venta->VEN_Id;
            $movi->save();

            // Si esta venta viene del tablero de "Ventas por Bahia", se cierra
            // la cuenta y queda enlazada a la venta recien creada.
            if ($request->filled('bahia_cuenta_id')) {
                \App\Models\TenantTallerMotos\BahiaCuenta::where('BCT_Id', $request->input('bahia_cuenta_id'))
                    ->where('BCT_Estado', \App\Models\TenantTallerMotos\BahiaCuenta::ESTADO_ABIERTA)
                    ->update([
                        'BCT_Estado' => \App\Models\TenantTallerMotos\BahiaCuenta::ESTADO_CERRADA,
                        'VEN_Id' => $venta->VEN_Id,
                        'BCT_CerradoEn' => now(),
                    ]);
            }

            DB::commit();

            // El envio a SUNAT corre en segundo plano: la caja no espera al
            // servicio externo, y si falla se reintenta sin perder la venta.
            if ($this->documentoVentaService->esComprobanteElectronico($VentaTipo)) {
                EnviarVentaSunatJob::dispatch(
                    $venta->VEN_Id,
                    tenant('id'),
                    tenant('tipo_negocio')
                );
            }

            return response()->json(['success' => true, 'venta_id' => $venta->VEN_Id]);
        } catch (\Throwable $e) {
            DB::rollback();

            // Con codigo 200 el navegador daba la venta por buena y mostraba
            // "Venta Generada" aunque no se hubiera guardado nada.
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 422);
        }
    }

    function ticket(string $idventa)
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

        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $ventae->ALM_Id)
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

            $codi = $ventae->fechaVenta . "| " . $datosalmacen->ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
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

        return view('tenant_' . tenant('tipo_negocio') . '/ventas/venta/ticket/ticketventa9cm', compact('ventae', 'detallese', 'Subtotal', 'igv', 'codi', 'UbiDoc', 'NumDoc', 'datosalmacen', 'calificarventa', 'datosdecuenta', 'LetrasTotal', 'generaimagen'));
    }

    function pdf(string $idventa)
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

        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $ventae->ALM_Id)
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

            $codi = $ventae->fechaVenta . "| " . $datosalmacen->ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
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

        return view('tenant_' . tenant('tipo_negocio') . '/ventas/venta/ticket/ticket_A4', compact('ventae', 'detallese', 'Subtotal', 'igv', 'codi', 'UbiDoc', 'NumDoc', 'datosalmacen', 'calificarventa', 'datosdecuenta', 'LetrasTotal', 'generaimagen'));
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

        // Se une con empresa_facturacion igual que en ticket() y pdf(): el RUC
        // y la razon social que imprime el ticket viven ahi, no en el almacen.
        $datosalmacen = DB::table('almacen as al')
            ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
            ->where('emp.tenant_id', tenant('id'))
            ->where('al.ALM_Id', '=', $ventae->ALM_Id)
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

            $codi = $ventae->fechaVenta . "| " . $datosalmacen->ruc . " | " . $datosalmacen->ALM_Celular . " " . $ventae->numDoc . "|" . $ventae->total_venta;
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

        $path = public_path('storage/' . $ubicacionNegocio . '/' . $id . '/archivos/tickets/');
        if (!file_exists($path)) {

            mkdir($path, 0777, true);
        }
        $fileName = $ventae->pdf . '.png';
        $rutaCompleta = $path . $fileName;

        // Se rehace solo si no existe: generarla cuesta abrir un navegador.
        if (!is_file($rutaCompleta)) {
            Browsershot::html($html)
                ->timeout(120)
                ->windowSize(900, 1200)
                ->save($rutaCompleta);
        }

        // url() y no asset(): con asset_helper_tenancy activo, asset() devuelve
        // una ruta /tenancy/assets/... que da 404, porque el ticket se guarda
        // bajo public/storage.
        return url('storage/' . $ubicacionNegocio . '/' . $id . '/archivos/tickets/' . $fileName);
    }

    /**
     * Genera (si hace falta) la imagen del ticket y devuelve su enlace, para
     * compartirla por WhatsApp.
     *
     * La imagen no se crea al vender: solo cuando alguien la pide, para no
     * llenar el disco con tickets que nadie mira.
     */
    public function ticketWhatsapp(string $idventa)
    {
        try {
            $url = self::ticketImagen($idventa);

            $cliente = DB::table('venta as v')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->where('v.VEN_Id', $idventa)
                ->select('c.CLI_Nombre', 'c.CLI_Celular', 'dov.DOV_Serie', 'dov.DOV_Numero')
                ->first();

            return response()->json([
                'success'   => true,
                'url'       => $url,
                'documento' => $cliente ? $cliente->DOV_Serie . '-' . $cliente->DOV_Numero : '',
                'celular'   => $cliente->CLI_Celular ?? '',
                'cliente'   => $cliente->CLI_Nombre ?? '',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success'     => false,
                'descripcion' => 'No se pudo generar la imagen del ticket: ' . $e->getMessage(),
            ], 422);
        }
    }




    /**
     * Display the specified resource.
     */
    public function show(string $id)
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
    public function edit(string $id)
    {
        $Venta = Venta::find($id);
        return response()->json(['data' => $Venta]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
    public function destroy(string $id)
    {
        $Venta = Venta::find($id);
        $Venta->delete();
        return response()->json(['success' => 'Venta Eliminado Exitosamente.']);
    }



    /**
     * Descuenta $can unidades de $pro en el almacen $alm, lote por lote
     * (FIFO por fecha de ingreso). Devuelve un arreglo plano
     * [LOT_Id, cantidadTomadaDeEseLote, LOT_Id2, cantidad2, ...] para armar
     * el detalle de venta (puede repartirse entre varios lotes).
     *
     * Si el stock no alcanza:
     * - $permitirSinStock = false (comportamiento de siempre): lanza una
     *   excepcion, la venta no se registra.
     * - $permitirSinStock = true (sede con ALM_PermitirVentaSinStock
     *   activado): el faltante se descuenta igual del ultimo lote usado
     *   (o de uno nuevo en 0 si el producto nunca tuvo lote en esta sede),
     *   dejandolo en negativo para que quede visible el sobregiro.
     */
    public static function ReducirStock($pro, $can, $alm, $permitirSinStock = false)
    {
        $lotes = DB::table('lote')
            ->select('LOT_Id', 'LOT_CantidadReal')
            ->where('PRO_Id', $pro)
            ->where('ALM_Id', $alm)
            ->orderBy('created_at', 'asc')
            ->get();

        $restante = $can;
        $lotid = [];
        $i = 0;

        foreach ($lotes as $lote) {
            if ($restante <= 0) {
                break;
            }

            if ($lote->LOT_CantidadReal <= 0) {
                continue;
            }

            $tomar = min($lote->LOT_CantidadReal, $restante);

            Lote::where('LOT_Id', $lote->LOT_Id)->decrement('LOT_CantidadReal', $tomar);

            $lotid[$i++] = $lote->LOT_Id;
            $lotid[$i++] = $tomar;
            $restante -= $tomar;
        }

        if ($restante > 0) {
            if (! $permitirSinStock) {
                throw new Exception('No hay stock suficiente de este producto en la sede.');
            }

            // Se reutiliza el ultimo lote de la lista (el mas antiguo con
            // datos, aunque ya estuviera en 0) para no perder el precio de
            // compra/venta que tenia registrado. Si el producto nunca tuvo
            // un lote en esta sede, se crea uno en 0 solo para poder anclar
            // el detalle de venta (la FK LOT_Id lo exige).
            $ultimoLoteId = optional($lotes->last())->LOT_Id;

            if (! $ultimoLoteId) {
                $ultimoLoteId = DB::table('lote')->insertGetId([
                    'ALM_Id' => $alm,
                    'PRO_Id' => $pro,
                    'LOT_TipoIngreso' => 'VENTA_SIN_STOCK',
                    'LOT_IdIngreso' => 0,
                    'LOT_CantidadReal' => 0,
                    'LOT_CantidadIngreso' => 0,
                    'LOT_PrecioCompra' => 0,
                    'LOT_PrecioVenta' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            Lote::where('LOT_Id', $ultimoLoteId)->decrement('LOT_CantidadReal', $restante);

            $lotid[$i++] = $ultimoLoteId;
            $lotid[$i++] = $restante;
        }

        return $lotid;
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
        $documento->DOV_Nombre = $serPro . '-' . $numPro;
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
