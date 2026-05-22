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
        Schema::create('venta', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('VEN_Id');
            /* CAMPOS */
            $table->tinyInteger('VEN_TipoPago')->nullable();
            $table->double('VEN_Vuelto', 10, 2)->nullable();
            $table->double('VEN_Pagado', 10, 2)->nullable();
            /* FOREIGN KEYS */
            $table->unsignedInteger('MEP_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->unsignedInteger('CLI_Id');
            $table->unsignedInteger('ALM_Id');
            /* STATUS */
            $table->boolean('VEN_Status')->default(1);
            /* FECHA */
            $table->dateTime('VEN_FechaEnvio')->nullable();
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('MEP_Id', 'VEN_KFR1');
            $table->index('ALM_Id', 'VEN_KFR2');
            $table->index('CLI_Id', 'VEN_KFR3');
            /* FOREIGN KEYS */
            $table->foreign('MEP_Id', 'VEN_KFR1')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('ALM_Id', 'VEN_KFR2')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('CLI_Id', 'VEN_KFR3')
                  ->references('CLI_Id')
                  ->on('cliente')
                  ->onDelete('cascade');

            // Relación con users
            $table->foreign('USU_Id')
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
        Schema::dropIfExists('venta');
    }
};
