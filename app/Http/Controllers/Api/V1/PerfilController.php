<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CuentaPublica;
use App\Support\ApiRespuesta;
use Illuminate\Http\Request;

/** Perfil basico de la cuenta de la app. */
class PerfilController extends Controller
{
    public function show(Request $request)
    {
        return ApiRespuesta::ok(CuentaPublica::datos($request->user('cliente')));
    }

    public function update(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['nullable', 'string', 'max:120'],
            'correo' => ['nullable', 'email', 'max:120'],
        ]);

        $cuenta = $request->user('cliente');

        $cuenta->forceFill([
            'CMV_Nombre' => $datos['nombre'] ?? $cuenta->CMV_Nombre,
            'CMV_Correo' => $datos['correo'] ?? $cuenta->CMV_Correo,
        ])->save();

        return ApiRespuesta::ok(CuentaPublica::datos($cuenta));
    }
}
