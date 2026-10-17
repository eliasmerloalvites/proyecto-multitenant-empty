<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Modulo "Garantia Hero": misma estructura que Mantenimiento Preventivo
 * Carburada (MPC), con prefijo MGH, en su propia tabla para poder reportar
 * por separado cuantas garantias son de Hero. Ya incluye de una vez las
 * columnas que MPC fue ganando en migraciones posteriores (PLAN_Id, RES_Id,
 * aviso al mecanico, fechas de inicio/termino, recomendacion y verificacion
 * final, origen/BCI_Id en repuestos).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_garantia_hero', function (Blueprint $table) {
            $table->id('MGH_Id');
            $table->string('MGH_Placa', 18);
            $table->string('MGH_Propietario', 50);
            $table->string('MGH_celular', 12)->nullable();
            $table->string('MGH_Unidad', 40);
            $table->string('MGH_KMEntrada', 20)->nullable();
            $table->text('MGH_DetalleIngreso')->nullable();
            $table->text('MGH_DetalleObservacion')->nullable();
            $table->string('MGH_Det1', 2)->nullable();
            $table->string('MGH_Det1Informacion', 50)->nullable();
            $table->string('MGH_Det2', 2)->nullable();
            $table->string('MGH_Det3', 2)->nullable();
            $table->string('MGH_Det4', 2)->nullable();
            $table->string('MGH_Det5', 2)->nullable();
            $table->string('MGH_Det6', 2)->nullable();
            $table->string('MGH_Det7', 2)->nullable();
            $table->string('MGH_Det7Admision', 10)->nullable();
            $table->string('MGH_Det7Escape', 10)->nullable();
            $table->string('MGH_Det8', 2)->nullable();
            $table->string('MGH_Det8Medida', 10)->nullable();
            $table->string('MGH_Det9', 10)->nullable();
            $table->string('MGH_Det10', 10)->nullable();
            $table->string('MGH_Det11', 2)->nullable();
            $table->string('MGH_Det11Vida', 20)->nullable();
            $table->string('MGH_Det11Carga', 20)->nullable();
            $table->string('MGH_Det11Arranque', 20)->nullable();
            $table->text('MGH_DetalleRealizado')->nullable();
            $table->text('MGH_CorrecionObservacion')->nullable();
            $table->string('MGH_ProximoCambioAceite', 50)->nullable();
            $table->string('MGH_ProximoServicio', 50)->nullable();
            $table->dateTime('MGH_FechaCreacion')->nullable();
            $table->dateTime('MGH_FechaInicio')->nullable();
            $table->dateTime('MGH_FechaEdicion')->nullable();
            $table->dateTime('MGH_FechaTermino')->nullable();
            $table->integer('MGH_UsuarioCreacion')->nullable();
            $table->integer('MGH_UsuarioEditado')->nullable();
            $table->string('MGH_Estado', 20)->default('PENDIENTE');
            $table->integer('PER_Id')->nullable();
            $table->boolean('statevalidate')->default(false);
            $table->boolean('notificar')->default(false);
            $table->text('observacion')->nullable();
            $table->text('respuesta')->nullable();
            $table->unsignedBigInteger('PLAN_Id')->nullable();
            $table->unsignedBigInteger('RES_Id')->nullable();
            $table->boolean('MGH_AvisoMecanico')->default(false);
            $table->text('MGH_Recomendacion')->nullable();
            $table->enum('MGH_RecomendacionPrioridad', ['ALTA', 'MEDIA', 'BAJA'])->nullable();
            $table->boolean('MGH_VerifArranque')->nullable();
            $table->boolean('MGH_VerifLuces')->nullable();
            $table->boolean('MGH_VerifDireccionales')->nullable();
            $table->boolean('MGH_VerifNivelAceite')->nullable();
            $table->boolean('MGH_VerifPruebaRuta')->nullable();
            $table->boolean('MGH_VerifLavado')->nullable();
            $table->string('MGH_VerifOtros', 150)->nullable();
            $table->boolean('MGH_VerifConforme')->nullable();

            $table->foreign('RES_Id')->references('RES_Id')->on('reservacion')->nullOnDelete();
        });

        Schema::create('mgh_detalle_reemplazo', function (Blueprint $table) {
            $table->unsignedBigInteger('MGH_Id');
            $table->unsignedBigInteger('MGHD_Item');
            $table->string('MGHD_Descripcion', 100);
            $table->decimal('MGH_Precio', 10, 2)->nullable();
            $table->string('origen', 10)->default('MANUAL');
            $table->unsignedBigInteger('BCI_Id')->nullable();

            $table->primary(['MGH_Id', 'MGHD_Item']);

            $table->foreign('MGH_Id', 'RMGHD_1')
                ->references('MGH_Id')
                ->on('mantenimiento_garantia_hero')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('BCI_Id')->references('BCI_Id')->on('bahia_cuenta_item')->cascadeOnDelete();
        });

        Schema::create('mgh_imagen', function (Blueprint $table) {
            $table->unsignedBigInteger('MGH_Id');
            $table->unsignedBigInteger('MGHI_Item');
            $table->text('MGHI_url')->nullable();
            $table->text('MGHI_Thumb')->nullable();
            $table->string('MGHI_Nombre', 100)->nullable();
            $table->string('MGHI_Peso', 20)->nullable();

            $table->primary(['MGH_Id', 'MGHI_Item']);

            $table->foreign('MGH_Id', 'RMGHI_1')
                ->references('MGH_Id')
                ->on('mantenimiento_garantia_hero')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mgh_imagen');
        Schema::dropIfExists('mgh_detalle_reemplazo');
        Schema::dropIfExists('mantenimiento_garantia_hero');
    }
};
