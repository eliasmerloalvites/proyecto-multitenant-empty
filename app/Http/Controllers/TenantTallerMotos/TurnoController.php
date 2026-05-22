<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TenantTallerMotos\Turno;
use Illuminate\Support\Facades\Redirect;
use DataTables;
use DB;
use Illuminate\Support\Facades\Auth;

class TurnoController extends Controller
{
	public function __construct()
	{

	}

	public function index(Request $request)
	{
		if ($request->ajax()) {
			$data = DB::table('turno as p')
			->get();
            return Datatables::of($data)
                ->addIndexColumn()
				->addColumn('action1', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->TUR_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editTurno"><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
					if($row->TUR_Estado == 'ACT'){
						$btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->TUR_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteTurno"><i class="fa fa-trash"></i></a>';
					}else{
						$btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->TUR_Id . '" data-original-title="Activar" class="btn btn-success btn-sm activarTurno"><i class="fa fa-check"></i></a>';
					}
                    return $btn;
                })
                ->rawColumns(['action1','action2'])
                ->make(true);
        }

        return view('tenant_'.tenant('tipo_negocio').'.configuracion.turno.index');
	}

	public function create()
	{

	}

	public function store (Request $request)
	{
        $Turno = Turno::create($request->all());
        return response()->json(['success' => 'Turno Registrado Exitosamente.']);
	}

	public function show($id)
	{
		//return view("rrhh.turno.show",["Turno"=>Turno::findOrFail($id)]);
	}

	public function edit(string $tenant_id, $id)
	{
		$turno = Turno::find($id);
        return response()->json(['data' => $turno]);
	}

	public function update(Request $request,string $tenant_id, string $id)
	{
		$Turno=Turno::findOrFail($id);
		$Turno->update($request->all());
		return response()->json(['success' => 'Turno Editado Exitosamente.']);
	}

	public function activar(string $tenant_id, string $id)
	{
		try{
			$turno = Turno::find($id);
			$turno->TUR_Estado = 'ACT';
			$turno->update();
			return response()->json(['success'=> true,'message' => 'Turno Activado Exitosamente.']);
		}
		catch(\Illuminate\Database\QueryException $ex)
		{
			return response()->json(['success'=> false,'message' => 'Turno Fallo al Eliminar.']);
		}
	}

	public function destroy(string $tenant_id, string $id)
	{
		try{
			$turno = Turno::find($id);
			$turno->TUR_Estado = 'DESACT';
			$turno->update();
			return response()->json(['success'=> true,'message' => 'Turno Eliminado Exitosamente.']);
		}
		catch(\Illuminate\Database\QueryException $ex)
		{
			return response()->json(['success'=> false,'message' => 'Turno Fallo al Eliminar.']);
		}

	}
}
