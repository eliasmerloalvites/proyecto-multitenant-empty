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
 */
function clientConTenant(array $clientAttrs = [], array $tenantAttrs = []): \App\Models\Client
{
    $tenantId = 'tallermoto_' . \Illuminate\Support\Str::random(8);

    $tenant = \App\Models\Tenant::create(array_merge([
        'id' => $tenantId,
        'tipo_negocio' => 'tallermoto',
        'plan' => 'basic',
        'status' => 'activo',
    ], $tenantAttrs));

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
