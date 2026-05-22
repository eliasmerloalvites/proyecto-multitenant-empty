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
        Schema::create('detalle_venta', function (Blueprint $table) {
             /* CAMPOS */
            $table->unsignedInteger('VEN_Id');
            $table->unsignedInteger('PRO_Id');
            $table->double('DEV_Cantidad');
            $table->integer('DEV_Item');
            $table->double('DEV_PrecioUnitario', 10, 2)->nullable();
            $table->unsignedInteger('LOT_Id');
            $table->double('DEV_Descuento', 10, 2);
            /* PRIMARY KEY COMPUESTA */
            $table->primary(['VEN_Id', 'DEV_Item']);
            /* INDEX */
            $table->index('VEN_Id', 'R_DV_KFR1');
            $table->index('PRO_Id', 'R_DV_KFR2');
            $table->index('LOT_Id', 'R_DV_KFR3');
            /* FOREIGN KEYS */
            $table->foreign('VEN_Id', 'R_DV_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');

            $table->foreign('PRO_Id', 'R_DV_KFR2')
                  ->references('PRO_Id')
                  ->on('producto')
                  ->onDelete('cascade');

            $table->foreign('LOT_Id', 'R_DV_KFR3')
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
        Schema::dropIfExists('detalle_venta');
    }
};
