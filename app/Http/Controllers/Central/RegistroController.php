<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Mail\RegistroVerificacionMail;
use App\Models\Plan;
use App\Models\RegistroVerificacion;
use App\Services\TenantProvisioningService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Models\Domain;

/**
 * Autoregistro público: cualquier visitante crea su cuenta de Taller de
 * Motos sin intervención de un admin. Sin cobro al momento de registrarse
 * (no hay pasarela de pago integrada): la cuenta queda activa de inmediato
 * y el ciclo de facturación normal (módulo Cobros) se encarga de avisar y,
 * si no paga, suspender — igual que a un cliente cargado manualmente.
 *
 * Anti-abuso: el formulario pide confirmar el correo ANTES de crear nada.
 * Mientras no se confirme, solo existe una fila barata en
 * registro_verificaciones — la base de datos del tenant (lo caro) recién
 * se crea al hacer clic en el link del correo. Sumado a rate-limit (ver
 * routes/web.php), honeypot y validación de RUC.
 */
class RegistroController extends Controller
{
    // Verticales habilitados para autoregistro público.
    private const TIPOS_NEGOCIO = ['tallermoto', 'generico'];

    private const MINUTOS_VIGENCIA = 30;

    public function show()
    {
        $planesPorNegocio = Plan::whereIn('tipo_negocio', self::TIPOS_NEGOCIO)
            ->where('key', '!=', 'empresarial')
            ->orderByRaw("FIELD(`key`, 'start', 'basic', 'plus')")
            ->get()
            ->groupBy('tipo_negocio');

        return view('central.registro.show', [
            'planesPorNegocio' => $planesPorNegocio,
            'planSeleccionado' => request('plan', 'basic'),
        ]);
    }

