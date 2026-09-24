<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cada pago parcial (abono) que reduce el saldo de una cuenta por pagar.
 * Mismo diseño que cuenta_por_cobrar_abono: guarda su propia caja/sesion
 * porque un abono casi siempre se paga en un turno de caja distinto al de
 * la compra original.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_por_pagar_abono', function (Blueprint $table) {
            $table->increments('CPPA_Id');
            $table->unsignedInteger('CPP_Id');
            $table->unsignedInteger('MEP_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->unsignedInteger('CAJ_Id')->nullable();
            $table->unsignedInteger('CS_Id')->nullable();
            $table->decimal('CPPA_Monto', 10, 2);
            $table->text('CPPA_Observacion')->nullable();
            $table->timestamps();

            $table->index('CPP_Id', 'CPPA_KFR1');
            $table->index('CS_Id', 'CPPA_KFR4');

            $table->foreign('CPP_Id', 'CPPA_KFR1')
                  ->references('CPP_Id')
                  ->on('cuenta_por_pagar')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'CPPA_KFR2')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'CPPA_KFR3')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_por_pagar_abono');
    }
};
