<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Entidad "moto" (hasta ahora no existia: la placa solo vivia como texto en
 * `reservacion` y en las 5 tablas de mantenimiento). Se identifica por la
 * placa NORMALIZADA (mayusculas, solo letras y numeros), y existe unicamente
 * para poder vincular cuentas de la app movil con motos de forma controlada.
 * Las tablas existentes no se tocan: se relacionan con la moto por esa placa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moto', function (Blueprint $table) {
            $table->id('MOT_Id');
            $table->string('MOT_Placa', 10)->unique();
            // Ultima descripcion conocida (marca/modelo tal como la escribio el taller).
            $table->string('MOT_Descripcion', 150)->nullable();
            $table->string('MOT_Estado', 15)->default('ACTIVA');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moto');
    }
};
