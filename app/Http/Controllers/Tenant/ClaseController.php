<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Clase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClaseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('clase as cl')
			->select('cl.*')
			->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-identificador="' . $row->CLA_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editClase" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->CLA_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteClase"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
               
                ->rawColumns(['action1','action2'])
                ->make(true);
        }
        return view('tenant_'.tenant('tipo_negocio').'.inventario.clase.index');
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
        $query=Clase::where('CLA_Nombre','=',$request->get('CLA_Nombre'))->get();
        if($query->count()!=0) //si lo encuentra, osea si no esta vacia
        {
            
            return response()->json(['error' => 'Clase ya registrado'], 401);                   
        }
        else{
            $Clase= new Clase();
            $Clase->CLA_Nombre=$request->CLA_Nombre;
            $Clase->save();
            return response()->json(['success' => 'Clase Registrado Exitosamente!',compact('Clase')]);    
        }
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
        $clase = Clase::find($id);
        return response()->json(['data' => $clase]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,  string $id)
    {
        $clase = Clase::find($id);
        $clase->CLA_Nombre = $request->CLA_Nombre;
		$clase->update();

        return response()->json(['success' => 'Clase Editado Exitosamente.',compact('clase')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $clase = Clase::find($id);
        $clase->delete();
        return response()->json(['success' => 'Clase Eliminado Exitosamente.']);
    }
}
