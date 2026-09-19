<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Cuentas por cobrar: ventas registradas al credito (Nota, Boleta o
 * Factura) que todavia no se cobraron por completo. Desde aqui se
 * registran los abonos (pagos parciales) hasta saldar la cuenta.
 */
class CuentaPorCobrarController extends Controller
{
    /**
     * Listado de cuentas por cobrar, con su saldo y estado. "VENCIDA" no
     * es un estado guardado: es PENDIENTE + fecha de vencimiento ya pasada.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DB::table('cuenta_por_cobrar as cpc')
                ->join('venta as v', 'v.VEN_Id', '=', 'cpc.VEN_Id')
                ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
                ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
                ->select(
                    'cpc.CPC_Id',
                    'v.VEN_Id',
                    'c.CLI_Nombre',
                    'c.CLI_NumDocumento',
                    'c.CLI_Celular',
                    'dov.DOV_Tipo',
                    'dov.DOV_Serie',
                    'dov.DOV_Numero',
                    'cpc.CPC_MontoTotal',
                    'cpc.CPC_MontoAbonado',
                    'cpc.CPC_MontoFaltante',
                    'cpc.CPC_FechaEmision',
                    'cpc.CPC_FechaVencimiento',
                    'cpc.CPC_Estado'
                )
                ->when($request->filled('estado'), function ($q) use ($request) {
                    $estado = $request->input('estado');
                    if ($estado === 'VENCIDA') {
                        $q->where('cpc.CPC_Estado', 'PENDIENTE')
                          ->whereDate('cpc.CPC_FechaVencimiento', '<', now()->toDateString());
                    } else {
                        $q->where('cpc.CPC_Estado', $estado);
                    }
                })
                ->when($request->filled('cliente'), function ($q) use ($request) {
                    $busqueda = $request->input('cliente');
                    $q->where(function ($qq) use ($busqueda) {
                        $qq->where('c.CLI_Nombre', 'like', '%' . $busqueda . '%')
                           ->orWhere('c.CLI_NumDocumento', 'like', '%' . $busqueda . '%');
                    });
                })
                ->orderByDesc('cpc.CPC_Id')
                ->get();

            return datatables()::of($data)
                ->addIndexColumn()
                ->addColumn('documento', function ($row) {
                    $tipos = ['PRO' => 'Nota', 'BOL' => 'Boleta', 'FAC' => 'Factura'];
                    return ($tipos[$row->DOV_Tipo] ?? $row->DOV_Tipo) . ' ' . $row->DOV_Serie . '-' . $row->DOV_Numero;
                })
                ->addColumn('estado_real', function ($row) {
                    if ($row->CPC_Estado === 'PENDIENTE' && $row->CPC_FechaVencimiento < now()->toDateString()) {
                        return '<span class="badge badge-danger">VENCIDA</span>';
                    }
                    if ($row->CPC_Estado === 'PAGADA') {
                        return '<span class="badge badge-success">PAGADA</span>';
                    }
                    return '<span class="badge badge-warning">PENDIENTE</span>';
                })
                ->addColumn('action', function ($row) {
                    $btn = '<a href="javascript:void(0)" data-toggle="tooltip" data-id="' . $row->CPC_Id . '" data-original-title="Ver" class="btn btn-info btn-sm verCuenta"><i class="fa fa-eye"></i></a>';
                    if ($row->CPC_Estado !== 'PAGADA') {
                        $btn .= ' <button type="button" class="btn btn-success btn-sm abonarCuenta" data-id="' . $row->CPC_Id . '" data-saldo="' . $row->CPC_MontoFaltante . '" title="Registrar abono"><i class="fa fa-hand-holding-usd"></i> Abonar</button>';
                    }
                    return $btn;
                })
                ->rawColumns(['estado_real', 'action'])
                ->make(true);
        }

        // "Credito" y "Pago Mixto" son etiquetas de referencia, no metodos
        // reales con los que se pueda cobrar un abono (ver la nota en
        // VentaController::create()).
        $metodosPago = DB::table('metodo_pago')
            ->whereNotIn('MEP_Pago', ['Credito', 'Pago Mixto'])
            ->orderBy('MEP_Pago')
            ->get();

        return view('tenant_' . tenant('tipo_negocio') . '.ventas.cuentasporcobrar.index', compact('metodosPago'));
    }

    /**
     * Detalle de una cuenta: datos de la venta + historial de abonos.
     */
    public function show(string $id)
    {
        $cuenta = DB::table('cuenta_por_cobrar as cpc')
            ->join('venta as v', 'v.VEN_Id', '=', 'cpc.VEN_Id')
            ->join('documento_venta as dov', 'dov.VEN_Id', '=', 'v.VEN_Id')
            ->join('cliente as c', 'c.CLI_Id', '=', 'v.CLI_Id')
            ->select(
                'cpc.*',
                'c.CLI_Nombre',
                'c.CLI_NumDocumento',
                'dov.DOV_Tipo',
                'dov.DOV_Serie',
                'dov.DOV_Numero'
            )
            ->where('cpc.CPC_Id', $id)
            ->first();

        if (!$cuenta) {
            return response()->json(['error' => 'Cuenta por cobrar no encontrada.'], 404);
        }

        $abonos = DB::table('cuenta_por_cobrar_abono as cpa')
            ->join('metodo_pago as mp', 'mp.MEP_Id', '=', 'cpa.MEP_Id')
            ->join('users as u', 'u.id', '=', 'cpa.USU_Id')
            ->select('cpa.CPA_Id', 'cpa.CPA_Monto', 'cpa.CPA_Observacion', 'cpa.created_at', 'mp.MEP_Pago', 'u.name as usuario')
            ->where('cpa.CPC_Id', $id)
            ->orderByDesc('cpa.CPA_Id')
            ->get();

        return response()->json(['cuenta' => $cuenta, 'abonos' => $abonos]);
    }