    public function store(Request $request)
    {
        // Honeypot: campo oculto por CSS que un humano nunca llena. Si viene
        // relleno, es un bot — respondemos como si todo hubiera ido bien
        // (para no darle pistas de que fue detectado) pero no hacemos nada.
        if ($request->filled('website')) {
            return view('central.registro.revisa-correo', ['email' => $request->input('email', '')]);
        }

        $validated = $request->validate([
            'razon_social' => 'required|string|max:255',
            'ruc' => 'required|string|max:11|min:11',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
            'subdomain' => ['required', 'alpha_dash', 'min:3', 'max:40'],
            'tipo_negocio' => ['required', 'in:' . implode(',', self::TIPOS_NEGOCIO)],
            'plan' => 'required|in:start,basic,plus',
        ]);

        $subdomain = Str::lower($validated['subdomain']);
        $fullDomain = $subdomain . '.' . config('app.central_domain');

        if (Domain::where('domain', $fullDomain)->exists()) {
            return back()->withInput()->withErrors(['subdomain' => 'Ese subdominio ya está en uso, elige otro.']);
        }

        if (! $this->rucParecevalido($validated['ruc'])) {
            return back()->withInput()->withErrors(['ruc' => 'No pudimos validar ese RUC. Verifícalo e intenta de nuevo.']);
        }

        $planExiste = Plan::paraNegocio($validated['tipo_negocio'])->where('key', $validated['plan'])->exists();
        if (! $planExiste) {
            return back()->withInput()->withErrors(['plan' => 'Ese plan no está disponible para el tipo de negocio elegido.']);
        }

        // Limpia intentos anteriores no confirmados del mismo correo/subdominio
        // para permitir reintentar sin toparse con el índice único del token.
        RegistroVerificacion::whereNull('verificado_en')
            ->where(fn ($q) => $q->where('email', $validated['email'])->orWhere('subdomain', $subdomain))
            ->delete();

        $token = Str::random(48);

        $verificacion = RegistroVerificacion::create([
            'token' => $token,
            'razon_social' => $validated['razon_social'],
            'ruc' => $validated['ruc'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'subdomain' => $subdomain,
            'tipo_negocio' => $validated['tipo_negocio'],
            'plan' => $validated['plan'],
            'expira_en' => now()->addMinutes(self::MINUTOS_VIGENCIA),
        ]);

        Mail::to($verificacion->email)->send(new RegistroVerificacionMail(
            $verificacion->razon_social,
            route('central.registro.verificar', ['token' => $token]),
        ));

        return view('central.registro.revisa-correo', ['email' => $verificacion->email]);
    }

    public function verificar(string $token, TenantProvisioningService $provisioning)
    {
        $verificacion = RegistroVerificacion::where('token', $token)->first();

        if (! $verificacion) {
            return view('central.registro.error', [
                'mensaje' => 'Este enlace de confirmación no es válido.',
            ]);
        }

        if ($verificacion->estaVerificado()) {
            return view('central.registro.error', [
                'mensaje' => 'Este enlace ya fue usado. Si ya creaste tu cuenta, inicia sesión normalmente.',
            ]);
        }

        if ($verificacion->estaVencido()) {
            return view('central.registro.error', [
                'mensaje' => 'Este enlace venció. Vuelve a registrarte para recibir uno nuevo.',
            ]);
        }

        $fullDomain = $verificacion->subdomain . '.' . config('app.central_domain');
        if (Domain::where('domain', $fullDomain)->exists()) {
            return view('central.registro.error', [
                'mensaje' => 'Ese subdominio ya fue tomado mientras confirmabas tu correo. Vuelve a registrarte con otro nombre.',
            ]);
        }

        // 7 días de prueba gratis (config('saas.cobros.dias_prueba_gratis'))
        // antes de que arranque el primer ciclo de cobro. El primer ciclo
        // cae en el día del mes en que termina el trial (no el día del
        // registro), para que el cliente tenga la prueba completa antes de
        // que el sistema empiece a marcarlo como vencido.
        $trialEndsAt = now()->addDays((int) config('saas.cobros.dias_prueba_gratis', 7));

        try {
            $tenant = $provisioning->provision([
                'tipo_negocio' => $verificacion->tipo_negocio,
                'plan' => $verificacion->plan,
                'subdomain' => $verificacion->subdomain,
                'razon_social' => $verificacion->razon_social,
                'ruc' => $verificacion->ruc,
                'email' => $verificacion->email,
                // Ya viene hasheada; TenantProvisioningService la vuelve a
                // hashear con Hash::make, así que le pasamos una contraseña
                // aleatoria interna y actualizamos el hash real después.
                'password' => Str::random(40),
                'billing_day' => min($trialEndsAt->day, 28),
                'trial_ends_at' => $trialEndsAt->toDateString(),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return view('central.registro.error', [
                'mensaje' => 'No se pudo crear tu empresa. Intenta nuevamente o contáctanos.',
            ]);
        }

        // Sobreescribimos con el hash real que el usuario eligió (provision()
        // no lo conoce porque solo recibe contraseñas en texto plano).
        $tenant->run(function () use ($verificacion) {
            \App\Models\Tenant\User::where('email', $verificacion->email)
                ->update(['password' => $verificacion->password]);
        });

        $verificacion->update(['verificado_en' => now()]);

        $domain = $tenant->domains()->first();

        return view('central.registro.exito', [
            'urlPanel' => 'https://' . $domain->domain . '/tenant/login',
        ]);
    }

    /**
     * Consulta rápida a un servicio de RUC (mismo proveedor que ya usa
     * ConsultaDocumentoController) para descartar RUCs inventados. Si el
     * servicio externo falla o no responde, no bloqueamos el registro por
     * eso — solo rechazamos cuando el proveedor confirma explícitamente
     * que el RUC no existe.
     */
    private function rucParecevalido(string $ruc): bool
    {
        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://api.migo.pe/api/v1/ruc',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => http_build_query([
                    'token' => 'I5Q2isdGve4xgTW53inHchckBvpTNnWeLaiDmN4isvuriO8cPAMwriqz5F1U',
                    'ruc' => $ruc,
                ]),
                CURLOPT_TIMEOUT => 4,
            ]);

            $response = curl_exec($curl);
            $curlError = curl_errno($curl);
            curl_close($curl);

            if ($curlError || ! $response) {
                return true; // servicio caído: no bloqueamos por una falla externa.
            }

            $info = json_decode($response, true);

            if (! is_array($info)) {
                return true;
            }

            // El proveedor solo confirma explícitamente cuando SÍ tiene datos.
            // Ausencia de 'nombre_o_razon_social' = RUC no encontrado.
            return ! empty($info['nombre_o_razon_social']);
        } catch (\Throwable $e) {
            report($e);

            return true;
        }
    }
}
