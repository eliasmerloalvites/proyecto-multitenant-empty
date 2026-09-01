<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tablas = [
        'mgc_detalle_reemplazo',
        'mgi_detalle_reemplazo',
        'mpc_detalle_reemplazo',
        'mpi_detalle_reemplazo',
        'mav_detalle_reemplazo',
    ];

    public function up(): void
    {
        foreach ($this->tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (!Schema::hasColumn($tabla, 'origen')) {
                    $table->string('origen', 10)->default('MANUAL');
                }
                if (!Schema::hasColumn($tabla, 'BCI_Id')) {
                    $table->unsignedBigInteger('BCI_Id')->nullable();
                    $table->foreign('BCI_Id')->references('BCI_Id')->on('bahia_cuenta_item')->cascadeOnDelete();
                }
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tablas as $tabla) {
            if (!Schema::hasTable($tabla)) {
                continue;
            }
            Schema::table($tabla, function (Blueprint $table) use ($tabla) {
                if (Schema::hasColumn($tabla, 'BCI_Id')) {
                    $table->dropForeign(['BCI_Id']);
                    $table->dropColumn('BCI_Id');
                }
                if (Schema::hasColumn($tabla, 'origen')) {
                    $table->dropColumn('origen');
                }
            });
        }
    }
};
