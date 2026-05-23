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
        Schema::create('horario', function (Blueprint $table) {
            $table->id('HOR_Id');
            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('TUR_Id');
            $table->string('HOR_Dia', 120);
            $table->string('HOR_Detalle', 120)->nullable();
            $table->string('HOR_Estado', 10)->default('ACT');

            // FOREIGN KEYS
            $table->foreign('ALM_Id', 'RHOR1')->references('ALM_Id')->on('almacen')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('TUR_Id', 'RHOR2')->references('TUR_Id')->on('turno')->restrictOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horario');
    }
};
