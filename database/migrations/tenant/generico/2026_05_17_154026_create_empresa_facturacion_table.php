<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('empresa_facturacion', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id')->unique();
            $table->string('ruc', 11);
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();
            $table->string('ubigeo', 6)->nullable();
            $table->string('direccion')->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('cod_local', 4)->default('0000');
            // SOL
            $table->string('sol_usuario')->nullable();
            $table->string('sol_password')->nullable();
            // CERTIFICADO
            $table->string('certificado')->nullable();
            $table->string('certificado_password')->nullable();
            // BETA / PRODUCCION
            $table->enum('ambiente', [
                'beta',
                'produccion'
            ])->default('beta');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresa_facturacion');
    }
};
