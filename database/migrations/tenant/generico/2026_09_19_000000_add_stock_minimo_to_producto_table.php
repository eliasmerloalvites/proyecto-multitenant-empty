<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mismo campo que ya existe en tallermoto (ver
 * tenant/tallermoto/2026_09_04_180108_add_stock_minimo_to_producto_table.php):
 * ProductoController es compartido entre verticales y su
 * controlinventario() usa PRO_StockMinimo sin ninguna guarda de vertical
 * (a diferencia de PRO_CodigoInterno/PRO_CodigoFabricacion/
 * PRO_MostrarCatalogo, que si son exclusivos de tallermoto), asi que
 * generico tambien la necesita para no tirar 500 en Control de Inventario.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->decimal('PRO_StockMinimo', 10, 2)->default(0)->after('PRO_Marca');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('PRO_StockMinimo');
        });
    }
};
