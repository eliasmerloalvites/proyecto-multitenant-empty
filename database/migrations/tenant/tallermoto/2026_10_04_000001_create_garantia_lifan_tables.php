<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modulo "Garantia Lifan": misma estructura que Mantenimiento Preventivo
 * Carburada (MPC), con prefijo MGL, en su propia tabla para poder reportar
 * por separado cuantas garantias son de Lifan. Ya incluye de una vez las
 * columnas que MPC fue ganando en migraciones posteriores (PLAN_Id, RES_Id,
 * aviso al mecanico, fechas de inicio/termino, recomendacion y verificacion
 * final, origen/BCI_Id en repuestos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_garantia_lifan', function (Blueprint $table) {
            $table->id('MGL_Id');
            $table->string('MGL_Placa', 18);
            $table->string('MGL_Propietario', 50);
            $table->string('MGL_celular', 12)->nullable();
            $table->string('MGL_Unidad', 40);
            $table->string('MGL_KMEntrada', 20)->nullable();
            $table->text('MGL_DetalleIngreso')->nullable();
            $table->text('MGL_DetalleObservacion')->nullable();
            $table->string('MGL_Det1', 2)->nullable();
            $table->string('MGL_Det1Informacion', 50)->nullable();
            $table->string('MGL_Det2', 2)->nullable();
            $table->string('MGL_Det3', 2)->nullable();
            $table->string('MGL_Det4', 2)->nullable();
            $table->string('MGL_Det5', 2)->nullable();
            $table->string('MGL_Det6', 2)->nullable();
            $table->string('MGL_Det7', 2)->nullable();
            $table->string('MGL_Det7Admision', 10)->nullable();
            $table->string('MGL_Det7Escape', 10)->nullable();
            $table->string('MGL_Det8', 2)->nullable();
            $table->string('MGL_Det8Medida', 10)->nullable();
            $table->string('MGL_Det9', 10)->nullable();
            $table->string('MGL_Det10', 10)->nullable();
            $table->string('MGL_Det11', 2)->nullable();
            $table->string('MGL_Det11Vida', 20)->nullable();
            $table->string('MGL_Det11Carga', 20)->nullable();
            $table->string('MGL_Det11Arranque', 20)->nullable();
            $table->text('MGL_DetalleRealizado')->nullable();
            $table->text('MGL_CorrecionObservacion')->nullable();
            $table->string('MGL_ProximoCambioAceite', 50)->nullable();
            $table->string('MGL_ProximoServicio', 50)->nullable();
            $table->dateTime('MGL_FechaCreacion')->nullable();
            $table->dateTime('MGL_FechaInicio')->nullable();
            $table->dateTime('MGL_FechaEdicion')->nullable();
            $table->dateTime('MGL_FechaTermino')->nullable();
            $table->integer('MGL_UsuarioCreacion')->nullable();
            $table->integer('MGL_UsuarioEditado')->nullable();
            $table->string('MGL_Estado', 20)->default('PENDIENTE');
            $table->integer('PER_Id')->nullable();
            $table->boolean('statevalidate')->default(false);
            $table->boolean('notificar')->default(false);
            $table->text('observacion')->nullable();
            $table->text('respuesta')->nullable();
            $table->unsignedBigInteger('PLAN_Id')->nullable();
            $table->unsignedBigInteger('RES_Id')->nullable();
            $table->boolean('MGL_AvisoMecanico')->default(false);
            $table->text('MGL_Recomendacion')->nullable();
            $table->enum('MGL_RecomendacionPrioridad', ['ALTA', 'MEDIA', 'BAJA'])->nullable();
            $table->boolean('MGL_VerifArranque')->nullable();
            $table->boolean('MGL_VerifLuces')->nullable();
            $table->boolean('MGL_VerifDireccionales')->nullable();
            $table->boolean('MGL_VerifNivelAceite')->nullable();
            $table->boolean('MGL_VerifPruebaRuta')->nullable();
            $table->boolean('MGL_VerifLavado')->nullable();
            $table->string('MGL_VerifOtros', 150)->nullable();
            $table->boolean('MGL_VerifConforme')->nullable();

            $table->foreign('RES_Id')->references('RES_Id')->on('reservacion')->nullOnDelete();
        });

        Schema::create('mgl_detalle_reemplazo', function (Blueprint $table) {
            $table->unsignedBigInteger('MGL_Id');
            $table->unsignedBigInteger('MGLD_Item');
            $table->string('MGLD_Descripcion', 100);
            $table->decimal('MGL_Precio', 10, 2)->nullable();
            $table->string('origen', 10)->default('MANUAL');
            $table->unsignedBigInteger('BCI_Id')->nullable();

            $table->primary(['MGL_Id', 'MGLD_Item']);

            $table->foreign('MGL_Id', 'RMGLD_1')
                ->references('MGL_Id')
                ->on('mantenimiento_garantia_lifan')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('BCI_Id')->references('BCI_Id')->on('bahia_cuenta_item')->cascadeOnDelete();
        });

        Schema::create('mgl_imagen', function (Blueprint $table) {
            $table->unsignedBigInteger('MGL_Id');
            $table->unsignedBigInteger('MGLI_Item');
            $table->text('MGLI_url')->nullable();
            $table->text('MGLI_Thumb')->nullable();
            $table->string('MGLI_Nombre', 100)->nullable();
            $table->string('MGLI_Peso', 20)->nullable();

            $table->primary(['MGL_Id', 'MGLI_Item']);

            $table->foreign('MGL_Id', 'RMGLI_1')
                ->references('MGL_Id')
                ->on('mantenimiento_garantia_lifan')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mgl_imagen');
        Schema::dropIfExists('mgl_detalle_reemplazo');
        Schema::dropIfExists('mantenimiento_garantia_lifan');
    }
};
