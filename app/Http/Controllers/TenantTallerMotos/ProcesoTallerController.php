<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\Tenant\User;
use App\Models\TenantTallerMotos\Reservacion;
use App\Services\TenantTallerMotos\GestionProcesoService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * "Gestion de Proceso": el tablero del dia a dia que une recepcion y
 * mecanico. Recepcion hace el check-in de cada reserva de hoy (detalle real
 * de trabajo + mecanico responsable); el mecanico ve solo lo suyo, con
 * aviso si recepcion agrego algo despues de que ya se lo asignaron. Convive
 * con las 5 pantallas de mantenimiento existentes: aqui solo se hace el
 * check-in rapido, el checklist/repuestos/fotos se llenan en el formulario
 * detallado de siempre (se enlaza desde cada tarjeta).
 */
class ProcesoTallerController extends Controller
{
    private function idAlmacenActivo(): int
    {
        return tenant_caja_activa_almacen_id() ?? 1;
    }

    /**
     * Admin/Gerente/Recepcion ven el tablero completo (todas las reservas
     * de hoy, pueden hacer check-in); cualquier otro rol (el mecanico) ve
     * solo lo que tiene asignado.
     */
    private function tieneVistaCompleta(): bool
    {
        $roles = Auth::user()->getRoleNames();

        return $roles->contains('Admin') || $roles->contains('Gerente') || $roles->contains('Recepcion');
    }

    /**
     * Mantenimiento (si existe) de cada reserva, cruzando los 5 tipos de
     * una sola pasada por tipo (5 queries en vez de 1 por reserva).
     *
     * @return array<int, array> RES_Id => metadata + registro
     */
    private function mantenimientosDeReservas($resIds): array
    {
        $resultado = [];

        foreach (GestionProcesoService::TIPOS as $tipo => $meta) {
            DB::table($meta['tabla'])
                ->whereIn('RES_Id', $resIds)
                ->get()
                ->each(function ($registro) use (&$resultado, $tipo, $meta) {
                    $resultado[$registro->RES_Id] = array_merge($meta, ['tipo' => $tipo, 'registro' => $registro]);
                });
        }

        return $resultado;
    }

