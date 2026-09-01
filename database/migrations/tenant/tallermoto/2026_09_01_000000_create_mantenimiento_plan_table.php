<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Paquetes de checklist configurables por tipo de mantenimiento: el dueño
 * arma un plan (ej. "Cambio de aceite basico") eligiendo cuales items del
 * catalogo fijo de ese tipo quiere que aparezcan. No se tocan las columnas
 * fisicas existentes de cada tabla de mantenimiento (serian ~80 columnas
 * entre los 4 tipos) — el plan solo guarda que codigos de columna mostrar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_plan', function (Blueprint $table) {
            $table->id('PLAN_Id');
            $table->string('PLAN_Tipo', 5); // MGC / MGI / MPC / MPI
            $table->string('PLAN_Nombre', 100);
            $table->json('PLAN_Items'); // ["Det1","Det7","Det7Admision",...]
            $table->boolean('PLAN_Activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_plan');
    }
};
