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
        Schema::create('mantenimiento_actividad_variadas', function (Blueprint $table) {
            $table->bigIncrements('MAV_Id');
            $table->string('MAV_Placa', 18);
            $table->string('MAV_Propietario', 50);
            $table->string('MAV_celular', 12);
            $table->string('MAV_Unidad', 40);
            $table->string('MAV_KMEntrada', 20);
            $table->text('MAV_DetalleIngreso')->nullable();
            $table->text('MAV_DetalleObservacion')->nullable();
            $table->text('MAV_DetalleRealizado')->nullable();
            $table->text('MAV_CorrecionObservacion')->nullable();
            $table->string('MAV_ProximoCambioAceite', 50)->nullable();
            $table->string('MAV_ProximoServicio', 50)->nullable();
            $table->dateTime('MAV_FechaCreacion')->nullable();
            $table->dateTime('MAV_FechaEdicion')->nullable();
            $table->integer('MAV_UsuarioCreacion')->nullable();
            $table->integer('MAV_UsuarioEditado')->nullable();
            $table->string('MAV_Estado', 20)->default('PENDIENTE');
            $table->integer('PER_Id')->nullable();
            $table->boolean('statevalidate')->default(false);
            $table->boolean('notificar')->default(false);
            $table->text('observacion')->nullable();
            $table->text('respuesta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_actividad_variadas');
    }
};
