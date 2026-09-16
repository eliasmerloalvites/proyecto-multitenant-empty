<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Respuesta de un item de recepcion para un registro de mantenimiento
 * puntual. Se ancla por (MTO_Tabla, MTO_Id) en vez de una FK a una sola
 * tabla: un mantenimiento se puede crear con o sin reserva de por medio
 * (ver GestionProcesoService::crearDesdeReserva()/checkIn(), y el
 * formulario "Nuevo mantenimiento" directo de cada uno de los 5 tipos),
 * asi que no hay una unica tabla padre valida. MTO_Tabla siempre es una de
 * GestionProcesoService::TIPOS[...]['tabla']; la integridad se valida en
 * GestionProcesoService::guardarEstadoRecepcion(), igual que ya hace
 * mantenimientoDeReserva() con este mismo patron "tabla + id".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepcion_respuesta', function (Blueprint $table) {
            $table->id('RRP_Id');
            $table->string('MTO_Tabla', 60);
            $table->unsignedBigInteger('MTO_Id');
            $table->unsignedBigInteger('RIT_Id');
            $table->string('RRP_Valor', 500)->nullable();
            $table->timestamps();

            $table->foreign('RIT_Id', 'RRP_KFR1')->references('RIT_Id')->on('recepcion_item')->restrictOnDelete();
            // El unique ya cubre las busquedas por (MTO_Tabla, MTO_Id) como indice compuesto.
            $table->unique(['MTO_Tabla', 'MTO_Id', 'RIT_Id'], 'RRP_UNQ1');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepcion_respuesta');
    }
};
