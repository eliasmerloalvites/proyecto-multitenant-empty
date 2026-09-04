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
        Schema::create('traslado', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('TRA_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('ALM_OrigenId');
            $table->unsignedInteger('ALM_DestinoId');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->text('TRA_Observacion')->nullable();
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('ALM_OrigenId', 'TRA_KFR1');
            $table->index('ALM_DestinoId', 'TRA_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('ALM_OrigenId', 'TRA_KFR1')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('ALM_DestinoId', 'TRA_KFR2')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'TRA_KFR3')
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
        Schema::dropIfExists('traslado');
    }
};
