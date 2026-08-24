<?php

use App\Models\Plan;

function makePlan(array $attrs = []): Plan
{
    return Plan::create(array_merge([
        'key' => 'basic',
        'tipo_negocio' => 'tallermoto',
        'nombre' => 'Basic',
        'price' => 49.9,
        'max_users' => 3,
        'max_images' => 5,
        'storage_limit_mb' => 200,
        'custom_domain_enabled' => false,
        'custom_branding' => false,
        'customizable' => false,
        'modules' => ['reports' => false, 'analytics' => false],
        'limits' => ['branches' => 1, 'cash_registers' => 1],
        'branding' => null,
    ], $attrs));
}

test('un admin central puede actualizar un plan', function () {
    $admin = centralUser();
    $plan = makePlan();

    $response = $this->actingAs($admin, 'central')->putJson(route('admin.planes.update', $plan), [
        'nombre' => 'Basic Plus',
        'price' => 79.9,
        'max_users' => 5,
        'max_images' => 10,
        'storage_limit_mb' => 500,
        'branches' => 2,
        'cash_registers' => 2,
        'modules' => ['reports'],
    ]);

    $response->assertOk()->assertJson(['success' => 'Plan "Basic Plus" actualizado correctamente.']);

    $plan->refresh();
    expect($plan->nombre)->toBe('Basic Plus');
    expect((float) $plan->price)->toBe(79.9);
    expect($plan->max_users)->toBe(5);
    expect($plan->limits)->toBe(['branches' => 2, 'cash_registers' => 2]);
});

test('actualizar un plan solo marca en modules los checkboxes enviados', function () {
    $admin = centralUser();
    $plan = makePlan(['modules' => ['reports' => true, 'analytics' => true, 'api_access' => true]]);

    $this->actingAs($admin, 'central')->putJson(route('admin.planes.update', $plan), [
        'nombre' => $plan->nombre,
        'price' => $plan->price,
        'max_users' => $plan->max_users,
        'max_images' => $plan->max_images,
        'storage_limit_mb' => $plan->storage_limit_mb,
        'branches' => 1,
        'cash_registers' => 1,
        'modules' => ['reports'], // solo este queda marcado
    ])->assertOk();

    $plan->refresh();
    expect($plan->modules)->toBe([
        'reports' => true,
        'analytics' => false,
        'api_access' => false,
        'mantenimientos' => false,
        'productos' => false,
        'inventario' => false,
        'compras' => false,
        'ventas' => false,
    ]);
});

test('actualizar un plan preserva claves de modules ajenas al vertical del plan', function () {
    $admin = centralUser();
    // 'legacy_flag' no pertenece a Plan::modulosPara('tallermoto'), así que
    // el controller no debe tocarlo al reconstruir el array de modules.
    $plan = makePlan(['modules' => ['reports' => true, 'legacy_flag' => true]]);

    $this->actingAs($admin, 'central')->putJson(route('admin.planes.update', $plan), [
        'nombre' => $plan->nombre,
        'price' => $plan->price,
        'max_users' => $plan->max_users,
        'max_images' => $plan->max_images,
        'storage_limit_mb' => $plan->storage_limit_mb,
        'branches' => 1,
        'cash_registers' => 1,
        'modules' => [],
    ])->assertOk();

    expect($plan->refresh()->modules)->toHaveKey('legacy_flag', true);
});

test('actualizar un plan rechaza max_users menor a 1', function () {
    $admin = centralUser();
    $plan = makePlan();

    $this->actingAs($admin, 'central')->putJson(route('admin.planes.update', $plan), [
        'nombre' => 'X',
        'price' => 10,
        'max_users' => 0,
        'max_images' => 1,
        'storage_limit_mb' => 1,
        'branches' => 1,
        'cash_registers' => 1,
    ])->assertJsonValidationErrors('max_users');
});

test('un módulo desconocido en la request es rechazado', function () {
    $admin = centralUser();
    $plan = makePlan();

    $this->actingAs($admin, 'central')->putJson(route('admin.planes.update', $plan), [
        'nombre' => 'X',
        'price' => 10,
        'max_users' => 1,
        'max_images' => 1,
        'storage_limit_mb' => 1,
        'branches' => 1,
        'cash_registers' => 1,
        'modules' => ['no_existe'],
    ])->assertJsonValidationErrors('modules.0');
});

test('un usuario sin autenticar no puede actualizar un plan', function () {
    $plan = makePlan();

    $this->putJson(route('admin.planes.update', $plan), ['nombre' => 'Hackeado'])
        ->assertUnauthorized();

    expect($plan->refresh()->nombre)->toBe('Basic');
});
