<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Plan de cuotas de una cuenta por cobrar (solo existe si el cliente
 * definio cuotas fijas al momento de la venta). Cada fila es una cuota
 * planificada; los abonos se aplican aqui en orden (FIFO) desde
 * CuentaCobrarAbonoService. Exclusiva del vertical 'generico'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_cobrar_cuota', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CCC_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('CXC_Id');
            /* CAMPOS */
            $table->unsignedTinyInteger('CCC_Numero');
            $table->double('CCC_MontoProgramado', 10, 2);
            $table->date('CCC_FechaVencimiento');
            $table->double('CCC_MontoAbonado', 10, 2)->default(0);
            /* ESTADO: 1 = Pendiente, 2 = Pagado */
            $table->tinyInteger('CCC_Estado')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->unique(['CXC_Id', 'CCC_Numero'], 'CCC_UQ1');
            /* FOREIGN KEYS */
            $table->foreign('CXC_Id', 'CCC_KFR1')
                  ->references('CXC_Id')
                  ->on('cuenta_cobrar')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobrar_cuota');
    }
};
