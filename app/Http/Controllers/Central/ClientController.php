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
use App\Models\Tenant\EmpresaFacturacion;
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
            // NOTA: se selecciona explícitamente 'clients.status' (y no 't.status') porque
            // antes ambas columnas se llamaban 'status' y la del tenant pisaba silenciosamente
            // a la del cliente. El estado comercial que se administra aquí es el de 'clients';
            // se mantiene sincronizado con 'tenants.status' desde update()/toggleStatus().
            $data = Client::join('domains as d', 'clients.domain_id', '=', 'd.id')
                ->join('tenants as t', 'd.tenant_id', '=', 't.id')
                ->select('clients.*', 'd.domain as domain', 't.tipo_negocio', 't.plan', 'clients.status as status')->get();

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
                ->addColumn('action4', function ($row) {
                    if ($row->status === 'activo') {
                        return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Dar de baja" class="btn btn-secondary btn-sm toggleStatusClient"><i class="fa fa-ban" aria-hidden="true"></i></a>';
                    }

                    return '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->id . '" data-original-title="Reactivar" class="btn btn-success btn-sm toggleStatusClient"><i class="fa fa-check" aria-hidden="true"></i></a>';
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

                        'CANCELADO' => [
                            'color' => '#DC2626',
                            'border' => '#FCA5A5',
                            'background' => 'rgba(220, 38, 38, 0.08)',
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
                ->rawColumns(['action1', 'action2', 'action3', 'action4', 'plan', 'estado'])
                ->make(true);
        }
        return view('central.admin.clients.index');
    }



    public function store(Request $request, \App\Services\TenantProvisioningService $provisioning)
    {
        $validated = $request->validate([
            'ruc' => 'required',
            'razon_social' => 'required|string|max:255',
            'tipo_negocio' => 'required',
            'plan' => 'required|in:start,basic,plus,empresarial',
            'billing_day' => 'required|integer|min:1|max:28',
            'email' => 'required|email',
            'password' => 'required|min:8',
            'domain_type' => 'required|in:subdomain,custom_domain',
            'subdomain' => ['nullable', 'alpha_dash'],
            'custom_domain' => ['nullable', 'string'],
        ]);

        if ($validated['domain_type'] === 'custom_domain') {
            $validated['custom_domain'] = $validated['custom_domain'] ?? null;
        }

        try {
            $provisioning->provision($validated);

            \App\Models\AuditLog::registrar(
                'cliente.creado',
                'Creó el cliente "' . $validated['razon_social'] . '" (plan ' . strtoupper($validated['plan']) . ')',
                ['razon_social' => $validated['razon_social'], 'plan' => $validated['plan'], 'tipo_negocio' => $validated['tipo_negocio']]
            );

            return response()->json(['success' => 'Cliente y entorno creados correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error en DB Central: ' . $e->getMessage()], 500);
        }
    }


    /**
     * Datos para precargar el modal de edición (solo lo editable desde aquí).
     */
    public function edit(Client $client)
    {
        $tenant = Tenant::find($client->tenant_id);
        $domain = Domain::find($client->domain_id);

        return response()->json([
            'data' => [
                'id'                => $client->id,
                'ruc'               => $client->ruc,
                'razon_social'      => $client->razon_social,
                'email'             => $client->email,
                'billing_day'       => $client->billing_day,
                'next_payment_date' => optional($client->next_payment_date)->format('Y-m-d'),
                'status'            => $client->status,
                'plan'              => $tenant->plan ?? null,
                'tipo_negocio'      => $tenant->tipo_negocio ?? null,
                'domain'            => $domain->domain ?? null,
            ],
        ]);
    }

    /**
     * Detalle completo para el modal "Ver".
     */
    public function show(Client $client)
    {
        $tenant = Tenant::find($client->tenant_id);
        $domain = Domain::find($client->domain_id);

        return response()->json([
            'data' => [
                'id'                    => $client->id,
                'ruc'                   => $client->ruc,
                'razon_social'          => $client->razon_social,
                'email'                 => $client->email,
                'billing_day'           => $client->billing_day,
                'next_payment_date'     => optional($client->next_payment_date)->format('Y-m-d'),
                'status'                => $client->status,
                'created_at'            => $client->created_at,
                'updated_at'            => $client->updated_at,
                'domain'                => $domain->domain ?? '—',
                'tipo_negocio'          => $tenant->tipo_negocio ?? '—',
                'plan'                  => $tenant->plan ?? '—',
                'max_users'             => $tenant->max_users ?? null,
                'max_images'            => $tenant->max_images ?? null,
                'storage_limit_mb'      => $tenant->storage_limit_mb ?? null,
                'custom_domain_enabled' => (bool) ($tenant->custom_domain_enabled ?? false),
                'custom_branding'       => (bool) ($tenant->custom_branding ?? false),
            ],
        ]);
    }

    /**
     * Actualiza los datos comerciales del cliente. NO permite cambiar
     * tipo_negocio ni dominio (requeriría re-provisionar el tenant).
     * Mantiene sincronizados plan/status con el modelo Tenant, y si el
     * plan cambia, sincroniza también sus límites (max_users, etc).
     */
    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'ruc'               => 'nullable|string|max:20',
            'razon_social'      => 'required|string|max:255',
            'email'             => 'nullable|email',
            'billing_day'       => 'required|integer|min:1|max:28',
            'next_payment_date' => 'nullable|date',
            'plan'              => 'required|in:start,basic,plus,empresarial',
            'status'            => 'required|in:activo,suspendido,cancelado',
        ]);

        $planAnterior = null;
        $statusAnterior = $client->status;

        try {
            DB::beginTransaction();

            $client->update([
                'ruc'               => $validated['ruc'] ?? null,
                'razon_social'      => $validated['razon_social'],
                'email'             => $validated['email'] ?? null,
                'billing_day'       => $validated['billing_day'],
                'next_payment_date' => $validated['next_payment_date'] ?? null,
                'status'            => $validated['status'],
            ]);

            $tenant = Tenant::find($client->tenant_id);

            if ($tenant) {
                $planAnterior = $tenant->plan;
                $planChanged = $tenant->plan !== $validated['plan'];

                $tenant->status = $validated['status'];
                $tenant->plan = $validated['plan'];

                if ($planChanged) {
                    $planConfig = saas_plans_config($tenant->tipo_negocio)[$validated['plan']];
                    $tenant->max_users = $planConfig['max_users'];
                    $tenant->max_images = $planConfig['max_images'];
                    $tenant->storage_limit_mb = $planConfig['storage_limit_mb'];
                    $tenant->custom_domain_enabled = $planConfig['custom_domain_enabled'];
                    $tenant->custom_branding = $planConfig['custom_branding'];

                    // Resincroniza módulos y límites del nuevo plan (mantenimientos/
                    // productos/inventario/compras/ventas/...) para que tome efecto
                    // de inmediato en el panel y en las rutas protegidas. Se preserva
                    // el branding ya personalizado por el cliente, si existe (mismo
                    // patrón que ClientController::store()).
                    $tenant->modules = $planConfig['data']['modules'];
                    $tenant->limits = $planConfig['data']['limits'];
                    if (! $tenant->branding) {
                        $tenant->branding = $planConfig['data']['branding'];
                    }
                }

                $tenant->save();
            }

            DB::commit();

            $cambios = [];
            if ($planAnterior && $planAnterior !== $validated['plan']) {
                $cambios[] = 'plan ' . strtoupper($planAnterior) . ' → ' . strtoupper($validated['plan']);
            }
            if ($statusAnterior !== $validated['status']) {
                $cambios[] = 'estado ' . $statusAnterior . ' → ' . $validated['status'];
            }

            \App\Models\AuditLog::registrar(
                'cliente.actualizado',
                'Actualizó a "' . $client->razon_social . '"' . ($cambios ? ' (' . implode(', ', $cambios) . ')' : ''),
                ['client_id' => $client->id, 'plan_anterior' => $planAnterior, 'plan_nuevo' => $validated['plan'], 'status_anterior' => $statusAnterior, 'status_nuevo' => $validated['status']]
            );

            return response()->json(['success' => 'Cliente actualizado correctamente.']);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json(['error' => 'No se pudo actualizar: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Baja/reactivación rápida (reversible). No borra nada, solo cambia
     * el estado comercial del cliente y del tenant.
     */
    public function toggleStatus(Client $client)
    {
        $newStatus = $client->status === 'activo' ? 'suspendido' : 'activo';

        $client->status = $newStatus;
        $client->save();

        $tenant = Tenant::find($client->tenant_id);
        if ($tenant) {
            $tenant->status = $newStatus;
            $tenant->save();
        }

        \App\Models\AuditLog::registrar(
            $newStatus === 'activo' ? 'cliente.reactivado' : 'cliente.suspendido',
            ($newStatus === 'activo' ? 'Reactivó' : 'Dio de baja') . ' a "' . $client->razon_social . '"',
            ['client_id' => $client->id]
        );

        $mensaje = $newStatus === 'activo'
            ? 'Cliente reactivado correctamente.'
            : 'Cliente dado de baja correctamente.';

        return response()->json(['success' => $mensaje, 'status' => $newStatus]);
    }

    /**
     * Elimina DEFINITIVAMENTE al cliente: borra el Tenant, lo que en cascada
     * (por el evento TenantDeleted -> Jobs\DeleteDatabase configurado en
     * TenancyServiceProvider) elimina también la base de datos del tenant,
     * su dominio y este registro de cliente (FK cascadeOnDelete). Acción
     * irreversible: la vista debe confirmar esto explícitamente antes de llamar.
     */
    public function destroy(Client $client)
    {
        try {
            $tenant = Tenant::find($client->tenant_id);

            if ($tenant) {
                $tenant->delete();
            } else {
                // El tenant ya no existía: limpiamos el registro huérfano.
                $client->delete();
            }

            return response()->json(['success' => 'Cliente y su entorno fueron eliminados definitivamente.']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo eliminar: ' . $e->getMessage()], 500);
        }
    }
}
