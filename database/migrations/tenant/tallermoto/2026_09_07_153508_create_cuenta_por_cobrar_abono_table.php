<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cada pago parcial (abono) que reduce el saldo de una cuenta por cobrar.
 * Guarda su propia caja/sesion (CAJ_Id/CS_Id) porque un abono casi
 * siempre se recibe en un turno de caja distinto al de la venta original
 * - por eso CajaSesionController lo suma aparte al cuadrar caja, en vez
 * de reusar venta_pago (que esta atado a la sesion de la venta).
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuenta_por_cobrar_abono', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CPA_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('CPC_Id');
            $table->unsignedInteger('MEP_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->unsignedInteger('CAJ_Id')->nullable();
            $table->unsignedInteger('CS_Id')->nullable();
            /* CAMPOS */
            $table->decimal('CPA_Monto', 10, 2);
            $table->text('CPA_Observacion')->nullable();
            /* TIMESTAMPS */
            $table->timestamps();
            /* INDEX */
            $table->index('CPC_Id', 'CPA_KFR1');
            $table->index('CS_Id', 'CPA_KFR4');
            /* FOREIGN KEYS */
            $table->foreign('CPC_Id', 'CPA_KFR1')
                  ->references('CPC_Id')
                  ->on('cuenta_por_cobrar')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'CPA_KFR2')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'CPA_KFR3')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_por_cobrar_abono');
    }
};
