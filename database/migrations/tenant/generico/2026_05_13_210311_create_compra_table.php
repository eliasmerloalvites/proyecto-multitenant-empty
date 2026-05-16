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
        Schema::create('compra', function (Blueprint $table) {
             /* PRIMARY KEY */
            $table->increments('COM_Id');
            /* CAMPOS */
            $table->string('COM_TipoDocumento', 50);
            $table->string('COM_NumDocumento', 12);
            $table->string('COM_TipoPago', 50);
            /* FOREIGN KEYS */
            $table->unsignedInteger('MEP_Id');
            $table->unsignedInteger('PROV_Id');
            $table->unsignedBigInteger('USU_Id');
            /* STATUS */
            $table->boolean('COM_Status')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('MEP_Id', 'COM_KFR1');
            $table->index('PROV_Id', 'COM_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('MEP_Id', 'COM_KFR1')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('PROV_Id', 'COM_KFR2')
                  ->references('PROV_Id')
                  ->on('proveedor')
                  ->onDelete('cascade');
            $table->foreign('USU_Id', 'COM_KFR3')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compra');
    }
};
