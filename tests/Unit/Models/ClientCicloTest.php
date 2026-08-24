<?php

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

test('fechaCicloActual cae en el billing_day del mes de hoy', function () {
    $client = makeClient(['billing_day' => 15]);
    $hoy = Carbon::create(2026, 8, 1);

    expect($client->fechaCicloActual($hoy)->toDateString())->toBe('2026-08-15');
});

test('fechaCicloActual respeta meses cortos (billing_day 31 en febrero)', function () {
    $client = makeClient(['billing_day' => 31]);
    $hoy = Carbon::create(2026, 2, 10);

    // 2026 no es bisiesto: febrero tiene 28 días.
    expect($client->fechaCicloActual($hoy)->toDateString())->toBe('2026-02-28');
});

test('proximaFechaCobro usa este mes si billing_day no ha pasado', function () {
    $client = makeClient(['billing_day' => 20]);
    $hoy = Carbon::create(2026, 8, 10);

    expect($client->proximaFechaCobro($hoy)->toDateString())->toBe('2026-08-20');
});

test('proximaFechaCobro salta al mes siguiente si billing_day ya pasó', function () {
    $client = makeClient(['billing_day' => 5]);
    $hoy = Carbon::create(2026, 8, 10);

    expect($client->proximaFechaCobro($hoy)->toDateString())->toBe('2026-09-05');
});

test('proximaFechaCobro se queda en el mes actual el mismo día del billing_day', function () {
    $client = makeClient(['billing_day' => 31]);
    $hoy = Carbon::create(2026, 1, 31);

    // Hoy ES el día de cobro: todavía no "pasó", no debe saltar de mes.
    expect($client->proximaFechaCobro($hoy)->toDateString())->toBe('2026-01-31');
});

test('proximaFechaCobro trunca al saltar a un mes más corto que billing_day', function () {
    $client = makeClient(['billing_day' => 29]);
    $hoy = Carbon::create(2026, 1, 31);

    // El ciclo de enero (día 29) ya pasó; salta a febrero, que en 2026
    // solo tiene 28 días.
    expect($client->proximaFechaCobro($hoy)->toDateString())->toBe('2026-02-28');
});

test('enPeriodoDePrueba es true mientras trial_ends_at no ha llegado', function () {
    $client = makeClient(['trial_ends_at' => Carbon::create(2026, 9, 1)]);
    $hoy = Carbon::create(2026, 8, 24);

    expect($client->enPeriodoDePrueba($hoy))->toBeTrue();
});

test('enPeriodoDePrueba es false el mismo día en que termina el trial', function () {
    $client = makeClient(['trial_ends_at' => Carbon::create(2026, 8, 24)]);
    $hoy = Carbon::create(2026, 8, 24);

    expect($client->enPeriodoDePrueba($hoy))->toBeFalse();
});

test('estadoCicloActual es en_prueba durante el trial, sin importar billing_day', function () {
    $client = makeClient([
        'billing_day' => 1,
        'trial_ends_at' => Carbon::create(2026, 9, 1),
    ]);
    $hoy = Carbon::create(2026, 8, 24);

    expect($client->estadoCicloActual($hoy))->toBe('en_prueba');
});

test('estadoCicloActual es pagado si ya existe un pago del periodo actual', function () {
    $client = makeClient(['billing_day' => 1]);
    $client->setRelation('pagos', new Collection([
        new App\Models\Pago(['periodo' => '2026-08']),
    ]));
    $hoy = Carbon::create(2026, 8, 24);

    expect($client->estadoCicloActual($hoy))->toBe('pagado');
});

test('estadoCicloActual es vencido si billing_day ya pasó y no hay pago', function () {
    $client = makeClient(['billing_day' => 1]);
    $hoy = Carbon::create(2026, 8, 24);

    expect($client->estadoCicloActual($hoy))->toBe('vencido');
});

test('estadoCicloActual es por_vencer si billing_day cae dentro de los próximos 7 días', function () {
    $client = makeClient(['billing_day' => 28]);
    $hoy = Carbon::create(2026, 8, 24);

    expect($client->estadoCicloActual($hoy))->toBe('por_vencer');
});

test('estadoCicloActual es pendiente si billing_day cae en más de 7 días', function () {
    $client = makeClient(['billing_day' => 31]);
    $hoy = Carbon::create(2026, 8, 1);

    expect($client->estadoCicloActual($hoy))->toBe('pendiente');
});
