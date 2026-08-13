<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCobroNotificacionesTable extends Migration
{
    public function up(): void
    {
        Schema::create('cobro_notificaciones', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            // Periodo (ciclo de facturación, 'YYYY-MM') al que corresponde el aviso.
            $table->string('periodo', 7);

            $table->enum('tipo', ['recordatorio', 'vencido', 'suspension']);

            $table->timestamp('enviado_en');

            $table->timestamps();

            // Evita reenviar el mismo tipo de aviso más de una vez por ciclo.
            $table->unique(['client_id', 'periodo', 'tipo']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cobro_notificaciones');
    }
}
