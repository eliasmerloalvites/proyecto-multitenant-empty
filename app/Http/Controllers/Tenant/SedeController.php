<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\Almacen;
use App\Models\Tenant\EmpresaFacturacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SedeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('almacen as al')
                ->join('empresa_facturacion as emp', 'al.EMP_Id', '=', 'emp.id')
                ->where('emp.tenant_id', tenant('id'))
                ->select('al.*', 'emp.ruc as ALM_Ruc', 'emp.razon_social as ALM_Nombre')
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

                ->rawColumns(['action1', 'action2'])
                ->make(true);
        }
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
        return view('tenant_' . tenant('tipo_negocio') . '.configuracion.sede.index', compact('empresa'));
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
        // 1. Validaciones de entrada
        $validated = $request->validate([
            'EMP_Id' => ['required', 'exists:empresa_facturacion,id'],
            'ALM_NombreAlmacen' => [
                'required',
                'string',
                'max:255',
                // Valida duplicado por nombre dentro de la misma empresa
                Rule::unique('almacen', 'ALM_NombreAlmacen')->where(function ($query) use ($request) {
                    return $query->where('EMP_Id', $request->EMP_Id);
                }),
            ],
            'ALM_Direccion'           => ['nullable', 'string', 'max:255'],
            'ALM_Referencia'          => ['nullable', 'string', 'max:255'],
            'ALM_Latitud'             => ['nullable', 'numeric', 'between:-90,90'],
            'ALM_Longitud'            => ['nullable', 'numeric', 'between:-180,180'],
            'ALM_Departamento'        => ['nullable', 'string', 'max:100'],
            'ALM_Provincia'           => ['nullable', 'string', 'max:100'],
            'ALM_Distrito'            => ['nullable', 'string', 'max:100'],
            'ALM_Ubigeo'              => ['nullable', 'string', 'max:6'],
            'ALM_Telefono'            => ['nullable', 'string', 'max:20'],
            'ALM_Celular'             => ['nullable', 'string', 'max:20'],
            'ALM_Email'               => ['nullable', 'email', 'max:150'],
            'ALM_CodigoSunat'         => ['nullable', 'string', 'max:10'],
            'ALM_SerieFactura'        => ['nullable', 'string', 'max:4'],
            'ALM_SerieBoleta'         => ['nullable', 'string', 'max:4'],
            'ALM_SerieNotaCredito'    => ['nullable', 'string', 'max:4'],
            'ALM_SerieNotaDebito'     => ['nullable', 'string', 'max:4'],
            'ALM_SerieGuiaRemision'   => ['nullable', 'string', 'max:4'],
            'ALM_SerieNotaVenta'      => ['nullable', 'string', 'max:4'],
            'ALM_EsPrincipal'         => ['nullable', 'boolean'],
            'ALM_PermitirVentaSinStock' => ['nullable', 'boolean'],
            'ALM_Status'              => ['nullable', 'in:0,1,true,false'],
        ], [
            'ALM_NombreAlmacen.unique' => 'Ya se encuentra registrada esta sede en la empresa seleccionada.',
            'EMP_Id.required'          => 'La empresa es obligatoria.',
            'ALM_NombreAlmacen.required' => 'El nombre de la sede es obligatorio.',
            'ALM_Latitud.between'      => 'La latitud debe ser un rango válido entre -90 y 90.',
            'ALM_Longitud.between'     => 'La longitud debe ser un rango válido entre -180 y 180.',
        ]);

        // 'Sede' (aquí) y 'Almacén' (AlmacenController) son literalmente la
        // misma tabla `almacen` — por eso se valida contra el mismo límite
        // de plan (limits.branches) en ambos controllers, para que no se
        // pueda pasar el límite creando desde la otra pantalla.
        $totalSedes = DB::table('almacen')->count();
        $limiteSedes = (int) (tenant('limits')['branches'] ?? 1);
        if ($totalSedes >= $limiteSedes) {
            return response()->json([
                'error' => 'Tu plan alcanzó el límite de ' . $limiteSedes . ' local(es)/sede(s). Actualiza tu plan para agregar más.'
            ], 422);
        }

        try {
            $almacen = DB::transaction(function () use ($request, $validated) {

                // Si la nueva sede se marca como principal, desmarcamos las demás sedes de la empresa
                if ($request->boolean('ALM_EsPrincipal')) {
                    Almacen::where('EMP_Id', $request->EMP_Id)
                        ->update(['ALM_EsPrincipal' => 0]);
                }

                return Almacen::create([
                    'EMP_Id'                    => $validated['EMP_Id'],
                    'ALM_NombreAlmacen'         => trim($validated['ALM_NombreAlmacen']),
                    'ALM_Direccion'             => $validated['ALM_Direccion'] ?? null,
                    'ALM_Referencia'            => $validated['ALM_Referencia'] ?? null,
                    'ALM_Latitud'               => $validated['ALM_Latitud'] ?? null,
                    'ALM_Longitud'              => $validated['ALM_Longitud'] ?? null,
                    'ALM_Departamento'          => $validated['ALM_Departamento'] ?? null,
                    'ALM_Provincia'             => $validated['ALM_Provincia'] ?? null,
                    'ALM_Distrito'              => $validated['ALM_Distrito'] ?? null,
                    'ALM_Ubigeo'                => $validated['ALM_Ubigeo'] ?? null,
                    'ALM_Telefono'              => $validated['ALM_Telefono'] ?? null,
                    'ALM_Celular'               => $validated['ALM_Celular'] ?? null,
                    'ALM_Email'                 => $validated['ALM_Email'] ?? null,
                    'ALM_CodigoSunat'           => $validated['ALM_CodigoSunat'] ?? null,
                    'ALM_SerieFactura'          => isset($validated['ALM_SerieFactura']) ? strtoupper($validated['ALM_SerieFactura']) : null,
                    'ALM_SerieBoleta'           => isset($validated['ALM_SerieBoleta']) ? strtoupper($validated['ALM_SerieBoleta']) : null,
                    'ALM_SerieNotaCredito'      => isset($validated['ALM_SerieNotaCredito']) ? strtoupper($validated['ALM_SerieNotaCredito']) : null,
                    'ALM_SerieNotaDebito'       => isset($validated['ALM_SerieNotaDebito']) ? strtoupper($validated['ALM_SerieNotaDebito']) : null,
                    'ALM_SerieGuiaRemision'     => isset($validated['ALM_SerieGuiaRemision']) ? strtoupper($validated['ALM_SerieGuiaRemision']) : null,
                    'ALM_SerieNotaVenta'        => isset($validated['ALM_SerieNotaVenta']) ? strtoupper($validated['ALM_SerieNotaVenta']) : null,
                    'ALM_EsPrincipal'           => $request->boolean('ALM_EsPrincipal') ? 1 : 0,
                    'ALM_PermitirVentaSinStock' => $request->boolean('ALM_PermitirVentaSinStock') ? 1 : 0,
                    'ALM_Status'                => $request->input('ALM_Status', 1),
                ]);
            });

            return response()->json([
                'success' => '¡Sede registrada exitosamente!',
                'data'    => $almacen
            ], 201);
        } catch (\Throwable $e) {
            Log::error('Error al guardar la sede: ' . $e->getMessage(), [
                'exception' => $e,
                'request'   => $request->all()
            ]);

            return response()->json([
                'error' => 'Ocurrió un error interno al intentar guardar la sede. Por favor, reintente.'
            ], 500);
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
        // 1. Buscar el registro o lanzar error 404 si no existe
        $almacen = Almacen::find($id);

        if (!$almacen) {
            return response()->json([
                'error' => 'La sede solicitada no existe o fue eliminada.'
            ], 404);
        }

        // 2. Validaciones de entrada
        $validated = $request->validate([
            'EMP_Id' => ['required', 'exists:empresa_facturacion,id'],
            'ALM_NombreAlmacen' => [
                'required',
                'string',
                'max:255',
                // Valida duplicado por nombre en la misma empresa, ignorando el registro actual
                Rule::unique('almacen', 'ALM_NombreAlmacen')
                    ->where(function ($query) use ($request) {
                        return $query->where('EMP_Id', $request->EMP_Id);
                    })
                    ->ignore($id, 'ALM_Id'), // Reemplaza 'ALM_Id' si tu PK se llama distinto
            ],
            'ALM_Direccion'           => ['nullable', 'string', 'max:255'],
            'ALM_Referencia'          => ['nullable', 'string', 'max:255'],
            'ALM_Latitud'             => ['nullable', 'numeric', 'between:-90,90'],
            'ALM_Longitud'            => ['nullable', 'numeric', 'between:-180,180'],
            'ALM_Departamento'        => ['nullable', 'string', 'max:100'],
            'ALM_Provincia'           => ['nullable', 'string', 'max:100'],
            'ALM_Distrito'            => ['nullable', 'string', 'max:100'],
            'ALM_Ubigeo'              => ['nullable', 'string', 'max:6'],
            'ALM_Telefono'            => ['nullable', 'string', 'max:20'],
            'ALM_Celular'             => ['nullable', 'string', 'max:20'],
            'ALM_Email'               => ['nullable', 'email', 'max:150'],
            'ALM_CodigoSunat'         => ['nullable', 'string', 'max:10'],
            'ALM_SerieFactura'        => ['nullable', 'string', 'max:4'],
            'ALM_SerieBoleta'         => ['nullable', 'string', 'max:4'],
            'ALM_SerieNotaCredito'    => ['nullable', 'string', 'max:4'],
            'ALM_SerieNotaDebito'     => ['nullable', 'string', 'max:4'],
            'ALM_SerieGuiaRemision'   => ['nullable', 'string', 'max:4'],
            'ALM_SerieNotaVenta'      => ['nullable', 'string', 'max:4'],
            'ALM_EsPrincipal'         => ['nullable', 'boolean'],
            'ALM_PermitirVentaSinStock' => ['nullable', 'boolean'],
            'ALM_Status'              => ['nullable', 'in:0,1,true,false'],
        ], [
            'ALM_NombreAlmacen.unique'   => 'Ya existe otra sede registrada con el mismo nombre en esta empresa.',
            'EMP_Id.required'            => 'La empresa es obligatoria.',
            'ALM_NombreAlmacen.required' => 'El nombre de la sede es obligatorio.',
            'ALM_Latitud.between'        => 'La latitud debe ser un rango válido entre -90 y 90.',
            'ALM_Longitud.between'       => 'La longitud debe ser un rango válido entre -180 y 180.',
        ]);

        try {
            DB::transaction(function () use ($request, $validated, $almacen) {

                // Si la sede actualizada pasa a ser principal, desmarcamos las demás de la misma empresa
                if ($request->boolean('ALM_EsPrincipal')) {
                    Almacen::where('EMP_Id', $request->EMP_Id)
                        ->where('ALM_Id', '!=', $almacen->ALM_Id) // Reemplaza 'ALM_Id' si tu PK se llama distinto
                        ->update(['ALM_EsPrincipal' => 0]);
                }

                // Actualizamos la entidad
                $almacen->update([
                    'EMP_Id'                    => $validated['EMP_Id'],
                    'ALM_NombreAlmacen'         => trim($validated['ALM_NombreAlmacen']),
                    'ALM_Direccion'             => $validated['ALM_Direccion'] ?? null,
                    'ALM_Referencia'            => $validated['ALM_Referencia'] ?? null,
                    'ALM_Latitud'               => $validated['ALM_Latitud'] ?? null,
                    'ALM_Longitud'              => $validated['ALM_Longitud'] ?? null,
                    'ALM_Departamento'          => $validated['ALM_Departamento'] ?? null,
                    'ALM_Provincia'             => $validated['ALM_Provincia'] ?? null,
                    'ALM_Distrito'              => $validated['ALM_Distrito'] ?? null,
                    'ALM_Ubigeo'                => $validated['ALM_Ubigeo'] ?? null,
                    'ALM_Telefono'              => $validated['ALM_Telefono'] ?? null,
                    'ALM_Celular'               => $validated['ALM_Celular'] ?? null,
                    'ALM_Email'                 => $validated['ALM_Email'] ?? null,
                    'ALM_CodigoSunat'           => $validated['ALM_CodigoSunat'] ?? null,
                    'ALM_SerieFactura'          => isset($validated['ALM_SerieFactura']) ? strtoupper($validated['ALM_SerieFactura']) : null,
                    'ALM_SerieBoleta'           => isset($validated['ALM_SerieBoleta']) ? strtoupper($validated['ALM_SerieBoleta']) : null,
                    'ALM_SerieNotaCredito'      => isset($validated['ALM_SerieNotaCredito']) ? strtoupper($validated['ALM_SerieNotaCredito']) : null,
                    'ALM_SerieNotaDebito'       => isset($validated['ALM_SerieNotaDebito']) ? strtoupper($validated['ALM_SerieNotaDebito']) : null,
                    'ALM_SerieGuiaRemision'     => isset($validated['ALM_SerieGuiaRemision']) ? strtoupper($validated['ALM_SerieGuiaRemision']) : null,
                    'ALM_SerieNotaVenta'        => isset($validated['ALM_SerieNotaVenta']) ? strtoupper($validated['ALM_SerieNotaVenta']) : null,
                    'ALM_EsPrincipal'           => $request->boolean('ALM_EsPrincipal') ? 1 : 0,
                    'ALM_PermitirVentaSinStock' => $request->boolean('ALM_PermitirVentaSinStock') ? 1 : 0,
                    'ALM_Status'                => $request->input('ALM_Status', 1),
                ]);
            });

            return response()->json([
                'success' => '¡Sede actualizada exitosamente!',
                'data'    => $almacen->fresh() // Devuelve el modelo actualizado con los datos frescos
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error al actualizar la sede ID ' . $id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'request'   => $request->all()
            ]);

            return response()->json([
                'error' => 'Ocurrió un error interno al intentar actualizar la sede.'
            ], 500);
        }
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
