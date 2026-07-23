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
        Schema::create('mpc_imagen', function (Blueprint $table) {

            $table->unsignedBigInteger('MPC_Id');

            $table->unsignedBigInteger('MPCI_Item');

            $table->text('MPCI_url')->nullable();;
            $table->text('MPCI_Thumb')->nullable();

            $table->string('MPCI_Nombre', 100)->nullable();

            $table->string('MPCI_Peso', 20)->nullable();

            $table->primary(['MPC_Id', 'MPCI_Item']);

            $table->foreign('MPC_Id', 'RMPCI_1')
                ->references('MPC_Id')
                ->on('mantenimiento_preventivo_carburada')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpc_imagenes');
    }
};
