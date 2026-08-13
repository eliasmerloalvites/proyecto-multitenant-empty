<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanesTable extends Migration
{
    public function up(): void
    {
        Schema::create('planes', function (Blueprint $table) {
            // 'start' | 'basic' | 'plus' | 'empresarial' — mismo valor que tenants.plan.
            $table->string('key')->primary();

            $table->string('nombre');

            /* PRECIO DE REFERENCIA (S/ / mes) */
            $table->decimal('price', 10, 2)->default(0);

            /* LIMITES */
            $table->unsignedInteger('max_users')->default(1);
            $table->unsignedInteger('max_images')->default(1);
            $table->unsignedInteger('storage_limit_mb')->default(500);

            /* FEATURES */
            $table->boolean('custom_domain_enabled')->default(false);
            $table->boolean('custom_branding')->default(false);
            $table->boolean('customizable')->default(false);

            /* MÓDULOS DEL PANEL (mantenimientos incluye el flujo de reservas) */
            $table->json('modules');

            /* LIMITES DE NEGOCIO (branches/warehouses/cash_registers) */
            $table->json('limits');

            /* BRANDING POR DEFECTO */
            $table->json('branding')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes');
    }
}
