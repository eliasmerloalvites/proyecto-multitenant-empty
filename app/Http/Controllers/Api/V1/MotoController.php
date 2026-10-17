<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\MotoPublica;
use App\Models\TenantTallerMotos\CuentaMovil;
use App\Models\TenantTallerMotos\CuentaMovilMoto;
use App\Services\AppMovil\NormalizadorPlaca;
use App\Services\AppMovil\RegistrosMotoService;
use App\Services\AppMovil\VinculacionMotoService;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;

/**
 * "Mi garaje": motos de la cuenta y su vinculacion.
 *
 * Toda consulta parte de los vinculos VERIFICADOS de la cuenta autenticada: un
 * id de moto que no sea de su garaje responde igual que uno inexistente (404),
 * sin revelar si la moto existe.
 */
class MotoController extends Controller
{
    public function __construct(
        private VinculacionMotoService $vinculacion,
        private RegistrosMotoService $registros,
    ) {
    }

    /** Garaje: motos verificadas y solicitudes pendientes. */
    public function index(Request $request)
    {
        $cuenta = $request->user('cliente');
        $this->vinculacion->sincronizarSiHaceFalta($cuenta);

        return ApiRespuesta::ok($this->garaje($cuenta));
    }

    /** Fuerza una nueva busqueda de motos por el celular de la cuenta. */
    public function actualizar(Request $request)
    {
        $cuenta = $request->user('cliente');
        $this->vinculacion->sincronizarPorTelefono($cuenta);

        return ApiRespuesta::ok($this->garaje($cuenta));
    }

    public function show(Request $request, int $moto)
    {
        $cuenta = $request->user('cliente');

        $vinculo = CuentaMovilMoto::with('moto')
            ->where('CMV_Id', $cuenta->CMV_Id)
            ->where('MOT_Id', $moto)
            ->where('CMM_Estado', CuentaMovilMoto::VERIFICADA)
            ->first();

        if (! $vinculo) {
            return ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos esa moto en tu garaje.', 404);
        }

        return ApiRespuesta::ok(MotoPublica::datos(
            $vinculo->moto,
            $vinculo,
            $this->registros->visiblesPara($vinculo, $cuenta, $vinculo->moto)
        ));
    }

    /** Vincula una moto con el codigo de invitacion que entrega el taller. */
    public function vincularInvitacion(Request $request)
    {
        $datos = $request->validate(['codigo' => ['required', 'string', 'max:20']]);
        $cuenta = $request->user('cliente');

        $vinculo = $this->vinculacion->canjearInvitacion($cuenta, $datos['codigo']);

        if (! $vinculo) {
            return ApiRespuesta::error('INVITACION_INVALIDA', 'El codigo es incorrecto, ya se uso o vencio.', 422);
        }

        $vinculo->load('moto');

        return ApiRespuesta::ok(MotoPublica::datos(
            $vinculo->moto,
            $vinculo,
            $this->registros->visiblesPara($vinculo, $cuenta, $vinculo->moto)
        ), 201);
    }

    /**
     * Pide al taller que vincule una placa. Responde siempre igual (no
     * confirma si la placa existe en el taller): el vinculo queda pendiente
     * hasta que el personal lo apruebe.
     */
    public function solicitarVinculo(Request $request)
    {
        $datos = $request->validate([
            'placa' => ['required', 'string', 'max:15'],
            'nota' => ['nullable', 'string', 'max:250'],
        ]);

        $placa = NormalizadorPlaca::normalizar($datos['placa']);

        if (! $placa) {
            return ApiRespuesta::error('ERROR_VALIDACION', 'Revisa los datos ingresados.', 422, [
                'placa' => ['Ingresa una placa valida (por ejemplo ABC-123).'],
            ]);
        }

        $resultado = $this->vinculacion->solicitar($request->user('cliente'), $placa, $datos['nota'] ?? null);

        if ($resultado === VinculacionMotoService::SOLICITUD_LIMITE) {
            return ApiRespuesta::error('LIMITE_SOLICITUDES', 'Ya tienes varias solicitudes pendientes. Espera a que el taller las revise.', 429);
        }

        return ApiRespuesta::ok([
            'mensaje' => 'Enviamos tu solicitud al taller. Te avisaremos cuando la revise.',
        ], 202);
    }

    /** La cuenta suelta una moto (por ejemplo, si la vendio). */
    public function desvincular(Request $request, int $moto)
    {
        $vinculo = CuentaMovilMoto::where('CMV_Id', $request->user('cliente')->CMV_Id)
            ->where('MOT_Id', $moto)
            ->whereIn('CMM_Estado', [CuentaMovilMoto::VERIFICADA, CuentaMovilMoto::PENDIENTE])
            ->first();

        if (! $vinculo) {
            return ApiRespuesta::error('NO_ENCONTRADO', 'No encontramos esa moto en tu garaje.', 404);
        }

        $this->vinculacion->revocar($vinculo);

        return ApiRespuesta::ok(['mensaje' => 'La moto se quito de tu garaje.']);
    }

    private function garaje(CuentaMovil $cuenta): array
    {
        $vinculos = CuentaMovilMoto::with('moto')
            ->where('CMV_Id', $cuenta->CMV_Id)
            ->whereIn('CMM_Estado', [CuentaMovilMoto::VERIFICADA, CuentaMovilMoto::PENDIENTE])
            ->orderBy('CMM_Id')
            ->get();

        $motos = $vinculos
            ->filter(fn ($v) => $v->esVerificada())
            ->map(fn ($v) => MotoPublica::datos($v->moto, $v, $this->registros->visiblesPara($v, $cuenta, $v->moto)))
            ->values()
            ->all();

        $solicitudes = $vinculos
            ->reject(fn ($v) => $v->esVerificada())
            ->map(fn ($v) => MotoPublica::pendiente($v->moto, $v))
            ->values()
            ->all();

        return ['motos' => $motos, 'solicitudes' => $solicitudes];
    }
}
