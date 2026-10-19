<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Firmas digitales de un mantenimiento (los 7 tipos de
 * GestionProcesoService::tipos()), en orden: TECNICO -> REPRESENTANTE ->
 * CLIENTE. Con la firma del cliente el mantenimiento queda cerrado para
 * editar (ver BloquearMantenimientoFirmado).
 *
 * - mantenimiento_firma: una fila por rol firmado. La imagen (PNG) vive en
 *   el disco privado; FIR_Tabla + FIR_RegistroId apuntan al mantenimiento.
 * - mantenimiento_firma_reapertura: cada vez que un Admin reabre un
 *   mantenimiento firmado (se borran las firmas, pero queda quien firmo y
 *   cuando en FRA_Firmas).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_firma', function (Blueprint $table) {
            $table->increments('FIR_Id');
            $table->string('FIR_Tabla', 60);
            $table->unsignedInteger('FIR_RegistroId');
            $table->string('FIR_Rol', 15);
            $table->string('FIR_Nombre', 150);
            $table->string('FIR_Documento', 20)->nullable();
            $table->string('FIR_Imagen', 255);
            $table->unsignedBigInteger('USU_Id');
            $table->string('FIR_Ip', 45)->nullable();
            $table->timestamps();

            $table->unique(['FIR_Tabla', 'FIR_RegistroId', 'FIR_Rol'], 'FIR_UNQ1');
            $table->foreign('USU_Id', 'FIR_KFR1')->references('id')->on('users');
        });

        Schema::create('mantenimiento_firma_reapertura', function (Blueprint $table) {
            $table->increments('FRA_Id');
            $table->string('FRA_Tabla', 60);
            $table->unsignedInteger('FRA_RegistroId');
            $table->unsignedBigInteger('USU_Id');
            $table->string('FRA_Motivo', 255);
            $table->json('FRA_Firmas');
            $table->timestamps();

            $table->index(['FRA_Tabla', 'FRA_RegistroId'], 'FRA_IDX1');
            $table->foreign('USU_Id', 'FRA_KFR1')->references('id')->on('users');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_firma_reapertura');
        Schema::dropIfExists('mantenimiento_firma');
    }
};
