<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plan de cuotas de una cuenta por pagar (solo existe si se definieron
 * cuotas fijas al momento de la compra). Cada fila es una cuota
 * planificada; los abonos se aplican aqui en orden (FIFO) desde
 * CuentaPagarAbonoService. Exclusiva del vertical 'generico'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_pagar_cuota', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CXPC_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('CXP_Id');
            /* CAMPOS */
            $table->unsignedTinyInteger('CXPC_Numero');
            $table->double('CXPC_MontoProgramado', 10, 2);
            $table->date('CXPC_FechaVencimiento');
            $table->double('CXPC_MontoAbonado', 10, 2)->default(0);
            /* ESTADO: 1 = Pendiente, 2 = Pagado */
            $table->tinyInteger('CXPC_Estado')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->unique(['CXP_Id', 'CXPC_Numero'], 'CXPC_UQ1');
            /* FOREIGN KEYS */
            $table->foreign('CXP_Id', 'CXPC_KFR1')
                  ->references('CXP_Id')
                  ->on('cuenta_pagar')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_pagar_cuota');
    }
};
