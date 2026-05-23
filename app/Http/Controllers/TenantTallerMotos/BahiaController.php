<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\Bahia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class BahiaController extends Controller
{
    // VALIDACIONES

    private function validateBahia(Request $request, $id = null)
    {
        return Validator::make($request->all(), [
            'BAH_Nombre' => ['required', 'string', 'max:60', 'unique:bahia,BAH_Nombre,' . $id . ',BAH_Id'],
            'ALM_Id' => ['required'],
            'USU_Id' => ['required'],
            'BAH_Estado' => ['nullable', 'in:ACT,DESACT']
        ]);
    }

    // LISTADO

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Bahia::join('almacen', 'bahia.ALM_Id', '=', 'almacen.ALM_Id')
                ->join('users', 'bahia.USU_Id', '=', 'users.id')
                ->select('bahia.*', 'almacen.ALM_NombreAlmacen', 'users.name as USU_Nombre')
                ->orderBy('BAH_Id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    return '<a href="javascript:void(0)" data-id="' . $row->BAH_Id . '" class="btn btn-primary btn-sm editBahia"><i class="fa fa-edit"></i></a>';
                })
                ->addColumn('action2', function ($row) {
                    if ($row->BAH_Estado == 'ACT') {
                        return '<a href="javascript:void(0)" data-id="' . $row->BAH_Id . '" class="btn btn-danger btn-sm deleteBahia"><i class="fa fa-trash"></i></a>';
                    }
                    return '<a href="javascript:void(0)" data-id="' . $row->BAH_Id . '" class="btn btn-success btn-sm activarBahia"><i class="fa fa-check"></i></a>';
                })
                ->rawColumns(['action1', 'action2'])
                ->make(true);
        }
        $sedes =  Almacen::get();
        $users =  User::select('id', 'name')->get();
        
        return view('tenant_' . tenant('tipo_negocio') . '.configuracion.bahia.index', compact('sedes','users'));
    }

    // REGISTRAR

    public function store(Request $request)
    {
        $validator = $this->validateBahia($request);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        //dd($payload);
        $payload['BAH_Estado'] = 'ACT';
        Bahia::create($payload);

        return response()->json(['success' => true, 'message' => 'Bahia registrado exitosamente.']);
    }

    // EDITAR

    public function edit(string $id)
    {
        $bahia = Bahia::find($id);

        if (!$bahia) {
            return response()->json(['success' => false, 'message' => 'Bahia no encontrado.'], 404);
        }

        return response()->json(['success' => true, 'data' => $bahia]);
    }

    // ACTUALIZAR

    public function update(Request $request, string $id)
    {
        $validator = $this->validateBahia($request, $id);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $bahia = Bahia::find($id);

        if (!$bahia) {
            return response()->json(['success' => false, 'message' => 'Bahia no encontrado.'], 404);
        }

        $bahia->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'Bahia actualizado exitosamente.']);
    }

    // ACTIVAR

    public function activar(string $id)
    {
        try {

            $bahia = Bahia::find($id);

            if (!$bahia) {
                return response()->json(['success' => false, 'message' => 'Bahia no encontrado.'], 404);
            }

            $bahia->BAH_Estado = 'ACT';
            $bahia->save();

            return response()->json(['success' => true, 'message' => 'Bahia activado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El bahia falló al activarse.'], 500);

        }
    }

    // ELIMINAR

    public function destroy(string $id)
    {
        try {

            $bahia = Bahia::find($id);

            if (!$bahia) {
                return response()->json(['success' => false, 'message' => 'Bahia no encontrado.'], 404);
            }

            $bahia->BAH_Estado = 'DESACT';
            $bahia->save();

            return response()->json(['success' => true, 'message' => 'Bahia eliminado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El bahia falló al eliminarse.'], 500);

        }
    }
}