<?php

use App\Models\AuditLog;
use App\Models\OrdenPago;
use App\Models\Pago;
use Illuminate\Support\Facades\Http;

function fakeCulqiEvento(string $eventId, array $orderData): void
{
    // El controller NUNCA confía en el body del POST recibido — siempre
    // vuelve a pedirle el evento a Culqi por su ID. Solo ese GET es lo que
    // hay que simular para que el webhook procese algo.
    Http::fake([
        "api.culqi.com/v2/events/{$eventId}" => Http::response([
            'object' => 'event',
            'id' => $eventId,
            'type' => 'order.status.changed',
            'creation_date' => now()->timestamp,
            'data' => $orderData,
        ], 200),
    ]);
}

function ordenDePrueba(array $attrs = []): OrdenPago
{
    $client = clientConTenant();

    return OrdenPago::create(array_merge([
        'client_id' => $client->id,
        'periodo' => '2026-08',
        'culqi_order_id' => 'ord_test_abc123',
        'order_number' => 'c' . $client->id . '-2026-08-xyz',
        'monto' => 49.9,
        'estado' => 'pending',
        'expires_at' => now()->addDays(3),
    ], $attrs));
}

test('un webhook de orden pagada crea el Pago y marca la orden como paid', function () {
    $orden = ordenDePrueba();
    fakeCulqiEvento('evt_test_1', [
        'object' => 'order', 'id' => $orden->culqi_order_id, 'state' => 'paid',
    ]);

    $response = $this->postJson(route('webhooks.culqi'), [
        'object' => 'event', 'id' => 'evt_test_1', 'type' => 'order.status.changed',
        // Un atacante podría mandar cualquier cosa acá — el controller la ignora.
        'data' => ['id' => $orden->culqi_order_id, 'state' => 'paid', 'amount' => 1],
    ]);

    $response->assertOk();
    expect($orden->fresh()->estado)->toBe('paid');
    expect(Pago::where('client_id', $orden->client_id)->where('periodo', '2026-08')->exists())->toBeTrue();
    expect(Pago::first()->metodo_pago)->toBe('pasarela');
    expect(AuditLog::where('accion', 'pago.registrado')->exists())->toBeTrue();
});

test('no confía en el body del POST: usa el estado que devuelve el GET a Culqi', function () {
    $orden = ordenDePrueba();
    // El GET de confirmación dice que SIGUE pendiente...
    fakeCulqiEvento('evt_test_2', [
        'object' => 'order', 'id' => $orden->culqi_order_id, 'state' => 'pending',
    ]);

    // ...aunque el POST recibido (falso/manipulado) diga que ya se pagó.
    $this->postJson(route('webhooks.culqi'), [
        'id' => 'evt_test_2', 'type' => 'order.status.changed',
        'data' => ['id' => $orden->culqi_order_id, 'state' => 'paid'],
    ])->assertOk();

    expect($orden->fresh()->estado)->toBe('pending');
    expect(Pago::count())->toBe(0);
});

test('un evento que Culqi no confirma (GET falla) no crea ningún Pago', function () {
    $orden = ordenDePrueba();
    Http::fake(['api.culqi.com/v2/events/*' => Http::response(['error' => 'not found'], 404)]);

    $this->postJson(route('webhooks.culqi'), [
        'id' => 'evt_test_3', 'type' => 'order.status.changed',
        'data' => ['id' => $orden->culqi_order_id, 'state' => 'paid'],
    ])->assertStatus(422);

    expect($orden->fresh()->estado)->toBe('pending');
    expect(Pago::count())->toBe(0);
});

test('un webhook reenviado dos veces no duplica el Pago', function () {
    $orden = ordenDePrueba();
    fakeCulqiEvento('evt_test_4', ['id' => $orden->culqi_order_id, 'state' => 'paid']);

    $payload = ['id' => 'evt_test_4', 'type' => 'order.status.changed', 'data' => ['id' => $orden->culqi_order_id, 'state' => 'paid']];
    $this->postJson(route('webhooks.culqi'), $payload)->assertOk();
    $this->postJson(route('webhooks.culqi'), $payload)->assertOk();

    expect(Pago::count())->toBe(1);
});

test('un order_id que no corresponde a ninguna orden registrada no rompe nada', function () {
    fakeCulqiEvento('evt_test_5', ['id' => 'ord_test_no_existe', 'state' => 'paid']);

    $this->postJson(route('webhooks.culqi'), [
        'id' => 'evt_test_5', 'type' => 'order.status.changed',
        'data' => ['id' => 'ord_test_no_existe', 'state' => 'paid'],
    ])->assertOk();

    expect(Pago::count())->toBe(0);
});

test('un payload sin id de evento válido se rechaza de inmediato', function () {
    $this->postJson(route('webhooks.culqi'), ['type' => 'order.status.changed'])
        ->assertStatus(400);

    Http::assertNothingSent(); // ni siquiera intenta confirmar contra Culqi
});
