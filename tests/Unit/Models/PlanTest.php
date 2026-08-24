<?php

use App\Models\Plan;

test('toConfigArray tiene la misma forma que antes tenía config(saas.plans)', function () {
    $plan = new Plan([
        'price' => 99.9,
        'max_users' => 5,
        'max_images' => 10,
        'storage_limit_mb' => 500,
        'custom_domain_enabled' => true,
        'custom_branding' => false,
        'customizable' => true,
        'modules' => ['reports' => true],
        'limits' => ['branches' => 2],
        'branding' => ['logo' => 'x.png', 'primary_color' => '#111111'],
    ]);

    expect($plan->toConfigArray())->toBe([
        'price' => 99.9,
        'max_users' => 5,
        'max_images' => 10,
        'storage_limit_mb' => 500,
        'custom_domain_enabled' => true,
        'custom_branding' => false,
        'data' => [
            'branding' => ['logo' => 'x.png', 'primary_color' => '#111111'],
            'modules' => ['reports' => true],
            'limits' => ['branches' => 2],
            'customizable' => true,
        ],
    ]);
});

test('toConfigArray usa un branding por defecto cuando el plan no tiene uno', function () {
    $plan = new Plan([
        'price' => 0,
        'max_users' => 1,
        'max_images' => 1,
        'storage_limit_mb' => 1,
        'custom_domain_enabled' => false,
        'custom_branding' => false,
        'customizable' => false,
        'modules' => null,
        'limits' => null,
        'branding' => null,
    ]);

    expect($plan->toConfigArray()['data']['branding'])
        ->toBe(['logo' => null, 'primary_color' => '#0B63CE']);
});

test('modulosPara devuelve los modulos especificos de tallermoto', function () {
    expect(Plan::modulosPara('tallermoto'))->toBe(Plan::MODULOS_POR_NEGOCIO['tallermoto']);
});

test('modulosPara devuelve los modulos especificos de generico', function () {
    expect(Plan::modulosPara('generico'))->toBe(Plan::MODULOS_POR_NEGOCIO['generico']);
});

test('modulosPara cae a la lista completa de MODULOS para un negocio desconocido', function () {
    expect(Plan::modulosPara('otro_negocio'))->toBe(Plan::MODULOS);
});

test('generico no incluye modulos que tenant_has_module() ya fuerza siempre a true', function () {
    // productos/inventario/compras/ventas son el negocio mismo en el
    // vertical genérico, no un addon de plan (ver helpers.php).
    $modulosGenerico = array_keys(Plan::MODULOS_POR_NEGOCIO['generico']);

    expect($modulosGenerico)->not->toContain('productos', 'inventario', 'compras', 'ventas', 'mantenimientos');
});

test('todos los modulos por negocio existen en la lista MODULOS de referencia', function () {
    foreach (Plan::MODULOS_POR_NEGOCIO as $tipoNegocio => $modulos) {
        foreach (array_keys($modulos) as $clave) {
            expect(Plan::MODULOS)->toHaveKey($clave);
        }
    }
});
