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
        Schema::create('mantenimiento_general_carburada', function (Blueprint $table) {
            $table->id('MGC_Id');

            $table->string('MGC_Placa', 18);
            $table->string('MGC_Propietario', 50);

            $table->string('MGC_celular', 12)->nullable();

            $table->string('MGC_Unidad', 40);

            $table->string('MGC_KMEntrada', 20)->nullable();

            $table->text('MGC_DetalleIngreso')->nullable();
            $table->text('MGC_DetalleObservacion')->nullable();

            $table->string('MGC_Det1', 2)->nullable();
            $table->string('MGC_Det1Informacion', 50)->nullable();

            $table->string('MGC_Det2', 2)->nullable();
            $table->string('MGC_Det3', 2)->nullable();
            $table->string('MGC_Det4', 2)->nullable();
            $table->string('MGC_Det5', 2)->nullable();
            $table->string('MGC_Det6', 2)->nullable();
            $table->string('MGC_Det7', 2)->nullable();

            $table->string('MGC_Det8', 2)->nullable();
            $table->string('MGC_Det8Admision', 10)->nullable();
            $table->string('MGC_Det8Escape', 10)->nullable();

            $table->string('MGC_Det9', 2)->nullable();
            $table->string('MGC_Det9Medida', 10)->nullable();

            $table->string('MGC_Det10', 2)->nullable();
            $table->string('MGC_Det11', 2)->nullable();
            $table->string('MGC_Det12', 2)->nullable();
            $table->string('MGC_Det13', 2)->nullable();
            $table->string('MGC_Det14', 2)->nullable();
            $table->string('MGC_Det15', 2)->nullable();

            $table->string('MGC_Det16', 10)->nullable();
            $table->string('MGC_Det17', 10)->nullable();

            $table->string('MGC_Det18', 2)->nullable();
            $table->string('MGC_Det18Humedad', 10)->nullable();

            $table->string('MGC_Det19', 2)->nullable();
            $table->string('MGC_Det19Ventilador', 10)->nullable();

            $table->string('MGC_Det20', 2)->nullable();

            $table->string('MGC_Det21', 2)->nullable();
            $table->string('MGC_Det21Vida', 20)->nullable();
            $table->string('MGC_Det21Carga', 20)->nullable();
            $table->string('MGC_Det21Arranque', 20)->nullable();

            $table->text('MGC_DetalleRealizado')->nullable();
            $table->text('MGC_CorrecionObservacion')->nullable();

            $table->string('MGC_ProximoCambioAceite', 50)->nullable();
            $table->string('MGC_ProximoServicio', 50)->nullable();

            $table->dateTime('MGC_FechaCreacion')->nullable();
            $table->dateTime('MGC_FechaEdicion')->nullable();

            $table->integer('MGC_UsuarioCreacion')->nullable();
            $table->integer('MGC_UsuarioEditado')->nullable();

            $table->string('MGC_Estado', 20)->default('PENDIENTE');

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
        Schema::dropIfExists('mantenimiento_general_carburada');
    }
};
