<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enlaza la Venta hacia atras con la Cotizacion que la origino (si aplica).
 * La cotizacion ya guarda VEN_Id (cotizacion -> venta); esta columna es el
 * enlace inverso (venta -> cotizacion) para poder mostrarlo en el listado,
 * el ticket y el PDF de la venta sin tener que buscar por VEN_Id en cotizacion.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->unsignedBigInteger('COT_Id')->nullable()->after('CS_Id');
            $table->foreign('COT_Id', 'VEN_KFR6')->references('COT_Id')->on('cotizacion')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropForeign('VEN_KFR6');
            $table->dropColumn('COT_Id');
        });
    }
};
