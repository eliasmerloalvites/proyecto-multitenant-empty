<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    const PAGINATION=10;
    public function index(Request $request)
    {
        
        $permissionsGrouped  = Permission::Where('estado','=',1)->get()->groupBy('group_name');
        //$permisos = Permission::Where('estado','=',1)->get();

        if ($request->ajax()) {
			$data = Role::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editRole" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteRole"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeRole"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action1','action2','action3'])
                ->make(true);
        }
        return view('seguridad.roles.index',compact('permissionsGrouped'));
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
        // $data=request()->validate([
        //     'name'=>'required',
        //     'guard_name'=>'required',

        // ],
        // [
        //     'name.required'=>'Ingrese nombre',
        //     'guard_name.required'=>'Ingrese Email',
        // ]);
        $query=Role::where('name','=',$request->get('name'))->get();
        if($query->count()!=0) //si lo encuentra, osea si no esta vacia
        {
            // return back()->withErrors(['name'=> 'Rol ya registrado'])
            // ->withInput(request(['name','guard_name']));          
            return response()->json(['error' => 'Rol ya registrado'], 401);         
        }
        else{
        

            if ($request->has('accesototal') && $request->has('accesocero')) {
                // El checkbox "accesototal" y accesocero están seleccionado
                
            //return redirect()->route('role.index')->with('datos','No se pueden seleccionar los dos permisos especiales');
            return response()->json(['error' => 'No se pueden seleccionar los dos permisos especiales'], 401);         

            }
            // if ($request->has('accesototal') && $request->has('permisos')) {
            //     //return redirect()->route('role.index')->with('datos','Tiene que seleccionar o bien un permiso especial ó alguno(s) de la lista de permisos');
            //     return response()->json(['error' => 'Tiene que seleccionar o bien un permiso especial ó alguno(s) de la lista de permisos'], 401);         

            // }
            if ($request->has('accesocero') && $request->has('permisos')) {
                //return redirect()->route('role.index')->with('datos','Tiene que seleccionar o bien un permiso especial ó alguno(s) de la lista de permisos');
                return response()->json(['error' => 'No puede seleccionar el permiso sin acceso y a la vez algun permiso de la lista'], 401);         

            }
            
            //$permisosSeleccionados = $request->input('permisos');
            $role = new Role();
            $role->name = $request->name;
            $role->save();

            if($request->has('accesototal') && !$request->has('accesocero'))
            {
                $permisos = Permission::all();

                // Recorrer los permisos y obtener sus IDs
                $permisosIds = $permisos->pluck('id')->toArray();

                // Asignar todos los permisos al rol
            // $role->syncPermissions($permisosIds);
                $role->permissions()->sync($permisosIds);
                
                //return redirect()->route('role.index')->with('datos','Rol agregado con todos los permisos ...!');
                return response()->json(['success' => 'Rol agregado con todos los permisos ...!',compact('role')]);

            }
            if($request->has('accesocero') && !$request->has('accesototal') && !$request->has('permisos'))
            {
                //return redirect()->route('role.index')->with('datos','Rol agregado sin permisos ...!');
                return response()->json(['success' => 'Rol agregado sin permisos ...!',compact('role')]);

            }

            $role->permissions()->sync($request->permisos);

            //$role->permisos()->sync($request->input('permisos'));
            

           // return redirect()->route('role.index')->with('datos','Rol agregado y permisos asignados correctamente ...!');
           return response()->json(['success' => 'Rol agregado y permisos asignados correctamente ...!',compact('role')]);

        }

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $role = Role::find($id);

        return response()->json(['data' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $role = Role::find($id);
        $permisos = $role->permissions;
        return response()->json(['data' => $role,'data2' => $permisos]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        if ($request->has('accesototal') && $request->has('accesocero')) {
            // El checkbox "accesototal" y accesocero están seleccionado
            
        //return redirect()->route('role.index')->with('datos','No se pueden seleccionar los dos permisos especiales');
        return response()->json(['error' => 'No se pueden seleccionar los dos permisos especiales'], 401);         

        }

        if ($request->has('accesocero') && $request->has('permisos')) {
            //return redirect()->route('role.index')->with('datos','Tiene que seleccionar o bien un permiso especial ó alguno(s) de la lista de permisos');
            return response()->json(['error' => 'No puede seleccionar el permiso sin acceso y a la vez algun permiso de la lista'], 401);         

        }

        $role = Role::find($id);


        if($request->has('accesototal') && !$request->has('accesocero'))
        {
            $permisos = Permission::all();

            // Recorrer los permisos y obtener sus IDs
            $permisosIds = $permisos->pluck('id')->toArray();

            // Asignar todos los permisos al rol
        // $role->syncPermissions($permisosIds);
            $role->permissions()->sync($permisosIds);
            
            //return redirect()->route('role.index')->with('datos','Rol agregado con todos los permisos ...!');
            return response()->json(['success' => 'Rol editado Exitosamente con todos los permisos.',compact('role')]);

        }
        if($request->has('accesocero') && !$request->has('accesototal') && !$request->has('permisos'))
        {
            //return redirect()->route('role.index')->with('datos','Rol agregado sin permisos ...!');
            return response()->json(['success' => 'Rol Editado Exitosamente sin permisos.',compact('role')]);

        }

        $role->permissions()->sync($request->permisos);

		return response()->json(['success' => 'Rol Editado Exitosamente.',compact('role')]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $role = Role::find($id);
        $role->estadorol = 0;
        $role->save();
       // return redirect()->route('role.index')->with('datos','Rol eliminado ...!');
        
        return response()->json(['success' => 'Rol Eliminado Exitosamente.']);
    }
}
