<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * La anulacion de compras (CompraAnulacionController) revierte el stock
 * ingresado creando una fila de 'lote' en NEGATIVO (misma compra, mismo
 * producto/almacen) en vez de borrar o editar el lote original, para que
 * el Kardex compartido (ProductoController::kardex()) la muestre como una
 * entrada mas, con su propia fecha, sin tocar ese controlador.
 *
 * Esas dos columnas se crearon como decimal ...->unsigned() en
 * 2026_05_13_211541_create_lote_table.php, asi que un insert con valores
 * negativos falla con "Out of range value" (SQLSTATE 22003). Esta
 * migracion las vuelve firmadas (quita unsigned) para permitir esa fila
 * de reversa.
 *
 * Esta migracion vive en database/migrations/tenant/generico, o sea que
 * solo se ejecuta contra las bases de datos de tenants de tipo generico
 * (cada vertical migra su propia carpeta por separado); no toca en nada a
 * los tenants de tallermoto, que tienen su propia copia de la tabla 'lote'
 * y su propia migracion equivalente
 * (tenant/tallermoto/2026_08_28_000000_allow_negative_stock_in_lote_table.php,
 * hecha antes por otro motivo: permitir venta sin stock).
 *
 * Se usa ALTER MODIFY (SQL crudo) en vez de Schema::table()->change() para
 * no depender de doctrine/dbal, que este proyecto no tiene instalado
 * (mismo criterio ya usado en la migracion de tallermoto mencionada arriba).
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE lote MODIFY LOT_CantidadReal DECIMAL(10,2) NOT NULL');
        DB::statement('ALTER TABLE lote MODIFY LOT_CantidadIngreso DECIMAL(10,0) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE lote MODIFY LOT_CantidadReal DECIMAL(10,2) UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE lote MODIFY LOT_CantidadIngreso DECIMAL(10,0) UNSIGNED NOT NULL');
    }
};
