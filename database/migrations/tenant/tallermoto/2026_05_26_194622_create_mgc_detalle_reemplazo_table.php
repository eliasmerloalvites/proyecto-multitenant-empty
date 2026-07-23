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
        Schema::create('mgc_detalle_reemplazo', function (Blueprint $table) {
            $table->unsignedBigInteger('MGC_Id');
            $table->unsignedBigInteger('MGCD_Item');
            $table->string('MGCD_Descripcion', 100);
            $table->decimal('MGC_Precio', 10, 2)->nullable();
            $table->primary(['MGC_Id', 'MGCD_Item']);
            $table->foreign('MGC_Id', 'RMGCD_1')
                ->references('MGC_Id')
                ->on('mantenimiento_general_carburada')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mgc_detalle_reemplazo');
    }
};
