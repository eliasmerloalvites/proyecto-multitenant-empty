<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\Horario;
use App\Models\TenantTallerMotos\Turno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class HorarioController extends Controller
{
    // VALIDACIONES

    private function validateHorario(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'HOR_Dia' => ['required', 'string', 'max:20'],
            'HOR_Detalle' => ['required', 'string', 'max:100'],
            'ALM_Id' => ['required'],
            'TUR_Id' => ['required'],
            'HOR_Estado' => ['nullable', 'in:ACT,DESACT']
        ]);
    }

    // LISTADO

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Horario::join('almacen', 'horario.ALM_Id', '=', 'almacen.ALM_Id')
                ->join('turno', 'horario.TUR_Id', '=', 'turno.TUR_Id')
                ->select('horario.*', 'almacen.ALM_NombreAlmacen', 'turno.TUR_Nombre as TUR_Nombre')
                ->orderBy('HOR_Id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    return '<a href="javascript:void(0)" data-id="' . $row->HOR_Id . '" class="btn btn-primary btn-sm editHorario"><i class="fa fa-edit"></i></a>';
                })
                ->addColumn('action2', function ($row) {
                    if ($row->HOR_Estado == 'ACT') {
                        return '<a href="javascript:void(0)" data-id="' . $row->HOR_Id . '" class="btn btn-danger btn-sm deleteHorario"><i class="fa fa-trash"></i></a>';
                    }
                    return '<a href="javascript:void(0)" data-id="' . $row->HOR_Id . '" class="btn btn-success btn-sm activarHorario"><i class="fa fa-check"></i></a>';
                })
                ->rawColumns(['action1', 'action2'])
                ->make(true);
        }
        $sedes =  Almacen::get();
        $turnos = Turno::select('TUR_Id as id', 'TUR_Nombre as name')->get();
        
        return view('tenant_' . tenant('tipo_negocio') . '.configuracion.horario.index', compact('sedes','turnos'));
    }

    // REGISTRAR

    public function store(Request $request)
    {
        $validator = $this->validateHorario($request);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        //dd($payload);
        $payload['HOR_Estado'] = 'ACT';
        Horario::create($payload);

        return response()->json(['success' => true, 'message' => 'Horario registrado exitosamente.']);
    }

    // EDITAR

    public function edit(string $id)
    {
        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json(['success' => false, 'message' => 'Horario no encontrado.'], 404);
        }

        return response()->json(['success' => true, 'data' => $horario]);
    }

    // ACTUALIZAR

    public function update(Request $request, string $id)
    {
        $validator = $this->validateHorario($request, $id);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $horario = Horario::find($id);

        if (!$horario) {
            return response()->json(['success' => false, 'message' => 'Horario no encontrado.'], 404);
        }

        $horario->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Horario actualizado exitosamente.']);
    }

    // ACTIVAR

    public function activar(string $id)
    {
        try {

            $horario = Horario::find($id);

            if (!$horario) {
                return response()->json(['success' => false, 'message' => 'Horario no encontrado.'], 404);
            }

            $horario->HOR_Estado = 'ACT';
            $horario->save();

            return response()->json(['success' => true, 'message' => 'Horario activado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El horario falló al activarse.'], 500);

        }
    }

    // ELIMINAR

    public function destroy(string $id)
    {
        try {

            $horario = Horario::find($id);

            if (!$horario) {
                return response()->json(['success' => false, 'message' => 'Horario no encontrado.'], 404);
            }

            $horario->HOR_Estado = 'DESACT';
            $horario->save();

            return response()->json(['success' => true, 'message' => 'Horario eliminado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El horario falló al eliminarse.'], 500);

        }
    }
}