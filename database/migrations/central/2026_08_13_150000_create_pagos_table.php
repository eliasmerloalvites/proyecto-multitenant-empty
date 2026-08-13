<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagosTable extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            $table->decimal('monto', 10, 2);

            // Periodo que cubre el pago (mes de facturación), formato 'YYYY-MM'.
            // Permite saber si el ciclo actual ya está pagado sin recalcular
            // sobre fecha_pago (que es cuándo se pagó, no qué mes cubre).
            $table->string('periodo', 7);

            $table->date('fecha_pago');

            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'yape_plin', 'tarjeta', 'otro'])
                ->default('transferencia');

            $table->text('nota')->nullable();

            $table->foreignId('registrado_por')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->unique(['client_id', 'periodo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
}
