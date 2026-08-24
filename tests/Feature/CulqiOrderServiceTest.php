<?php

use App\Models\OrdenPago;
use App\Services\CulqiOrderService;
use Illuminate\Support\Facades\Http;

function fakeCulqiCrearOrden(array $overrides = []): void
{
    // Closure fake (no un Http::response() estático): cada request que crea
    // una orden debe devolver un culqi_order_id distinto, igual que la API
    // real, para poder probar que "orden expiró -> se crea una nueva".
    Http::fake([
        'api.culqi.com/v2/orders' => fn () => Http::response(array_merge([
            'object' => 'order',
            'id' => 'ord_test_' . \Illuminate\Support\Str::random(10),
            'amount' => 4990,
            'payment_code' => '12345678',
            'currency_code' => 'PEN',
            'state' => 'pending',
            'qr' => 'https://checkout.culqi.com/qr/fake',
            'url_pe' => 'https://pagoefectivo.pe/fake',
        ], $overrides), 201),
    ]);
}

test('crea una orden en Culqi y la guarda localmente', function () {
    fakeCulqiCrearOrden();
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    $orden = $service->ordenParaCicloActual($client, '2026-08', 49.9);

    expect($orden)->not->toBeNull();
    expect($orden->estado)->toBe('pending');
    expect((float) $orden->monto)->toBe(49.9);
    expect($orden->culqi_order_id)->toStartWith('ord_test_');

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.culqi.com/v2/orders'
            && $request['amount'] === 4990 // soles -> centavos
            && $request['currency_code'] === 'PEN'
            && $request->hasHeader('Authorization', 'Bearer sk_test_dummy');
    });
});

test('no crea ninguna orden si el monto es 0 o negativo (cortesía)', function () {
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    expect($service->ordenParaCicloActual($client, '2026-08', 0))->toBeNull();

    Http::assertNothingSent();
});

test('rechaza montos fuera del rango que acepta Culqi', function () {
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    $service->ordenParaCicloActual($client, '2026-08', 3.0); // menor a S/6
})->throws(RuntimeException::class, 'fuera del rango');

test('reutiliza la orden existente si sigue pendiente, no expiró y el monto es el mismo', function () {
    fakeCulqiCrearOrden();
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    $primera = $service->ordenParaCicloActual($client, '2026-08', 49.9);
    $segunda = $service->ordenParaCicloActual($client, '2026-08', 49.9);

    expect($segunda->id)->toBe($primera->id);
    Http::assertSentCount(1); // solo se llamó a Culqi una vez
});

test('crea una nueva orden si la anterior expiró', function () {
    fakeCulqiCrearOrden();
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    $primera = $service->ordenParaCicloActual($client, '2026-08', 49.9);
    $primera->update(['expires_at' => now()->subDay()]);

    $segunda = $service->ordenParaCicloActual($client, '2026-08', 49.9);

    expect($segunda->id)->toBe($primera->id); // mismo row (updateOrCreate)
    expect($segunda->culqi_order_id)->not->toBe($primera->getOriginal('culqi_order_id'));
    Http::assertSentCount(2);
});

test('crea una nueva orden si el monto cambió (ej. le fijaron un precio_personalizado nuevo)', function () {
    fakeCulqiCrearOrden();
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    $service->ordenParaCicloActual($client, '2026-08', 49.9);
    $service->ordenParaCicloActual($client, '2026-08', 150.0);

    Http::assertSentCount(2);
    expect((float) OrdenPago::first()->monto)->toBe(150.0);
});

test('lanza una excepción clara si Culqi rechaza la orden', function () {
    Http::fake([
        'api.culqi.com/v2/orders' => Http::response(['merchant_message' => 'RUC inválido'], 400),
    ]);
    $client = clientConTenant();
    $service = app(CulqiOrderService::class);

    $service->ordenParaCicloActual($client, '2026-08', 49.9);
})->throws(RuntimeException::class, 'RUC inválido');
