<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('cliente')->where('CLI_Status',1)->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-identificador="' . $row->CLI_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editCliente" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->CLI_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteCliente"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->CLI_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeCliente"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
               
                ->rawColumns(['action1','action2','action3'])
                ->make(true);
        }
        return view('gestion.cliente.index');
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
        $query=Cliente::where('CLI_Nombre','=',$request->get('CLI_Nombre'))->where('CLI_NumDocumento','=',$request->get('CLI_NumDocumento'))->get();
        if($query->count()!=0) //si lo encuentra, osea si no esta vacia
        {            
            return response()->json(['error' => 'Cliente ya registrado'], 401);                   
        }
        else{
            $Cliente = Cliente::create($request->all());
            return response()->json(['success' => 'Cliente Registrado Exitosamente!',compact('Cliente')]);    
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::find($id);

        return response()->json(['data' => $cliente]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::find($id);
        return response()->json(['data' => $cliente]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente=Cliente::findOrFail($id);
		$cliente->update($request->all());

        return response()->json(['success' => 'Cliente Editado Exitosamente.',compact('cliente')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::find($id);
        $cliente->CLI_Status = 0;
		$cliente->update();

        return response()->json(['success' => 'Cliente Eliminado Exitosamente.']);
    }
}
