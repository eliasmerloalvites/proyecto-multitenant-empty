<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * SUNAT exige que la nota de credito use una serie distinta segun el
     * comprobante que afecta (p.ej. BC01 para boletas, FC01 para facturas).
     * Antes habia una sola columna ALM_SerieNotaCredito compartida; se deja
     * intacta como respaldo y se agregan las dos series especificas.
     */
    public function up(): void
    {
        if (!Schema::hasTable('almacen')) {
            return;
        }

        // Sin ->after(): algunos tenants mas antiguos no tienen la columna
        // ALM_SerieNotaCredito (la version vieja/compartida) en su tabla
        // almacen, y anclar la posicion a esa columna rompe la migracion ahi.
        if (!Schema::hasColumn('almacen', 'ALM_SerieNotaCreditoBoleta')) {
            Schema::table('almacen', function (Blueprint $table) {
                $table->string('ALM_SerieNotaCreditoBoleta', 4)->nullable()->comment('Ej. BC01');
            });
        }

        if (!Schema::hasColumn('almacen', 'ALM_SerieNotaCreditoFactura')) {
            Schema::table('almacen', function (Blueprint $table) {
                $table->string('ALM_SerieNotaCreditoFactura', 4)->nullable()->comment('Ej. FC01');
            });
        }

        // Backfill: las sedes que ya tenian una unica serie configurada la
        // heredan en ambas columnas nuevas, para no dejarlas sin numeracion
        // hasta que el usuario entre a configurar las dos por separado.
        // Solo aplica si la columna vieja existe en esta tabla.
        if (Schema::hasColumn('almacen', 'ALM_SerieNotaCredito')) {
            DB::table('almacen')
                ->whereNotNull('ALM_SerieNotaCredito')
                ->where('ALM_SerieNotaCredito', '!=', '')
                ->update([
                    'ALM_SerieNotaCreditoBoleta' => DB::raw('ALM_SerieNotaCredito'),
                    'ALM_SerieNotaCreditoFactura' => DB::raw('ALM_SerieNotaCredito'),
                ]);
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('almacen')) {
            return;
        }

        Schema::table('almacen', function (Blueprint $table) {
            if (Schema::hasColumn('almacen', 'ALM_SerieNotaCreditoFactura')) {
                $table->dropColumn('ALM_SerieNotaCreditoFactura');
            }
            if (Schema::hasColumn('almacen', 'ALM_SerieNotaCreditoBoleta')) {
                $table->dropColumn('ALM_SerieNotaCreditoBoleta');
            }
        });
    }
};
