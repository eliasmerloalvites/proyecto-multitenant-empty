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
        Schema::create('ajuste', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('AJU_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->string('AJU_Motivo', 30);
            $table->text('AJU_Observacion')->nullable();
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('ALM_Id', 'AJU_KFR1');
            /* FOREIGN KEYS */
            $table->foreign('ALM_Id', 'AJU_KFR1')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'AJU_KFR2')
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
        Schema::dropIfExists('ajuste');
    }
};
