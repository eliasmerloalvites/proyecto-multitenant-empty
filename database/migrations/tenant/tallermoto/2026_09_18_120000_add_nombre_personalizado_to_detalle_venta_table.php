<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Nombre alternativo que el vendedor puede escribir para una linea de venta
 * puntual (ej. "Cambio de aceite + filtro" en vez del nombre real del
 * producto/servicio en catalogo). Solo afecta como se imprime esa venta en
 * ticket/PDF; nunca se toca producto.PRO_Nombre ni el stock, que se sigue
 * descontando del PRO_Id real (ver VentaController::store()).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->string('DEV_NombrePersonalizado', 191)->nullable()->after('PRO_Id');
        });
    }

    public function down(): void
    {
        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->dropColumn('DEV_NombrePersonalizado');
        });
    }
};
