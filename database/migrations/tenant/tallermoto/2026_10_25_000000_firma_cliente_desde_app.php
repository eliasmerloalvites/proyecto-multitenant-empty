<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El cliente tambien puede firmar su conformidad desde la app movil (ver
 * FirmasMantenimientoService::firmarClienteDesdeApp). Esa firma no tiene un
 * usuario del taller detras: queda ligada a la cuenta de la app (CMV_Id) y
 * marcada con FIR_Origen = 'APP' (las del panel son 'TALLER').
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mantenimiento_firma', function (Blueprint $table) {
            $table->unsignedBigInteger('USU_Id')->nullable()->change();
            $table->unsignedBigInteger('CMV_Id')->nullable()->after('USU_Id');
            $table->string('FIR_Origen', 10)->default('TALLER')->after('CMV_Id');

            $table->foreign('CMV_Id', 'FIR_KFR2')->references('CMV_Id')->on('cuenta_movil');
        });
    }

    public function down(): void
    {
        Schema::table('mantenimiento_firma', function (Blueprint $table) {
            $table->dropForeign('FIR_KFR2');
            $table->dropColumn(['CMV_Id', 'FIR_Origen']);
        });
    }
};
