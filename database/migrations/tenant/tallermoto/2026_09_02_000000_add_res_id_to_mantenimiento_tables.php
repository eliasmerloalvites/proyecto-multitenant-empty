<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = [
        'mantenimiento_general_carburada',
        'mantenimiento_general_inyectada',
        'mantenimiento_preventivo_carburada',
        'mantenimiento_preventivo_inyectada',
        'mantenimiento_actividad_variadas',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla) && !Schema::hasColumn($tabla, 'RES_Id')) {
                Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                    if (Schema::hasColumn($tabla, 'PLAN_Id')) {
                        $table->unsignedBigInteger('RES_Id')->nullable()->after('PLAN_Id');
                    } else {
                        $table->unsignedBigInteger('RES_Id')->nullable();
                    }
                    $table->foreign('RES_Id')->references('RES_Id')->on('reservacion')->nullOnDelete();
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, 'RES_Id')) {
                Schema::table($tabla, function (Blueprint $table) {
                    $table->dropForeign(['RES_Id']);
                    $table->dropColumn('RES_Id');
                });
            }
        }
    }
};
