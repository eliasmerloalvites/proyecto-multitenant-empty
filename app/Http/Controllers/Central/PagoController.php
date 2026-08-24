<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Pago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class PagoController extends Controller
{
    /**
     * Listado de cobros: un renglón por cliente activo con billing_day,
     * mostrando el estado de su ciclo de facturación actual (pagado /
     * vencido / por vencer / pendiente) y el monto esperado según su plan.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $hoy = Carbon::now('America/Lima');
            $periodo = $hoy->format('Y-m');
            $planesConfig = [];

            $data = Client::where('clients.status', 'activo')
                ->whereNotNull('billing_day')
                ->join('domains as d', 'clients.domain_id', '=', 'd.id')
                ->join('tenants as t', 'd.tenant_id', '=', 't.id')
                ->select('clients.*', 't.plan', 't.tipo_negocio')
                ->with(['pagos' => fn ($q) => $q->where('periodo', $periodo)])
                ->get()
                ->map(function ($cliente) use ($hoy, &$planesConfig) {
                    $cliente->estado_ciclo = $cliente->estadoCicloActual($hoy);
                    $cliente->fecha_cobro = $cliente->fechaCicloActual($hoy);
                    $planesConfig[$cliente->tipo_negocio] ??= saas_plans_config($cliente->tipo_negocio);
                    $cliente->monto_esperado = $cliente->montoEsperado($planesConfig[$cliente->tipo_negocio]);

                    return $cliente;
                });

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('plan', fn ($row) => strtoupper($row->plan))
                ->addColumn('fecha_cobro', fn ($row) => $row->fecha_cobro->format('d/m/Y'))
                ->addColumn('monto', fn ($row) => 'S/ ' . number_format($row->monto_esperado, 2))
                ->addColumn('estado', function ($row) {
                    $estilos = [
                        'en_prueba' => ['label' => 'EN PRUEBA', 'color' => '#2563EB', 'bg' => 'rgba(37,99,235,.1)'],
                        'pagado' => ['label' => 'PAGADO', 'color' => '#16A34A', 'bg' => 'rgba(34,197,94,.1)'],
                        'vencido' => ['label' => 'VENCIDO', 'color' => '#DC2626', 'bg' => 'rgba(239,68,68,.1)'],
                        'por_vencer' => ['label' => 'POR VENCER', 'color' => '#D97706', 'bg' => 'rgba(245,158,11,.1)'],
                        'pendiente' => ['label' => 'PENDIENTE', 'color' => '#6B7280', 'bg' => 'rgba(107,114,128,.1)'],
                    ];

                    $e = $estilos[$row->estado_ciclo];

                    return '<span style="color:' . $e['color'] . ';background:' . $e['bg'] . ';padding:5px 12px;border-radius:8px;font-size:12px;font-weight:600;display:inline-block;">' . $e['label'] . '</span>';
                })
                ->addColumn('action', function ($row) {
                    if ($row->estado_ciclo === 'en_prueba') {
                        return '<span class="text-muted small">Prueba hasta ' . $row->trial_ends_at->format('d/m/Y') . '</span>
                                <button class="btn btn-sm btn-outline-secondary verHistorial" data-id="' . $row->id . '" data-nombre="' . e($row->razon_social) . '" title="Ver historial"><i class="fa fa-eye"></i></button>';
                    }

                    if ($row->estado_ciclo === 'pagado') {
                        return '<button class="btn btn-sm btn-outline-secondary verHistorial" data-id="' . $row->id . '" data-nombre="' . e($row->razon_social) . '" title="Ver historial"><i class="fa fa-eye"></i></button>';
                    }

                    return '<button class="btn btn-sm btn-success registrarPago" data-id="' . $row->id . '" data-nombre="' . e($row->razon_social) . '" data-monto="' . $row->monto_esperado . '"><i class="fa fa-dollar-sign mr-1"></i>Registrar pago</button>
                            <button class="btn btn-sm btn-outline-secondary verHistorial" data-id="' . $row->id . '" data-nombre="' . e($row->razon_social) . '" title="Ver historial"><i class="fa fa-eye"></i></button>';
                })
                ->rawColumns(['estado', 'action'])
                ->make(true);
        }

        return view('central.admin.pagos.index');
    }

    /**
     * Registra el pago del ciclo actual de un cliente. Un pago por
     * client_id + periodo (índice único en la tabla pagos evita duplicados).
     */
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'monto' => 'required|numeric|min:0',
            'fecha_pago' => 'required|date',
            'metodo_pago' => 'required|in:efectivo,transferencia,yape_plin,tarjeta,otro',
            'nota' => 'nullable|string|max:500',
        ]);

        $periodo = Carbon::parse($validated['fecha_pago'])->format('Y-m');

        if ($client->pagos()->where('periodo', $periodo)->exists()) {
            return response()->json([
                'error' => 'Ya existe un pago registrado para el periodo ' . $periodo . ' de este cliente.',
            ], 422);
        }

        $pago = $client->pagos()->create([
            'monto' => $validated['monto'],
            'periodo' => $periodo,
            'fecha_pago' => $validated['fecha_pago'],
            'metodo_pago' => $validated['metodo_pago'],
            'nota' => $validated['nota'] ?? null,
            'registrado_por' => Auth::guard('central')->id(),
        ]);

        \App\Models\AuditLog::registrar(
            'pago.registrado',
            'Registró un pago de S/ ' . number_format($validated['monto'], 2) . ' de "' . $client->razon_social . '" (periodo ' . $periodo . ')',
            ['client_id' => $client->id, 'periodo' => $periodo, 'monto' => $validated['monto'], 'metodo_pago' => $validated['metodo_pago']]
        );

        return response()->json([
            'success' => 'Pago registrado correctamente.',
            'pago' => $pago,
        ]);
    }

    /**
     * Historial de pagos de un cliente (AJAX, para el modal "Ver historial").
     */
    public function historial(Client $client)
    {
        $pagos = $client->pagos()
            ->with('registradoPor:id,name')
            ->orderByDesc('fecha_pago')
            ->get()
            ->map(fn ($pago) => [
                'periodo' => $pago->periodo,
                'monto' => number_format($pago->monto, 2),
                'fecha_pago' => $pago->fecha_pago->format('d/m/Y'),
                'metodo_pago' => $pago->metodo_pago,
                'nota' => $pago->nota,
                'registrado_por' => $pago->registradoPor->name ?? '—',
            ]);

        return response()->json(['data' => $pagos]);
    }
}
