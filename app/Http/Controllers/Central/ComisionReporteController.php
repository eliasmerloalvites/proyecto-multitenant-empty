<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use App\Models\ComisionLiquidacion;
use App\Models\Vendedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ComisionReporteController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Comision::reporteQuery();

            if ($request->filled('vendedor_id')) {
                $query->where('c.vendedor_id', $request->integer('vendedor_id'));
            }

            if ($request->filled('periodo')) {
                $query->where('c.periodo', $request->string('periodo'));
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('vendedor_nombre', fn ($row) => $row->vendedor_nombre)
                ->addColumn('periodo', fn ($row) => $row->periodo)
                ->addColumn('clientes_pagaron', fn ($row) => $row->clientes_pagaron)
                ->addColumn('total_comision', fn ($row) => 'S/ ' . number_format($row->total_comision, 2))
                ->addColumn('estado', function ($row) {
                    return $row->liquidacion_id
                        ? '<span class="badge badge-success">LIQUIDADO</span>'
                        : '<span class="badge badge-warning">PENDIENTE</span>';
                })
                ->addColumn('action', function ($row) {
                    if ($row->liquidacion_id) {
                        return '<span class="text-muted small">' . \Carbon\Carbon::parse($row->liquidado_en)->format('d/m/Y') . '</span>';
                    }

                    return '<button class="btn btn-sm btn-success liquidarComision" data-vendedor="' . $row->vendedor_id . '" data-periodo="' . $row->periodo . '"><i class="fa fa-check mr-1"></i>Marcar liquidado</button>';
                })
                ->rawColumns(['estado', 'action'])
                ->make(true);
        }

        return view('central.admin.comisiones.index', [
            'vendedores' => Vendedor::with('user:id,name')->get(),
        ]);
    }

    /**
     * Marca vendedor+periodo como liquidado (el pago en sí se hace fuera del
     * sistema). Idempotente: si ya estaba liquidado, no crea una fila
     * duplicada (unique en la tabla) ni rompe.
     */
    public function liquidar(Request $request)
    {
        $validated = $request->validate([
            'vendedor_id' => 'required|exists:vendedores,id',
            'periodo' => 'required|string|size:7',
            'nota' => 'nullable|string|max:500',
        ]);

        $liquidacion = ComisionLiquidacion::firstOrCreate(
            ['vendedor_id' => $validated['vendedor_id'], 'periodo' => $validated['periodo']],
            [
                'liquidado_por' => Auth::guard('central')->id(),
                'liquidado_en' => now(),
                'nota' => $validated['nota'] ?? null,
            ]
        );

        \App\Models\AuditLog::registrar(
            'comision.liquidada',
            'Marcó como liquidado el periodo ' . $validated['periodo'] . ' del vendedor #' . $validated['vendedor_id'],
            ['vendedor_id' => $validated['vendedor_id'], 'periodo' => $validated['periodo']]
        );

        return response()->json(['success' => 'Periodo marcado como liquidado.', 'liquidacion' => $liquidacion]);
    }
}
