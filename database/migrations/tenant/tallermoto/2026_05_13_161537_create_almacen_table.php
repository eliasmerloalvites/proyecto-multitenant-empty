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
        Schema::create('almacen', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('ALM_Id');
            /* CAMPOS */
            $table->string('ALM_Nombre', 50);
            $table->string('ALM_NombreAlmacen', 50);
            $table->string('ALM_Direccion', 50)->nullable();
            $table->string('ALM_Ruc', 12);
            $table->string('ALM_Celular', 9)->nullable();
            $table->boolean('ALM_Status')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen');
    }
};
