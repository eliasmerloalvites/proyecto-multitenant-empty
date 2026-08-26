<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->boolean('RES_Notificado')->default(false)->after('RES_Estado');
            $table->dateTime('RES_NotificadoEn')->nullable()->after('RES_Notificado');
        });
    }

    public function down(): void
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->dropColumn(['RES_Notificado', 'RES_NotificadoEn']);
        });
    }
};
