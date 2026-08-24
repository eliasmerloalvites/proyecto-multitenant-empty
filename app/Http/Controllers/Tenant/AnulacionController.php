<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Services\Facturacion\AnulacionService;
use Illuminate\Http\Request;

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

    public function solicitar(Request $request, $ventaId)
    {
        $motivo = trim((string) $request->input('motivo'));

        if ($motivo === '') {
            return response()->json(['success' => false, 'descripcion' => 'Indica el motivo de la anulacion.'], 422);
        }

        $resultado = $this->service->solicitarBaja((int) $ventaId, $motivo);

        return response()->json($resultado, $resultado['success'] ? 200 : 422);
    }

    public function consultar($ventaId)
    {
        $resultado = $this->service->consultarBaja((int) $ventaId);

        return response()->json($resultado, $resultado['success'] ? 200 : 422);
    }
}
