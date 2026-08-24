<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Campos que necesita una nota de credito / debito: el motivo (catalogo
     * 09/10 de SUNAT) y el documento que afecta. No se agrega una foreign key
     * a DOV_DocAfectadoId a proposito: es una referencia informativa para
     * trazabilidad, no una relacion que deba bloquear borrados.
     */
    public function up(): void
    {
        Schema::table('documento_venta', function (Blueprint $table) {
            $table->string('DOV_CodMotivo', 2)->nullable()->after('DOV_TipoOriginal');
            $table->string('DOV_DesMotivo', 255)->nullable()->after('DOV_CodMotivo');
            $table->string('DOV_TipoDocAfectado', 2)->nullable()->after('DOV_DesMotivo');
            $table->string('DOV_NumDocAfectado', 20)->nullable()->after('DOV_TipoDocAfectado');
            $table->unsignedInteger('DOV_DocAfectadoId')->nullable()->after('DOV_NumDocAfectado');
            $table->index('DOV_DocAfectadoId', 'R_DOV_DocAfectado');
        });
    }

    public function down(): void
    {
        Schema::table('documento_venta', function (Blueprint $table) {
            $table->dropIndex('R_DOV_DocAfectado');
            $table->dropColumn([
                'DOV_CodMotivo',
                'DOV_DesMotivo',
                'DOV_TipoDocAfectado',
                'DOV_NumDocAfectado',
                'DOV_DocAfectadoId',
            ]);
        });
    }
};
