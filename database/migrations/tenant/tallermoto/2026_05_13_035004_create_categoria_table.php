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
        Schema::create('categoria', function (Blueprint $table) {
            $table->increments('CAT_Id');
            $table->string('CAT_Nombre', 20)->nullable();
            $table->string('CAT_Imagen', 10)->nullable();
            $table->unsignedInteger('CLA_Id');
            $table->timestamps();

            /* INDEX */
            $table->index('CLA_Id', 'KFR1');

            /* FOREIGN KEY */
            $table->foreign('CLA_Id')->references('CLA_Id')->on('clase')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
