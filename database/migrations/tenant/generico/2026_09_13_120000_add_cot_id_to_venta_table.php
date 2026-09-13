<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cotizaciones (presupuestos) solo existen en el vertical tallermoto: aqui
 * en generico no hay tabla `cotizacion`, asi que esta columna se agrega
 * igual (sin FK) unicamente para que VentaController, que es compartido
 * entre ambos verticales, pueda usar la misma consulta/select sin tener
 * que ramificar por tipo_negocio en cada punto. Para generico siempre
 * queda en null.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->unsignedBigInteger('COT_Id')->nullable()->after('CS_Id');
        });
    }

    public function down(): void
    {
        Schema::table('venta', function (Blueprint $table) {
            $table->dropColumn('COT_Id');
        });
    }
};
