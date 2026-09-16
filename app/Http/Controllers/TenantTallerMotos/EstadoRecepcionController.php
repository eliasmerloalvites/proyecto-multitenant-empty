<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Services\TenantTallerMotos\GestionProcesoService;
use Exception;
use Illuminate\Http\Request;

/**
 * "Estado de Recepcion de la Motocicleta": el checklist de condiciones
 * (inventario visual + inspeccion) de un mantenimiento puntual, sin
 * importar si vino de un check-in, de aprobar una reserva sin check-in, o
 * de crearse directo (walk-in). Ver GestionProcesoService::estadoRecepcion()/
 * guardarEstadoRecepcion() para la logica real; este controlador solo
 * valida la entrada y traduce a JSON para el partial que se incluye en la
 * ficha de cada uno de los 5 tipos de mantenimiento.
 */
class EstadoRecepcionController extends Controller
{
    public function mostrar(string $tabla, int $id)
    {
        try {
            $estado = GestionProcesoService::estadoRecepcion($tabla, $id);
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }

        return response()->json([
            'success' => true,
            'categorias' => $estado['categorias'],
            'respuestas' => $estado['respuestas'],
            'observaciones' => $estado['observaciones'],
        ]);
    }

    public function guardar(Request $request, string $tabla, int $id)
    {
        $validated = $request->validate([
            'respuestas' => 'nullable|array',
            'respuestas.*' => 'nullable|string|max:500',
            'observaciones' => 'nullable|array',
            'observaciones.*' => 'nullable|string|max:2000',
        ]);

        try {
            GestionProcesoService::guardarEstadoRecepcion(
                $tabla,
                $id,
                $validated['respuestas'] ?? [],
                $validated['observaciones'] ?? []
            );
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        return response()->json(['success' => true, 'message' => 'Estado de recepción guardado.']);
    }
}
