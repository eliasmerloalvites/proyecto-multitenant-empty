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
        Schema::create('mantenimiento_general_inyectada', function (Blueprint $table) {
            $table->id('MGI_Id');

            $table->string('MGI_Placa', 18);
            $table->string('MGI_Propietario', 50);
            $table->string('MGI_celular', 12)->nullable();
            $table->string('MGI_Unidad', 40);

            $table->string('MGI_KMEntrada', 20)->nullable();

            $table->text('MGI_DetalleIngreso')->nullable();
            $table->text('MGI_DetalleObservacion')->nullable();

            $table->string('MGI_Det1', 2)->nullable();
            $table->string('MGI_Det1Informacion', 50)->nullable();

            $table->string('MGI_Det2', 2)->nullable();
            $table->string('MGI_Det3', 2)->nullable();
            $table->string('MGI_Det4', 2)->nullable();
            $table->string('MGI_Det5', 2)->nullable();
            $table->string('MGI_Det6', 2)->nullable();
            $table->string('MGI_Det7', 2)->nullable();
            $table->string('MGI_Det8', 2)->nullable();

            $table->string('MGI_Det9', 2)->nullable();
            $table->string('MGI_Det9Admision', 10)->nullable();
            $table->string('MGI_Det9Escape', 10)->nullable();

            $table->string('MGI_Det10', 2)->nullable();
            $table->string('MGI_Det10Medida', 10)->nullable();

            $table->string('MGI_Det11', 2)->nullable();
            $table->string('MGI_Det11Medida', 10)->nullable();

            $table->string('MGI_Det12', 2)->nullable();
            $table->string('MGI_Det13', 2)->nullable();
            $table->string('MGI_Det14', 2)->nullable();
            $table->string('MGI_Det15', 2)->nullable();
            $table->string('MGI_Det16', 2)->nullable();
            $table->string('MGI_Det17', 2)->nullable();

            $table->string('MGI_Det18', 10)->nullable();
            $table->string('MGI_Det19', 10)->nullable();

            $table->string('MGI_Det20', 2)->nullable();
            $table->string('MGI_Det20Humedad', 10)->nullable();

            $table->string('MGI_Det21', 2)->nullable();

            $table->string('MGI_Det22', 2)->nullable();
            $table->string('MGI_Det22Ventilador', 10)->nullable();

            $table->string('MGI_Det23', 2)->nullable();

            $table->string('MGI_Det24', 2)->nullable();
            $table->string('MGI_Det24Vida', 20)->nullable();
            $table->string('MGI_Det24Carga', 20)->nullable();
            $table->string('MGI_Det24Arranque', 20)->nullable();

            $table->string('MGI_Det25', 2)->nullable();
            $table->string('MGI_Det26', 2)->nullable();
            $table->string('MGI_Det27', 2)->nullable();

            $table->text('MGI_DetalleRealizado')->nullable();
            $table->text('MGI_CorrecionObservacion')->nullable();

            $table->string('MGI_ProximoCambioAceite', 50)->nullable();
            $table->string('MGI_ProximoServicio', 50)->nullable();

            $table->dateTime('MGI_FechaCreacion')->nullable();
            $table->dateTime('MGI_FechaEdicion')->nullable();

            $table->integer('MGI_UsuarioCreacion')->nullable();
            $table->integer('MGI_UsuarioEditado')->nullable();

            $table->string('MGI_Estado', 20)->default('PENDIENTE');

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
        Schema::dropIfExists('mantenimiento_general_inyectada');
    }
};
