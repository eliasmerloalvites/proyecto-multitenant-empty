<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cotizacion (presupuesto para el cliente, sin efecto en stock hasta que se
 * aprueba): PENDIENTE -> APROBADA (queda enlazada a la Venta real que generó)
 * o RECHAZADA. COT_Pdf es el código público/opaco con el que se comparte el
 * PDF (mismo mecanismo que DOV_Pdf en documento_venta).
 *
 * RES_Id es opcional: si la cotización se originó desde una reserva/bahía en
 * curso (Gestión de Proceso / Ventas por Bahía) queda enlazada a esa reserva;
 * si se armó standalone (ej. por teléfono, antes de que la moto llegue), va
 * en null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion', function (Blueprint $table) {
            $table->id('COT_Id');
            $table->unsignedInteger('CLI_Id');
            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->unsignedInteger('VEN_Id')->nullable();
            $table->unsignedBigInteger('RES_Id')->nullable();
            $table->string('COT_Estado', 20)->default('PENDIENTE'); // PENDIENTE / APROBADA / RECHAZADA
            $table->date('COT_FechaVencimiento')->nullable();
            $table->string('COT_Observacion', 500)->nullable();
            $table->string('COT_Pdf', 20)->unique();
            $table->timestamps();

            $table->foreign('CLI_Id')->references('CLI_Id')->on('cliente')->onDelete('cascade');
            $table->foreign('ALM_Id')->references('ALM_Id')->on('almacen')->onDelete('cascade');
            $table->foreign('USU_Id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('VEN_Id')->references('VEN_Id')->on('venta')->nullOnDelete();
            $table->foreign('RES_Id')->references('RES_Id')->on('reservacion')->nullOnDelete();

            $table->index(['COT_Estado', 'ALM_Id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion');
    }
};
