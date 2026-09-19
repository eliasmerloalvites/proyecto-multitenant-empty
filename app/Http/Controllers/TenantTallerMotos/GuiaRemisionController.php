<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Jobs\EnviarGuiaRemisionSunatJob;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\DocumentoVenta;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\GuiaRemision;
use App\Services\Facturacion\GuiaRemisionService;
use App\Services\Facturacion\GuiaRemisionSunatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Emite la guia de remision de una venta ya facturada: entrega de los
 * productos vendidos al cliente (motivo SUNAT "01 Venta").
 */
class GuiaRemisionController extends Controller
{
    protected $guiaRemisionService;

    public function __construct(GuiaRemisionService $guiaRemisionService)
    {
        $this->guiaRemisionService = $guiaRemisionService;
    }

    /**
     * Lista las guias emitidas, mas recientes primero.
     */
    public function index(Request $request)
    {
        $guias = DB::table('guia_remision as g')
            ->join('venta as v', 'v.VEN_Id', '=', 'g.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->select(
                'g.GRM_Id',
                'v.VEN_Id',
                DB::raw("CONCAT(g.GRM_Serie, '-', LPAD(g.GRM_Numero, 8, '0')) as GRM_Nombre"),
                'g.GRM_Estado',
                'g.GRM_ModoTransporte',
                'g.GRM_FechaTraslado',
                'c.CLI_Nombre',
                'g.created_at'
            )
            ->when($request->filled('estado'), fn ($q) => $q->where('g.GRM_Estado', $request->input('estado')))
            ->when($request->filled('modo_transporte'), fn ($q) => $q->where('g.GRM_ModoTransporte', $request->input('modo_transporte')))
            ->when($request->filled('fecha_inicio'), fn ($q) => $q->where(DB::raw('date(g.GRM_FechaTraslado)'), '>=', $request->input('fecha_inicio')))
            ->when($request->filled('fecha_fin'), fn ($q) => $q->where(DB::raw('date(g.GRM_FechaTraslado)'), '<=', $request->input('fecha_fin')))
            ->when($request->filled('cliente'), function ($q) use ($request) {
                $busqueda = $request->input('cliente');
                $q->where(function ($qq) use ($busqueda) {
                    $qq->where('c.CLI_Nombre', 'like', '%' . $busqueda . '%')
                       ->orWhere('c.CLI_NumDocumento', 'like', '%' . $busqueda . '%');
                });
            })
            ->orderByDesc('g.GRM_Id')
            ->get();

        return view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.guias-remision',
            [
                'guias' => $guias,
                'filtros' => $request->only(['estado', 'modo_transporte', 'fecha_inicio', 'fecha_fin', 'cliente']),
            ]
        );
    }

    /**
     * Formulario: datos de traslado a partir de una venta ya facturada.
     */
    public function create($ventaId)
    {
        $documento = DocumentoVenta::where('VEN_Id', $ventaId)->first();

        if (!$documento || !in_array($documento->DOV_Estado, ['ACEPTADO', 'OBSERVADO'], true)) {
            abort(422, 'La venta debe tener un comprobante aceptado por SUNAT antes de emitir su guia de remision.');
        }

        $venta = DB::table('venta as v')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->where('v.VEN_Id', $ventaId)
            ->select(
                'v.ALM_Id',
                'v.CLI_Id',
                'c.CLI_Nombre',
                'c.CLI_NumDocumento',
                'c.CLI_TipoDocumento',
                'c.CLI_Direccion',
                'a.ALM_NombreAlmacen',
                'a.ALM_Ubigeo',
                'a.ALM_Direccion',
                'a.ALM_Departamento',
                'a.ALM_Provincia',
                'a.ALM_Distrito'
            )
            ->first();

        $items = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $ventaId)
            ->select('dv.DEV_Item', 'p.PRO_Nombre', 'dv.DEV_Cantidad')
            ->get();

        $sede = Almacen::find($venta->ALM_Id);
        $problemasSede = array_filter([
            !$sede->seriePara(EmpresaFacturacion::TIPO_GUIA_REMISION)
                ? 'A "' . $sede->ALM_NombreAlmacen . '" le falta la serie de guia de remision.'
                : null,
        ]);

        return view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.guia-remision',
            [
                'ventaId'       => $ventaId,
                'venta'         => $venta,
                'items'         => $items,
                'problemasSede' => $problemasSede,
            ]
        );
    }

    /**
     * Crea la guia y despacha su envio a SUNAT.
     */
    public function store(Request $request, $ventaId)
    {
        $modoTransporte = (string) $request->input('modo_transporte');

        if (!in_array($modoTransporte, [GuiaRemision::MODO_PUBLICO, GuiaRemision::MODO_PRIVADO], true)) {
            return response()->json(['success' => false, 'error' => 'Selecciona el modo de transporte.'], 422);
        }

        $datos = [
            'fecha_traslado'    => $request->input('fecha_traslado'),
            'peso_total'        => $request->input('peso_total'),
            'und_peso'          => $request->input('und_peso', 'KGM'),
            'ubigeo_partida'    => $request->input('ubigeo_partida'),
            'direccion_partida' => $request->input('direccion_partida'),
            'ubigeo_llegada'    => $request->input('ubigeo_llegada'),
            'direccion_llegada' => $request->input('direccion_llegada'),
            'modo_transporte'   => $modoTransporte,
        ];

        $camposBase = ['fecha_traslado', 'peso_total', 'ubigeo_partida', 'direccion_partida', 'ubigeo_llegada', 'direccion_llegada'];

        if ($modoTransporte === GuiaRemision::MODO_PUBLICO) {
            $datos['transportista_tipo_doc'] = $request->input('transportista_tipo_doc');
            $datos['transportista_numero'] = $request->input('transportista_numero');
            $datos['transportista_razon_social'] = $request->input('transportista_razon_social');
            $camposModo = ['transportista_tipo_doc', 'transportista_numero', 'transportista_razon_social'];
        } else {
            $datos['vehiculo_placa'] = $request->input('vehiculo_placa');
            $datos['conductor_tipo_doc'] = $request->input('conductor_tipo_doc');
            $datos['conductor_numero'] = $request->input('conductor_numero');
            $datos['conductor_nombres'] = $request->input('conductor_nombres');
            $datos['conductor_apellidos'] = $request->input('conductor_apellidos');
            $datos['conductor_licencia'] = $request->input('conductor_licencia');
            $camposModo = ['vehiculo_placa', 'conductor_tipo_doc', 'conductor_numero', 'conductor_nombres', 'conductor_apellidos'];
        }

        foreach (array_merge($camposBase, $camposModo) as $campo) {
            if (blank($datos[$campo] ?? null)) {
                return response()->json(['success' => false, 'error' => "Falta el campo '$campo'."], 422);
            }
        }

        try {
            $guia = $this->guiaRemisionService->crear((int) $ventaId, $datos);
        } catch (RuntimeException $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()], 422);
        }

        EnviarGuiaRemisionSunatJob::dispatch($guia->GRM_Id, tenant('id'), tenant('tipo_negocio'));

        return response()->json([
            'success' => true,
            'guia'    => $guia->GRM_Nombre,
            'guia_id' => $guia->GRM_Id,
        ]);
    }

    /**
     * Vuelve a consultar el ticket cuando el envio quedo PENDIENTE.
     */
    public function consultarTicket($guiaId, GuiaRemisionSunatService $service)
    {
        $resultado = $service->consultarTicket((int) $guiaId);

        return response()->json($resultado);
    }

    /**
     * Simula una aceptacion de SUNAT (solo BETA) para poder probar el
     * registro y el documento impreso sin depender de las credenciales
     * OAuth2 de la API GRE ni gastar un envio real.
     */
    public function simular($guiaId, GuiaRemisionSunatService $service)
    {
        try {
            $resultado = $service->simularAceptado((int) $guiaId);

            return response()->json($resultado);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'descripcion' => $e->getMessage()], 422);
        }
    }

    /**
     * Representacion impresa de la guia (para ver/imprimir), no es el XML
     * que exige SUNAT sino un formato legible para el chofer/cliente.
     */
    public function imprimir($guiaId)
    {
        $guia = DB::table('guia_remision as g')
            ->join('venta as v', 'v.VEN_Id', '=', 'g.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->join('almacen as a', 'a.ALM_Id', '=', 'v.ALM_Id')
            ->where('g.GRM_Id', $guiaId)
            ->select('g.*', 'c.CLI_Nombre', 'c.CLI_NumDocumento', 'c.CLI_TipoDocumento', 'a.ALM_NombreAlmacen')
            ->first();

        if (!$guia) {
            abort(404);
        }

        $items = DB::table('detalle_venta as dv')
            ->join('producto as p', 'p.PRO_Id', '=', 'dv.PRO_Id')
            ->where('dv.VEN_Id', $guia->VEN_Id)
            ->select('p.PRO_Id', 'p.PRO_Nombre', 'dv.DEV_Cantidad')
            ->get();

        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.guia-remision-pdf',
            [
                'guia'    => $guia,
                'items'   => $items,
                'empresa' => $empresa,
            ]
        )->setOptions([
            'defaultFont'     => 'sans-serif',
            'chroot'          => public_path(),
            'isRemoteEnabled' => true,
        ]);

        return $pdf->stream('guia-remision-' . str_replace('/', '-', $guia->GRM_Serie . '-' . $guia->GRM_Numero) . '.pdf');
    }

    public function xml($guiaId)
    {
        return $this->descargar($guiaId, 'xml', 'xml_name', 'application/xml');
    }

    public function cdr($guiaId)
    {
        return $this->descargar($guiaId, 'cdr', 'cdr_name', 'application/zip');
    }

    private function descargar($guiaId, string $carpeta, string $clave, string $mime)
    {
        try {
            $guia = GuiaRemision::findOrFail($guiaId);

            $respuesta = json_decode($guia->GRM_ResponseSunat ?? '', true);
            $nombre = $respuesta[$clave] ?? null;

            if (!$nombre) {
                throw new RuntimeException(
                    'Esta guia todavia no tiene ' . strtoupper($carpeta) .
                    '; probablemente aun no se resolvio el envio a SUNAT.'
                );
            }

            $ruta = 'tenant/' . tenant('tipo_negocio') . '/' . tenant('id') . '/sunat/' . $carpeta . '/' . $nombre;

            if (!Storage::exists($ruta)) {
                throw new RuntimeException(
                    'El archivo ' . $nombre . ' no esta en el servidor.'
                );
            }

            return response(Storage::get($ruta), 200, [
                'Content-Type'        => $mime,
                'Content-Disposition' => 'attachment; filename="' . $nombre . '"',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'descripcion' => $e->getMessage()], 422);
        }
    }
}
