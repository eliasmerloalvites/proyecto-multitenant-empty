<?php

use Carbon\Carbon;

/**
 * Tests de integración reales: provisionan un tenant completo (BD propia,
 * migraciones, usuario) vía provisionarTenantDePrueba() y hacen requests
 * HTTP de verdad contra sus rutas, para probar lo que solo existe dentro
 * del panel de un tenant — el middleware EnsureClientHasPaidCycle y la
 * página "Mi Facturación" — que no se puede probar con clientConTenant()
 * (ese no crea una BD de tenant real, solo las filas centrales).
 */
function loginTenant(array $tenant): void
{
    test()->post("http://{$tenant['domain']}/tenant/login", [
        'email' => $tenant['email'],
        'password' => $tenant['password'],
    ])->assertOk();
}

/**
 * Client::estadoCicloActual() y ProcesarCobrosCommand usan siempre
 * Carbon::now('America/Lima') (UTC-5), pero config('app.timezone') es
 * 'UTC' — Carbon::create() sin zona explícita crea el instante en UTC.
 * A medianoche eso cae 5 horas ANTES en Lima, corriéndose al día
 * calendario anterior. Fijar la zona a mediodía Lima evita ese desfase.
 */
function fijarFecha(int $anio, int $mes, int $dia): void
{
    Carbon::setTestNow(Carbon::create($anio, $mes, $dia, 12, 0, 0, 'America/Lima'));
}

afterEach(function () {
    Carbon::setTestNow();
});

test('provisiona un tenant real y su usuario puede loguearse', function () {
    $t = provisionarTenantDePrueba();

    $response = $this->postJson("http://{$t['domain']}/tenant/login", [
        'email' => $t['email'],
        'password' => $t['password'],
    ]);

    $response->assertOk()->assertJson(['success' => 'Inicio de sesión exitoso']);
});

test('un cliente al día puede entrar normal a su panel', function () {
    fijarFecha(2026, 8, 1);
    // billing_day muy lejos de hoy (>7 días): ciclo 'pendiente', no dispara ningún bloqueo.
    $t = provisionarTenantDePrueba(['billing_day' => 20]);
    loginTenant($t);

    $this->get("http://{$t['domain']}/tenant/home")
        ->assertOk();
});

test('un cliente con el ciclo vencido es redirigido a Mi Facturación desde cualquier ruta', function () {
    fijarFecha(2026, 8, 24);
    // billing_day=1: para el 24, el ciclo de este mes ya venció hace rato.
    $t = provisionarTenantDePrueba(['billing_day' => 1]);
    loginTenant($t);

    $this->get("http://{$t['domain']}/tenant/home")
        ->assertRedirect(route('tenant.facturacion.index'));
});

test('Mi Facturación siempre es alcanzable aunque el ciclo esté vencido (no hay loop de redirección)', function () {
    fijarFecha(2026, 8, 24);
    $t = provisionarTenantDePrueba(['billing_day' => 1]);
    loginTenant($t);

    $this->get("http://{$t['domain']}/tenant/facturacion")
        ->assertOk()
        ->assertSee('VENCIDO')
        ->assertSee('Pagar ahora');
});

test('Mi Facturación muestra el precio personalizado del cliente, no el del plan', function () {
    fijarFecha(2026, 8, 24);
    $t = provisionarTenantDePrueba(['billing_day' => 10]);
    // TenantProvisioningService no acepta precio_personalizado al crear —
    // es un ajuste que se hace después, desde ClientController::update().
    $t['client']->update(['precio_personalizado' => 350]);
    loginTenant($t);

    $this->get("http://{$t['domain']}/tenant/facturacion")
        ->assertOk()
        ->assertSee('S/ 350.00', escape: false);
});
