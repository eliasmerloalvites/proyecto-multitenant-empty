<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSeguimientoClientesTables extends Migration
{
    /**
     * Seguimiento comercial de los clientes (sobre todo los que se registraron
     * en prueba): una ficha por cliente (etapa, proxima accion, nivel de uso
     * medido) y su bitacora de contactos y notas.
     */
    public function up(): void
    {
        Schema::create('seguimiento_clientes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->unique()->constrained('clients')->cascadeOnDelete();

            // nuevo | contactado | demo | interesado | convertido | perdido
            $table->string('etapa', 20)->default('nuevo');
            $table->string('motivo_perdida', 255)->nullable();

            $table->date('proxima_accion_at')->nullable();
            $table->string('proxima_accion_nota', 255)->nullable();
            $table->timestamp('ultimo_contacto_at')->nullable();

            // El cliente pidio no recibir mas mensajes: ningun envio (ni manual
            // ni automatico) debe salirle.
            $table->boolean('no_contactar')->default(false);

            // Medicion de uso (ver UsoClienteService): sin_uso | poco_uso | activo
            $table->string('uso_nivel', 15)->nullable();
            $table->json('uso_datos')->nullable();
            $table->timestamp('ultima_actividad_at')->nullable();
            $table->timestamp('uso_medido_at')->nullable();

            $table->unsignedBigInteger('responsable_id')->nullable();

            $table->timestamps();

            $table->index('etapa');
            $table->index('proxima_accion_at');
        });

        Schema::create('seguimiento_eventos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('client_id')->constrained('clients')->cascadeOnDelete();

            // nota | correo | whatsapp | llamada | etapa | auto
            $table->string('tipo', 15);
            $table->string('asunto', 255)->nullable();
            $table->text('detalle')->nullable();

            // Evita repetir un envio automatico (ej. 'auto_sin_uso').
            $table->string('clave', 40)->nullable();

            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nombre', 120)->nullable();

            $table->timestamp('created_at')->useCurrent();

            $table->index(['client_id', 'created_at']);
            $table->unique(['client_id', 'clave']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguimiento_eventos');
        Schema::dropIfExists('seguimiento_clientes');
    }
}
