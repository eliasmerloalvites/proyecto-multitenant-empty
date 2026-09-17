<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionesTable extends Migration
{
    /**
     * Ledger append-only: una fila por cada pago real que generó comisión
     * (nunca por "mes esperado" sin pagar). porcentaje_aplicado y
     * monto_comision quedan precalculados al momento de crearse (ver
     * PagoController::store()) para que el reporte nunca tenga que
     * recalcular contra esquemas_comision, que puede haber cambiado desde
     * entonces.
     */
    public function up(): void
    {
        Schema::create('comisiones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pago_id')->unique()->constrained('pagos')->cascadeOnDelete();
            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();
            $table->foreignId('vendedor_id')->constrained('vendedores')->cascadeOnDelete();

            // Copiado de pagos.periodo (formato 'YYYY-MM').
            $table->string('periodo', 7);

            $table->decimal('monto_pago', 10, 2);
            $table->decimal('porcentaje_aplicado', 5, 2);
            $table->decimal('monto_comision', 10, 2);

            $table->timestamps();

            $table->index(['vendedor_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones');
    }
}
