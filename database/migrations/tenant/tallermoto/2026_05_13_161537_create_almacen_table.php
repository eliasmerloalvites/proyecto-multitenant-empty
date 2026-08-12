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
        Schema::create('almacen', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('ALM_Id');

            /* RELACIÓN CON EMPRESA */
            $table->unsignedBigInteger('EMP_Id');
            // Si tienes la tabla empresas, puedes agregar la clave foránea:
            // $table->foreign('EMP_Id')->references('id')->on('empresa')->onDelete('cascade');

            /* DATOS GENERALES Y SEDE */
            $table->string('ALM_NombreAlmacen', 200);
            $table->string('ALM_CodigoSunat', 4)->default('0000')->comment('Código de establecimiento anexo de SUNAT (ej. 0000, 0001)');
            $table->boolean('ALM_EsPrincipal')->default(false)->comment('Define si es la sede/almacén principal');

            /* UBICACIÓN Y GEOGRAFÍA */
            $table->string('ALM_Direccion', 250)->nullable();
            $table->string('ALM_Departamento', 100)->nullable();
            $table->string('ALM_Provincia', 100)->nullable();
            $table->string('ALM_Distrito', 100)->nullable();
            $table->string('ALM_Ubigeo', 6)->nullable()->comment('Código ubigeo de 6 dígitos para SUNAT');
            $table->string('ALM_Referencia', 250)->nullable();
            $table->decimal('ALM_Latitud', 10, 8)->nullable();
            $table->decimal('ALM_Longitud', 11, 8)->nullable();

            /* CONTACTO Y RESPONSABLE */
            $table->string('ALM_Encargado', 150)->nullable()->comment('Nombre del responsable del almacén');
            $table->string('ALM_Celular', 20)->nullable();
            $table->string('ALM_Telefono', 20)->nullable();
            $table->string('ALM_Email', 150)->nullable();

            /* SERIES DE COMPROBANTES DE PAGO (SUNAT) */
            $table->string('ALM_SerieFactura', 4)->nullable()->comment('Ej. F001');
            $table->string('ALM_SerieBoleta', 4)->nullable()->comment('Ej. B001');
            $table->string('ALM_SerieNotaCredito', 4)->nullable()->comment('Ej. FC01');
            $table->string('ALM_SerieNotaDebito', 4)->nullable()->comment('Ej. FD01');
            $table->string('ALM_SerieGuiaRemision', 4)->nullable()->comment('Ej. T001');
            $table->string('ALM_SerieNotaVenta', 4)->nullable()->comment('Ej. NV01 para tickets/notas de venta internas');

            /* CONFIGURACIÓN Y ESTADO */
            $table->boolean('ALM_PermitirVentaSinStock')->default(false);
            $table->boolean('ALM_Status')->default(1);

            /* TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('almacen');
    }
};
