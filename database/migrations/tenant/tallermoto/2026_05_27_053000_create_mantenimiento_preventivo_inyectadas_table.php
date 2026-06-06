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
        Schema::create('mantenimiento_preventivo_inyectada', function (Blueprint $table) {

            $table->id('MPI_Id');

            $table->string('MPI_Placa', 18);

            $table->string('MPI_Propietario', 50);

            $table->string('MPI_celular', 12)->nullable();

            $table->string('MPI_Unidad', 40);

            $table->string('MPI_KMEntrada', 20)->nullable();

            $table->text('MPI_DetalleIngreso')->nullable();

            $table->text('MPI_DetalleObservacion')->nullable();

            $table->string('MPI_Det1', 2)->nullable();

            $table->string('MPI_Det1Informacion', 50)->nullable();

            $table->string('MPI_Det2', 2)->nullable();

            $table->string('MPI_Det3', 2)->nullable();

            $table->string('MPI_Det4', 2)->nullable();

            $table->string('MPI_Det5', 2)->nullable();

            $table->string('MPI_Det6', 2)->nullable();

            $table->string('MPI_Det7', 2)->nullable();

            $table->string('MPI_Det7Admision', 10)->nullable();

            $table->string('MPI_Det7Escape', 10)->nullable();

            $table->string('MPI_Det8', 2)->nullable();

            $table->string('MPI_Det8Medida', 10)->nullable();

            $table->string('MPI_Det9', 2)->nullable();

            $table->string('MPI_Det10', 2)->nullable();

            $table->string('MPI_Det11', 2)->nullable();

            $table->string('MPI_Det12', 2)->nullable();

            $table->string('MPI_Det13', 2)->nullable();

            $table->string('MPI_Det14', 2)->nullable();

            $table->string('MPI_Det15', 10)->nullable();

            $table->string('MPI_Det16', 10)->nullable();

            $table->string('MPI_Det17', 2)->nullable();

            $table->string('MPI_Det17Ventilador', 10)->nullable();

            $table->string('MPI_Det18', 2)->nullable();

            $table->string('MPI_Det19', 2)->nullable();

            $table->string('MPI_Det19Vida', 20)->nullable();

            $table->string('MPI_Det19Carga', 20)->nullable();

            $table->string('MPI_Det19Arranque', 20)->nullable();

            $table->string('MPI_Det20', 2)->nullable();

            $table->text('MPI_DetalleRealizado')->nullable();

            $table->text('MPI_CorrecionObservacion')->nullable();

            $table->string('MPI_ProximoCambioAceite', 50)->nullable();

            $table->string('MPI_ProximoServicio', 50)->nullable();

            $table->dateTime('MPI_FechaCreacion')->nullable();

            $table->dateTime('MPI_FechaEdicion')->nullable();

            $table->integer('MPI_UsuarioCreacion')->nullable();

            $table->integer('MPI_UsuarioEditado')->nullable();

            $table->string('MPI_Estado', 20)->default('PENDIENTE');

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
        Schema::dropIfExists('mantenimiento_preventivo_inyectada');
    }
};
