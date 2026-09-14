<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Categorias del "Estado de Recepcion de la Motocicleta" (Inventario
 * Visual / Inspeccion de la Unidad). 100% configurable desde
 * Configuracion > Estado de Recepcion — el seeder solo carga un punto de
 * partida, no hay categorias fijas en codigo. Nunca se borran (solo
 * RCT_Activo = 0) para no perder el historico de recepciones ya guardadas
 * que las referencian.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepcion_categoria', function (Blueprint $table) {
            $table->id('RCT_Id');
            $table->string('RCT_Nombre', 100);
            $table->string('RCT_Grupo', 20); // INVENTARIO | INSPECCION
            $table->unsignedInteger('RCT_Orden')->default(0);
            $table->boolean('RCT_Activo')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepcion_categoria');
    }
};
