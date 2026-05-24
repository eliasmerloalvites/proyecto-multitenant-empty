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
        Schema::create('mav_imagen', function (Blueprint $table) {
            $table->unsignedBigInteger('MAV_Id');
            $table->unsignedBigInteger('MAVI_Item');
            $table->text('MAVI_url')->nullable();
            $table->string('MAVI_Nombre', 100)->nullable();
            $table->string('MAVI_Peso', 20)->nullable();
            $table->text('MAVI_Thumb')->nullable();

            $table->primary(['MAV_Id', 'MAVI_Item']);

            $table->foreign('MAV_Id', 'RMAVI_1')
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
        Schema::dropIfExists('mav_imagens');
    }
};
