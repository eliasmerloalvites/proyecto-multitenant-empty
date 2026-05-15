<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Gasto;
use App\Models\Tenant\Proveedor;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GastoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('gasto as g')
            ->join('tipo_gasto as tg', 'tg.TG_Id', '=', 'g.TG_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'g.MEP_Id')
			->select('g.*','tg.*','mp.*')
			->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-identificador="' . $row->GAS_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editGasto" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->GAS_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteGasto"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->GAS_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeGasto"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })                
                ->rawColumns(['action1','action2','action3'])
                ->make(true);
        }
        $metodo_pago = DB::table('metodo_pago')->orderBy('MEP_Pago', 'asc')->get();
        $tipo_gasto = DB::table('tipo_gasto')->orderBy('TG_Descripcion', 'asc')->get();
        $proveedor = Proveedor::all();
        return view('tenant_generico.compras.gasto.index',compact('metodo_pago','tipo_gasto','proveedor'));
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
        try {
            DB::beginTransaction();

            $idusu = Auth::user()->id;
            $fecha=$request->get('GAS_Fecha');
            $mytime = Carbon::now('America/Lima');
            $horaactual = $mytime->toTimeString();
            $request->merge([
                'ALM_Id' => 1,
                'GAS_Status' => 1,
                'USU_Id' => $idusu,
                'GAS_Fecha' =>  $fecha.' '.$horaactual
            ]);

            $gasto = Gasto::create($request->all());

            $ubicacionNegocio = "";
            $id = null;
            if (tenant()) {
                // Estás en el contexto de un TENANT
                $id = tenant('id');
                $ubicacionNegocio = tenant('tipo_negocio') ;
            }
            $path = public_path('storage/' .$ubicacionNegocio .'/' .$id .'/archivos/gasto/');

            $file = $request->file('file');
            if($file){
            if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $gasto->GAS_Id . '.' . $extension;
                $file->move($path, $fileName);
                
                $updategasto = DB::table('gasto')
                ->where('GAS_Id', $gasto->GAS_Id)
                ->update(['GAS_Documento' => $fileName]);

            }

            DB::commit();
        } catch (Exception $e)
        {
            DB::rollback();
            $e->getMessage();
            return response()->json(['error' => $e->getMessage(),]);
        }

        return response()->json(['success' => 'Gasto Registrado Exitosamente!']); 
    }

    /**
     * Display the specified resource.
     */
    public function show(string $tenant_id, string $id)
    {
        $gasto = DB::table('gasto as g')
            ->join('tipo_gasto as tg', 'tg.TG_Id', '=', 'g.TG_Id')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'g.MEP_Id')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'g.PROV_Id')
            ->select('g.*','tg.*','mp.*','p.PROV_RazonSocial')
            ->where('GAS_Id',$id)
            ->first();

        $imagen="";
        if($gasto->GAS_Documento){
            $imagen = '/archivos/gasto/'.$gasto->GAS_Documento.'?' . uniqid();
        }
        return response()->json(['data' => $gasto,'imagen'=> $imagen]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $tenant_id, string $id)
    {
        $gasto = Gasto::find($id);
        return response()->json(['data' => $gasto]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $tenant_id, string $id)
    {
        try {
            DB::beginTransaction();

            $gasto = Gasto::find($id);
            $gasto->GAS_Descripcion = $request->GAS_Descripcion;
            $gasto->GAS_Monto = $request->GAS_Monto;
            $gasto->TG_Id = $request->TG_Id;
            $gasto->MEP_Id = $request->MEP_Id;
            $gasto->PROV_Id = $request->PROV_Id;
            $gasto->GAS_Afecta = $request->GAS_Afecta;
            $gasto->update();

            $ubicacionNegocio = "";
            $id = null;
            if (tenant()) {
                // Estás en el contexto de un TENANT
                $id = tenant('id');
                $ubicacionNegocio = tenant('tipo_negocio') ;
            }

            $path = public_path('storage/' .$ubicacionNegocio .'/' .$id .'/archivos/gasto/');
            $file = $request->file('file');
            if($file){
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $gasto->GAS_Id . '.' . $extension;
                $file->move($path, $fileName);
                
                $gasto = DB::table('gasto')
                ->where('GAS_Id', $gasto->GAS_Id)
                ->update(['GAS_Documento' => $fileName]);
            }

            DB::commit();
        } catch (Exception $e)
        {
            DB::rollback();
        }

        return response()->json(['success' => 'Gasto Editado Exitosamente.',compact('gasto')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $tenant_id, string $id)
    {
        $gasto = Gasto::find($id);
        $gasto->delete();
        return response()->json(['success' => 'Tipo Gasto Eliminado Exitosamente.']);
    }
}
