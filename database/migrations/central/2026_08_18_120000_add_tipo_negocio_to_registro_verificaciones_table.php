<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoNegocioToRegistroVerificacionesTable extends Migration
{
    /**
     * El autoregistro público ahora deja elegir el vertical (antes siempre
     * era 'tallermoto', fijo en el controller). Default 'tallermoto' para
     * no romper filas viejas ya insertadas antes de este cambio.
     */
    public function up(): void
    {
        Schema::table('registro_verificaciones', function (Blueprint $table) {
            $table->string('tipo_negocio', 30)->default('tallermoto')->after('subdomain');
        });
    }

    public function down(): void
    {
        Schema::table('registro_verificaciones', function (Blueprint $table) {
            $table->dropColumn('tipo_negocio');
        });
    }
}
