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
            $table->string('tenant_id')->nullable(); // se asigna luego
            $table->string('razon_social');
            $table->string('ruc')->nullable();
            $table->enum('tipo_negocio', [
                'generico',
                'optica',
                'ferreteria',
                'restaurant',
                'hotel'
            ]);
            $table->unsignedTinyInteger('billing_day'); // día de facturación
            $table->enum('status', ['activo', 'suspendido'])->default('activo');
            $table->timestamps();
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
