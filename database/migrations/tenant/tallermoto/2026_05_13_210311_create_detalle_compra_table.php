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
        Schema::create('detalle_compra', function (Blueprint $table) {
            /* FOREIGN KEYS */
            $table->unsignedInteger('COM_Id');
            $table->unsignedInteger('ALM_Id');
            $table->unsignedInteger('PRO_Id');
            /* CAMPOS */
            $table->integer('DCOM_Item');
            $table->integer('DCOM_Cantidad');
            $table->decimal('DCOM_PrecioCompra', 10, 2);
            $table->decimal('DCOM_PrecioVenta', 10, 2);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('COM_Id', 'DCOM_KFR1');
            $table->index('ALM_Id', 'DCOM_KFR2');
            $table->index('PRO_Id', 'DCOM_KFR3');
            /* PRIMARY KEY COMPUESTA */
            $table->primary(['COM_Id', 'DCOM_Item']);
            /* FOREIGN KEYS */
            $table->foreign('COM_Id', 'DCOM_KFR1')
                  ->references('COM_Id')
                  ->on('compra')
                  ->onDelete('cascade');

            $table->foreign('ALM_Id', 'DCOM_KFR2')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('PRO_Id', 'DCOM_KFR3')
                  ->references('PRO_Id')
                  ->on('producto')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalle_compra');
    }
};
