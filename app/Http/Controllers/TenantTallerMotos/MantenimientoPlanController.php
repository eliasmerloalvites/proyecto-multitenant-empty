<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\MantenimientoPlan;
use App\Services\Mantenimiento\ChecklistCatalogo;
use Illuminate\Http\Request;

/**
 * CRUD de "planes/paquetes" de checklist: cada plan elige un subconjunto
 * del catalogo fijo de items de un tipo de mantenimiento (ver
 * ChecklistCatalogo), para que el formulario de creacion/edicion de ese
 * tipo solo muestre esos items en vez del checklist completo.
 */
class MantenimientoPlanController extends Controller
{
    public function index()
    {
        $planes = MantenimientoPlan::orderBy('PLAN_Tipo')->orderBy('PLAN_Nombre')->get()
            ->groupBy('PLAN_Tipo');

        $catalogos = collect(ChecklistCatalogo::TIPOS)
            ->mapWithKeys(fn ($nombre, $tipo) => [$tipo => ChecklistCatalogo::items($tipo)]);

        return view('tenant_tallermoto.mantenimientos.planes.index', [
            'planes' => $planes,
            'tipos' => ChecklistCatalogo::TIPOS,
            'catalogos' => $catalogos,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validarPlan($request);

        MantenimientoPlan::create($validated);

        return response()->json(['success' => true, 'message' => 'Plan creado correctamente.']);
    }

    public function update(Request $request, MantenimientoPlan $plan)
    {
        $validated = $this->validarPlan($request);

        $plan->update($validated);

        return response()->json(['success' => true, 'message' => 'Plan actualizado correctamente.']);
    }

    public function destroy(MantenimientoPlan $plan)
    {
        $plan->delete();

        return response()->json(['success' => true, 'message' => 'Plan eliminado correctamente.']);
    }

    private function validarPlan(Request $request): array
    {
        $validated = $request->validate([
            'PLAN_Tipo' => 'required|string|in:' . implode(',', array_keys(ChecklistCatalogo::TIPOS)),
            'PLAN_Nombre' => 'required|string|max:100',
            'PLAN_Items' => 'required|array|min:1',
            'PLAN_Items.*' => 'string',
            'PLAN_Activo' => 'nullable|boolean',
        ]);

        // Solo se guardan codigos que realmente existen en el catalogo de
        // ese tipo (evita basura si el request viene manipulado).
        $codigosValidos = collect(ChecklistCatalogo::items($validated['PLAN_Tipo']))->pluck('codigo')->all();
        $validated['PLAN_Items'] = array_values(array_intersect($validated['PLAN_Items'], $codigosValidos));
        $validated['PLAN_Activo'] = $request->boolean('PLAN_Activo', true);

        return $validated;
    }
}
