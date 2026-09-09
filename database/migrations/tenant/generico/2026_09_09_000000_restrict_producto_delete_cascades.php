<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Eliminar un producto borraba en cascada su historial de lotes, detalle
 * de ventas y detalle de compras (las 3 tenian onDelete('cascade') hacia
 * producto.PRO_Id) -- un solo click en "Eliminar" en el listado de
 * productos corrompia ventas y compras ya declaradas/facturadas. Se
 * cambia a RESTRICT (el default de MySQL al omitir onDelete): si el
 * producto tiene historial, la base de datos rechaza el DELETE en vez de
 * arrastrarlo. ProductoController::destroy() ya se ajusta aparte para
 * desactivar el producto en lugar de borrarlo cuando esto pasa.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lote', function (Blueprint $table) {
            $table->dropForeign('LOT_KFR1');
            $table->foreign('PRO_Id', 'LOT_KFR1')->references('PRO_Id')->on('producto');
        });

        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->dropForeign('R_DV_KFR2');
            $table->foreign('PRO_Id', 'R_DV_KFR2')->references('PRO_Id')->on('producto');
        });

        Schema::table('detalle_compra', function (Blueprint $table) {
            $table->dropForeign('DCOM_KFR3');
            $table->foreign('PRO_Id', 'DCOM_KFR3')->references('PRO_Id')->on('producto');
        });
    }

    public function down(): void
    {
        Schema::table('lote', function (Blueprint $table) {
            $table->dropForeign('LOT_KFR1');
            $table->foreign('PRO_Id', 'LOT_KFR1')->references('PRO_Id')->on('producto')->onDelete('cascade');
        });

        Schema::table('detalle_venta', function (Blueprint $table) {
            $table->dropForeign('R_DV_KFR2');
            $table->foreign('PRO_Id', 'R_DV_KFR2')->references('PRO_Id')->on('producto')->onDelete('cascade');
        });

        Schema::table('detalle_compra', function (Blueprint $table) {
            $table->dropForeign('DCOM_KFR3');
            $table->foreign('PRO_Id', 'DCOM_KFR3')->references('PRO_Id')->on('producto')->onDelete('cascade');
        });
    }
};
