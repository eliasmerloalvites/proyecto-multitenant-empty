<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Recomendaciones" (texto + prioridad) y "Verificacion Final" (control de
 * calidad antes de entregar la moto) para la Orden de Servicio — antes eran
 * casillas en blanco en el PDF porque no existia donde guardarlas; ahora se
 * llenan en el formulario de edicion de cada tipo de mantenimiento y el PDF
 * imprime el dato real.
 *
 * Tecnico/Fecha de la verificacion no se agregan aparte: se reusa el
 * mecanico ya asignado (PER_Id) y la fecha de termino del servicio
 * ({prefijo}_FechaTermino), que ya existen.
 */
return new class extends Migration
{
    private array $prefijos = [
        'mantenimiento_general_carburada' => 'MGC',
        'mantenimiento_general_inyectada' => 'MGI',
        'mantenimiento_preventivo_carburada' => 'MPC',
        'mantenimiento_preventivo_inyectada' => 'MPI',
        'mantenimiento_actividad_variadas' => 'MAV',
    ];

    public function up(): void
    {
        foreach ($this->prefijos as $tabla => $prefijo) {
            if (!Schema::hasTable($tabla) || Schema::hasColumn($tabla, $prefijo . '_Recomendacion')) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($prefijo) {
                $table->text($prefijo . '_Recomendacion')->nullable();
                $table->enum($prefijo . '_RecomendacionPrioridad', ['ALTA', 'MEDIA', 'BAJA'])->nullable();
                $table->boolean($prefijo . '_VerifArranque')->nullable();
                $table->boolean($prefijo . '_VerifLuces')->nullable();
                $table->boolean($prefijo . '_VerifDireccionales')->nullable();
                $table->boolean($prefijo . '_VerifNivelAceite')->nullable();
                $table->boolean($prefijo . '_VerifPruebaRuta')->nullable();
                $table->boolean($prefijo . '_VerifLavado')->nullable();
                $table->string($prefijo . '_VerifOtros', 150)->nullable();
                $table->boolean($prefijo . '_VerifConforme')->nullable();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->prefijos as $tabla => $prefijo) {
            if (!Schema::hasTable($tabla) || !Schema::hasColumn($tabla, $prefijo . '_Recomendacion')) {
                continue;
            }

            Schema::table($tabla, function (Blueprint $table) use ($prefijo) {
                $table->dropColumn([
                    $prefijo . '_Recomendacion',
                    $prefijo . '_RecomendacionPrioridad',
                    $prefijo . '_VerifArranque',
                    $prefijo . '_VerifLuces',
                    $prefijo . '_VerifDireccionales',
                    $prefijo . '_VerifNivelAceite',
                    $prefijo . '_VerifPruebaRuta',
                    $prefijo . '_VerifLavado',
                    $prefijo . '_VerifOtros',
                    $prefijo . '_VerifConforme',
                ]);
            });
        }
    }
};
