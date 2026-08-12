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

            // RELACIÓN TENANT
            // Nota: Si usas stancl/tenancy u otro paquete, asegúrate de ajustar 'tenants' o usar foreignUuid / foreignId.
            $table->string('tenant_id')->unique();
            $table->foreign('tenant_id')
                  ->references('id')
                  ->on('tenants')
                  ->onDelete('cascade');

            // IDENTIFICACIÓN EMPRESA
            $table->string('ruc', 11)->index();
            $table->string('razon_social');
            $table->string('nombre_comercial')->nullable();

            // DIRECCIÓN FISCAL Y LOCAL
            $table->string('ubigeo', 6)->nullable();
            $table->text('direccion')->nullable(); // 'text' por si la dirección fiscal es muy extendida
            $table->string('departamento', 100)->nullable();
            $table->string('provincia', 100)->nullable();
            $table->string('distrito', 100)->nullable();
            $table->string('cod_local', 4)->default('0000'); // Código de establecimiento anexo SUNAT

            // CONTACTO
            $table->string('telefono', 30)->nullable();
            $table->string('whatsapp', 30)->nullable();
            $table->string('correo')->nullable();
            $table->string('web')->nullable();

            // RECURSOS VISUALES / LOGOS
            $table->string('logo')->nullable();
            $table->string('logo_pdf')->nullable();
            $table->string('logo_portada1')->nullable();
            $table->string('logo_portada2')->nullable();

            // CREDENCIALES SOL (Guardar encriptado usando Crypt/Attribute Casting en Eloquent)
            $table->string('sol_usuario', 50)->nullable();
            $table->text('sol_password')->nullable(); // 'text' para alojar la cadena encriptada con Crypt::encryptString()

            // CERTIFICADO DIGITAL
            $table->string('certificado_ruta')->nullable();
            $table->text('certificado_password')->nullable(); // Encriptado
            $table->date('certificado_vencimiento')->nullable();

            // PARAMETRIZACIÓN FACTURACIÓN
            $table->enum('ambiente', ['beta', 'produccion'])->default('beta');
            $table->enum('proveedor_facturacion', ['sunat', 'ose', 'nubefact'])->default('sunat');
            $table->boolean('facturacion_electronica')->default(true);

            // SERIES PREDETERMINADAS (Series SUNAT tienen 4 caracteres: F001, B001, FC01, FN01, etc.)
            $table->string('serie_factura', 4)->nullable();
            $table->string('serie_boleta', 4)->nullable();
            $table->string('serie_nota_credito', 4)->nullable();
            $table->string('serie_nota_debito', 4)->nullable();
            $table->string('serie_guia_remision', 4)->nullable(); // Adicionado sugerido

            // CONFIGURACIÓN DE IMPRESIÓN Y MONEDA
            $table->string('moneda', 3)->default('PEN');
            $table->unsignedTinyInteger('decimales')->default(2);
            $table->enum('formato_pdf', ['ticket', 'a4', 'a5'])->default('ticket');

            // PERSONALIZACIÓN Y BRANDING (Colores Hexadecimales #RRGGBB)
            $table->string('color_principal', 7)->default('#00398A');
            $table->enum('tipo_tema', ['dark', 'light'])->default('dark');
            $table->string('color_main', 7)->default('#3b82f6');
            $table->string('color_light', 7)->default('#60a5fa');
            $table->string('color_bg', 7)->default('#030712');
            $table->string('color_card', 7)->default('#070b17');

            // ESTADO
            $table->boolean('activo')->default(true)->index();

            $table->timestamps();
            $table->softDeletes(); // Opcional: Recomendado para auditorías de configuración
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