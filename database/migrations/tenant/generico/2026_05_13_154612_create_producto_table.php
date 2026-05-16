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
        Schema::create('producto', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('PRO_Id');
            /* CAMPOS */
            $table->string('PRO_Nombre', 50);
            $table->string('PRO_Descripcion', 250)->nullable();
            $table->decimal('PRO_PrecioCompra', 10, 2);
            $table->decimal('PRO_PrecioVenta', 10, 2);
            $table->string('PRO_Marca', 50)->nullable();
            $table->string('PRO_Imagen', 10)->nullable();
            $table->unsignedInteger('CAT_Id');
            $table->boolean('PRO_Status')->default(1);
            $table->timestamps();
            /* INDEX */
            $table->index('CAT_Id', 'PRO_KFR1');
            /* FOREIGN KEY */
            $table->foreign('CAT_Id')->references('CAT_Id')->on('categoria')->onDelete('cascade');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto');
    }
};
