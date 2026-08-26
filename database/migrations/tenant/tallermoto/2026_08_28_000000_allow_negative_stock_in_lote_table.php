<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Permite que LOT_CantidadReal quede en negativo, para las sedes que tienen
 * activado "permitir venta sin stock" (ALM_PermitirVentaSinStock): la venta
 * se registra igual y el lote queda mostrando el faltante en vez de fallar.
 * Se usa ALTER MODIFY (SQL crudo) para no depender de doctrine/dbal, que
 * este proyecto no tiene instalado.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE lote MODIFY LOT_CantidadReal DECIMAL(10,2) NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE lote MODIFY LOT_CantidadReal DECIMAL(10,2) UNSIGNED NOT NULL');
    }
};
