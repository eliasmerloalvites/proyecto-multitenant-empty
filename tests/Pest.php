<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(Tests\TestCase::class)
    ->use(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->afterEach(function () {
        // Limpia las BDs de tenants reales creadas vía provisionarTenantDePrueba()
        // en este test. RefreshDatabase solo hace rollback de la conexión
        // central (`tenants`/`domains`/`clients`) — la BD física del tenant
        // (una BD de MySQL aparte) no se toca con eso, así que hay que
        // borrarla a mano o se acumulan bases de datos "tenant_..." de test.
        foreach ($GLOBALS['__tenants_de_prueba_creados'] ?? [] as $dbName) {
            \Illuminate\Support\Facades\DB::connection('mysql')
                ->statement("DROP DATABASE IF EXISTS `{$dbName}`");
        }
        $GLOBALS['__tenants_de_prueba_creados'] = [];
    })
    ->in('Feature');

// Los tests unitarios también necesitan la app booteada (p.ej. los modelos
// con casts de fecha llaman a getConnection() para resolver el formato),
// pero sin RefreshDatabase: no deben tocar la base de datos.
pest()->extend(Tests\TestCase::class)
    ->in('Unit');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Crea (y persiste) un usuario del guard 'central' listo para actingAs(),
 * junto con el registro 'personal' que su FK exige. Los Feature tests del
 * panel admin (planes, cobros, clientes) parten de aquí en vez de repetir
 * este setup en cada archivo.
 */
function centralUser(array $attrs = []): \App\Models\Central\User
{
    $personal = \App\Models\Central\Personal::create([
        'PER_Nombre' => 'Admin',
        'PER_Apellido' => 'Test',
        'PER_TipoDocumento' => 'DNI',
        'PER_NumeroDocumento' => (string) fake()->unique()->numerify('########'),
        'PER_Edad' => 30,
        'PER_Sexo' => 'MASCULINO',
    ]);

    $user = new \App\Models\Central\User(array_merge([
        'name' => 'Admin Test',
        'email' => fake()->unique()->safeEmail(),
        'password' => bcrypt('password'),
        'estadousuario' => 1,
        'tipousuario' => 1,
    ], $attrs));
    $user->PER_Id = $personal->PER_Id;
    $user->save();

    return $user;
}

/**
 * Client "suelto" (sin persistir, sin Tenant/Domain reales) para tests
 * unitarios de puros métodos del modelo (ciclo de facturación, monto
 * esperado). 'plan' no es $fillable en el modelo real —en producción llega
 * como atributo crudo de un join a `tenants`— así que aquí se asigna
 * directo para simular ese mismo escenario sin tocar la BD.
 */
function makeClient(array $attrs = []): \App\Models\Client
{
    $plan = $attrs['plan'] ?? null;
    unset($attrs['plan']);

    $client = new \App\Models\Client(array_merge([
        'billing_day' => 15,
        'trial_ends_at' => null,
    ], $attrs));

    if ($plan !== null) {
        $client->plan = $plan;
    }

    if (! array_key_exists('pagos', $attrs)) {
        $client->setRelation('pagos', new \Illuminate\Database\Eloquent\Collection);
    }

    return $client;
}

/**
 * Crea un Client con su Tenant y Domain asociados (el join que usa
 * cobros:procesar), listo para probar el ciclo de facturación sin tocar
 * ninguna BD de tenant real.
 *
 * Tenant::create() dispara el evento TenantCreated de stancl/tenancy, que
 * por config (TenancyServiceProvider) crea una BASE DE DATOS FÍSICA real
 * para el tenant (Jobs\CreateDatabase) — un CREATE DATABASE es DDL, así
 * que el rollback de RefreshDatabase NO lo deshace, dejando una BD huérfana
 * por cada test que use este helper. withoutEvents() evita ese efecto
 * secundario: acá solo se necesita la FILA en `tenants` para los joins que
 * usa cobros:procesar, nunca una BD de tenant real (para eso está
 * provisionarTenantDePrueba()).
 */
function clientConTenant(array $clientAttrs = [], array $tenantAttrs = []): \App\Models\Client
{
    $tenantId = 'tallermoto_' . \Illuminate\Support\Str::random(8);

    $tenant = \App\Models\Tenant::withoutEvents(fn () => \App\Models\Tenant::create(array_merge([
        'id' => $tenantId,
        'tipo_negocio' => 'tallermoto',
        'plan' => 'basic',
        'status' => 'activo',
    ], $tenantAttrs)));

    $domain = \Stancl\Tenancy\Database\Models\Domain::create([
        'domain' => $tenantId . '.localhost',
        'tenant_id' => $tenant->id,
    ]);

    return \App\Models\Client::create(array_merge([
        'tenant_id' => $tenant->id,
        'razon_social' => 'Cliente de prueba',
        'ruc' => '20123456789',
        'email' => 'cliente@example.com',
        'billing_day' => 15,
        'status' => 'activo',
        'domain_id' => $domain->id,
    ], $clientAttrs));
}

/**
 * Provisiona un tenant REAL de punta a punta para tests de integración:
 * BD física propia, migraciones, seeders y un usuario admin ya logueable
 * (guard 'tenant'). Reutiliza TenantProvisioningService — el mismo código
 * que usa producción al crear un cliente — para que el tenant de prueba
 * sea idéntico a uno real y no una aproximación.
 *
 * Caro (crea una BD MySQL real): resérvalo para tests que necesiten
 * probar algo que solo existe *dentro* del panel de un tenant (rutas
 * `tenant.*`, middleware que corre ahí, vistas del panel). Para lo demás
 * (lógica de Client/Plan, ProcesarCobrosCommand, Culqi) usa clientConTenant(),
 * que es prácticamente instantáneo.
 *
 * La BD se borra sola en el afterEach global de este archivo — no hace
 * falta limpiar nada manualmente en el test.
 */
function provisionarTenantDePrueba(array $overrides = []): array
{
    $tipoNegocio = $overrides['tipo_negocio'] ?? 'tallermoto';
    $plan = $overrides['plan'] ?? 'basic';

    \App\Models\Plan::firstOrCreate(
        ['key' => $plan, 'tipo_negocio' => $tipoNegocio],
        [
            'nombre' => ucfirst($plan),
            'price' => 49.9,
            'max_users' => 5,
            'max_images' => 10,
            'storage_limit_mb' => 500,
            'custom_domain_enabled' => false,
            'custom_branding' => false,
            'customizable' => false,
            'modules' => \App\Models\Plan::modulosPara($tipoNegocio),
            'limits' => ['branches' => 2, 'cash_registers' => 2],
            'branding' => null,
        ]
    );

    $password = 'password123';
    $subdomain = 'test' . strtolower(\Illuminate\Support\Str::random(10));

    $data = array_merge([
        'tipo_negocio' => $tipoNegocio,
        'plan' => $plan,
        'subdomain' => $subdomain,
        'razon_social' => 'Tenant de Prueba',
        'ruc' => '20123456789',
        'email' => $subdomain . '@example.com',
        'password' => $password,
        'billing_day' => 15,
    ], $overrides);

    $tenant = app(\App\Services\TenantProvisioningService::class)->provision($data);

    $dbName = config('tenancy.database.prefix') . $tenant->id . config('tenancy.database.suffix');
    $GLOBALS['__tenants_de_prueba_creados'][] = $dbName;

    return [
        'tenant' => $tenant,
        'client' => \App\Models\Client::where('tenant_id', $tenant->id)->first(),
        'domain' => $subdomain . '.' . config('app.central_domain'),
        'email' => $data['email'],
        'password' => $password,
    ];
}
