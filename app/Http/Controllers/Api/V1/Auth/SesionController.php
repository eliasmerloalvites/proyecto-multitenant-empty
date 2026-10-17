<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Http\Controllers\Controller;
use App\Services\AppMovil\SesionMovilService;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;

/** Renovacion y cierre de sesion de la app (tokens por dispositivo). */
class SesionController extends Controller
{
    public function __construct(private SesionMovilService $sesiones)
    {
    }

    /** Cambia el token actual por uno nuevo con nueva vigencia. */
    public function refresh(Request $request)
    {
        $cuenta = $request->user('cliente');
        $sesion = $this->sesiones->rotar($cuenta, $cuenta->currentAccessToken());

        return ApiRespuesta::ok([
            'token' => $sesion['token'],
            'tipo' => 'Bearer',
            'vence_en' => $sesion['vence_en']->toIso8601String(),
        ]);
    }

    /** Cierra la sesion de este dispositivo. */
    public function logout(Request $request)
    {
        $cuenta = $request->user('cliente');
        $this->sesiones->cerrarSesion($cuenta->currentAccessToken());

        return ApiRespuesta::ok(['mensaje' => 'Sesion cerrada.']);
    }

    /** Cierra la sesion en todos los dispositivos. */
    public function logoutAll(Request $request)
    {
        $this->sesiones->cerrarTodas($request->user('cliente'));

        return ApiRespuesta::ok(['mensaje' => 'Se cerro la sesion en todos tus dispositivos.']);
    }
}
