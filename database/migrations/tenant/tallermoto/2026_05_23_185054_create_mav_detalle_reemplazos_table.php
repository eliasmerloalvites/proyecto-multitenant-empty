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
        Schema::create('mav_detalle_reemplazo', function (Blueprint $table) {
            $table->unsignedBigInteger('MAV_Id');
            $table->unsignedBigInteger('MAVD_Item');
            $table->string('MAVD_Descripcion', 100);
            $table->decimal('MAV_Precio', 10, 2)->nullable();

            $table->primary(['MAV_Id', 'MAVD_Item']);
            $table->foreign('MAV_Id', 'RMAVD_1')
                ->references('MAV_Id')
                ->on('mantenimiento_actividad_variadas')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mav_detalle_reemplazos');
    }
};
