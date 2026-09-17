<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateComisionesLiquidacionesTable extends Migration
{
    /**
     * Marca "ya facturé a este vendedor este mes" (el pago en sí se hace
     * fuera del sistema). Separada del ledger `comisiones` a propósito: acá
     * se liquida por vendedor+periodo completo, no cliente por cliente — una
     * sola fila que existe o no existe, sin riesgo de quedar a medias entre
     * los clientes de ese vendedor ese mes.
     */
    public function up(): void
    {
        Schema::create('comisiones_liquidaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendedor_id')->constrained('vendedores')->cascadeOnDelete();
            $table->string('periodo', 7);

            $table->foreignId('liquidado_por')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('liquidado_en')->useCurrent();
            $table->text('nota')->nullable();

            $table->timestamps();

            $table->unique(['vendedor_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comisiones_liquidaciones');
    }
}
