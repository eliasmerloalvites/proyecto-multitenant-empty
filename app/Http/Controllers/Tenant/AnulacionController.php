<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\AnulacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Anula un comprobante ya aceptado por SUNAT (boleta por resumen diario,
 * factura/nota de credito por comunicacion de baja).
 */
class AnulacionController extends Controller
{
    protected $service;

    public function __construct(AnulacionService $service)
    {
        $this->service = $service;
    }

    /**
     * Lista las anulaciones solicitadas, mas recientes primero. Incluye
     * tanto las que ya se resolvieron como las que siguen en tramite.
     */
    public function index()
    {
        $anulaciones = DB::table('documento_venta as dov')
            ->join('venta as v', 'v.VEN_Id', '=', 'dov.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->whereNotNull('dov.DOV_EstadoBaja')
            ->select(
                'v.VEN_Id',
                'dov.DOV_Nombre',
                'dov.DOV_Tipo',
                'dov.DOV_MotivoBaja',
                'dov.DOV_TicketBaja',
                'dov.DOV_EstadoBaja',
                'dov.DOV_DescripcionBaja',
                'dov.DOV_FechaSolicitudBaja',
                'dov.DOV_FechaRespuestaBaja',
                'c.CLI_Nombre'
            )
            ->orderByDesc('dov.DOV_FechaSolicitudBaja')
            ->get();

        return view(
            'tenant_' . tenant('tipo_negocio') . '.ventas.venta.anulaciones',
            ['anulaciones' => $anulaciones]
        );
    }

    public function solicitar(Request $request, $ventaId)
    {
        $motivo = trim((string) $request->input('motivo'));

        if ($motivo === '') {
            return response()->json(['success' => false, 'descripcion' => 'Indica el motivo de la anulacion.'], 422);
        }

        $resultado = $this->service->solicitarBaja(
            (int) $ventaId,
            $motivo,
            $request->boolean('devolver_stock')
        );

        return response()->json($resultado, $resultado['success'] ? 200 : 422);
    }

    public function consultar($ventaId)
    {
        $resultado = $this->service->consultarBaja((int) $ventaId);

        return response()->json($resultado, $resultado['success'] ? 200 : 422);
    }
}
