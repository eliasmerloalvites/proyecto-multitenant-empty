<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Una orden de cobro creada en Culqi para un cliente+periodo (el
     * "intento de pago" con su link/QR). Separada de `pagos`: una orden
     * puede quedar pendiente o expirar sin pagarse, y `pagos` debe seguir
     * significando "esto ya se cobró de verdad" (lo que exige
     * ProcesarCobrosCommand/estadoCicloActual). El webhook de Culqi marca
     * esta orden como 'paid' y recién ahí crea el Pago correspondiente.
     */
    public function up(): void
    {
        Schema::create('orden_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->string('periodo', 7); // 'Y-m', igual que pagos.periodo

            $table->string('culqi_order_id')->unique(); // ord_test_xxx / ord_live_xxx
            $table->string('order_number')->unique(); // el que le mandamos a Culqi
            $table->decimal('monto', 8, 2);
            $table->enum('estado', ['pending', 'paid', 'expired'])->default('pending');
            $table->string('payment_code')->nullable(); // código CIP / referencia
            $table->string('qr_url')->nullable();
            $table->timestamp('expires_at');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->unique(['client_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orden_pagos');
    }
};
