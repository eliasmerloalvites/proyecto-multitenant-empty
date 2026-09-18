<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Mismo concepto que detalle_venta.DEV_NombrePersonalizado: nombre
 * alternativo solo para esta linea de la cuenta de bahia (ticket/PDF al
 * cobrar), sin tocar producto.PRO_Nombre. Se traslada a
 * detalle_venta.DEV_NombrePersonalizado cuando la cuenta se cobra (ver
 * VentaController::create(), prefillCarrito).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bahia_cuenta_item', function (Blueprint $table) {
            $table->string('BCI_NombrePersonalizado', 191)->nullable()->after('PRO_Id');
        });
    }

    public function down(): void
    {
        Schema::table('bahia_cuenta_item', function (Blueprint $table) {
            $table->dropColumn('BCI_NombrePersonalizado');
        });
    }
};
