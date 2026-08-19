<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Apertura/cierre automático por horario: si CAJ_ProgramacionActiva
     * está encendida, el comando caja:programacion (corre cada minuto, ver
     * routes/console.php) aperturará la caja a CAJ_HoraApertura y la
     * cerrará a CAJ_HoraCierre sin que nadie tenga que hacerlo a mano.
     */
    public function up(): void
    {
        Schema::table('caja', function (Blueprint $table) {
            $table->boolean('CAJ_ProgramacionActiva')->default(false)->after('CAJ_Status');
            $table->time('CAJ_HoraApertura')->nullable()->after('CAJ_ProgramacionActiva');
            $table->time('CAJ_HoraCierre')->nullable()->after('CAJ_HoraApertura');
        });
    }

    public function down(): void
    {
        Schema::table('caja', function (Blueprint $table) {
            $table->dropColumn(['CAJ_ProgramacionActiva', 'CAJ_HoraApertura', 'CAJ_HoraCierre']);
        });
    }
};
