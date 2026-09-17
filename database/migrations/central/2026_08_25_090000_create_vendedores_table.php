<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendedoresTable extends Migration
{
    /**
     * Un vendedor ES un usuario central (App\Models\Central\User) con rol
     * 'Vendedor' — esta tabla solo guarda los datos propios del negocio de
     * referidos, sin duplicar el login.
     */
    public function up(): void
    {
        Schema::create('vendedores', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();

            // Va en el link público de registro (?ref=CODIGO).
            $table->string('codigo_referido')->unique();

            $table->enum('estado', ['activo', 'inactivo'])->default('activo');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendedores');
    }
}
