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
        Schema::create('mantenimiento_preventivo_carburada', function (Blueprint $table) {

            $table->id('MPC_Id');

            $table->string('MPC_Placa', 18);

            $table->string('MPC_Propietario', 50);

            $table->string('MPC_celular', 12)->nullable();

            $table->string('MPC_Unidad', 40);

            $table->string('MPC_KMEntrada', 20)->nullable();

            $table->text('MPC_DetalleIngreso')->nullable();

            $table->text('MPC_DetalleObservacion')->nullable();

            $table->string('MPC_Det1', 2)->nullable();

            $table->string('MPC_Det1Informacion', 50)->nullable();

            $table->string('MPC_Det2', 2)->nullable();

            $table->string('MPC_Det3', 2)->nullable();

            $table->string('MPC_Det4', 2)->nullable();

            $table->string('MPC_Det5', 2)->nullable();

            $table->string('MPC_Det6', 2)->nullable();

            $table->string('MPC_Det7', 2)->nullable();

            $table->string('MPC_Det7Admision', 10)->nullable();

            $table->string('MPC_Det7Escape', 10)->nullable();

            $table->string('MPC_Det8', 2)->nullable();

            $table->string('MPC_Det8Medida', 10)->nullable();

            $table->string('MPC_Det9', 10)->nullable();

            $table->string('MPC_Det10', 10)->nullable();

            $table->string('MPC_Det11', 2)->nullable();

            $table->string('MPC_Det11Vida', 20)->nullable();

            $table->string('MPC_Det11Carga', 20)->nullable();

            $table->string('MPC_Det11Arranque', 20)->nullable();

            $table->text('MPC_DetalleRealizado')->nullable();

            $table->text('MPC_CorrecionObservacion')->nullable();

            $table->string('MPC_ProximoCambioAceite', 50)->nullable();

            $table->string('MPC_ProximoServicio', 50)->nullable();

            $table->dateTime('MPC_FechaCreacion')->nullable();

            $table->dateTime('MPC_FechaEdicion')->nullable();

            $table->integer('MPC_UsuarioCreacion')->nullable();

            $table->integer('MPC_UsuarioEditado')->nullable();

            $table->string('MPC_Estado', 20)->default('PENDIENTE');

            $table->integer('PER_Id')->nullable();

            $table->boolean('statevalidate')->default(false);

            $table->boolean('notificar')->default(false);

            $table->text('observacion')->nullable();

            $table->text('respuesta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_preventivo_carburada');
    }
};
