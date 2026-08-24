<?php

use App\Models\AuditLog;
use App\Models\Tenant;

test('un admin puede fijar un precio personalizado para un cliente', function () {
    $admin = centralUser();
    $client = clientConTenant();

    $response = $this->actingAs($admin, 'central')->putJson(route('admin.clients.update', $client), [
        'razon_social' => $client->razon_social,
        'billing_day' => $client->billing_day,
        'precio_personalizado' => 150.50,
        'plan' => 'basic',
        'status' => 'activo',
    ]);

    $response->assertOk();
    expect((float) $client->refresh()->precio_personalizado)->toBe(150.50);
    expect(AuditLog::where('accion', 'cliente.actualizado')->latest('id')->first()->descripcion)
        ->toContain('precio personalizado estándar del plan → S/150.5');
});

test('un admin puede quitar el precio personalizado para volver al precio estándar del plan', function () {
    $admin = centralUser();
    $client = clientConTenant(['precio_personalizado' => 200]);

    $this->actingAs($admin, 'central')->putJson(route('admin.clients.update', $client), [
        'razon_social' => $client->razon_social,
        'billing_day' => $client->billing_day,
        'precio_personalizado' => null,
        'plan' => 'basic',
        'status' => 'activo',
    ])->assertOk();

    expect($client->refresh()->precio_personalizado)->toBeNull();
});

test('precio_personalizado rechaza valores negativos', function () {
    $admin = centralUser();
    $client = clientConTenant();

    $this->actingAs($admin, 'central')->putJson(route('admin.clients.update', $client), [
        'razon_social' => $client->razon_social,
        'billing_day' => $client->billing_day,
        'precio_personalizado' => -10,
        'plan' => 'basic',
        'status' => 'activo',
    ])->assertJsonValidationErrors('precio_personalizado');
});

test('show() expone el monto_esperado ya resuelto (personalizado o del plan)', function () {
    $admin = centralUser();

    \App\Models\Plan::create([
        'key' => 'basic', 'tipo_negocio' => 'tallermoto', 'nombre' => 'Basic',
        'price' => 49.9, 'max_users' => 3, 'max_images' => 5, 'storage_limit_mb' => 200,
        'custom_domain_enabled' => false, 'custom_branding' => false, 'customizable' => false,
        'modules' => [], 'limits' => [], 'branding' => null,
    ]);

    $clienteEstandar = clientConTenant(['precio_personalizado' => null], ['plan' => 'basic']);
    $clientePersonalizado = clientConTenant(['precio_personalizado' => 300], ['plan' => 'basic']);

    $this->actingAs($admin, 'central')->getJson(route('admin.clients.show', $clienteEstandar))
        ->assertJsonPath('data.monto_esperado', 49.9);

    $this->actingAs($admin, 'central')->getJson(route('admin.clients.show', $clientePersonalizado))
        ->assertJsonPath('data.monto_esperado', 300);
});
