<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Adelantos de pago sobre una cuenta de bahia todavia abierta: el cliente
 * paga una parte hoy (entra a la caja del turno abierto en ese momento, por
 * eso guarda CAJ_Id/CS_Id) y el resto al cobrar la cuenta, quizas otro dia.
 * Al cobrar, el punto de venta solo pide el saldo y la caja de ese dia
 * cuenta la venta menos lo ya adelantado (ver CajaSesionController).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahia_cuenta_adelanto', function (Blueprint $table) {
            $table->id('BCA_Id');

            $table->unsignedBigInteger('BCT_Id');
            $table->unsignedInteger('MEP_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->unsignedInteger('CAJ_Id')->nullable();
            $table->unsignedInteger('CS_Id')->nullable();

            $table->decimal('BCA_Monto', 10, 2);
            $table->string('BCA_Observacion', 255)->nullable();

            $table->timestamps();

            $table->index('CS_Id', 'BCA_IDX_CS');

            $table->foreign('BCT_Id', 'BCA_FK_CUENTA')->references('BCT_Id')->on('bahia_cuenta')->cascadeOnDelete();
            $table->foreign('MEP_Id', 'BCA_FK_METODO')->references('MEP_Id')->on('metodo_pago')->restrictOnDelete();
            $table->foreign('USU_Id', 'BCA_FK_USUARIO')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahia_cuenta_adelanto');
    }
};
