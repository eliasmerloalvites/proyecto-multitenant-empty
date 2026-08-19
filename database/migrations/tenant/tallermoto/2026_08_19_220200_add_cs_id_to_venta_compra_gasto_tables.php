<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * CS_Id ata cada movimiento a la sesión de caja (turno) exacta en la
     * que ocurrió, no solo a la caja en general — así el cierre de un
     * turno puede reconstruir con precisión qué pasó durante ese turno,
     * incluso si la misma caja tuvo varios turnos ese día.
     */
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->unsignedInteger('CS_Id')->nullable()->after('CAJ_Id');
            $table->foreign('CS_Id', 'VEN_KFR5')->references('CS_Id')->on('caja_sesion')->nullOnDelete();
        });

        Schema::table('compra', function (Blueprint $table) {
            $table->unsignedInteger('CS_Id')->nullable()->after('CAJ_Id');
            $table->foreign('CS_Id', 'COM_KFR5')->references('CS_Id')->on('caja_sesion')->nullOnDelete();
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->unsignedInteger('CS_Id')->nullable()->after('CAJ_Id');
            $table->foreign('CS_Id', 'GAS_KFR7')->references('CS_Id')->on('caja_sesion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropForeign('VEN_KFR5');
            $table->dropColumn('CS_Id');
        });

        Schema::table('compra', function (Blueprint $table) {
            $table->dropForeign('COM_KFR5');
            $table->dropColumn('CS_Id');
        });

        Schema::table('gasto', function (Blueprint $table) {
            $table->dropForeign('GAS_KFR7');
            $table->dropColumn('CS_Id');
        });
    }
};
