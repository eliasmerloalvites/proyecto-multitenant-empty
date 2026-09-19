<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La vertical tallermoto ya nace con estos campos en su propio
     * create_almacen_table.php (sede/establecimiento anexo SUNAT, series de
     * comprobantes, geolocalización). En generico nunca se agregaron, así
     * que TenantProvisioningService::provision() y el modelo Almacen (que
     * ya los declara en $fillable) fallaban con "Column not found" al crear
     * el almacén principal de un tenant nuevo. Cada columna se agrega
     * guardada con hasColumn() para que esta migración sea segura de correr
     * también sobre tenants generico ya existentes (creados antes de esto).
     */
    public function up(): void
    {
        if (!Schema::hasTable('almacen')) {
            return;
        }

        Schema::table('almacen', function (Blueprint $table) {
            if (!Schema::hasColumn('almacen', 'ALM_CodigoSunat')) {
                $table->string('ALM_CodigoSunat', 4)->default('0000')->comment('Código de establecimiento anexo de SUNAT (ej. 0000, 0001)');
            }
            if (!Schema::hasColumn('almacen', 'ALM_EsPrincipal')) {
                $table->boolean('ALM_EsPrincipal')->default(false)->comment('Define si es la sede/almacén principal');
            }
            if (!Schema::hasColumn('almacen', 'ALM_Departamento')) {
                $table->string('ALM_Departamento', 100)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Provincia')) {
                $table->string('ALM_Provincia', 100)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Distrito')) {
                $table->string('ALM_Distrito', 100)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Ubigeo')) {
                $table->string('ALM_Ubigeo', 6)->nullable()->comment('Código ubigeo de 6 dígitos para SUNAT');
            }
            if (!Schema::hasColumn('almacen', 'ALM_Referencia')) {
                $table->string('ALM_Referencia', 250)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Latitud')) {
                $table->decimal('ALM_Latitud', 10, 8)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Longitud')) {
                $table->decimal('ALM_Longitud', 11, 8)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Encargado')) {
                $table->string('ALM_Encargado', 150)->nullable()->comment('Nombre del responsable del almacén');
            }
            if (!Schema::hasColumn('almacen', 'ALM_Telefono')) {
                $table->string('ALM_Telefono', 20)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_Email')) {
                $table->string('ALM_Email', 150)->nullable();
            }
            if (!Schema::hasColumn('almacen', 'ALM_SerieFactura')) {
                $table->string('ALM_SerieFactura', 4)->nullable()->comment('Ej. F001');
            }
            if (!Schema::hasColumn('almacen', 'ALM_SerieBoleta')) {
                $table->string('ALM_SerieBoleta', 4)->nullable()->comment('Ej. B001');
            }
            if (!Schema::hasColumn('almacen', 'ALM_SerieNotaCredito')) {
                $table->string('ALM_SerieNotaCredito', 4)->nullable()->comment('Ej. FC01 (serie compartida legada; ver ALM_SerieNotaCreditoBoleta/Factura)');
            }
            if (!Schema::hasColumn('almacen', 'ALM_SerieNotaDebito')) {
                $table->string('ALM_SerieNotaDebito', 4)->nullable()->comment('Ej. FD01');
            }
            if (!Schema::hasColumn('almacen', 'ALM_SerieGuiaRemision')) {
                $table->string('ALM_SerieGuiaRemision', 4)->nullable()->comment('Ej. T001');
            }
            if (!Schema::hasColumn('almacen', 'ALM_SerieNotaVenta')) {
                $table->string('ALM_SerieNotaVenta', 4)->nullable()->comment('Ej. NV01 para tickets/notas de venta internas');
            }
            if (!Schema::hasColumn('almacen', 'ALM_PermitirVentaSinStock')) {
                $table->boolean('ALM_PermitirVentaSinStock')->default(false);
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('almacen')) {
            return;
        }

        Schema::table('almacen', function (Blueprint $table) {
            foreach ([
                'ALM_CodigoSunat',
                'ALM_EsPrincipal',
                'ALM_Departamento',
                'ALM_Provincia',
                'ALM_Distrito',
                'ALM_Ubigeo',
                'ALM_Referencia',
                'ALM_Latitud',
                'ALM_Longitud',
                'ALM_Encargado',
                'ALM_Telefono',
                'ALM_Email',
                'ALM_SerieFactura',
                'ALM_SerieBoleta',
                'ALM_SerieNotaCredito',
                'ALM_SerieNotaDebito',
                'ALM_SerieGuiaRemision',
                'ALM_SerieNotaVenta',
                'ALM_PermitirVentaSinStock',
            ] as $columna) {
                if (Schema::hasColumn('almacen', $columna)) {
                    $table->dropColumn($columna);
                }
            }
        });
    }
};
