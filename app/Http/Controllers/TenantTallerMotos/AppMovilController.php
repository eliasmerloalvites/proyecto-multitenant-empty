<?php

namespace App\Http\Controllers\TenantTallerMotos;

use App\Http\Controllers\Controller;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Models\TenantTallerMotos\InvitacionMovil;
use App\Services\AppMovil\NormalizadorCelular;
use App\Services\AppMovil\NormalizadorPlaca;
use App\Services\AppMovil\RegistrosMotoService;
use App\Services\AppMovil\VinculacionMotoService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Panel "App movil" del taller: el personal revisa las solicitudes de vinculo
 * que llegan desde la app (y decide si el solicitante es de verdad el dueno),
 * entrega codigos de invitacion en el mostrador y puede quitar un acceso.
 * Aprobar un vinculo da acceso COMPLETO al historial de la placa, por eso
 * esta pantalla muestra a quien figura en los registros del taller antes de
 * decidir.
 */
class AppMovilController extends Controller
{
    public function __construct(
        private VinculacionMotoService $vinculacion,
        private RegistrosMotoService $registros,
    ) {
    }

    public function index()
    {
        $filas = CuentaMovilMoto::with(['cuenta', 'moto'])
            ->whereIn('CMM_Estado', [CuentaMovilMoto::PENDIENTE, CuentaMovilMoto::VERIFICADA])
            ->orderByDesc('CMM_Id')
            ->get();

        $pendientes = $filas->where('CMM_Estado', CuentaMovilMoto::PENDIENTE)->map(function ($v) {
            $registros = $this->registros->registrosDeLaPlaca($v->moto->MOT_Placa);

            return (object) [
                'id' => $v->CMM_Id,
                'placa' => $v->moto->MOT_Placa,
                'celular' => $v->cuenta->CMV_Celular,
                'nombre_cuenta' => $v->cuenta->CMV_Nombre,
                'nota' => $v->CMM_Nota,
                'fecha' => $v->created_at,
                'cantidad_registros' => $registros->count(),
                'a_nombre_de' => $registros->pluck('propietario')->map(fn ($n) => trim((string) $n))->filter()->unique()->take(3)->values()->all(),
                'celular_coincide' => $this->registros->conCelular($registros, $v->cuenta->CMV_Celular)->isNotEmpty(),
            ];
        })->values();

        $activos = $filas->where('CMM_Estado', CuentaMovilMoto::VERIFICADA)->map(fn ($v) => (object) [
            'id' => $v->CMM_Id,
            'placa' => $v->moto->MOT_Placa,
            'celular' => $v->cuenta->CMV_Celular,
            'metodo' => $v->CMM_Metodo,
            'alcance' => $v->CMM_Alcance,
            'fecha' => $v->CMM_VerificadaEn ?? $v->created_at,
        ])->values();

        $invitaciones = InvitacionMovil::with('moto')->orderByDesc('INV_Id')->limit(30)->get()->map(function ($i) {
            $estado = match (true) {
                $i->INV_UsadaEn !== null => 'Usada',
                $i->INV_RevocadaEn !== null => 'Revocada',
                $i->INV_ExpiraEn->isPast() => 'Vencida',
                default => 'Vigente',
            };

            return (object) ['id' => $i->INV_Id, 'placa' => $i->moto->MOT_Placa, 'estado' => $estado, 'vence' => $i->INV_ExpiraEn, 'creada' => $i->created_at];
        });

        $mostrarPrecios = app(\App\Services\AppMovil\OrdenesMotoService::class)->mostrarPrecios();

        return view('tenant_tallermoto.appmovil.index', compact('pendientes', 'activos', 'invitaciones', 'mostrarPrecios'));
    }

    public function configuracion(Request $request)
    {
        $request->validate(['mostrar_precios' => 'required|boolean']);

        $empresa = \App\Models\Tenant\EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        if (! $empresa) {
            return response()->json(['error' => 'Primero completa los datos de la empresa.'], 422);
        }

        $empresa->update(['app_movil_mostrar_precios' => $request->boolean('mostrar_precios')]);

        return response()->json(['success' => 'Configuración guardada.']);
    }

    public function aprobar(int $id)
    {
        $vinculo = CuentaMovilMoto::where('CMM_Estado', CuentaMovilMoto::PENDIENTE)->findOrFail($id);
        $this->vinculacion->aprobar($vinculo, Auth::guard('tenant')->id());

        return response()->json(['success' => 'Vinculo aprobado: el cliente ya puede ver el historial de la moto.']);
    }

    public function rechazar(int $id)
    {
        $vinculo = CuentaMovilMoto::where('CMM_Estado', CuentaMovilMoto::PENDIENTE)->findOrFail($id);
        $this->vinculacion->rechazar($vinculo, Auth::guard('tenant')->id());

        return response()->json(['success' => 'Solicitud rechazada.']);
    }

    public function revocar(int $id)
    {
        $vinculo = CuentaMovilMoto::where('CMM_Estado', CuentaMovilMoto::VERIFICADA)->findOrFail($id);
        $this->vinculacion->revocar($vinculo, Auth::guard('tenant')->id());

        return response()->json(['success' => 'Acceso quitado: la cuenta ya no ve esta moto.']);
    }

    public function crearInvitacion(Request $request)
    {
        $datos = $request->validate(['placa' => ['required', 'string', 'max:15']]);

        $placa = NormalizadorPlaca::normalizar($datos['placa']);

        if (! $placa) {
            return response()->json(['error' => 'Ingresa una placa valida (por ejemplo ABC-123).'], 422);
        }

        $descripcion = $this->registros->registrosDeLaPlaca($placa)->pluck('unidad')->map(fn ($u) => trim((string) $u))->filter()->first();
        $moto = $this->vinculacion->motoDePlaca($placa, $descripcion);
        $codigo = $this->vinculacion->crearInvitacion($moto, Auth::guard('tenant')->id());

        return response()->json([
            'success' => 'Invitacion creada. Entregale este codigo al cliente: no se vuelve a mostrar.',
            'codigo' => $codigo,
            'placa' => $placa,
            'vence' => Carbon::now()->addDays(7)->format('d/m/Y'),
        ]);
    }

    public function revocarInvitacion(int $id)
    {
        $this->vinculacion->revocarInvitacion(InvitacionMovil::findOrFail($id));

        return response()->json(['success' => 'Invitacion revocada.']);
    }

    /** Celular con formato legible para el personal: 51987654321 -> 987 654 321. */
    public static function celularLegible(string $normalizado): string
    {
        $nacional = substr($normalizado, -9);

        return trim(chunk_split($nacional, 3, ' '));
    }
}
