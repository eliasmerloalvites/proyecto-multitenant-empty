<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class TurnoController extends Controller
{
    // VALIDACIONES

    private function validateTurno(Request $request, $id = null)
    {
        return Validator::make($request->all(), [

            'TUR_Nombre' => ['required', 'string', 'max:60', 'unique:turno,TUR_Nombre,' . $id . ',TUR_Id'],

            'TUR_Descripcion' => ['required', 'string', 'max:60'],

            'TUR_Estado' => ['nullable', 'in:ACT,DESACT']

        ]);
    }

    // LISTADO

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = Turno::orderBy('TUR_Id', 'desc');

            return DataTables::of($data)

                ->addIndexColumn()

                ->addColumn('action1', function ($row) {
                    return '<a href="javascript:void(0)" data-id="' . $row->TUR_Id . '" class="btn btn-primary btn-sm editTurno"><i class="fa fa-edit"></i></a>';
                })

                ->addColumn('action2', function ($row) {

                    if ($row->TUR_Estado == 'ACT') {
                        return '<a href="javascript:void(0)" data-id="' . $row->TUR_Id . '" class="btn btn-danger btn-sm deleteTurno"><i class="fa fa-trash"></i></a>';
                    }

                    return '<a href="javascript:void(0)" data-id="' . $row->TUR_Id . '" class="btn btn-success btn-sm activarTurno"><i class="fa fa-check"></i></a>';
                })

                ->rawColumns(['action1', 'action2'])

                ->make(true);
        }

        return view('tenant_' . tenant('tipo_negocio') . '.configuracion.turno.index');
    }

    // REGISTRAR

    public function store(Request $request)
    {
        $validator = $this->validateTurno($request);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();

        $payload['TUR_Estado'] = 'ACT';

        Turno::create($payload);

        return response()->json(['success' => true, 'message' => 'Turno registrado exitosamente.']);
    }

    // EDITAR

    public function edit(string $tenant_id, string $id)
    {
        $turno = Turno::find($id);

        if (!$turno) {
            return response()->json(['success' => false, 'message' => 'Turno no encontrado.'], 404);
        }

        return response()->json(['success' => true, 'data' => $turno]);
    }

    // ACTUALIZAR

    public function update(Request $request, string $tenant_id, string $id)
    {
        $validator = $this->validateTurno($request, $id);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $turno = Turno::find($id);

        if (!$turno) {
            return response()->json(['success' => false, 'message' => 'Turno no encontrado.'], 404);
        }

        $turno->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Turno actualizado exitosamente.']);
    }

    // ACTIVAR

    public function activar(string $tenant_id, string $id)
    {
        try {

            $turno = Turno::find($id);

            if (!$turno) {
                return response()->json(['success' => false, 'message' => 'Turno no encontrado.'], 404);
            }

            $turno->TUR_Estado = 'ACT';

            $turno->save();

            return response()->json(['success' => true, 'message' => 'Turno activado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El turno falló al activarse.'], 500);

        }
    }

    // ELIMINAR

    public function destroy(string $tenant_id, string $id)
    {
        try {

            $turno = Turno::find($id);

            if (!$turno) {
                return response()->json(['success' => false, 'message' => 'Turno no encontrado.'], 404);
            }

            $turno->TUR_Estado = 'DESACT';

            $turno->save();

            return response()->json(['success' => true, 'message' => 'Turno eliminado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El turno falló al eliminarse.'], 500);

        }
    }
}