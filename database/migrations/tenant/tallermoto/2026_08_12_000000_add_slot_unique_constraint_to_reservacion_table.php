<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Evita el doble-reservado de una misma bahía+turno+fecha a nivel de base
 * de datos (la única garantía real contra condiciones de carrera: dos
 * personas reservando el mismo slot casi al mismo tiempo).
 *
 * RES_SlotKey es una columna GENERADA (MySQL STORED) que vale
 * "BAH_Id-TUR_Id-RES_FechaProgramada" SOLO cuando la reserva realmente
 * ocupa el slot (RES_Estado = 'ACT' y RES_State distinto de 'RECHAZADO').
 * En cualquier otro caso vale NULL. MySQL permite múltiples NULL en un
 * índice único (no chocan entre sí), así que las reservas rechazadas o
 * desactivadas nunca bloquean el slot para una nueva reserva, pero dos
 * reservas "vivas" para el mismo slot sí violan el índice único y el
 * segundo INSERT falla con un error controlado en vez de duplicarse.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->string('RES_SlotKey', 160)
                ->nullable()
                ->storedAs("CASE WHEN RES_Estado = 'ACT' AND RES_State <> 'RECHAZADO' THEN CONCAT(BAH_Id, '-', TUR_Id, '-', RES_FechaProgramada) ELSE NULL END")
                ->after('RES_Estado');
        });

        Schema::table('reservacion', function (Blueprint $table) {
            $table->unique('RES_SlotKey', 'RES_UQ_SLOT');
        });
    }

    public function down(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->dropUnique('RES_UQ_SLOT');
            $table->dropColumn('RES_SlotKey');
        });
    }
};
