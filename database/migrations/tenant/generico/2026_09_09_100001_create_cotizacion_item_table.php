<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Linea de una cotizacion. PRO_Id nulo = item "libre" (algo que no esta en
 * el catalogo, ej. mano de obra): en ese caso COI_Nombre guarda el texto y
 * recien se materializa como Producto+Lote (via ItemRapidoService) si la
 * cotizacion se aprueba y se convierte en Venta -- mientras es solo una
 * cotizacion no toca el catalogo ni el stock.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion_item', function (Blueprint $table) {
            $table->id('COI_Id');
            $table->unsignedBigInteger('COT_Id');
            $table->unsignedInteger('PRO_Id')->nullable();
            $table->string('COI_Nombre', 150)->nullable();
            $table->decimal('COI_Cantidad', 10, 2);
            $table->decimal('COI_PrecioUnitario', 10, 2);
            $table->decimal('COI_Descuento', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('COT_Id')->references('COT_Id')->on('cotizacion')->onDelete('cascade');
            $table->foreign('PRO_Id')->references('PRO_Id')->on('producto');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_item');
    }
};
