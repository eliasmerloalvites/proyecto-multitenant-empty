<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Date;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action1', function ($row) {
                    $btn = '<a data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Edit" class="edit btn btn-primary btn-sm editClient" ><i class="fa fa-edit"></i></a>';
                    return $btn;
                })
                ->addColumn('action2', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Delete" class="btn btn-danger btn-sm deleteClient"><i class="fa fa-trash"></i></a>';

                    return $btn;
                })
                ->addColumn('action3', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip"  data-id="' . $row->id . '" data-original-title="Ver" class="btn btn-warning btn-sm eyeClient"><i class="fa fa-eye" aria-hidden="true"></i></a>';

                    return $btn;
                })
                ->rawColumns(['action1', 'action2', 'action3'])
                ->make(true);
        }
        return view('central.admin.clients.index');
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruc' => 'required',
            'razon_social' => 'required|string|max:255',
            'tipo_negocio' => 'required',
            'billing_day' => 'required|integer|min:1|max:28',
            'email'        => 'required|email',
            'password'     => 'required|min:8',
            'domain'       => 'required|alpha_dash|unique:domains,domain',
        ]);

        // 1. Iniciar el proceso fuera de un bloque de closure para tener control total
        DB::beginTransaction();

        try {
            // 1️⃣ Crear TENANT (DB + dominio)
            $tenant = Tenant::create([
                'id' => $validated['domain'],
                'tipo_negocio' => $validated['tipo_negocio'],
            ]);

            $tenant->domains()->create([
                'domain' => $validated['domain'] . '.' . config('app.central_domain'),
            ]);

            // 2️⃣ Crear CLIENTE (DB CENTRAL)
            Client::create([
                'tenant_id'    => $tenant->id,
                'razon_social' => $validated['razon_social'],
                'ruc'          => $validated['ruc'],
                'tipo_negocio' => $validated['tipo_negocio'],
                'billing_day'  => $validated['billing_day'],
                'status'       => 'activo',
            ]);

            // 3️⃣ Crear USUARIO ADMIN dentro del TENANT
            $tenant->run(function () use ($validated) {
                // Ejecutamos las migraciones EXTRA (ej: database/migrations/tenant/optica)
                $extraPath = "database/migrations/tenant/" . $validated['tipo_negocio'];
                if (is_dir(base_path($extraPath))) {
                    Artisan::call('migrate', [
                        '--path' => $extraPath,
                        '--force' => true,
                    ]);
                }
                // 2. Ejecutar Seeders Maestros (Datos fijos: categorías, configuraciones, etc.)
                // Usamos el namespace completo de tu seeder seccionado
                Artisan::call('db:seed', [
                    '--class' => "Database\Seeders\Tenant\\" . $validated['tipo_negocio'] . "DatabaseSeeder",
                    '--force' => true,
                ]);
                User::create([
                    'name'              => 'Admin ' . $validated['razon_social'],
                    'email'             => $validated['email'],
                    'password'          => Hash::make($validated['password']),
                    'email_verified_at' => now(),
                    'estadousuario'     => 1,
                    'tipousuario'       => 0,
                    'avatar'            => '',
                    'PER_Id'            => 1, // Asignar un PER_Id válido según tu lógica
                ]);
            });

            return redirect()->route('admin.clients.index')->with('success', 'Cliente y entorno creados correctamente.');
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack(); // Revertir cualquier cambio en la DB
            if (isset($tenant)) {
                $tenant->delete(); // elimina tenant + DB
            }
            return back()->withInput()->withErrors(['error' => 'Error en DB Central: ' . $e->getMessage()]);
        }
    }


    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'razon_social' => 'required|string|max:255',
            'billing_day' => 'required|integer|min:1|max:28',
            'status' => 'required',
        ]);

        $client->update($request->all());

        return redirect()
            ->route('admin.clients.index')
            ->with('success', 'Cliente actualizado');
    }

    public function destroy(string $id)
    {
        $client = Client::find($id);
        $client->status = 0;
        $client->save();
        //return redirect()->route('usuario.index')->with('datos','Usuario eliminado ...!');
        return response()->json(['success' => 'Cliente Eliminado Exitosamente.']);
    }
}
