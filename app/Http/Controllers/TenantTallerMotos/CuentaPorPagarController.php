<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cuentas por pagar: compras registradas al credito que todavia no se
 * pagaron por completo a su proveedor. Desde aqui se registran los abonos
 * (pagos parciales) hasta saldar la cuenta. Mismo diseño que
 * CuentaPorCobrarController, del lado de compras/proveedores.
 */
class CuentaPorPagarController extends Controller
{
    /**
     * Listado de cuentas por pagar, con su saldo y estado. "VENCIDA" no
     * es un estado guardado: es PENDIENTE + fecha de vencimiento ya pasada.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('cuenta_por_pagar as cpp')
                ->join('compra as c', 'c.COM_Id', '=', 'cpp.COM_Id')
                ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
                ->select(
                    'cpp.CPP_Id',
                    'c.COM_Id',
                    'p.PROV_RazonSocial',
                    'p.PROV_NumDocumento',
                    'p.PROV_Celular',
                    'c.COM_TipoDocumento',
                    'c.COM_NumDocumento',
                    'cpp.CPP_MontoTotal',
                    'cpp.CPP_MontoAbonado',
                    'cpp.CPP_MontoFaltante',
                    'cpp.CPP_FechaEmision',
                    'cpp.CPP_FechaVencimiento',
                    'cpp.CPP_Estado'
                )
                ->when($request->filled('estado'), function ($q) use ($request) {
                    $estado = $request->input('estado');
                    if ($estado === 'VENCIDA') {
                        $q->where('cpp.CPP_Estado', 'PENDIENTE')
                          ->whereDate('cpp.CPP_FechaVencimiento', '<', now()->toDateString());
                    } else {
                        $q->where('cpp.CPP_Estado', $estado);
                    }
                })
                ->when($request->filled('proveedor'), function ($q) use ($request) {
                    $busqueda = $request->input('proveedor');
                    $q->where(function ($qq) use ($busqueda) {
                        $qq->where('p.PROV_RazonSocial', 'like', '%' . $busqueda . '%')
                           ->orWhere('p.PROV_NumDocumento', 'like', '%' . $busqueda . '%');
                    });
                })
                ->orderByDesc('cpp.CPP_Id')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('documento', function ($row) {
                    return $row->COM_TipoDocumento . ' ' . $row->COM_NumDocumento;
                })
                ->addColumn('estado_real', function ($row) {
                    if ($row->CPP_Estado === 'PENDIENTE' && $row->CPP_FechaVencimiento < now()->toDateString()) {
                        return '<span class="badge badge-danger">VENCIDA</span>';
                    }
                    if ($row->CPP_Estado === 'PAGADA') {
                        return '<span class="badge badge-success">PAGADA</span>';
                    }
                    return '<span class="badge badge-warning">PENDIENTE</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->CPP_Id . '" data-original-title="Ver" class="btn btn-info btn-sm verCuenta"><i class="fa fa-eye"></i></a>';
                    if ($row->CPP_Estado !== 'PAGADA') {
                        $btn .= ' <button type="button" class="btn btn-success btn-sm abonarCuenta" data-id="' . $row->CPP_Id . '" data-saldo="' . $row->CPP_MontoFaltante . '" title="Registrar abono"><i class="fa fa-hand-holding-usd"></i> Abonar</button>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_real', 'action'])
                ->make(true);
        }

        // "Credito" y "Pago Mixto" son etiquetas de referencia, no metodos
        // reales con los que se pueda pagar un abono.
        $metodosPago = DB::table('metodo_pago')
            ->whereNotIn('MEP_Pago', ['Credito', 'Pago Mixto'])
            ->orderBy('MEP_Pago')
            ->get();

        return view('tenant_' . tenant('tipo_negocio') . '.compras.cuentasporpagar.index', compact('metodosPago'));
    }

    /**
     * Detalle de una cuenta: datos de la compra + historial de abonos.
     */
    public function show(string $id)
    {
        $cuenta = DB::table('cuenta_por_pagar as cpp')
            ->join('compra as c', 'c.COM_Id', '=', 'cpp.COM_Id')
            ->join('proveedor as p', 'p.PROV_Id', '=', 'c.PROV_Id')
            ->select(
                'cpp.*',
                'p.PROV_RazonSocial',
                'p.PROV_NumDocumento',
                'c.COM_TipoDocumento',
                'c.COM_NumDocumento'
            )
            ->where('cpp.CPP_Id', $id)
            ->first();

        if (!$cuenta) {
            return response()->json(['error' => 'Cuenta por pagar no encontrada.'], 404);
        }

        $abonos = DB::table('cuenta_por_pagar_abono as cppa')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cppa.MEP_Id')
            ->join('users as u', 'u.id', '=', 'cppa.USU_Id')
            ->select('cppa.CPPA_Id', 'cppa.CPPA_Monto', 'cppa.CPPA_Observacion', 'cppa.created_at', 'mp.MEP_Pago', 'u.name as usuario')
            ->where('cppa.CPP_Id', $id)
            ->orderByDesc('cppa.CPPA_Id')
            ->get();

        return response()->json(['cuenta' => $cuenta, 'abonos' => $abonos]);
    }

