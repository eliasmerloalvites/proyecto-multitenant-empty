<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cabecera de "cuenta por cobrar": una fila por venta al credito
 * (venta.VEN_TipoPago = 2), exclusiva del vertical 'generico'. Guarda el
 * total de la venta, cuanto se dejo de adelanto, cuanto se ha abonado
 * (cache) y cuanto falta, y si el cliente definio un plan de cuotas fijo
 * o va a abonar libremente (ver cuenta_cobrar_cuota / cuenta_cobrar_abono).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_cobrar', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CXC_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('VEN_Id');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->double('CXC_MontoTotal', 10, 2);
            $table->double('CXC_MontoAdelanto', 10, 2)->default(0);
            $table->double('CXC_MontoAbonado', 10, 2)->default(0);
            $table->double('CXC_MontoPendiente', 10, 2);
            $table->boolean('CXC_TieneCuotas')->default(0);
            $table->unsignedTinyInteger('CXC_NumCuotas')->nullable();
            $table->unsignedSmallInteger('CXC_FrecuenciaDias')->nullable();
            $table->date('CXC_FechaEmision');
            $table->date('CXC_FechaVencimiento')->nullable();
            /* ESTADO: 1 = Pendiente, 2 = Pagado */
            $table->tinyInteger('CXC_Estado')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->unique('VEN_Id', 'CXC_UQ1');
            $table->index('USU_Id', 'CXC_KFR2');
            /* FOREIGN KEYS */
            $table->foreign('VEN_Id', 'CXC_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'CXC_KFR2')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobrar');
    }
};
