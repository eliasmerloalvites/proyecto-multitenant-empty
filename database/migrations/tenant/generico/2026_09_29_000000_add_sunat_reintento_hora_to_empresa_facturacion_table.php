<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hora en la que corre el reintento automatico de comprobantes SUNAT que
 * quedaron en PENDIENTE o ERROR (ver comando sunat:reintentar-pendientes).
 * Configurable por tenant para que cada negocio elija un momento en que el
 * sistema no este en uso; 21:00 por defecto, como pidio el cliente.
 *
 * Sin ->after(): a diferencia de tallermoto, generico no tiene las
 * columnas reserva_notif_* (exclusivas de ese vertical) para anclarse.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->time('sunat_reintento_hora')->default('21:00:00');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->dropColumn('sunat_reintento_hora');
        });
    }
};
