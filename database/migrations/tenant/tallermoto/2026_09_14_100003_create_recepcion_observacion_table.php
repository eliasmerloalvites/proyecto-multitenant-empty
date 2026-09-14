<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Observacion de texto libre por categoria (una por categoria de
 * inspeccion) o general (RCT_Id null = "lo que manifiesta el cliente",
 * unica por mantenimiento). Mismo ancla (MTO_Tabla, MTO_Id) que
 * recepcion_respuesta — ver esa migracion para el porque.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepcion_observacion', function (Blueprint $table) {
            $table->id('ROB_Id');
            $table->string('MTO_Tabla', 60);
            $table->unsignedBigInteger('MTO_Id');
            $table->unsignedBigInteger('RCT_Id')->nullable();
            $table->text('ROB_Texto')->nullable();
            $table->timestamps();

            $table->foreign('RCT_Id', 'ROB_KFR1')->references('RCT_Id')->on('recepcion_categoria')->restrictOnDelete();
            $table->index(['MTO_Tabla', 'MTO_Id'], 'ROB_KIDX1');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepcion_observacion');
    }
};
