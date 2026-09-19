<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Detalle de abonos (pagos parciales) hechos contra una cuenta por cobrar.
 * Una fila por cada aplicacion real de dinero: si un abono cae dentro de
 * un plan de cuotas y cruza mas de una cuota, se registra una fila por
 * cada cuota que toco (CCC_Id no nulo); si la cuenta no tiene cuotas
 * definidas, CCC_Id queda null y el abono solo reduce el saldo general.
 * Exclusiva del vertical 'generico'.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_cobrar_abono', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CCA_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('CXC_Id');
            $table->unsignedInteger('CCC_Id')->nullable();
            $table->unsignedInteger('MEP_Id');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->double('CCA_Monto', 10, 2);
            $table->dateTime('CCA_Fecha');
            $table->string('CCA_Descripcion', 150)->nullable();
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('CCC_Id', 'CCA_KFR2');
            $table->index('MEP_Id', 'CCA_KFR3');
            $table->index('USU_Id', 'CCA_KFR4');
            /* FOREIGN KEYS */
            $table->foreign('CXC_Id', 'CCA_KFR1')
                  ->references('CXC_Id')
                  ->on('cuenta_cobrar')
                  ->onDelete('cascade');

            $table->foreign('CCC_Id', 'CCA_KFR2')
                  ->references('CCC_Id')
                  ->on('cuenta_cobrar_cuota')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'CCA_KFR3')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'CCA_KFR4')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_cobrar_abono');
    }
};
