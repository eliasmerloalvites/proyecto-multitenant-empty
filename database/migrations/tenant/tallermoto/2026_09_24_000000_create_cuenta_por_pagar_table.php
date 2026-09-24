<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cuenta por pagar: una por cada compra registrada al credito. Mismo
 * diseño que cuenta_por_cobrar (ver esa migracion) pero del lado de
 * compras/proveedores: el saldo se va reduciendo con los abonos
 * (cuenta_por_pagar_abono) hasta llegar a 0, momento en que pasa a
 * CPP_Estado = 'PAGADA'. "VENCIDA" no se guarda aparte: se calcula al
 * mostrarla comparando CPP_FechaVencimiento contra hoy mientras siga
 * PENDIENTE.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_por_pagar', function (Blueprint $table) {
            $table->increments('CPP_Id');
            $table->unsignedInteger('COM_Id')->unique();
            $table->decimal('CPP_MontoTotal', 10, 2);
            $table->decimal('CPP_MontoAbonado', 10, 2)->default(0);
            $table->decimal('CPP_MontoFaltante', 10, 2);
            $table->date('CPP_FechaEmision');
            $table->date('CPP_FechaVencimiento');
            $table->string('CPP_Estado', 15)->default('PENDIENTE');
            $table->timestamps();

            $table->foreign('COM_Id', 'CPP_KFR1')
                  ->references('COM_Id')
                  ->on('compra')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_por_pagar');
    }
};
