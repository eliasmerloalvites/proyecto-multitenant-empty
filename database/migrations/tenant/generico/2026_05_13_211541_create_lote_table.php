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
        Schema::create('lote', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('LOT_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('ALM_Id');
            $table->unsignedInteger('PRO_Id');
            /* CAMPOS */
            $table->string('LOT_TipoIngreso', 20);
            $table->unsignedInteger('LOT_IdIngreso');
            $table->decimal('LOT_CantidadReal', 10, 2)->unsigned();
            $table->decimal('LOT_CantidadIngreso', 10, 0)->unsigned();
            $table->decimal('LOT_PrecioCompra', 10, 2);
            $table->decimal('LOT_PrecioVenta', 10, 2);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('PRO_Id', 'LOT_KFR1');
            $table->index('ALM_Id', 'LOT_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('PRO_Id', 'LOT_KFR1')
                  ->references('PRO_Id')
                  ->on('producto')
                  ->onDelete('cascade');

            $table->foreign('ALM_Id', 'LOT_KFR2')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lote');
    }
};
