<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CuentaPublica;
use App\Models\TenantTallerMotos\CuentaMovil;
use App\Services\AppMovil\NormalizadorCelular;
use App\Services\AppMovil\OtpService;
use App\Services\AppMovil\SesionMovilService;
use App\Support\ApiRespuesta;
use Carbon\Carbon;
use Illuminate\Http\Request;

/**
 * Inicio de sesion por celular con codigo de un solo uso.
 *
 * Para no permitir averiguar si un celular ya tiene cuenta, "solicitar"
 * responde igual exista o no, y "verificar" crea la cuenta en el primer
 * ingreso: no hay un paso de registro aparte que delate nada.
 */
class OtpController extends Controller
{
    public function __construct(
        private OtpService $otp,
        private SesionMovilService $sesiones,
    ) {
    }

    public function solicitar(Request $request)
    {
        $datos = $request->validate([
            'celular' => ['required', 'string', 'max:20'],
            'dispositivo_id' => ['nullable', 'string', 'max:100'],
        ]);

        $celular = NormalizadorCelular::normalizar($datos['celular']);

        if (! $celular) {
            return ApiRespuesta::error('ERROR_VALIDACION', 'Revisa los datos ingresados.', 422, [
                'celular' => ['Ingresa un celular peruano valido de 9 digitos.'],
            ]);
        }

        $resultado = $this->otp->solicitar($celular, $request->ip(), $datos['dispositivo_id'] ?? null);

        return match ($resultado['resultado']) {
            OtpService::RESULTADO_ESPERAR => ApiRespuesta::error('ESPERA_REENVIO', 'Espera un momento antes de pedir otro codigo.', 429, [
                'reenviar_en_segundos' => [(string) $resultado['esperar_segundos']],
            ]),
            OtpService::RESULTADO_LIMITE_HORA => ApiRespuesta::error('LIMITE_ENVIOS', 'Pediste demasiados codigos. Intentalo mas tarde.', 429),
            default => ApiRespuesta::ok([
                'mensaje' => 'Si el numero es correcto, recibiras un codigo de verificacion.',
                'reenviar_en_segundos' => $resultado['esperar_segundos'],
            ], 202),
        };
    }

    public function verificar(Request $request)
    {
        $datos = $request->validate([
            'celular' => ['required', 'string', 'max:20'],
            'codigo' => ['required', 'string', 'regex:/^[0-9]{' . (int) config('app_movil.otp.longitud') . '}$/'],
            'dispositivo' => ['required', 'string', 'max:100'],
        ]);

        $celular = NormalizadorCelular::normalizar($datos['celular']);

        // Mismo error para celular invalido y codigo incorrecto/vencido/usado.
        if (! $celular || ! $this->otp->verificar($celular, $datos['codigo'])) {
            return ApiRespuesta::error('CODIGO_INVALIDO', 'El codigo es incorrecto o ya vencio.', 422);
        }

        $cuenta = CuentaMovil::firstOrCreate(
            ['CMV_Celular' => $celular],
            ['CMV_Estado' => CuentaMovil::ESTADO_ACTIVA]
        );

        if (! $cuenta->estaActiva()) {
            return ApiRespuesta::error('CUENTA_NO_DISPONIBLE', 'Tu cuenta no esta disponible. Contacta al taller.', 403);
        }

        if (! $cuenta->CMV_VerificadoEn) {
            $cuenta->forceFill(['CMV_VerificadoEn' => Carbon::now()])->save();
        }

        $sesion = $this->sesiones->emitir($cuenta, $datos['dispositivo']);

        return ApiRespuesta::ok([
            'token' => $sesion['token'],
            'tipo' => 'Bearer',
            'vence_en' => $sesion['vence_en']->toIso8601String(),
            'cuenta' => CuentaPublica::datos($cuenta),
        ]);
    }
}
