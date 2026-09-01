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
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (! Schema::hasColumn($tabla, 'PLAN_Id')) {
                    $table->unsignedBigInteger('PLAN_Id')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (Schema::hasColumn($tabla, 'PLAN_Id')) {
                    $table->dropColumn('PLAN_Id');
                }
            });
        }
    }
};
