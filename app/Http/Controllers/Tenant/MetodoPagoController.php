<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\MetodoPago;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetodoPagoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('metodo_pago as mp')
			->select('mp.*')
			->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-identificador="' . $row->MEP_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editMetodoPago" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->MEP_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteMetodoPago"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
               
                ->rawColumns(['action1','action2'])
                ->make(true);
        }
        return view('tenant_'.tenant('tipo_negocio').'.ventas.metodoPago.index');
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
        $query=MetodoPago::where('MEP_Pago','=',$request->get('MEP_Pago'))->get();
        if($query->count()!=0) //si lo encuentra, osea si no esta vacia
        {
            
            return response()->json(['error' => 'Metodo de Pago ya registrado'], 401);                   
        }
        else{
            $MetodoPago= new MetodoPago();
            $MetodoPago->MEP_Pago=$request->MEP_Pago;
            $MetodoPago->MEP_Status=$request->MEP_Status ?? 1;
            $MetodoPago->save();
            return response()->json(['success' => 'Metodo de Pago Registrado Exitosamente!',compact('MetodoPago')]);    
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
        $MetodoPago = MetodoPago::find($id);
        return response()->json(['data' => $MetodoPago]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $MetodoPago = MetodoPago::find($id);
        $MetodoPago->MEP_Pago = $request->MEP_Pago;
		$MetodoPago->update();

        return response()->json(['success' => 'Metodo de Pago Editado Exitosamente.',compact('MetodoPago')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $MetodoPago = MetodoPago::find($id);
        $MetodoPago->delete();
        return response()->json(['success' => 'Metodo de Pago Eliminado Exitosamente.']);
    }
}
