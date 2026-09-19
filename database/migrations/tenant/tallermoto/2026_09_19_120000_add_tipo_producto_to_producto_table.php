<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Distingue un producto fisico (con stock, lotes, control de inventario) de
 * un servicio (mano de obra, diagnostico, lavado, etc: se vende igual que
 * un producto pero nunca tiene lotes ni descuenta stock). Ver
 * ProductoController::store()/update() y
 * VentaController::ReducirStock()/getProductos().
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->enum('PRO_TipoProducto', ['PRODUCTO', 'SERVICIO'])->default('PRODUCTO')->after('PRO_Marca');
        });
    }

    public function down(): void
    {
        Schema::table('producto', function (Blueprint $table) {
            $table->dropColumn('PRO_TipoProducto');
        });
    }
};
