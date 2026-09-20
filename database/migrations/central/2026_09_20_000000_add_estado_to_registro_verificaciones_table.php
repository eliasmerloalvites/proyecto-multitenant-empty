<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * El aprovisionamiento del tenant (crear BD, migrar, seedear) ya no corre
 * sincrono dentro de verificar() -- eso causaba 504 de nginx en el VPS
 * cuando tardaba mas que el timeout del proxy, aunque el tenant terminara
 * creandose igual en segundo plano. Ahora corre en ProvisionarTenantJob
 * (cola) y esta fila es el estado que la pantalla de "creando tu cuenta"
 * va consultando por AJAX hasta que termina.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('registro_verificaciones', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'procesando', 'completado', 'error'])
                ->default('pendiente')->after('verificado_en');
            $table->text('error_mensaje')->nullable()->after('estado');
            $table->string('tenant_domain')->nullable()->after('error_mensaje');
        });
    }

    public function down(): void
    {
        Schema::table('registro_verificaciones', function (Blueprint $table) {
            $table->dropColumn(['estado', 'error_mensaje', 'tenant_domain']);
        });
    }
};
