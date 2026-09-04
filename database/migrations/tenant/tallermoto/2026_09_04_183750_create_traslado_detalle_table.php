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
        Schema::create('traslado_detalle', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('TRD_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('TRA_Id');
            $table->unsignedInteger('PRO_Id');
            $table->unsignedInteger('LOT_IdDestino');
            /* CAMPOS */
            $table->decimal('TRD_Cantidad', 10, 2);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('TRA_Id', 'TRD_KFR1');
            $table->index('PRO_Id', 'TRD_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('TRA_Id', 'TRD_KFR1')
                  ->references('TRA_Id')
                  ->on('traslado')
                  ->onDelete('cascade');

            $table->foreign('PRO_Id', 'TRD_KFR2')
                  ->references('PRO_Id')
                  ->on('producto')
                  ->onDelete('cascade');

            $table->foreign('LOT_IdDestino', 'TRD_KFR3')
                  ->references('LOT_Id')
                  ->on('lote')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('traslado_detalle');
    }
};
