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
        Schema::create('proveedor', function (Blueprint $table) {
             /* PRIMARY KEY */
            $table->increments('PROV_Id');
            /* CAMPOS */
            $table->string('PROV_TipoDocumento', 50);
            $table->string('PROV_NumDocumento', 12);
            $table->string('PROV_RazonSocial', 50);
            $table->string('PROV_Direccion', 50)->nullable();
            $table->string('PROV_Descripcion', 100)->nullable();
            $table->string('PROV_Celular', 9)->nullable();
            $table->string('PROV_Correo', 50)->nullable();
            $table->boolean('PROV_Status')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('proveedor');
    }
};
