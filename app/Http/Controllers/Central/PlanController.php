<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    // Etiquetas legibles de cada vertical, en el orden que se muestran las pestañas.
    private const TIPOS_NEGOCIO = [
        'tallermoto' => 'Taller de Motos',
        'generico' => 'Negocio Genérico',
    ];

    public function index()
    {
        $planesPorNegocio = Plan::orderByRaw("FIELD(`key`, 'start', 'basic', 'plus', 'empresarial')")
            ->get()
            ->groupBy('tipo_negocio');

        $modulosPorNegocio = collect(array_keys(self::TIPOS_NEGOCIO))
            ->mapWithKeys(fn ($tipo) => [$tipo => Plan::modulosPara($tipo)]);

        return view('central.admin.planes.index', [
            'tiposNegocio' => self::TIPOS_NEGOCIO,
            'planesPorNegocio' => $planesPorNegocio,
            'modulosPorNegocio' => $modulosPorNegocio,
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
            'cash_registers' => 'required|integer|min:1',
        ]);

        // El form solo manda los checkboxes marcados, y solo lista los
        // módulos editables para el vertical de este plan (Plan::modulosPara).
        // Se reconstruye ese subconjunto (true solo los marcados) sin tocar
        // otras claves que ya tuviera guardadas (ej. 'mantenimientos' de un
        // plan viejo), para que tenant_has_module() siga funcionando con
        // array_key_exists().
        $modulosMarcados = $validated['modules'] ?? [];
        $modules = $plan->modules ?? [];
        foreach (array_keys(Plan::modulosPara($plan->tipo_negocio)) as $clave) {
            $modules[$clave] = in_array($clave, $modulosMarcados, true);
        }

        $precioAnterior = $plan->price;

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
            // 'branches' es el único límite de "locales" — Locales/Sedes y
            // Almacenes son la misma tabla/recurso en el sistema hoy (ver
            // SedeController/AlmacenController), así que ya no se guarda un
            // 'warehouses' separado que nunca se llegó a distinguir de él.
            'limits' => [
                'branches' => $validated['branches'],
                'cash_registers' => $validated['cash_registers'],
            ],
        ]);

        \App\Models\AuditLog::registrar(
            'plan.actualizado',
            'Actualizó el plan "' . $plan->nombre . '" (' . (self::TIPOS_NEGOCIO[$plan->tipo_negocio] ?? $plan->tipo_negocio) . ')' . ($precioAnterior != $validated['price'] ? ' — precio S/' . $precioAnterior . ' → S/' . $validated['price'] : ''),
            ['plan' => $plan->key, 'tipo_negocio' => $plan->tipo_negocio, 'precio_anterior' => $precioAnterior, 'precio_nuevo' => $validated['price'], 'modules' => $modules]
        );

        return response()->json(['success' => 'Plan "' . $plan->nombre . '" actualizado correctamente.']);
    }
}
