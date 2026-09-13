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
        Schema::create('ajuste_detalle', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('AJD_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('AJU_Id');
            $table->unsignedInteger('PRO_Id');
            $table->unsignedInteger('LOT_Id')->nullable();
            /* CAMPOS */
            $table->string('AJD_Tipo', 12);
            $table->decimal('AJD_Cantidad', 10, 2);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('AJU_Id', 'AJD_KFR1');
            $table->index('PRO_Id', 'AJD_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('AJU_Id', 'AJD_KFR1')
                  ->references('AJU_Id')
                  ->on('ajuste')
                  ->onDelete('cascade');

            $table->foreign('PRO_Id', 'AJD_KFR2')
                  ->references('PRO_Id')
                  ->on('producto')
                  ->onDelete('cascade');

            $table->foreign('LOT_Id', 'AJD_KFR3')
                  ->references('LOT_Id')
                  ->on('lote')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ajuste_detalle');
    }
};
