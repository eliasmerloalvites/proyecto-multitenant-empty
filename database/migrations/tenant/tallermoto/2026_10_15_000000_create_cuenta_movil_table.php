<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cuenta de un cliente final en la app movil. La identidad es el celular
 * (verificado por codigo de un solo uso); no tiene contrasena. Es una tabla
 * aparte de `cliente` a proposito: `cliente` no tiene unicidad en celular ni
 * documento y tiene datos irregulares, asi que el vinculo entre una cuenta y
 * los clientes/motos del taller se hace en una fase posterior y de forma
 * controlada.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_movil', function (Blueprint $table) {
            $table->id('CMV_Id');
            // Celular normalizado con codigo de pais, solo digitos (ej. 51987654321).
            $table->string('CMV_Celular', 15)->unique();
            $table->string('CMV_Nombre', 120)->nullable();
            $table->string('CMV_Correo', 120)->nullable();
            $table->dateTime('CMV_VerificadoEn')->nullable();
            $table->string('CMV_Estado', 20)->default('ACTIVA');
            $table->dateTime('CMV_UltimoAcceso')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_movil');
    }
};