    public function index()
    {
        $idAlmacen = $this->idAlmacenActivo();
        $hoy = Carbon::now('America/Lima')->toDateString();
        $vistaCompleta = $this->tieneVistaCompleta();
        $miId = Auth::id();

        $bahias = DB::table('bahia')
            ->where('ALM_Id', $idAlmacen)
            ->where('BAH_Estado', 'ACT')
            ->orderBy('BAH_Nombre')
            ->get();

        $reservas = DB::table('reservacion as r')
            ->join('turno as t', 't.TUR_Id', '=', 'r.TUR_Id')
            ->where('r.ALM_Id', $idAlmacen)
            ->where('r.RES_Estado', 'ACT')
            ->where('r.RES_State', '!=', 'RECHAZADO')
            ->whereDate('r.RES_FechaProgramada', $hoy)
            ->select('r.*', 't.TUR_Descripcion', 't.TUR_Nombre')
            ->orderBy('t.TUR_Id')
            ->get();

        $mantenimientosPorReserva = $this->mantenimientosDeReservas($reservas->pluck('RES_Id'));

        if (!$vistaCompleta) {
            $reservas = $reservas->filter(function ($reserva) use ($mantenimientosPorReserva, $miId) {
                $mtto = $mantenimientosPorReserva[$reserva->RES_Id] ?? null;

                return $reserva->RES_CheckIn !== null && $mtto && (int) $mtto['registro']->PER_Id === $miId;
            })->values();
        }

        $reservasPorBahia = $reservas->groupBy('BAH_Id');

        $tablero = $bahias->map(function ($bahia) use ($reservasPorBahia, $mantenimientosPorReserva) {
            $filas = ($reservasPorBahia[$bahia->BAH_Id] ?? collect())->map(function ($reserva) use ($mantenimientosPorReserva) {
                return [
                    'reserva' => $reserva,
                    'mantenimiento' => $mantenimientosPorReserva[$reserva->RES_Id] ?? null,
                ];
            })->values();

            return ['bahia' => $bahia, 'reservas' => $filas];
        });

        // Si es la vista del mecanico, no tiene caso mostrar columnas de
        // bahias donde no tiene nada asignado hoy.
        if (!$vistaCompleta) {
            $tablero = $tablero->filter(fn ($col) => $col['reservas']->isNotEmpty())->values();
        }

        return view('tenant_tallermoto.procesos.index', [
            'tablero' => $tablero,
            'fechaHoy' => Carbon::now('America/Lima')->translatedFormat('l d \d\e F'),
            'vistaCompleta' => $vistaCompleta,
            'tipos' => GestionProcesoService::TIPOS,
            'mecanicos' => User::role('Mecanico')->select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Check-in de recepcion (o Admin/Gerente): crea o actualiza el
     * mantenimiento de la reserva con el detalle real y el mecanico
     * responsable. Reusable tanto para el primer check-in (moto recien
     * llegada) como para ediciones posteriores (recepcion agrega algo mas).
     */
    public function checkIn(Request $request, string $reservacionId)
    {
        $reserva = Reservacion::findOrFail($reservacionId);

        $validated = $request->validate([
            'tipo_mantenimiento' => 'required|string|in:' . implode(',', array_keys(GestionProcesoService::TIPOS)),
            'detalle' => 'required|string|max:2000',
            'mecanico_id' => 'required|integer|exists:users,id',
        ]);

        try {
            $resultado = GestionProcesoService::checkIn(
                $reserva,
                $validated['tipo_mantenimiento'],
                trim($validated['detalle']),
                (int) $validated['mecanico_id']
            );
        } catch (Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 422);
        }

        $idColumna = $resultado['prefijo'] . '_Id';
        $id = $resultado['registro']->{$idColumna};

        return response()->json([
            'success' => true,
            'message' => 'Check-in registrado correctamente.',
            'tabla' => $resultado['tabla'],
            'id' => $id,
            'ruta_edit' => tenant_url($resultado['ruta_edit'], [$resultado['ruta_param'] => $id]),
        ]);
    }

    /**
     * El mecanico confirma que vio el cambio que recepcion agrego despues
     * de la asignacion. Solo el mecanico dueño del registro (o Admin/
     * Gerente) puede confirmarlo.
     */
    public function entendido(string $tabla, string $id)
    {
        $meta = collect(GestionProcesoService::TIPOS)->firstWhere('tabla', $tabla);

        if (!$meta) {
            abort(404);
        }

        $registro = DB::table($tabla)->where($meta['prefijo'] . '_Id', $id)->first();

        if (!$registro) {
            abort(404);
        }

        $roles = Auth::user()->getRoleNames();
        $puedeConfirmar = $roles->contains('Admin') || $roles->contains('Gerente') || (int) $registro->PER_Id === Auth::id();

        if (!$puedeConfirmar) {
            abort(403, 'Este aviso no es tuyo.');
        }

        GestionProcesoService::marcarEntendido($tabla, (int) $id);

        return response()->json(['success' => true]);
    }

    /**
     * Polling del mecanico (cada 20-30s desde el tablero): que trabajos
     * suyos tienen un aviso sin confirmar ahora mismo. Se usa para
     * detectar en vivo que recepcion agrego algo, sin recargar la pagina.
     */
    public function alertas()
    {
        $miId = Auth::id();
        $alertas = [];

        foreach (GestionProcesoService::TIPOS as $tipo => $meta) {
            DB::table($meta['tabla'] . ' as m')
                ->join('reservacion as r', 'r.RES_Id', '=', 'm.RES_Id')
                ->where('m.PER_Id', $miId)
                ->where('m.' . $meta['prefijo'] . '_AvisoMecanico', 1)
                ->select(
                    'r.RES_Id',
                    'm.' . $meta['prefijo'] . '_Id as id',
                    'm.' . $meta['prefijo'] . '_Placa as placa',
                    'm.' . $meta['prefijo'] . '_DetalleIngreso as detalle'
                )
                ->get()
                ->each(function ($fila) use (&$alertas, $meta, $tipo) {
                    $alertas[] = [
                        'res_id' => $fila->RES_Id,
                        'tabla' => $meta['tabla'],
                        'id' => $fila->id,
                        'placa' => $fila->placa,
                        'detalle' => $fila->detalle,
                        'tipo' => $tipo,
                    ];
                });
        }

        return response()->json(['alertas' => $alertas]);
    }
}
