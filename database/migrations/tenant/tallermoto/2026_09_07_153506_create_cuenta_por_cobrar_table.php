<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cuenta por cobrar: una por cada venta registrada al credito (Nota,
 * Boleta o Factura). El saldo se va reduciendo con los abonos
 * (cuenta_por_cobrar_abono) hasta llegar a 0, momento en que pasa a
 * CPC_Estado = 'PAGADA'. "VENCIDA" no se guarda aparte: se calcula al
 * mostrarla comparando CPC_FechaVencimiento contra hoy mientras siga
 * PENDIENTE.
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cuenta_por_cobrar', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CPC_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('VEN_Id')->unique();
            /* CAMPOS */
            $table->decimal('CPC_MontoTotal', 10, 2);
            $table->decimal('CPC_MontoAbonado', 10, 2)->default(0);
            $table->decimal('CPC_MontoFaltante', 10, 2);
            $table->date('CPC_FechaEmision');
            $table->date('CPC_FechaVencimiento');
            $table->string('CPC_Estado', 15)->default('PENDIENTE');
            /* TIMESTAMPS */
            $table->timestamps();
            /* FOREIGN KEYS */
            $table->foreign('VEN_Id', 'CPC_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuenta_por_cobrar');
    }
};
