<?php

namespace App\Http\Controllers\Central\Vendedor;

use App\Http\Controllers\Controller;
use App\Models\Comision;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

/**
 * Panel del vendedor: "Mis comisiones", solo lectura. Igual que
 * ClienteController, nunca recibe un vendedor_id del request — siempre el
 * del usuario autenticado.
 */
class ComisionController extends Controller
{
    public function index(Request $request)
    {
        $vendedor = Auth::guard('central')->user()->vendedor;

        if ($request->ajax()) {
            $query = Comision::reporteQuery($vendedor->id);

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('periodo', fn ($row) => $row->periodo)
                ->addColumn('clientes_pagaron', fn ($row) => $row->clientes_pagaron)
                ->addColumn('total_comision', fn ($row) => 'S/ ' . number_format($row->total_comision, 2))
                ->addColumn('estado', function ($row) {
                    return $row->liquidacion_id
                        ? '<span class="badge badge-success">LIQUIDADO</span>'
                        : '<span class="badge badge-warning">PENDIENTE</span>';
                })
                ->rawColumns(['estado'])
                ->make(true);
        }

        return view('central.vendedor.comisiones.index');
    }
}
