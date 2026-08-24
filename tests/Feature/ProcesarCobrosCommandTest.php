<?php

use App\Models\CobroNotificacion;
use App\Models\Pago;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

beforeEach(function () {
    Mail::fake();
    Carbon::setTestNow(Carbon::create(2026, 8, 24)); // billing_day=15 → 9 días de atraso
});

afterEach(function () {
    Carbon::setTestNow();
});

/**
 * El envío de avisos no es el foco de este archivo (eso lo cubre
 * CulqiOrderServiceTest) — bloquea la red real para que el intento de
 * generar el link de pago falle rápido y limpio (ver el catch en
 * ProcesarCobrosCommand::notificarUnaVez). Debe llamarse ANTES que
 * cualquier otro Http::fake() del test: Laravel resuelve el PRIMER stub
 * registrado que matchee la URL, no el más específico.
 */
function bloquearCulqi(): void
{
    Http::fake(['api.culqi.com/*' => Http::response(['error' => 'no faked in this test'], 500)]);
}

test('envía un recordatorio cuando el billing_day cae dentro de los próximos 7 días', function () {
    bloquearCulqi();
    $client = clientConTenant(['billing_day' => 28]); // hoy=24, cae en 4 días

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertSent(\App\Mail\CobroNotificacionMail::class, fn ($mail) => $mail->hasTo($client->email));
    expect(CobroNotificacion::where('client_id', $client->id)->where('tipo', 'recordatorio')->exists())->toBeTrue();
    expect($client->fresh()->status)->toBe('activo');
});

test('un cliente vencido pero dentro de los días de gracia no se suspende', function () {
    bloquearCulqi();
    // billing_day=20, hoy=24 → 4 días de atraso, gracia por defecto = 5.
    $client = clientConTenant(['billing_day' => 20]);

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertSent(\App\Mail\CobroNotificacionMail::class, fn ($mail) => $mail->hasTo($client->email));
    expect(CobroNotificacion::where('client_id', $client->id)->where('tipo', 'vencido')->exists())->toBeTrue();
    expect(CobroNotificacion::where('client_id', $client->id)->where('tipo', 'suspension')->exists())->toBeFalse();
    expect($client->fresh()->status)->toBe('activo');
    expect(Tenant::find($client->tenant_id)->status)->toBe('activo');
});

test('suspende al cliente y a su tenant tras superar los días de gracia', function () {
    bloquearCulqi();
    // billing_day=15, hoy=24 → 9 días de atraso, gracia por defecto = 5.
    $client = clientConTenant(['billing_day' => 15]);

    $this->artisan('cobros:procesar')->assertSuccessful();

    expect($client->fresh()->status)->toBe('suspendido');
    expect(Tenant::find($client->tenant_id)->status)->toBe('suspendido');
    expect(CobroNotificacion::where('client_id', $client->id)->where('tipo', 'suspension')->exists())->toBeTrue();
    expect(\App\Models\AuditLog::where('accion', 'cliente.suspendido.auto')->exists())->toBeTrue();
});

test('un cliente con el periodo ya pagado no recibe avisos ni se suspende', function () {
    bloquearCulqi();
    $client = clientConTenant(['billing_day' => 15]);
    Pago::create([
        'client_id' => $client->id,
        'monto' => 49.9,
        'periodo' => '2026-08',
        'fecha_pago' => '2026-08-10',
        'metodo_pago' => 'efectivo',
    ]);

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertNothingSent();
    expect($client->fresh()->status)->toBe('activo');
});

test('un cliente en periodo de prueba no recibe avisos ni se suspende sin importar el atraso', function () {
    $client = clientConTenant([
        'billing_day' => 1, // muy vencido si no fuera por el trial
        'trial_ends_at' => Carbon::create(2026, 9, 1),
    ]);

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertNothingSent();
    expect($client->fresh()->status)->toBe('activo');
});

test('--dry-run no envía correos, no crea notificaciones ni suspende a nadie', function () {
    $client = clientConTenant(['billing_day' => 15]);

    $this->artisan('cobros:procesar', ['--dry-run' => true])->assertSuccessful();

    Mail::assertNothingSent();
    expect(CobroNotificacion::count())->toBe(0);
    expect($client->fresh()->status)->toBe('activo');
    expect(Tenant::find($client->tenant_id)->status)->toBe('activo');
});

test('no reenvía el mismo tipo de aviso dos veces en el mismo periodo', function () {
    bloquearCulqi();
    $client = clientConTenant(['billing_day' => 20]); // vencido, sin gracia superada

    $this->artisan('cobros:procesar')->assertSuccessful();
    Mail::assertSentCount(1);

    $this->artisan('cobros:procesar')->assertSuccessful();
    Mail::assertSentCount(1); // sigue en 1: no duplica el aviso 'vencido'
    expect(CobroNotificacion::where('client_id', $client->id)->count())->toBe(1);
});

test('un cliente con precio personalizado recibe el aviso con SU monto, no el del plan', function () {
    bloquearCulqi();
    // billing_day=28, hoy=24 → por_vencer, dispara el aviso de 'recordatorio'.
    $client = clientConTenant(['billing_day' => 28, 'precio_personalizado' => 250.00], ['plan' => 'basic']);

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertSent(\App\Mail\CobroNotificacionMail::class, fn ($mail) => $mail->hasTo($client->email) && $mail->monto === 250.00
    );
});

test('el aviso incluye la orden de pago de Culqi cuando la generación funciona', function () {
    Http::fake([
        'api.culqi.com/v2/orders' => Http::response([
            'object' => 'order', 'id' => 'ord_test_ok', 'amount' => 4990,
            'payment_code' => '99999999', 'currency_code' => 'PEN', 'state' => 'pending',
            'qr' => 'https://checkout.culqi.com/qr/fake',
        ], 201),
    ]);
    $client = clientConTenant(['billing_day' => 28]); // por_vencer

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertSent(\App\Mail\CobroNotificacionMail::class, fn ($mail) => $mail->hasTo($client->email)
        && $mail->orden !== null
        && $mail->orden->culqi_order_id === 'ord_test_ok'
    );
    expect(\App\Models\OrdenPago::where('client_id', $client->id)->exists())->toBeTrue();
});

test('un cliente suspendido o sin billing_day no se procesa', function () {
    $suspendido = clientConTenant(['billing_day' => 15, 'status' => 'suspendido']);

    $this->artisan('cobros:procesar')->assertSuccessful();

    Mail::assertNothingSent();
    expect(CobroNotificacion::where('client_id', $suspendido->id)->exists())->toBeFalse();
});
