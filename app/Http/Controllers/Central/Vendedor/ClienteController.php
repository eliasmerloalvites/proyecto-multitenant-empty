<?php

namespace App\Http\Controllers\Central\Vendedor;

use App\Http\Controllers\Controller;
use App\Services\TenantProvisioningService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

/**
 * Panel del vendedor: "Mis clientes". A propósito NUNCA recibe un
 * vendedor_id del request/URL — siempre opera sobre el vendedor del usuario
 * autenticado (ver Auth::guard('central')->user()->vendedor), así no existe
 * ningún parámetro que un vendedor pueda manipular para ver clientes ajenos.
 */
class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::guard('central')->user()->vendedor;

        if ($request->ajax()) {
            $data = DB::table('cliente_vendedor as cv')
                ->join('clients as c', 'c.id', '=', 'cv.client_id')
                ->join('domains as d', 'c.domain_id', '=', 'd.id')
                ->join('tenants as t', 'd.tenant_id', '=', 't.id')
                ->where('cv.vendedor_id', $vendedor->id)
                ->select(
                    'c.id',
                    'c.razon_social',
                    'c.status',
                    'd.domain',
                    't.tipo_negocio',
                    't.plan',
                    'cv.porcentaje_congelado',
                    'cv.meses_congelado',
                    'cv.referido_en'
                )
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('plan', fn ($row) => strtoupper($row->plan))
                ->addColumn('esquema', fn ($row) => $row->porcentaje_congelado . '% / ' . $row->meses_congelado . ' meses')
                ->addColumn('referido_en', fn ($row) => Carbon::parse($row->referido_en)->format('d/m/Y'))
                ->addColumn('estado', function ($row) {
                    $badge = $row->status === 'activo' ? 'badge-success' : 'badge-secondary';

                    return '<span class="badge ' . $badge . '">' . strtoupper($row->status) . '</span>';
                })
                ->rawColumns(['estado'])
                ->make(true);
        }

        return view('central.vendedor.clientes.index');
    }

    /**
     * Registra una empresa nueva, forzando vendedor_id = el vendedor
     * logueado (nunca del request) — así queda enganchada para comisión con
     * el esquema vigente de este vendedor en este instante.
     */
    public function store(Request $request, TenantProvisioningService $provisioning)
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

        $validated['vendedor_id'] = Auth::guard('central')->user()->vendedor->id;

        try {
            $provisioning->provision($validated);

            \App\Models\AuditLog::registrar(
                'cliente.creado',
                'Vendedor registró el cliente "' . $validated['razon_social'] . '" (plan ' . strtoupper($validated['plan']) . ')',
                ['razon_social' => $validated['razon_social'], 'plan' => $validated['plan'], 'vendedor_id' => $validated['vendedor_id']]
            );

            return response()->json(['success' => 'Cliente y entorno creados correctamente.']);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Error en DB Central: ' . $e->getMessage()], 500);
        }
    }
}
