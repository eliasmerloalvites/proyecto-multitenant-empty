<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Detalle de pagos de una venta. Una venta simple tiene una sola fila
 * aqui (el mismo metodo que ya queda en venta.MEP_Id); una venta con pago
 * mixto (ej. mitad efectivo, mitad Yape) tiene una fila por cada metodo
 * usado. venta.MEP_Id sigue existiendo para no romper reportes que ya
 * unen por ese campo: en un pago mixto apunta a un metodo "Pago Mixto"
 * de referencia.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venta_pago', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('VNP_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('VEN_Id');
            $table->unsignedInteger('MEP_Id');
            /* CAMPOS */
            $table->decimal('VNP_Monto', 10, 2);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('VEN_Id', 'VNP_KFR1');
            $table->index('MEP_Id', 'VNP_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('VEN_Id', 'VNP_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'VNP_KFR2')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_pago');
    }
};
