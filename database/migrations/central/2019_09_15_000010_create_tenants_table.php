<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {

            $table->string('id')->primary();
            $table->enum('tipo_negocio', ['generico','tallermoto','optica','ferreteria','restaurant','hotel']);
            $table->enum('plan', ['start','basic','plus','empresarial',])->default('start');
            $table->enum('status', ['activo','suspendido', 'cancelado',])->default('activo');
            /* | LIMITES |*/
            $table->integer('max_users')->default(3);
            $table->integer('max_images')->default(4);
            $table->integer('storage_limit_mb')->default(500);
            /* | FEATURES |*/
            $table->boolean('custom_domain_enabled')->default(false);
            $table->boolean('custom_branding')->default(false);
            /* | CONFIGURACION DINAMICA | */
            $table->json('data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('tenants');
    }
}
