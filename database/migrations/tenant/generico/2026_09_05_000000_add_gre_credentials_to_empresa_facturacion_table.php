<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La tabla empresa_facturacion de 'generico' nunca recibio las columnas
     * de credenciales GRE (guia de remision electronica) que si tiene
     * 'tallermoto' desde su migracion
     * 2026_08_26_170000_add_gre_credentials_to_empresa_facturacion_table.
     *
     * El modelo App\Models\Tenant\EmpresaFacturacion (compartido entre
     * verticales) y EmpresaFacturacionController::store() siempre leen y
     * escriben gre_client_id/gre_client_secret, así que sin estas columnas
     * el guardado de Configuracion > Empresa en un tenant generico falla con
     * "Column not found: 1054 Unknown column 'gre_client_id'".
     *
     * Guardado con hasColumn() para poder correrla sin romper nada en un
     * tenant que ya las tuviera por cualquier motivo.
     */
    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            if (!Schema::hasColumn('empresa_facturacion', 'gre_client_id')) {
                $table->string('gre_client_id', 100)->nullable()->after('sol_password');
            }

            if (!Schema::hasColumn('empresa_facturacion', 'gre_client_secret')) {
                $table->text('gre_client_secret')->nullable()->after('gre_client_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            if (Schema::hasColumn('empresa_facturacion', 'gre_client_secret')) {
                $table->dropColumn('gre_client_secret');
            }

            if (Schema::hasColumn('empresa_facturacion', 'gre_client_id')) {
                $table->dropColumn('gre_client_id');
            }
        });
    }
};
