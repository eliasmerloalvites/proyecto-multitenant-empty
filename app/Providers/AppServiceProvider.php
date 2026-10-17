<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Mail;
use App\Models\Tenant\EmpresaFacturacion;
use App\Models\Tenant\Caja;
use App\Models\Client;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Mailer\Bridge\Brevo\Transport\BrevoTransportFactory;
use Symfony\Component\Mailer\Transport\Dsn;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ProductoController y VentaController tienen ahora una clase propia
        // por vertical (App\Http\Controllers\Tenant\Generico\* y
        // App\Http\Controllers\TenantTallerMotos\*), pero routes/tenant.php
        // sigue registrando una sola ruta por accion apuntando a la clase
        // base App\Http\Controllers\Tenant\*Controller (no se puede elegir
        // la clase por vertical al registrar rutas: ese archivo se carga una
        // sola vez, sin contexto de tenant, y ademas queda cacheado en
        // produccion). Este binding intercepta la resolucion del
        // controlador en el momento de atender el request (ahi si hay
        // tenant() disponible) y la redirige a la subclase que corresponda,
        // sin tocar rutas ni vistas de ningun vertical.
        // Canal por el que se entrega el codigo de verificacion de la app
        // movil. Hoy solo existe el canal de desarrollo ('log'); un canal real
        // (WhatsApp/SMS) se agrega implementando OtpChannel y mapeandolo aqui.
        $this->app->bind(\App\Services\AppMovil\Contracts\OtpChannel::class, function () {
            return match (config('app_movil.otp.canal')) {
                default => new \App\Services\AppMovil\Canales\LogOtpChannel(),
            };
        });

        $this->app->bind(\App\Http\Controllers\Tenant\ProductoController::class, function ($app) {
            return tenant('tipo_negocio') === 'tallermoto'
                ? $app->make(\App\Http\Controllers\TenantTallerMotos\ProductoController::class)
                : $app->make(\App\Http\Controllers\Tenant\Generico\ProductoController::class);
        });

        $this->app->bind(\App\Http\Controllers\Tenant\VentaController::class, function ($app) {
            return tenant('tipo_negocio') === 'tallermoto'
                ? $app->make(\App\Http\Controllers\TenantTallerMotos\VentaController::class)
                : $app->make(\App\Http\Controllers\Tenant\Generico\VentaController::class);
        });

        // CompraController y AjusteController no necesitan una clase propia
        // de generico (generico sigue usando la base tal cual): solo
        // tallermoto necesita su propia subclase, para excluir del
        // catalogo de "comprar"/"ajustar stock" los productos marcados
        // como Servicio (PRO_TipoProducto, columna exclusiva de
        // tallermoto). $app->build() evita el bucle infinito de volver a
        // resolver la misma clase base que se esta bindeando aqui.
        $this->app->bind(\App\Http\Controllers\Tenant\CompraController::class, function ($app) {
            return tenant('tipo_negocio') === 'tallermoto'
                ? $app->make(\App\Http\Controllers\TenantTallerMotos\CompraController::class)
                : $app->build(\App\Http\Controllers\Tenant\CompraController::class);
        });

        $this->app->bind(\App\Http\Controllers\Tenant\AjusteController::class, function ($app) {
            return tenant('tipo_negocio') === 'tallermoto'
                ? $app->make(\App\Http\Controllers\TenantTallerMotos\AjusteController::class)
                : $app->build(\App\Http\Controllers\Tenant\AjusteController::class);
        });

        // Igual que Compra/Ajuste: solo tallermoto necesita su propia
        // subclase, para mostrar el resumen de almacenamiento de archivos
        // en Configuracion > Empresa (generico sigue usando la base tal
        // cual).
        $this->app->bind(\App\Http\Controllers\Tenant\EmpresaFacturacionController::class, function ($app) {
            return tenant('tipo_negocio') === 'tallermoto'
                ? $app->make(\App\Http\Controllers\TenantTallerMotos\EmpresaFacturacionController::class)
                : $app->build(\App\Http\Controllers\Tenant\EmpresaFacturacionController::class);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Transporte de correo vía la API HTTP de Brevo (puerto 443), en vez
        // de SMTP (puertos 587/465): el VPS de producción tiene esos puertos
        // bloqueados por el proveedor de hosting, así que se evita el
        // problema hablando por HTTPS en lugar de una conexión SMTP directa.
        // Se lee vía config() (no env() directo): con el config cacheado en
        // producción, env() fuera de un archivo config/*.php devuelve null
        // aunque la variable exista en el .env.
        // API movil: limites de peticiones. Los limites por celular (espera
        // entre envios, maximo por hora) los aplica OtpService desde la base.
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-directorio', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(30)->by($request->ip()));
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-otp-solicitar', fn ($request) => [
            \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by($request->ip()),
            \Illuminate\Cache\RateLimiting\Limit::perHour(20)->by($request->ip()),
        ]);
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-otp-verificar', fn ($request) => [
            \Illuminate\Cache\RateLimiting\Limit::perMinute(10)->by($request->ip()),
            \Illuminate\Cache\RateLimiting\Limit::perHour(30)->by($request->ip() . '|' . $request->input('celular')),
        ]);
        // Garaje: canjear invitaciones y pedir vinculos tienen limites propios (por cuenta).
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-invitacion', fn ($request) => [
            \Illuminate\Cache\RateLimiting\Limit::perMinute(5)->by('inv|' . ($request->user('cliente')?->getAuthIdentifier() ?: $request->ip())),
            \Illuminate\Cache\RateLimiting\Limit::perHour(20)->by('inv|' . ($request->user('cliente')?->getAuthIdentifier() ?: $request->ip())),
        ]);
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-solicitud', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perHour(10)->by('sol|' . ($request->user('cliente')?->getAuthIdentifier() ?: $request->ip())));
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-sincronizar', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(3)->by('sin|' . ($request->user('cliente')?->getAuthIdentifier() ?: $request->ip())));
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-pedido', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perHour(10)->by('ped|' . ($request->user('cliente')?->getAuthIdentifier() ?: $request->ip())));
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-reserva', fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perHour(10)->by('res|' . ($request->user('cliente')?->getAuthIdentifier() ?: $request->ip())));
        \Illuminate\Support\Facades\RateLimiter::for('app-movil-api',fn ($request) => \Illuminate\Cache\RateLimiting\Limit::perMinute(120)->by($request->user('cliente')?->getAuthIdentifier() ?: $request->ip()));

        Mail::extend('brevo', function () {
            return (new BrevoTransportFactory())->create(
                new Dsn('brevo+api', 'default', config('mail.mailers.brevo.key'))
            );
        });

        /* Si la conexión es la central, llama a los de central
        if (config('database.default') === 'central') {
            $this->call(\Database\Seeders\Central\DatabaseSeeder::class);
        } else {
            // Si no, llama a los de tenant
            $this->call(\Database\Seeders\Tenant\DatabaseSeeder::class);
        } */

        // Los Feature tests solo cubren el lado central (Plan, Client,
        // Pago, Tenant, etc.) contra la BD dedicada
        // proyecto_multitenant_empty_test (ver phpunit.xml). Registrar esto
        // solo en 'testing' evita tocar el flujo normal de migrate en local
        // /producción, donde ya se corre manualmente con --path.
        if ($this->app->environment('testing')) {
            $this->loadMigrationsFrom(database_path('migrations/central'));
        }
        /* if(env('app.env') !== 'local') {
        URL::forceScheme('https');
        } */

        // El sidebar y el layout del panel (tallermoto/generico) usan $empresa
        // (logo, nombre, tema claro/oscuro) sin depender de que cada
        // controlador la pase manualmente. Se resuelve una sola vez por
        // request y se cachea en memoria para no repetir la consulta si la
        // vista se renderiza más de una vez en la misma respuesta.
        View::composer([
            'tenant_tallermoto.partials.sidebar',
            'tenant_generico.partials.sidebar',
            'tenant_tallermoto.layout.appAdminLte',
            'tenant_generico.layout.appAdminLte',
        ], function ($view) {
            static $empresa = null;
            static $resuelto = false;

            if (! $resuelto) {
                $resuelto = true;
                if (tenant() !== null) {
                    $empresa = EmpresaFacturacion::where('tenant_id', tenant('id'))->first();
                }
            }

            $view->with('empresa', $view->empresa ?? $empresa);
        });

        // Caja con la que opera el usuario logueado: el layout usa esto para
        // el indicador de la barra superior y para forzar el modal de
        // selección cuando hay más de una caja activa y todavía no eligió
        // ninguna. Ver tenant_caja_activa_id() en app/helpers.php.
        View::composer([
            'tenant_tallermoto.layout.appAdminLte',
            'tenant_generico.layout.appAdminLte',
        ], function ($view) {
            if (! tenant() || ! Auth::guard('tenant')->check()) {
                $view->with(['cajaActiva' => null, 'cajasDisponibles' => collect(), 'requiereSeleccionCaja' => false]);

                return;
            }

            // Todas las cajas activas (para ofrecer "aperturar" las que estén
            // cerradas), con su sesión abierta precargada si la tienen.
            $cajasDisponibles = Caja::where('CAJ_Status', 1)->with('sesionAbierta')->orderBy('CAJ_Nombre')->get();
            $cajasAbiertas = $cajasDisponibles->filter(fn ($c) => $c->sesionAbierta !== null);

            $cajaActivaId = tenant_caja_activa_id();
            $cajaActiva = $cajaActivaId ? $cajasDisponibles->firstWhere('CAJ_Id', $cajaActivaId) : null;

            $view->with([
                'cajaActiva' => $cajaActiva,
                'cajasDisponibles' => $cajasDisponibles,
                // Modal obligatorio solo cuando hay 2+ cajas YA aperturadas
                // y todavía no se eligió con cuál operar (si solo hay una
                // abierta se autoselecciona sola, sin pedir nada).
                'requiereSeleccionCaja' => $cajasAbiertas->count() > 1 && ! $cajaActiva,
            ]);
        });

        // Aviso de "tu pago está por vencer/vencido" en el dashboard del
        // tenant (banner no bloqueante — el bloqueo real cuando ya está
        // vencido lo hace el middleware EnsureClientHasPaidCycle). Mismo
        // Client::estadoCicloActual() que usa cobros:procesar del lado
        // central, para no tener dos fuentes de verdad sobre el estado del
        // ciclo de pago.
        View::composer([
            'tenant_tallermoto.partials.container',
            'tenant_generico.partials.container',
        ], function ($view) {
            static $resuelto = false;
            static $datos = ['estadoCicloPago' => null, 'fechaCicloPago' => null];

            if (! $resuelto) {
                $resuelto = true;

                if (tenant() && Auth::guard('tenant')->check()) {
                    $client = Client::where('tenant_id', tenant('id'))->first();

                    if ($client) {
                        $hoy = Carbon::now('America/Lima');
                        $datos = [
                            'estadoCicloPago' => $client->estadoCicloActual($hoy),
                            'fechaCicloPago' => $client->fechaCicloActual($hoy),
                        ];
                    }
                }
            }

            $view->with($datos);
        });

        // Aviso global de "hay reservas de mañana por notificar", visible en
        // cualquier pantalla (no solo en la vista de Notificar reservas) para
        // que no se pase por alto si el usuario esta ocupado en otra parte
        // del sistema. Solo aplica a tallermoto (reservas es propio de ese
        // rubro) y solo se calcula una vez por request.
        View::composer([
            'tenant_tallermoto.partials.container',
        ], function ($view) {
            static $resuelto = false;
            static $datos = ['avisoReservasPendientes' => 0];

            if (! $resuelto) {
                $resuelto = true;

                if (tenant() && Auth::guard('tenant')->check() && tenant_has_module('mantenimientos')) {
                    $empresa = EmpresaFacturacion::delTenantActual();

                    if ($empresa && $empresa->reserva_notif_activo) {
                        $ahora = Carbon::now('America/Lima');
                        $yaEsHora = $empresa->reserva_notif_hora
                            ? $ahora->format('H:i') >= substr($empresa->reserva_notif_hora, 0, 5)
                            : true;

                        if ($yaEsHora) {
                            $manana = $ahora->copy()->addDay()->toDateString();

                            $pendientes = \App\Models\TenantTallerMotos\Reservacion::where('RES_Estado', 'ACT')
                                ->where('RES_State', '!=', 'RECHAZADO')
                                ->where('RES_Notificado', false)
                                ->whereDate('RES_FechaProgramada', $manana)
                                ->count();

                            $datos = ['avisoReservasPendientes' => $pendientes];
                        }
                    }
                }
            }

            $view->with($datos);
        });
    }
}
