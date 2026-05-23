<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('almacen as al')
			->select('al.*')
			->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-identificador="' . $row->ALM_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editAlmacen" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->ALM_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteAlmacen"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
               
                ->rawColumns(['action1','action2'])
                ->make(true);
        }
        return view('tenant_'.tenant('tipo_negocio').'.configuracion.sede.index');
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
        $query=Almacen::where('ALM_Ruc','=',$request->get('ALM_Ruc'))
        ->where('ALM_NombreAlmacen', $request->get('ALM_NombreAlmacen'))
        ->where('ALM_Nombre', $request->get('ALM_Nombre'))
        ->first();

        if($query) //si lo encuentra, osea si no esta vacia
        {
            return response()->json(['error' => 'Ya se encuentra registrado esta sede en la empresa ingresada'], 422);                   
        }
        else{
            $Almacen= new Almacen();
            $Almacen->ALM_Nombre=$request->ALM_Nombre;
            $Almacen->ALM_NombreAlmacen=$request->ALM_NombreAlmacen;
            $Almacen->ALM_Direccion=$request->ALM_Direccion;
            $Almacen->ALM_Ruc=$request->ALM_Ruc;
            $Almacen->ALM_Celular=$request->ALM_Celular;
            $Almacen->ALM_Status=$request->ALM_Status ?? 1 ;
            $Almacen->save();
            return response()->json(['success' => 'Sede Registrado Exitosamente!',compact('Almacen')]);    
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
        $almacen = Almacen::find($id);
        return response()->json(['data' => $almacen]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $almacen = Almacen::find($id);
        /* $almacen->ALM_Nombre=$request->ALM_Nombre;
        $almacen->ALM_Ruc=$request->ALM_Ruc; */
        $almacen->ALM_NombreAlmacen=$request->ALM_NombreAlmacen;
        $almacen->ALM_Direccion=$request->ALM_Direccion;
        $almacen->ALM_Celular=$request->ALM_Celular;
		$almacen->update();

        return response()->json(['success' => 'Sede Editado Exitosamente.',compact('almacen')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $almacen = Almacen::find($id);
        $almacen->delete();
        return response()->json(['success' => 'Sede Eliminado Exitosamente.']);
    }
}
