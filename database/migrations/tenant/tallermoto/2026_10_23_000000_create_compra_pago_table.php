<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pago mixto de una compra al contado: cuanto se pago con cada metodo
 * (ej. S/ 50 en Efectivo + S/ 30 por Yape). Mismo diseno que venta_pago:
 * la compra queda con MEP_Id = "Pago Mixto" (etiqueta de referencia) y el
 * detalle real vive aqui. La caja reparte la compra por estas filas para
 * descontar del efectivo solo lo que de verdad salio en efectivo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('compra_pago', function (Blueprint $table) {
            $table->increments('CPG_Id');
            $table->unsignedInteger('COM_Id');
            $table->unsignedInteger('MEP_Id');
            $table->decimal('CPG_Monto', 10, 2);
            $table->timestamps();

            $table->index('COM_Id', 'CPG_KFR1');
            $table->foreign('COM_Id', 'CPG_KFR1')->references('COM_Id')->on('compra')->onDelete('cascade');
            $table->foreign('MEP_Id', 'CPG_KFR2')->references('MEP_Id')->on('metodo_pago');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('compra_pago');
    }
};
