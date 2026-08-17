<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditLogsTable extends Migration
{
    /**
     * Registro inmutable de acciones del staff en el panel central (quién
     * creó/editó/suspendió un cliente, quién registró un pago, quién
     * cambió un plan). user_name queda como copia además del user_id por
     * si esa cuenta se llega a borrar más adelante, para no perder el rastro.
     */
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('user_name')->nullable();

            $table->string('accion'); // ej: 'cliente.creado', 'plan.actualizado'
            $table->string('descripcion');
            $table->json('datos')->nullable(); // contexto extra: valores antes/después, etc.
            $table->string('ip', 45)->nullable();

            $table->timestamp('created_at')->nullable();

            $table->index('accion');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
}
