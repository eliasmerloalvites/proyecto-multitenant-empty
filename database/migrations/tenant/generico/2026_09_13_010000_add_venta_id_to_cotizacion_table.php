<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * "Convertir a Venta": desde una cotizacion Pendiente se puede cargar su
 * carrito en la pantalla de Ventas (create.blade.php) y, si el cajero
 * termina de vender ahi, la cotizacion queda marcada como Aprobada. Esta
 * columna guarda a que venta quedo asociada, para poder mostrar el enlace
 * desde el listado de Cotizaciones (trazabilidad), nada mas -- no participa
 * en ningun calculo.
 *
 * Nullable y con 'set null' al borrar la venta: si la venta se elimina, la
 * cotizacion no se borra ni cambia de estado, solo pierde la referencia.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cotizacion', function (Blueprint $table) {
            $table->unsignedInteger('VEN_Id')->nullable()->after('COT_Estado');

            $table->foreign('VEN_Id', 'COT_KFR3')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('cotizacion', function (Blueprint $table) {
            $table->dropForeign('COT_KFR3');
            $table->dropColumn('VEN_Id');
        });
    }
};
