<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\MarcaVehiculo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * Mantenimientos > Marcas de Vehiculo: catalogo de marcas que se eligen
 * (opcionalmente) al reservar y en cada mantenimiento.
 */
class MarcaVehiculoController extends Controller
{
    public function index()
    {
        $marcas = DB::table('marca_vehiculo as m')
            ->select('m.*', DB::raw('(SELECT COUNT(*) FROM reservacion r WHERE r.MAR_Id = m.MAR_Id) as reservas'))
            ->orderByDesc('m.MAR_Estado')
            ->orderBy('m.MAR_Nombre')
            ->get();

        return view('tenant_tallermoto.mantenimientos.marcas.index', compact('marcas'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'MAR_Nombre' => ['required', 'string', 'max:60', Rule::unique('marca_vehiculo', 'MAR_Nombre')],
        ], ['MAR_Nombre.unique' => 'Esa marca ya existe.']);

        MarcaVehiculo::create(['MAR_Nombre' => trim($datos['MAR_Nombre']), 'MAR_Estado' => true]);

        return response()->json(['success' => true, 'message' => 'Marca agregada.']);
    }

    public function update(Request $request, MarcaVehiculo $marca)
    {
        $datos = $request->validate([
            'MAR_Nombre' => ['required', 'string', 'max:60', Rule::unique('marca_vehiculo', 'MAR_Nombre')->ignore($marca->MAR_Id, 'MAR_Id')],
            'MAR_Estado' => ['required', 'boolean'],
        ], ['MAR_Nombre.unique' => 'Esa marca ya existe.']);

        $marca->update(['MAR_Nombre' => trim($datos['MAR_Nombre']), 'MAR_Estado' => (bool) $datos['MAR_Estado']]);

        return response()->json(['success' => true, 'message' => 'Marca actualizada.']);
    }

    /**
     * Elimina la marca si nunca se uso; si ya esta en reservas o
     * mantenimientos solo se desactiva (deja de ofrecerse, pero los
     * registros conservan su marca).
     */
    public function destroy(MarcaVehiculo $marca)
    {
        $tablas = ['reservacion', 'mantenimiento_general_carburada', 'mantenimiento_general_inyectada', 'mantenimiento_preventivo_carburada',
            'mantenimiento_preventivo_inyectada', 'mantenimiento_actividad_variadas', 'mantenimiento_garantia_hero', 'mantenimiento_garantia_lifan'];

        $enUso = collect($tablas)->contains(fn ($t) => \Illuminate\Support\Facades\Schema::hasTable($t) && DB::table($t)->where('MAR_Id', $marca->MAR_Id)->exists());

        if ($enUso) {
            $marca->update(['MAR_Estado' => false]);

            return response()->json(['success' => true, 'message' => 'La marca ya se usó en reservas o mantenimientos: se desactivó en lugar de eliminarse.']);
        }

        $marca->delete();

        return response()->json(['success' => true, 'message' => 'Marca eliminada.']);
    }
}
