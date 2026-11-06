<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Firma automatica del representante (dueño/gerente) en los mantenimientos:
 * si esta activa, al firmar el tecnico se registra sola la firma del
 * representante con la imagen guardada aqui (ver
 * FirmasMantenimientoService::firmarRepresentanteAutomatico). La imagen se
 * guarda en el disco privado, no en el publico.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->boolean('firma_representante_auto')->default(false);
            $table->string('firma_representante_imagen', 255)->nullable();
            $table->string('firma_representante_nombre', 150)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->dropColumn(['firma_representante_auto', 'firma_representante_imagen', 'firma_representante_nombre']);
        });
    }
};
