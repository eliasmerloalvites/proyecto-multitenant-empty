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

            // EMPRESA
            $table->string('ruc', 11);
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();

            // DIRECCION
            $table->string('ubigeo', 6)->nullable();
            $table->string('direccion')->nullable();
            $table->string('departamento')->nullable();
            $table->string('provincia')->nullable();
            $table->string('distrito')->nullable();
            $table->string('cod_local', 4)->default('0000');

            // CONTACTO
            $table->string('telefono')->nullable();
            $table->string('whatsapp')->nullable();
            $table->string('correo')->nullable();
            $table->string('web')->nullable();

            // LOGOS
            $table->string('logo')->nullable();
            $table->string('logo_pdf')->nullable();

            // SOL
            $table->string('sol_usuario')->nullable();
            $table->string('sol_password')->nullable();

            // CERTIFICADO
            $table->string('certificado_ruta')->nullable();
            $table->string('certificado_password')->nullable();
            $table->date('certificado_vencimiento')->nullable();

            // FACTURACION
            $table->enum('ambiente', [
                'beta',
                'produccion'
            ])->default('beta');

            $table->enum('proveedor_facturacion', [
                'sunat',
                'ose',
                'nubefact'
            ])->default('sunat');

            $table->boolean('facturacion_electronica')->default(true);

            // SERIES
            $table->string('serie_factura')->nullable();
            $table->string('serie_boleta')->nullable();
            $table->string('serie_nota_credito')->nullable();
            $table->string('serie_nota_debito')->nullable();

            // CONFIG
            $table->string('moneda', 3)->default('PEN');
            $table->integer('decimales')->default(2);

            $table->enum('formato_pdf', [
                'ticket',
                'a4',
                'a5'
            ])->default('ticket');

            // BRANDING
            $table->string('color_principal')->default('#00398A');

            // ESTADO
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
