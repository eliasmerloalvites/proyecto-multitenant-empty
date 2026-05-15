<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\TipoGasto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipoGastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('tipo_gasto as cl')
			->select('cl.*')
			->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-identificador="' . $row->TG_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editTipoGasto" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->TG_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteTipoGasto"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
               
                ->rawColumns(['action1','action2'])
                ->make(true);
        }
        
        return view('tenant_generico.compras.tipogasto.index');
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
        $query=TipoGasto::where('TG_Descripcion','=',$request->get('TG_Descripcion'))->get();
        if($query->count()!=0) //si lo encuentra, osea si no esta vacia
        {
            
            return response()->json(['error' => 'TipoGasto ya registrado'], 401);                   
        }
        else{
            $TipoGasto= new TipoGasto();
            $TipoGasto->TG_Descripcion=$request->TG_Descripcion;
            $TipoGasto->save();
            return response()->json(['success' => 'Tipo Gasto Registrado Exitosamente!',compact('TipoGasto')]);    
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $tenant_id, string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $tenant_id, string $id)
    {
        $tipogasto = TipoGasto::find($id);
        return response()->json(['data' => $tipogasto]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $tenant_id, string $id)
    {
        $tipogasto = TipoGasto::find($id);
        $tipogasto->TG_Descripcion = $request->TG_Descripcion;
		$tipogasto->update();

        return response()->json(['success' => 'Tipo Gasto Editado Exitosamente.',compact('tipogasto')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $tenant_id, string $id)
    {
        $tipogasto = TipoGasto::find($id);
        $tipogasto->delete();
        return response()->json(['success' => 'Tipo Gasto Eliminado Exitosamente.']);
    }
}
