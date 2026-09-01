<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite a un tenant que migra desde otro sistema seguir su propia
     * numeracion (ej. ya iba en B001-01500) en vez de arrancar en 1. El
     * siguiente correlativo emitido nunca sera menor a este valor, pero
     * tampoco pisa numeros ya usados dentro de esta misma app.
     *
     * Sin ->after(): ver 2026_09_02_000000_add_series_nota_credito_por_tipo,
     * no todos los tenants tienen las mismas columnas de serie como ancla.
     */
    private array $columnas = [
        'ALM_CorrelativoInicialBoleta',
        'ALM_CorrelativoInicialFactura',
        'ALM_CorrelativoInicialNotaCreditoBoleta',
        'ALM_CorrelativoInicialNotaCreditoFactura',
        'ALM_CorrelativoInicialGuiaRemision',
    ];

    public function up(): void
    {
        if (!Schema::hasTable('almacen')) {
            return;
        }

        foreach ($this->columnas as $columna) {
            if (!Schema::hasColumn('almacen', $columna)) {
                Schema::table('almacen', function (Blueprint $table) use ($columna) {
                    $table->unsignedInteger($columna)->nullable();
                });
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('almacen')) {
            return;
        }

        Schema::table('almacen', function (Blueprint $table) {
            foreach ($this->columnas as $columna) {
                if (Schema::hasColumn('almacen', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
