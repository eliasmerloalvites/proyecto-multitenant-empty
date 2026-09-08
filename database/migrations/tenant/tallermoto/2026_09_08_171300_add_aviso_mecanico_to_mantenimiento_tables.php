<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Semaforo de "recepcion agrego/cambio algo despues de que el mecanico ya
 * tenia este trabajo asignado" (tablero de "Gestion de Proceso"). No
 * confundir con la columna "notificar" ya existente: esa es para avisarle
 * al CLIENTE que su moto esta lista, un concepto totalmente distinto.
 * Se apaga solo cuando el mecanico presiona "Entendido" (accion explicita,
 * no automatica al abrir el registro).
 */
return new class extends Migration
{
    private array $tablas = [
        'mantenimiento_general_carburada',
        'mantenimiento_general_inyectada',
        'mantenimiento_preventivo_carburada',
        'mantenimiento_preventivo_inyectada',
        'mantenimiento_actividad_variadas',
    ];

    private array $prefijos = [
        'mantenimiento_general_carburada' => 'MGC',
        'mantenimiento_general_inyectada' => 'MGI',
        'mantenimiento_preventivo_carburada' => 'MPC',
        'mantenimiento_preventivo_inyectada' => 'MPI',
        'mantenimiento_actividad_variadas' => 'MAV',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            $columna = $this->prefijos[$tabla] . '_AvisoMecanico';

            if (Schema::hasTable($tabla) && !Schema::hasColumn($tabla, $columna)) {
                Schema::table($tabla, function (Blueprint $table) use ($columna) {
                    $table->boolean($columna)->default(false)->after('RES_Id');
                });
            }
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            $columna = $this->prefijos[$tabla] . '_AvisoMecanico';

            if (Schema::hasTable($tabla) && Schema::hasColumn($tabla, $columna)) {
                Schema::table($tabla, function (Blueprint $table) use ($columna) {
                    $table->dropColumn($columna);
                });
            }
        }
    }
};
