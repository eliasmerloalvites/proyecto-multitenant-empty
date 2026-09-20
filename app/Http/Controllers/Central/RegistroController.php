<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Mail\RegistroVerificacionMail;
use App\Models\Plan;
use App\Models\RegistroVerificacion;
use App\Models\Vendedor;
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
            'ref' => 'nullable|string|max:32',
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

        // Código de referido opcional (?ref=CODIGO en el link). Si no
        // corresponde a ningún vendedor activo, se ignora en silencio (mismo
        // criterio que el chequeo de RUC caído más arriba) — no bloquea el
        // registro de nadie por un link mal copiado o vencido.
        $vendedorId = Vendedor::where('codigo_referido', $validated['ref'] ?? null)
            ->where('estado', 'activo')
            ->value('id');

        $verificacion = RegistroVerificacion::create([
            'token' => $token,
            'razon_social' => $validated['razon_social'],
            'ruc' => $validated['ruc'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'subdomain' => $subdomain,
            'tipo_negocio' => $validated['tipo_negocio'],
            'plan' => $validated['plan'],
            'vendedor_id' => $vendedorId,
            'expira_en' => now()->addMinutes(self::MINUTOS_VIGENCIA),
        ]);

        Mail::to($verificacion->email)->send(new RegistroVerificacionMail(
            $verificacion->razon_social,
            route('central.registro.verificar', ['token' => $token]),
        ));

        return view('central.registro.revisa-correo', ['email' => $verificacion->email]);
    }

    public function verificar(string $token)
    {
        $verificacion = RegistroVerificacion::where('token', $token)->first();

        if (! $verificacion) {
            return view('central.registro.error', [
                'mensaje' => 'Este enlace de confirmación no es válido.',
            ]);
        }

        // Ya se creó (el Job de la cola ya terminó bien en algún momento):
        // no se vuelve a aprovisionar, se manda directo al panel.
        if ($verificacion->estaCompletado()) {
            return view('central.registro.exito', [
                'urlPanel' => 'https://' . $verificacion->tenant_domain . '/tenant/login',
            ]);
        }

        // El Job ya está corriendo (el usuario recargó la pantalla de
        // "creando tu cuenta", o volvió a abrir el link mientras tanto):
        // no se dispara dos veces, solo se le vuelve a mostrar la misma
        // pantalla, que sigue consultando el estado por AJAX.
        if ($verificacion->estaProcesando()) {
            return view('central.registro.procesando', ['token' => $token]);
        }

        if ($verificacion->tieneError()) {
            return view('central.registro.error', [
                'mensaje' => $verificacion->error_mensaje ?? 'No se pudo crear tu empresa. Intenta nuevamente o contáctanos.',
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

        // Primera vez que se visita el link: dispara la creación en segundo
        // plano (ver ProvisionarTenantJob) y muestra la pantalla de espera
        // de inmediato -- esta petición nunca corre el aprovisionamiento
        // pesado, asi que nginx nunca la va a cortar con un 504.
        $verificacion->update(['estado' => 'procesando']);

        \App\Jobs\ProvisionarTenantJob::dispatch($verificacion->id);

        return view('central.registro.procesando', ['token' => $token]);
    }

    /**
     * Consultado por AJAX desde central.registro.procesando mientras el
     * Job de aprovisionamiento corre en segundo plano.
     */
    public function estadoVerificacion(string $token)
    {
        $verificacion = RegistroVerificacion::where('token', $token)->first();

        if (! $verificacion) {
            return response()->json(['estado' => 'error', 'mensaje' => 'Enlace no válido.'], 404);
        }

        return response()->json([
            'estado' => $verificacion->estado,
            'mensaje' => $verificacion->error_mensaje,
            'url_panel' => $verificacion->estaCompletado()
                ? 'https://' . $verificacion->tenant_domain . '/tenant/login'
                : null,
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
