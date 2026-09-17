<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEsquemasComisionTable extends Migration
{
    /**
     * "% de comisión + meses de duración" que le rige a un vendedor. Estas
     * filas nunca se editan una vez creadas: "subirle el % a un vendedor" es
     * insertar una fila nueva con activo=true y desactivar la anterior (ver
     * EsquemaComisionController). Así el historial queda intacto, y cada
     * cliente referido (cliente_vendedor) congela una copia de estos valores
     * en el momento del alta — un cambio aquí nunca afecta retroactivamente
     * a un cliente ya referido.
     */
    public function up(): void
    {
        Schema::create('esquemas_comision', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendedor_id')->constrained('vendedores')->cascadeOnDelete();

            $table->decimal('porcentaje', 5, 2);
            $table->smallInteger('meses_duracion')->unsigned();

            $table->boolean('activo')->default(true);
            $table->timestamp('vigente_desde')->useCurrent();

            $table->timestamps();

            $table->index(['vendedor_id', 'activo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('esquemas_comision');
    }
}
