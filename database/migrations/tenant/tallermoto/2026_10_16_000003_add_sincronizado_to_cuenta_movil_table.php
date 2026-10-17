<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/** Marca de la ultima vez que se buscaron motos de la cuenta por su celular. */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_movil', function (Blueprint $table) {
            $table->dateTime('CMV_SincronizadoEn')->nullable()->after('CMV_UltimoAcceso');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_movil', function (Blueprint $table) {
            $table->dropColumn('CMV_SincronizadoEn');
        });
    }
};
