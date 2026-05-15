<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Categoria;
use App\Models\Tenant\Clase;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
			$data = DB::table('categoria as ct')
			->join('clase as cl','ct.CLA_Id','=','cl.CLA_Id')
            ->select('ct.*','cl.CLA_Nombre')
            ->get();
            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->CAT_Id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editCategoria" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->CAT_Id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteCategoria"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->CAT_Id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeCategoria"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })               
                ->rawColumns(['action1','action2','action3'])
                ->make(true);
        }

        $clases = DB::table('clase')->get();
        return view('tenant_generico.inventario.categoria.index', compact('clases'));
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
    public function store(Request $request,string $tenant_id)
    {
        try {
            DB::beginTransaction();

            $query=Categoria::where('CAT_Nombre','=',$request->get('CAT_Nombre'))->get();
            if($query->count()!=0) //si lo encuentra, osea si no esta vacia
            {
                
                return response()->json(['error' => 'Categoria ya registrado'], 401);                   
            }
            else{
                $categoria=new Categoria();
                $categoria->CAT_Nombre=$request->CAT_Nombre;
                $categoria->CLA_Id=$request->CLA_Id;
                $categoria->save();

                $ubicacionNegocio = "";
                $id = null;
                if (tenant()) {
                    // Estás en el contexto de un TENANT
                    $id = tenant('id');
                    $ubicacionNegocio = tenant('tipo_negocio') ;
                }

                $path = public_path('storage/' .$ubicacionNegocio .'/' .$id .'/archivos/categoria/');
                $file = $request->file('file');
                if($file){
                    // Crear carpeta si no existe
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }

                    $extension = $file->getClientOriginalExtension();
                    $fileName = $categoria->CAT_Id . '.' . $extension;
                    $file->move($path, $fileName);

                    $updatecategoria = DB::table('categoria')
                    ->where('CAT_Id', $categoria->CAT_Id)
                    ->update(['CAT_Imagen' => $fileName]);

                }
                   
            }

            DB::commit();
        } catch (Exception $e)
        {
            DB::rollback();
        }

        
        return response()->json(['success' => 'Categoria Registrado Exitosamente!',compact('categoria')]); 
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $tenant_id, string $id)
    {
        $categoria = DB::table('categoria as ct')
			->join('clase as cl','ct.CLA_Id','=','cl.CLA_Id')
            ->select('ct.*','cl.CLA_Nombre')
            ->where('CAT_Id',$id)
            ->first();
        $imagen="";
        if($categoria->CAT_Imagen){
            $id = tenant('id');
            $ubicacionNegocio = tenant('tipo_negocio') ;
            $imagen = '/storage/' .$ubicacionNegocio .'/' .$id .'/archivos/categoria/'.$categoria->CAT_Imagen.'?' . uniqid();
        }
        return response()->json(['data' => $categoria,'imagen'=> $imagen]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $tenant_id, string $id)
    {
        $categoria = Categoria::find($id);
        return response()->json(['data' => $categoria]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $tenant_id, string $id)
    {
        try {
            DB::beginTransaction();

            $categoria = Categoria::find($id);
            $categoria->CAT_Nombre = $request->CAT_Nombre;
            $categoria->CLA_Id = $request->CLA_Id;
            $categoria->update();

            $ubicacionNegocio = "";
            $id = null;
            if (tenant()) {
                // Estás en el contexto de un TENANT
                $id = tenant('id');
                $ubicacionNegocio = tenant('tipo_negocio') ;
            }

            $path = public_path('storage/' .$ubicacionNegocio .'/' .$id .'/archivos/categoria/');
            $file = $request->file('file');
            if($file){
                if (!file_exists($path)) {
                    mkdir($path, 0777, true);
                }

                $extension = $file->getClientOriginalExtension();
                $fileName = $categoria->CAT_Id . '.' . $extension;
                $file->move($path, $fileName);
                
                $updatecategoria = DB::table('categoria')
                ->where('CAT_Id', $categoria->CAT_Id)
                ->update(['CAT_Imagen' => $fileName]);
            }
        
            DB::commit();
        } catch (Exception $e)
        {
            DB::rollback();
        }

        return response()->json(['success' => 'Categoria Editado Exitosamente.',compact('categoria')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $tenant_id, string $id)
    {
        $categoria = Categoria::find($id);
        $categoria->delete();
        return response()->json(['success' => 'Categoria Eliminado Exitosamente.']);
    }
}
