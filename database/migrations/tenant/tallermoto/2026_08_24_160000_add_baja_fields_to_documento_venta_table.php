<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Estado de la solicitud de anulacion de un comprobante.
     *
     * A diferencia de la nota de credito, una baja no es un documento
     * tributario nuevo: es un estado sobre el documento existente. Por eso
     * estos campos viven directo en documento_venta, junto al DOV_Anulado
     * que ya existia (se marca true cuando SUNAT acepta la baja).
     *
     * Boletas y facturas/notas usan mecanismos distintos ante SUNAT
     * (resumen diario vs comunicacion de baja) pero ambos responden con un
     * ticket que se consulta despues, asi que comparten los mismos campos.
     */
    public function up(): void
    {
        Schema::table('documento_venta', function (Blueprint $table) {
            $table->string('DOV_TicketBaja', 30)->nullable()->after('DOV_Anulado');
            $table->string('DOV_MotivoBaja', 100)->nullable()->after('DOV_TicketBaja');
            $table->string('DOV_EstadoBaja', 20)->nullable()->after('DOV_MotivoBaja');
            $table->text('DOV_DescripcionBaja')->nullable()->after('DOV_EstadoBaja');
            $table->dateTime('DOV_FechaSolicitudBaja')->nullable()->after('DOV_DescripcionBaja');
            $table->dateTime('DOV_FechaRespuestaBaja')->nullable()->after('DOV_FechaSolicitudBaja');
        });
    }

    public function down(): void
    {
        Schema::table('documento_venta', function (Blueprint $table) {
            $table->dropColumn([
                'DOV_TicketBaja',
                'DOV_MotivoBaja',
                'DOV_EstadoBaja',
                'DOV_DescripcionBaja',
                'DOV_FechaSolicitudBaja',
                'DOV_FechaRespuestaBaja',
            ]);
        });
    }
};
