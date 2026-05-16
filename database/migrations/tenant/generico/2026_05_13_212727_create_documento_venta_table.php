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
        Schema::create('documento_venta', function (Blueprint $table) {
             /* PRIMARY KEY */
            $table->increments('DOV_Id');
            /* CAMPOS */
            $table->string('DOV_Tipo', 3);
            $table->string('DOV_TipoOriginal', 3)->nullable();
            $table->string('DOV_Serie', 13)->nullable();
            $table->integer('DOV_Numero', false, true);
            /* FOREIGN KEY */
            $table->unsignedInteger('VEN_Id');
            /* ESTADO */
            $table->string('DOV_Estado', 15);
            $table->string('DOV_Nombre', 100)->nullable();
            $table->integer('DOV_IdRes')->nullable();
            $table->tinyInteger('DOV_StateToRes');
            $table->string('DOV_Pdf', 40)->nullable();
            $table->boolean('DOV_Anulado')->default(0);
            $table->integer('DOV_Vista')->nullable();
            /* INDEX */
            $table->index('VEN_Id', 'R_DOV_KFR1');
            /* FOREIGN KEY */
            $table->foreign('VEN_Id', 'R_DOV_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_venta');
    }
};
