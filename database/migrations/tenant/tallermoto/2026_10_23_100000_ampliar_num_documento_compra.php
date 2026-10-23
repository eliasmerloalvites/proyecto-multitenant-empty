<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El N° de comprobante de una compra (ej. F001-00456510, 13 caracteres)
 * no entraba: la columna quedo en varchar(12) en las bases ya creadas.
 * Se amplia a 30, lo mismo que permite el formulario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('compra', function (Blueprint $table) {
            $table->string('COM_NumDocumento', 30)->change();
        });
    }

    public function down(): void
    {
        // No se reduce: podria cortar numeros ya guardados de mas de 12.
    }
};
