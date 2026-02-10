<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class PermisoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    const PAGINATION=10;
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('permissions as p')
            ->where('p.estado','=',1)
			->select('p.*')
			->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editPermiso" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deletePermiso"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyePermiso"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action1','action2','action3'])
                ->make(true);
        }
        return view('seguridad.permisos.index');

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
        $query=Permission::where('name','=',$request->get('name'))->get();
        if($query->count()!=0) //si lo encuentra, osea si no esta vacia
        {
            
            return response()->json(['error' => 'Permiso ya registrado'], 401);                   
        }
        else{
            $permiso = new Permission();
            $permiso->group_name = $request->group_name;
            $permiso->name = $request->name;
            $permiso->nombre = $request->nombre;
            $permiso->description = $request->description;
            $permiso->estado = 1;
            $permiso->save();
            //return redirect()->route('permiso.index')->with('datos','Prrmisos agregado con todos los permisos ...!');

            return response()->json(['success' => 'Permiso Registrado Exitosamente!',compact('permiso')]);
            
            
            
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $permiso = Permission::find($id);

        return response()->json(['data' => $permiso]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permiso = Permission::find($id);
        
        return response()->json(['data' => $permiso]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $permiso = Permission::find($id);
        $permiso->group_name = $request->group_name;
        $permiso->nombre = $request->nombre;
        $permiso->description = $request->description;		
		$permiso->update();

        return response()->json(['success' => 'Permiso Editado Exitosamente.',compact('permiso')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permiso = Permission::find($id);
        $permiso->estado = 0;
        $permiso->save();

        return response()->json(['success' => 'Permiso Eliminado Exitosamente.']);
    }
}
