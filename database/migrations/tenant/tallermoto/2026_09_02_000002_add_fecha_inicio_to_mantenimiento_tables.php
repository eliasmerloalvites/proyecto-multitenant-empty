<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'mantenimiento_actividad_variadas' => ['MAV_FechaInicio', 'MAV_FechaCreacion'],
        'mantenimiento_general_carburada' => ['MGC_FechaInicio', 'MGC_FechaCreacion'],
        'mantenimiento_general_inyectada' => ['MGI_FechaInicio', 'MGI_FechaCreacion'],
        'mantenimiento_preventivo_carburada' => ['MPC_FechaInicio', 'MPC_FechaCreacion'],
        'mantenimiento_preventivo_inyectada' => ['MPI_FechaInicio', 'MPI_FechaCreacion'],
    ];

    public function up(): void
    {
        foreach ($this->columns as $tableName => [$column, $after]) {
            if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, $column)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($column, $after) {
                $table->dateTime($column)->nullable()->after($after);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->columns as $tableName => [$column, $after]) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, $column)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($column) {
                $table->dropColumn($column);
            });
        }
    }
};
