<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Cuenta" que acompaña a una reserva mientras se atiende la moto en su
 * bahía: se abre al empezar, se le van cargando items (bahia_cuenta_item,
 * sin tocar stock todavía) y se cierra cuando se cobra (queda enlazada a la
 * Venta real) o cuando se cierra sin cobrar (revisión que no generó venta).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahia_cuenta', function (Blueprint $table) {
            $table->id('BCT_Id');
            $table->unsignedBigInteger('RES_Id');
            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('BAH_Id');
            $table->unsignedInteger('VEN_Id')->nullable();
            $table->string('BCT_Estado', 20)->default('ABIERTA'); // ABIERTA / CERRADA / CERRADA_SIN_COBRO
            $table->unsignedBigInteger('USU_Id_Abre');
            $table->timestamp('BCT_AbiertoEn')->useCurrent();
            $table->timestamp('BCT_CerradoEn')->nullable();
            $table->timestamps();

            $table->foreign('RES_Id')->references('RES_Id')->on('reservacion')->onDelete('cascade');
            $table->foreign('ALM_Id')->references('ALM_Id')->on('almacen')->onDelete('cascade');
            $table->foreign('BAH_Id')->references('BAH_Id')->on('bahia')->onDelete('cascade');
            $table->foreign('VEN_Id')->references('VEN_Id')->on('venta')->nullOnDelete();
            $table->foreign('USU_Id_Abre')->references('id')->on('users')->restrictOnDelete();

            $table->index(['RES_Id', 'BCT_Estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahia_cuenta');
    }
};
