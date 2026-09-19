<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cabecera de "cuenta por pagar": una fila por compra al credito
 * (compra.COM_TipoPago = 'Credito'), exclusiva del vertical 'generico'.
 * Guarda el total de la compra, cuanto se dejo de adelanto al proveedor,
 * cuanto se ha abonado (cache) y cuanto falta, y si se definio un plan de
 * cuotas fijo o se va a abonar libremente (ver cuenta_pagar_cuota /
 * cuenta_pagar_abono).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_pagar', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CXP_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('COM_Id');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->double('CXP_MontoTotal', 10, 2);
            $table->double('CXP_MontoAdelanto', 10, 2)->default(0);
            $table->double('CXP_MontoAbonado', 10, 2)->default(0);
            $table->double('CXP_MontoPendiente', 10, 2);
            $table->boolean('CXP_TieneCuotas')->default(0);
            $table->unsignedTinyInteger('CXP_NumCuotas')->nullable();
            $table->unsignedSmallInteger('CXP_FrecuenciaDias')->nullable();
            $table->date('CXP_FechaEmision');
            $table->date('CXP_FechaVencimiento')->nullable();
            /* ESTADO: 1 = Pendiente, 2 = Pagado */
            $table->tinyInteger('CXP_Estado')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->unique('COM_Id', 'CXP_UQ1');
            $table->index('USU_Id', 'CXP_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('COM_Id', 'CXP_KFR1')
                  ->references('COM_Id')
                  ->on('compra')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'CXP_KFR2')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_pagar');
    }
};
