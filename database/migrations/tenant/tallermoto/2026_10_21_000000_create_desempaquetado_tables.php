<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Desempaquetado de kits: un producto "kit" (ej. Kit de herramientas) se
 * puede vender entero o abrirse para vender sus piezas por separado.
 *
 * - producto_kit_componente: la receta del kit (que trae y cuanto de cada
 *   cosa). Es solo la base: al desempaquetar se puede corregir.
 * - desempaquetado: cada vez que se abren N kits en un almacen. El kit
 *   sale del stock por FIFO (como un ajuste) y queda trazado aqui.
 * - desempaquetado_detalle: lo que entro por cada componente, con el lote
 *   nuevo que se le creo (LOT_TipoIngreso = DESEMPAQUETADO).
 *
 * Anular solo se permite si esos lotes siguen intactos: se vacian y el kit
 * vuelve con un lote nuevo (LOT_TipoIngreso = DESEMPAQ_ANULADO).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('producto_kit_componente', function (Blueprint $table) {
            $table->increments('PKC_Id');
            $table->unsignedInteger('PRO_IdKit');
            $table->unsignedInteger('PRO_IdComponente');
            $table->decimal('PKC_Cantidad', 10, 2);
            $table->timestamps();

            $table->unique(['PRO_IdKit', 'PRO_IdComponente'], 'PKC_UNQ1');
            $table->foreign('PRO_IdKit', 'PKC_KFR1')->references('PRO_Id')->on('producto')->onDelete('cascade');
            $table->foreign('PRO_IdComponente', 'PKC_KFR2')->references('PRO_Id')->on('producto')->onDelete('cascade');
        });

        Schema::create('desempaquetado', function (Blueprint $table) {
            $table->increments('DES_Id');
            $table->unsignedInteger('ALM_Id');
            $table->unsignedInteger('PRO_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->decimal('DES_Cantidad', 10, 2);
            $table->text('DES_Observacion')->nullable();
            $table->string('DES_Estado', 10)->default('ACTIVO');
            $table->unsignedInteger('LOT_IdDevolucion')->nullable();
            $table->unsignedBigInteger('USU_Id_Anula')->nullable();
            $table->string('DES_MotivoAnulacion', 255)->nullable();
            $table->timestamp('DES_AnuladoEn')->nullable();
            $table->timestamps();

            $table->index('PRO_Id', 'DES_KFR2');
            $table->foreign('ALM_Id', 'DES_KFR1')->references('ALM_Id')->on('almacen');
            $table->foreign('PRO_Id', 'DES_KFR2')->references('PRO_Id')->on('producto');
            $table->foreign('USU_Id', 'DES_KFR3')->references('id')->on('users');
        });

        Schema::create('desempaquetado_detalle', function (Blueprint $table) {
            $table->increments('DSD_Id');
            $table->unsignedInteger('DES_Id');
            $table->unsignedInteger('PRO_Id');
            $table->unsignedInteger('LOT_Id')->nullable();
            $table->decimal('DSD_CantidadPorKit', 10, 2);
            $table->decimal('DSD_Cantidad', 10, 2);
            $table->decimal('DSD_PrecioCompra', 10, 2)->default(0);
            $table->timestamps();

            $table->index('PRO_Id', 'DSD_KFR2');
            $table->foreign('DES_Id', 'DSD_KFR1')->references('DES_Id')->on('desempaquetado')->onDelete('cascade');
            $table->foreign('PRO_Id', 'DSD_KFR2')->references('PRO_Id')->on('producto');
            $table->foreign('LOT_Id', 'DSD_KFR3')->references('LOT_Id')->on('lote')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('desempaquetado_detalle');
        Schema::dropIfExists('desempaquetado');
        Schema::dropIfExists('producto_kit_componente');
    }
};
