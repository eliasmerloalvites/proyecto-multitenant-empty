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
        Schema::create('mpi_imagen', function (Blueprint $table) {

            $table->unsignedBigInteger('MPI_Id');

            $table->unsignedBigInteger('MPII_Item');

            $table->text('MPII_url')->nullable();;

            $table->text('MPII_Thumb')->nullable();;

            $table->string('MPII_Nombre', 100)->nullable();

            $table->string('MPII_Peso', 20)->nullable();

            $table->primary(['MPI_Id', 'MPII_Item']);

            $table->foreign('MPI_Id', 'RMPII_1')
                ->references('MPI_Id')
                ->on('mantenimiento_preventivo_inyectada')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpi_imagenes');
    }
};