    /**
     * Registra un abono (pago parcial) contra una cuenta por pagar. Queda
     * ligado a la caja/sesion ACTIVA en este momento (no a la de la compra
     * original), porque el dinero sale de la caja de hoy, no de cuando se
     * compro.
     */
    public function abonar(Request $request, string $id)
    {
        $request->validate([
            'monto'       => 'required|numeric|min:0.01',
            'metodo_pago' => 'required|integer|exists:metodo_pago,MEP_Id',
            'observacion' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $cuenta = DB::table('cuenta_por_pagar')->where('CPP_Id', $id)->lockForUpdate()->first();

            if (!$cuenta) {
                throw new Exception('Cuenta por pagar no encontrada.');
            }

            if ($cuenta->CPP_Estado === 'PAGADA') {
                throw new Exception('Esta cuenta ya esta pagada por completo.');
            }

            $monto = round((float) $request->input('monto'), 2);

            if ($monto > (float) $cuenta->CPP_MontoFaltante + 0.009) {
                throw new Exception('El abono (S/ ' . number_format($monto, 2) . ') no puede ser mayor al saldo pendiente (S/ ' . number_format($cuenta->CPP_MontoFaltante, 2) . ').');
            }

            DB::table('cuenta_por_pagar_abono')->insert([
                'CPP_Id'           => $id,
                'MEP_Id'           => $request->input('metodo_pago'),
                'USU_Id'           => Auth::id(),
                'CAJ_Id'           => tenant_caja_activa_id(),
                'CS_Id'            => tenant_caja_sesion_activa_id(),
                'CPPA_Monto'       => $monto,
                'CPPA_Observacion' => $request->input('observacion'),
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            $nuevoAbonado = round((float) $cuenta->CPP_MontoAbonado + $monto, 2);
            $nuevoFaltante = round((float) $cuenta->CPP_MontoTotal - $nuevoAbonado, 2);
            $nuevoFaltante = max(0, $nuevoFaltante);

            DB::table('cuenta_por_pagar')->where('CPP_Id', $id)->update([
                'CPP_MontoAbonado'  => $nuevoAbonado,
                'CPP_MontoFaltante' => $nuevoFaltante,
                'CPP_Estado'        => $nuevoFaltante <= 0.009 ? 'PAGADA' : 'PENDIENTE',
                'updated_at'        => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => 'Abono registrado.',
                'saldo'   => $nuevoFaltante,
                'estado'  => $nuevoFaltante <= 0.009 ? 'PAGADA' : 'PENDIENTE',
            ]);
        } catch (Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()], 422);
        }
    }
}
