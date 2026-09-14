<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Codigo interno: el que el propio negocio le asigna al producto para
 * ubicarlo/buscarlo rapido (no tiene por que coincidir con nada externo).
 * Codigo de fabricacion: el codigo/SKU que trae el producto de fabrica o
 * del proveedor. Ambos opcionales y buscables (ver ProductoController,
 * VentaController::getProductos, AjusteController::productos,
 * TrasladoController::productos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->string('PRO_CodigoInterno', 60)->nullable()->after('PRO_Marca');
            $table->string('PRO_CodigoFabricacion', 60)->nullable()->after('PRO_CodigoInterno');
            $table->index('PRO_CodigoInterno', 'PRO_KIDX1');
            $table->index('PRO_CodigoFabricacion', 'PRO_KIDX2');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropIndex('PRO_KIDX1');
            $table->dropIndex('PRO_KIDX2');
            $table->dropColumn(['PRO_CodigoInterno', 'PRO_CodigoFabricacion']);
        });
    }
};
