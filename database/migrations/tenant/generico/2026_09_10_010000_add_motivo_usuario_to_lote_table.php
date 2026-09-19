<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Soporte para "Control de Inventario -> Ingreso/Salida manual" (exclusivo
 * de generico, InventarioAjusteController). Cada ajuste manual sigue
 * guardandose como una fila mas de 'lote' (igual que una compra), pero
 * necesita dos datos que 'lote' no tenia: el motivo (opcional, lo escribe
 * quien hace el ajuste) y quien lo registro.
 *
 * Columnas nuevas, NULLABLE: no rompen ningun INSERT existente de
 * CompraController/VentaController (que no las mencionan) ni para
 * tallermoto (que tiene su propia copia de 'lote' con su propia migracion
 * de esta carpeta hermana, no afectada por esta).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lote', function (Blueprint $table) {
            $table->string('LOT_Motivo', 255)->nullable()->after('LOT_PrecioVenta');
            $table->unsignedInteger('USU_Id')->nullable()->after('LOT_Motivo');
        });
    }

    public function down(): void
    {
        Schema::table('lote', function (Blueprint $table) {
            $table->dropColumn(['LOT_Motivo', 'USU_Id']);
        });
    }
};
