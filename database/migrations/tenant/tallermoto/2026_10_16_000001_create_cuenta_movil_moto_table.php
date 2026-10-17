<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Vinculo entre una cuenta de la app y una moto.
 *
 * CMM_Estado : PENDIENTE (esperando aprobacion del taller), VERIFICADA,
 *              RECHAZADA o REVOCADA (la cuenta o el taller lo dio de baja).
 * CMM_Metodo : TELEFONO (el celular de la cuenta coincide con registros del
 *              taller), INVITACION (codigo entregado en el taller) o
 *              APROBACION (el taller aprobo una solicitud).
 * CMM_Alcance: REGISTROS_PROPIOS (solo ve los registros del taller hechos con
 *              SU celular: protege al dueno anterior de la moto) o COMPLETO
 *              (el taller respalda que es el dueno: ve todo el historial).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_movil_moto', function (Blueprint $table) {
            $table->id('CMM_Id');
            $table->unsignedBigInteger('CMV_Id');
            $table->unsignedBigInteger('MOT_Id');
            $table->string('CMM_Estado', 15)->default('PENDIENTE');
            $table->string('CMM_Metodo', 15)->nullable();
            $table->string('CMM_Alcance', 20)->default('REGISTROS_PROPIOS');
            $table->unsignedBigInteger('CMM_VerificadaPor')->nullable();
            $table->dateTime('CMM_VerificadaEn')->nullable();
            $table->string('CMM_Nota', 250)->nullable();
            $table->timestamps();

            $table->unique(['CMV_Id', 'MOT_Id'], 'CMM_UQ_CUENTA_MOTO');
            $table->index('CMM_Estado');

            $table->foreign('CMV_Id', 'CMM_FK_CUENTA')->references('CMV_Id')->on('cuenta_movil')->cascadeOnDelete();
            $table->foreign('MOT_Id', 'CMM_FK_MOTO')->references('MOT_Id')->on('moto')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_movil_moto');
    }
};
