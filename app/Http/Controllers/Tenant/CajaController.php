<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\Caja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('caja as c')
                ->leftJoin('almacen as al', 'al.ALM_Id', '=', 'c.ALM_Id')
                ->leftJoin('caja_sesion as cs', function ($join) {
                    $join->on('cs.CAJ_Id', '=', 'c.CAJ_Id')->where('cs.CS_Estado', '=', 'abierta');
                })
                ->select('c.*', 'al.ALM_NombreAlmacen', 'cs.CS_Id', 'cs.CS_FechaApertura', 'cs.CS_MontoApertura as CS_MontoApertura_actual')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('sede', fn ($row) => $row->ALM_NombreAlmacen ?? '—')
                ->addColumn('estado', function ($row) {
                    if (! $row->CAJ_Status) {
                        return '<span class="badge badge-secondary">Inactiva</span>';
                    }

                    return $row->CS_Id
                        ? '<span class="badge badge-success">Abierta desde ' . \Carbon\Carbon::parse($row->CS_FechaApertura)->format('d/m H:i') . '</span>'
                        : '<span class="badge badge-warning">Cerrada</span>';
                })
                ->addColumn('turno_action', function ($row) {
                    if (! $row->CAJ_Status) {
                        return '';
                    }

                    return $row->CS_Id
                        ? '<button class="btn btn-sm btn-danger cerrarCaja" data-id="' . $row->CAJ_Id . '" data-nombre="' . e($row->CAJ_Nombre) . '"><i class="fa fa-lock mr-1"></i>Cerrar</button>'
                        : '<button class="btn btn-sm btn-success aperturarCaja" data-id="' . $row->CAJ_Id . '" data-nombre="' . e($row->CAJ_Nombre) . '" data-monto="' . $row->CAJ_MontoApertura . '"><i class="fa fa-unlock mr-1"></i>Aperturar</button>';
                })
                ->addColumn('action1', function ($row) {
                    return '<a data-toggle="tooltip" data-identificador="' . $row->CAJ_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editCaja"><i class="fa fa-edit"></i></a>';
                })
                ->addColumn('action2', function ($row) {
                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->CAJ_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteCaja"><i class="fa fa-trash"></i></a>';
                })
                ->rawColumns(['estado', 'turno_action', 'action1', 'action2'])
                ->make(true);
        }

        $almacenes = Almacen::where('ALM_Status', 1)->get();

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.caja.index', compact('almacenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ALM_Id' => 'nullable|exists:almacen,ALM_Id',
            'CAJ_Nombre' => 'required|string|max:100',
            'CAJ_MontoApertura' => 'nullable|numeric|min:0',
            'CAJ_ProgramacionActiva' => 'nullable|boolean',
            'CAJ_HoraApertura' => 'nullable|date_format:H:i',
            'CAJ_HoraCierre' => 'nullable|date_format:H:i',
        ], [
            'CAJ_Nombre.required' => 'El nombre de la caja es obligatorio.',
        ]);

        $totalCajas = Caja::count();
        $limiteCajas = (int) (tenant('limits')['cash_registers'] ?? 1);
        if ($totalCajas >= $limiteCajas) {
            return response()->json([
                'error' => 'Tu plan alcanzó el límite de ' . $limiteCajas . ' caja(s). Actualiza tu plan para agregar más.'
            ], 422);
        }

        $existe = Caja::where('CAJ_Nombre', $validated['CAJ_Nombre'])->exists();
        if ($existe) {
            return response()->json(['error' => 'Ya existe una caja con ese nombre.'], 422);
        }

        $caja = Caja::create([
            'ALM_Id' => $validated['ALM_Id'] ?? null,
            'CAJ_Nombre' => $validated['CAJ_Nombre'],
            'CAJ_MontoApertura' => $validated['CAJ_MontoApertura'] ?? 0,
            'CAJ_Status' => 1,
            'CAJ_ProgramacionActiva' => $request->boolean('CAJ_ProgramacionActiva'),
            'CAJ_HoraApertura' => $validated['CAJ_HoraApertura'] ?? null,
            'CAJ_HoraCierre' => $validated['CAJ_HoraCierre'] ?? null,
        ]);

        return response()->json(['success' => '¡Caja registrada exitosamente!', 'data' => $caja]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $caja = Caja::find($id);
        return response()->json(['data' => $caja]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $caja = Caja::find($id);
        if (! $caja) {
            return response()->json(['error' => 'La caja solicitada no existe.'], 404);
        }

        $validated = $request->validate([
            'ALM_Id' => 'nullable|exists:almacen,ALM_Id',
            'CAJ_Nombre' => 'required|string|max:100',
            'CAJ_MontoApertura' => 'nullable|numeric|min:0',
            'CAJ_Status' => 'nullable|boolean',
            'CAJ_ProgramacionActiva' => 'nullable|boolean',
            'CAJ_HoraApertura' => 'nullable|date_format:H:i',
            'CAJ_HoraCierre' => 'nullable|date_format:H:i',
        ], [
            'CAJ_Nombre.required' => 'El nombre de la caja es obligatorio.',
        ]);

        $existe = Caja::where('CAJ_Nombre', $validated['CAJ_Nombre'])
            ->where('CAJ_Id', '!=', $id)
            ->exists();
        if ($existe) {
            return response()->json(['error' => 'Ya existe otra caja con ese nombre.'], 422);
        }

        $caja->update([
            'ALM_Id' => $validated['ALM_Id'] ?? null,
            'CAJ_Nombre' => $validated['CAJ_Nombre'],
            'CAJ_MontoApertura' => $validated['CAJ_MontoApertura'] ?? 0,
            'CAJ_Status' => $request->has('CAJ_Status') ? $request->boolean('CAJ_Status') : $caja->CAJ_Status,
            'CAJ_ProgramacionActiva' => $request->boolean('CAJ_ProgramacionActiva'),
            'CAJ_HoraApertura' => $validated['CAJ_HoraApertura'] ?? null,
            'CAJ_HoraCierre' => $validated['CAJ_HoraCierre'] ?? null,
        ]);

        return response()->json(['success' => '¡Caja actualizada exitosamente!', 'data' => $caja]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $caja = Caja::find($id);
        if (! $caja) {
            return response()->json(['error' => 'La caja solicitada no existe.'], 404);
        }

        if ($caja->sesiones()->exists()) {
            return response()->json([
                'error' => 'Esta caja ya tiene historial de aperturas/cierres, no se puede eliminar (perderías el arqueo). Desactívala en vez de borrarla.'
            ], 422);
        }

        $caja->delete();

        return response()->json(['success' => 'Caja eliminada exitosamente.']);
    }
}
