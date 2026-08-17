<?php

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;

class PasswordResetController extends Controller
{
    public function show()
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        return view('tenant_' . tenant('tipo_negocio') . '.password.olvide', compact('empresa'));
    }

    public function enviar(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::broker('tenant_users')->sendResetLink(
            $request->only('email')
        );

        if ($request->wantsJson()) {
            return $status === Password::RESET_LINK_SENT
                ? response()->json(['success' => 'Te enviamos un enlace para restablecer tu contraseña.'])
                : response()->json(['error' => 'No encontramos una cuenta con ese correo.'], 422);
        }

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', 'Te enviamos un enlace para restablecer tu contraseña.')
            : back()->withErrors(['email' => 'No encontramos una cuenta con ese correo.']);
    }

    public function showReset(Request $request, string $token)
    {
        $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();

        return view('tenant_' . tenant('tipo_negocio') . '.password.restablecer', [
            'token' => $token,
            'email' => $request->query('email'),
            'empresa' => $empresa,
        ]);
    }

    public function actualizar(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::broker('tenant_users')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($request->wantsJson()) {
            return $status === Password::PASSWORD_RESET
                ? response()->json(['success' => 'Tu contraseña se actualizó correctamente.'])
                : response()->json(['error' => 'El enlace no es válido o ya venció.'], 422);
        }

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('tenant.login')->with('status', 'Tu contraseña se actualizó correctamente.')
            : back()->withErrors(['email' => 'El enlace no es válido o ya venció.']);
    }
}
