<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Detalle de abonos (pagos parciales) hechos contra una cuenta por pagar,
 * es decir, dinero que se le entrega al proveedor. Una fila por cada
 * aplicacion real de dinero: si un abono cae dentro de un plan de cuotas y
 * cruza mas de una cuota, se registra una fila por cada cuota que toco
 * (CXPC_Id no nulo); si la cuenta no tiene cuotas definidas, CXPC_Id queda
 * null y el abono solo reduce el saldo general. Exclusiva del vertical
 * 'generico'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_pagar_abono', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CXPA_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('CXP_Id');
            $table->unsignedInteger('CXPC_Id')->nullable();
            $table->unsignedInteger('MEP_Id');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->double('CXPA_Monto', 10, 2);
            $table->dateTime('CXPA_Fecha');
            $table->string('CXPA_Descripcion', 150)->nullable();
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('CXPC_Id', 'CXPA_KFR2');
            $table->index('MEP_Id', 'CXPA_KFR3');
            $table->index('USU_Id', 'CXPA_KFR4');
            /* FOREIGN KEYS */
            $table->foreign('CXP_Id', 'CXPA_KFR1')
                  ->references('CXP_Id')
                  ->on('cuenta_pagar')
                  ->onDelete('cascade');

            $table->foreign('CXPC_Id', 'CXPA_KFR2')
                  ->references('CXPC_Id')
                  ->on('cuenta_pagar_cuota')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'CXPA_KFR3')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'CXPA_KFR4')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_pagar_abono');
    }
};
