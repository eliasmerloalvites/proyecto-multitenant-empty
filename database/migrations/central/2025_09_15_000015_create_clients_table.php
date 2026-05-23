<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();

            /* RELACION TENANT */
            $table->string('tenant_id');

            /* INFORMACION COMERCIAL */
            $table->string('razon_social');
            $table->string('ruc')->nullable();
            /* FACTURACION */
            $table->unsignedTinyInteger('billing_day');
            $table->date('next_payment_date')->nullable();
            /* ESTADO COMERCIAL */
            $table->enum('status', ['activo','suspendido','cancelado'])->default('activo');
            $table->timestamps();

            /*  FORENG KEYS */
            $table->foreignId('domain_id')
            ->constrained('domains')
            ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
