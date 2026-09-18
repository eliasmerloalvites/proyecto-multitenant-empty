<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClienteVendedorTable extends Migration
{
    /**
     * Engancha un cliente a quien lo refirió, congelando el % y los meses
     * vigentes del vendedor EN EL MOMENTO DEL ALTA (porcentaje_congelado /
     * meses_congelado). No es un puntero vivo a esquemas_comision: aunque el
     * esquema del vendedor cambie después, esta fila nunca se vuelve a leer
     * contra la tabla de esquemas.
     */
    public function up(): void
    {
        Schema::create('cliente_vendedor', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();
            $table->foreignId('vendedor_id')->constrained('vendedores')->cascadeOnDelete();
            $table->foreignId('esquema_comision_id')->nullable()->constrained('esquemas_comision')->nullOnDelete();

            $table->decimal('porcentaje_congelado', 5, 2);
            $table->smallInteger('meses_congelado')->unsigned();

            $table->timestamp('referido_en')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_vendedor');
    }
}
