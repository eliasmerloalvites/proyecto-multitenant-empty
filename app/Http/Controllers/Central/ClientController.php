<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Client;
use App\Models\Tenant;
use App\Models\Tenant\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Stancl\Tenancy\Database\Models\Domain;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Client::join('domains as d', 'clients.domain_id', '=', 'd.id')
                ->join('tenants as t', 'd.tenant_id', '=', 't.id')
                ->select('clients.*', 'd.domain as domain', 't.tipo_negocio', 't.plan', 't.status')->get();

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
                ->addColumn('plan', function ($row) {
                    $plan = strtoupper($row->plan);

                    $styles = [
                        'START' => 'background:#9B5DE5;',
                        'BASIC' => 'background:#3B82F6;',
                        'PLUS' => 'background:#22C55E;',
                        'EMPRESARIAL' => 'background:#A855F7;',
                    ];

                    $style = $styles[$plan] ?? 'background:#F59E0B;';

                    return '
                        <span style="
                            ' . $style . '
                            color:white;
                            padding:5px 12px;
                            border-radius:6px;
                            font-size:12px;
                            font-weight:600;
                            letter-spacing:0.5px;
                            display:inline-block;
                            text-transform:uppercase;
                            box-shadow:0 2px 4px rgba(0,0,0,0.15);
                        ">
                            ' . $plan . '
                        </span>
                    ';
                })
                ->addColumn('estado', function ($row) {

                    $estado = strtoupper($row->status);

                    $styles = [
                        'ACTIVO' => [
                            'color' => '#22C55E',
                            'border' => '#86EFAC',
                            'background' => 'rgba(34, 197, 94, 0.08)',
                        ],

                        'SUSPENDIDO' => [
                            'color' => '#F59E0B',
                            'border' => '#FCD34D',
                            'background' => 'rgba(245, 158, 11, 0.08)',
                        ],
                    ];

                    $style = $styles[$estado] ?? [
                        'color' => '#6B7280',
                        'border' => '#D1D5DB',
                        'background' => 'rgba(107, 114, 128, 0.08)',
                    ];

                    return '
                        <span style="
                            color: ' . $style['color'] . ';
                            border:1px solid ' . $style['border'] . ';
                            background: ' . $style['background'] . ';
                            padding:5px 12px;
                            border-radius:8px;
                            font-size:12px;
                            font-weight:600;
                            display:inline-block;
                            text-transform:capitalize;
                        ">
                            ' . $estado . '
                        </span>
                    ';
                })
                ->rawColumns(['action1', 'action2', 'action3', 'plan', 'estado'])
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
            'plan' => 'required',
            'billing_day' => 'required|integer|min:1|max:28',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'domain_type' => 'required|in:subdomain,custom_domain',
            'subdomain' => ['nullable', 'alpha_dash'],
            'custom_domain' => ['nullable', 'string'],
        ]);

        $plans = config('saas.plans');
        $planConfig = $plans[$validated['plan']];

        try {
            // Validando Dominio
            if ($validated['domain_type'] === 'custom_domain') {
                $fullDomain = $validated['custom_domain'];
            } else {
                $fullDomain = $validated['subdomain'] . '.' . config('app.central_domain');
            }
            if (Domain::where('domain', $fullDomain)->exists()) {
                return back()->withErrors(['domain' => 'El dominio ya existe.']);
            }
            // dd($validated['plan']);
            // 1️Crear TENANT (DB + dominio)
            $tenantId = $validated['tipo_negocio'] . '_' . Str::slug($validated['subdomain']);
            $tenant = Tenant::create([
                'id' => $tenantId,
                'tipo_negocio' => $validated['tipo_negocio'],
                'plan' => $validated['plan'],
                'status' => 'activo',
                'max_users' => $planConfig['max_users'],
                'max_images' => $planConfig['max_images'],
                'storage_limit_mb' => $planConfig['storage_limit_mb'],
                'custom_domain_enabled' => $planConfig['custom_domain_enabled'],
                'custom_branding' => $planConfig['custom_branding'],
            ]);
            // Refrescar tenant
            $tenant->refresh();
            
            $data = $tenant->data ?? [];
            $data['branding'] = ['logo' => null, 'primary_color' => '#0B63CE'];
            $data['modules'] = ['agenda' => true, 'reports' => false];
            $tenant->update(['data' => $data]);



            $domain = $tenant->domains()->create(['domain' => $fullDomain]);

            // 2️⃣ Crear CLIENTE (DB CENTRAL)
            Client::create([
                'tenant_id'    => $tenant->id,
                'razon_social' => $validated['razon_social'],
                'ruc'          => $validated['ruc'],
                'billing_day'  => $validated['billing_day'],
                'domain_id'    => $domain->id,
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
                    '--class' => "Database\Seeders\Tenant\\" . $validated['tipo_negocio'] . "\DatabaseSeeder",
                    '--force' => true,
                ]);
                $user = User::create([
                    'name'              => 'Admin ' . $validated['razon_social'],
                    'email'             => $validated['email'],
                    'password'          => Hash::make($validated['password']),
                    'email_verified_at' => now(),
                    'estadousuario'     => 1,
                    'tipousuario'       => 0,
                    'avatar'            => '',
                    'PER_Id'            => 1, // Asignar un PER_Id válido según tu lógica
                ]);

                if ($user) {
                    $user->assignRole('Gerente');
                }
            });

            return redirect()->route('admin.clients.index')->with('success', 'Cliente y entorno creados correctamente.');
        } catch (\Exception $e) {
            if (isset($tenant)) {
                $tenant->delete(); // elimina tenant + DB
            }
            dd($e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Error en DB Central: ' . $e->getMessage()]);
        }
    }


    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function show(string $id)
    {
        $cliente = Client::find($id);
        return response()->json(['data' => $cliente]);
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
