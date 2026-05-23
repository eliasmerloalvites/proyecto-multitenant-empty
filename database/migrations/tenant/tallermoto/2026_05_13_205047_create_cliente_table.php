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
        Schema::create('cliente', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CLI_Id');
            /* CAMPOS */
            $table->string('CLI_TipoDocumento', 5);
            $table->string('CLI_NumDocumento', 12);
            $table->string('CLI_Nombre', 150);
            $table->string('CLI_Direccion', 150)->nullable();
            $table->string('CLI_Celular', 12)->nullable();
            $table->string('CLI_Correo', 50)->nullable();
            $table->boolean('CLI_Status')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cliente');
    }
};