    /**
     * Registra un abono (pago parcial) contra una cuenta por cobrar. Queda
     * ligado a la caja/sesion ACTIVA en este momento (no a la de la venta
     * original), porque el dinero entra a la caja de hoy, no a la de cuando
     * se vendio.
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

            $cuenta = DB::table('cuenta_por_cobrar')->where('CPC_Id', $id)->lockForUpdate()->first();

            if (!$cuenta) {
                throw new Exception('Cuenta por cobrar no encontrada.');
            }

            if ($cuenta->CPC_Estado === 'PAGADA') {
                throw new Exception('Esta cuenta ya esta pagada por completo.');
            }

            $monto = round((float) $request->input('monto'), 2);

            if ($monto > (float) $cuenta->CPC_MontoFaltante + 0.009) {
                throw new Exception('El abono (S/ ' . number_format($monto, 2) . ') no puede ser mayor al saldo pendiente (S/ ' . number_format($cuenta->CPC_MontoFaltante, 2) . ').');
            }

            DB::table('cuenta_por_cobrar_abono')->insert([
                'CPC_Id'          => $id,
                'MEP_Id'          => $request->input('metodo_pago'),
                'USU_Id'          => Auth::id(),
                'CAJ_Id'          => tenant_caja_activa_id(),
                'CS_Id'           => tenant_caja_sesion_activa_id(),
                'CPA_Monto'       => $monto,
                'CPA_Observacion' => $request->input('observacion'),
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);

            $nuevoAbonado = round((float) $cuenta->CPC_MontoAbonado + $monto, 2);
            $nuevoFaltante = round((float) $cuenta->CPC_MontoTotal - $nuevoAbonado, 2);
            $nuevoFaltante = max(0, $nuevoFaltante);

            DB::table('cuenta_por_cobrar')->where('CPC_Id', $id)->update([
                'CPC_MontoAbonado'  => $nuevoAbonado,
                'CPC_MontoFaltante' => $nuevoFaltante,
                'CPC_Estado'        => $nuevoFaltante <= 0.009 ? 'PAGADA' : 'PENDIENTE',
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
