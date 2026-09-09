<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Marca el momento en que la moto llega fisicamente al taller y recepcion
 * hace el check-in (llena el detalle real de trabajo y asigna mecanico).
 * Antes de eso la reserva es solo una cita agendada; el tablero de
 * "Gestion de Proceso" usa este campo para separar "agendado" de "ya esta
 * aqui, hay que atenderlo".
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->dateTime('RES_CheckIn')->nullable()->after('RES_Estado');
        });
    }

    public function down(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->dropColumn('RES_CheckIn');
        });
    }
};
