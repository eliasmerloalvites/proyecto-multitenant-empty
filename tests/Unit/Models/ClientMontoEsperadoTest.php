<?php

$planConfig = [
    'start' => ['price' => 29.9],
    'basic' => ['price' => 49.9],
    'plus' => ['price' => 79.9],
];

test('montoEsperado usa el precio del plan cuando no hay precio personalizado', function () use ($planConfig) {
    $client = makeClient(['precio_personalizado' => null, 'plan' => 'basic']);

    expect($client->montoEsperado($planConfig))->toBe(49.9);
});

test('montoEsperado usa el precio personalizado cuando está definido, aunque sea 0', function () use ($planConfig) {
    $client = makeClient(['precio_personalizado' => 120.0, 'plan' => 'basic']);
    expect($client->montoEsperado($planConfig))->toBe(120.0);

    $clienteCortesia = makeClient(['precio_personalizado' => 0, 'plan' => 'basic']);
    expect($clienteCortesia->montoEsperado($planConfig))->toBe(0.0);
});

test('montoEsperado acepta un plan explícito en vez de leer $this->plan', function () use ($planConfig) {
    $client = makeClient(['precio_personalizado' => null]);

    expect($client->montoEsperado($planConfig, 'plus'))->toBe(79.9);
});

test('montoEsperado devuelve 0 si el plan no existe en la config (defensivo)', function () use ($planConfig) {
    $client = makeClient(['precio_personalizado' => null, 'plan' => 'plan_borrado']);

    expect($client->montoEsperado($planConfig))->toBe(0.0);
});
