<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\MantenimientoActividadVariada;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MantenimientoActividadVariadaController extends Controller
{
    // VALIDACIONES

    private function validateMantenimientoActividadVariada(Request $request, $id = null)
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
            $data = MantenimientoActividadVariada::join('almacen', 'bahia.ALM_Id', '=', 'almacen.ALM_Id')
                ->join('users', 'bahia.USU_Id', '=', 'users.id')
                ->select('bahia.*', 'almacen.ALM_NombreAlmacen', 'users.name as USU_Nombre')
                ->orderBy('BAH_Id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    return '<a href="javascript:void(0)" data-id="' . $row->BAH_Id . '" class="btn btn-primary btn-sm editMantenimientoActividadVariada"><i class="fa fa-edit"></i></a>';
                })
                ->addColumn('action2', function ($row) {
                    if ($row->BAH_Estado == 'ACT') {
                        return '<a href="javascript:void(0)" data-id="' . $row->BAH_Id . '" class="btn btn-danger btn-sm deleteMantenimientoActividadVariada"><i class="fa fa-trash"></i></a>';
                    }
                    return '<a href="javascript:void(0)" data-id="' . $row->BAH_Id . '" class="btn btn-success btn-sm activarMantenimientoActividadVariada"><i class="fa fa-check"></i></a>';
                })
                ->rawColumns(['action1', 'action2'])
                ->make(true);
        }
        $sedes =  Almacen::get();
        $users =  User::select('id', 'name')->get();
        
        return view('tenant_' . tenant('tipo_negocio') . '.actividades.variadas.index', compact('sedes','users'));
    }

    // REGISTRAR
    public function create()
	{
        $idusu = Auth::user()->id;
        $roles = Auth::user()->getRoleNames();
        
        $ubicaAdministrador = false;
        if ($roles->contains('Admin')) {
            $ubicaAdministrador = true;
        }
        $personal = User::select('id', 'name')->get();


		return view('tenant_' . tenant('tipo_negocio') . '.actividades.variadas.create',["admin"=>$ubicaAdministrador,"personal"=>$personal]);
	}
    public function store(Request $request)
    {
        $validator = $this->validateMantenimientoActividadVariada($request);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $payload = $validator->validated();
        //dd($payload);
        $payload['BAH_Estado'] = 'ACT';
        MantenimientoActividadVariada::create($payload);

        return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada registrado exitosamente.']);
    }

    // EDITAR

    public function edit(string $id)
    {
        $bahia = MantenimientoActividadVariada::find($id);

        if (!$bahia) {
            return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
        }

        return response()->json(['success' => true, 'data' => $bahia]);
    }

    // ACTUALIZAR

    public function update(Request $request, string $id)
    {
        $validator = $this->validateMantenimientoActividadVariada($request, $id);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
        }

        $bahia = MantenimientoActividadVariada::find($id);

        if (!$bahia) {
            return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
        }

        $bahia->update($validator->validated());

        return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada actualizado exitosamente.']);
    }

    // ACTIVAR

    public function activar(string $id)
    {
        try {

            $bahia = MantenimientoActividadVariada::find($id);

            if (!$bahia) {
                return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
            }

            $bahia->BAH_Estado = 'ACT';
            $bahia->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada activado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El bahia falló al activarse.'], 500);

        }
    }

    // ELIMINAR

    public function destroy(string $id)
    {
        try {

            $bahia = MantenimientoActividadVariada::find($id);

            if (!$bahia) {
                return response()->json(['success' => false, 'message' => 'MantenimientoActividadVariada no encontrado.'], 404);
            }

            $bahia->BAH_Estado = 'DESACT';
            $bahia->save();

            return response()->json(['success' => true, 'message' => 'MantenimientoActividadVariada eliminado exitosamente.']);

        } catch (\Exception $e) {

            return response()->json(['success' => false, 'message' => 'El bahia falló al eliminarse.'], 500);

        }
    }
}