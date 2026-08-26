<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        'mantenimiento_actividad_variadas' => ['MAV_FechaTermino', 'MAV_FechaEdicion'],
        'mantenimiento_general_carburada' => ['MGC_FechaTermino', 'MGC_FechaEdicion'],
        'mantenimiento_general_inyectada' => ['MGI_FechaTermino', 'MGI_FechaEdicion'],
        'mantenimiento_preventivo_carburada' => ['MPC_FechaTermino', 'MPC_FechaEdicion'],
        'mantenimiento_preventivo_inyectada' => ['MPI_FechaTermino', 'MPI_FechaEdicion'],
    ];

    /**
     * Run the migrations.
     */
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

    /**
     * Reverse the migrations.
     */
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
