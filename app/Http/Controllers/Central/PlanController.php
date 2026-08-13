<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $planes = Plan::orderByRaw("FIELD(`key`, 'start', 'basic', 'plus', 'empresarial')")->get();

        return view('central.admin.planes.index', [
            'planes' => $planes,
            'modulos' => Plan::MODULOS,
        ]);
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:50',
            'price' => 'required|numeric|min:0',
            'max_users' => 'required|integer|min:1',
            'max_images' => 'required|integer|min:1',
            'storage_limit_mb' => 'required|integer|min:1',
            'custom_domain_enabled' => 'nullable|boolean',
            'custom_branding' => 'nullable|boolean',
            'customizable' => 'nullable|boolean',
            'modules' => 'nullable|array',
            'modules.*' => 'string|in:' . implode(',', array_keys(Plan::MODULOS)),
            'branches' => 'required|integer|min:1',
            'warehouses' => 'required|integer|min:1',
            'cash_registers' => 'required|integer|min:1',
        ]);

        // El form solo manda los checkboxes marcados; se reconstruye el mapa
        // completo (todas las claves posibles, true solo las marcadas) para
        // que tenant_has_module() siga funcionando con array_key_exists().
        $modulosMarcados = $validated['modules'] ?? [];
        $modules = [];
        foreach (array_keys(Plan::MODULOS) as $clave) {
            $modules[$clave] = in_array($clave, $modulosMarcados, true);
        }

        $plan->update([
            'nombre' => $validated['nombre'],
            'price' => $validated['price'],
            'max_users' => $validated['max_users'],
            'max_images' => $validated['max_images'],
            'storage_limit_mb' => $validated['storage_limit_mb'],
            'custom_domain_enabled' => $request->boolean('custom_domain_enabled'),
            'custom_branding' => $request->boolean('custom_branding'),
            'customizable' => $request->boolean('customizable'),
            'modules' => $modules,
            'limits' => [
                'branches' => $validated['branches'],
                'warehouses' => $validated['warehouses'],
                'cash_registers' => $validated['cash_registers'],
            ],
        ]);

        return response()->json(['success' => 'Plan "' . $plan->nombre . '" actualizado correctamente.']);
    }
}
