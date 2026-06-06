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
        Schema::create('mpc_detalle_reemplazo', function (Blueprint $table) {

            $table->unsignedBigInteger('MPC_Id');

            $table->unsignedBigInteger('MPCD_Item');

            $table->string('MPCD_Descripcion', 100);

            $table->decimal('MPC_Precio', 10, 2)->nullable();

            $table->primary(['MPC_Id', 'MPCD_Item']);

            $table->foreign('MPC_Id', 'RMPCD_1')
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
        Schema::dropIfExists('mpc_detalle_reemplazo');
    }
};
