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
        Schema::create('mpi_detalle_reemplazo', function (Blueprint $table) {

            $table->unsignedBigInteger('MPI_Id');

            $table->unsignedBigInteger('MPID_Item');

            $table->string('MPID_Descripcion', 100);

            $table->decimal('MPI_Precio', 10, 2)->nullable();

            $table->primary(['MPI_Id', 'MPID_Item']);

            $table->foreign('MPI_Id', 'RMPID_1')
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
        Schema::dropIfExists('mpi_detalle_reemplazo');
    }
};
