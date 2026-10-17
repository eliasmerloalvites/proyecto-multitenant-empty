<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Reservas hechas desde la app movil: CMV_Id guarda la cuenta que la creo
 * (la reserva web no lo tiene) y RES_CanceladaEn cuando la cancela el propio
 * cliente. Una cancelacion deja la reserva en RECHAZADO, que es lo que ya
 * libera el slot (ver RES_SlotKey).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->unsignedBigInteger('CMV_Id')->nullable()->after('RES_Estado');
            $table->dateTime('RES_CanceladaEn')->nullable()->after('CMV_Id');
            $table->index('CMV_Id', 'RES_IDX_CMV');
        });
    }

    public function down(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->dropIndex('RES_IDX_CMV');
            $table->dropColumn(['CMV_Id', 'RES_CanceladaEn']);
        });
    }
};
