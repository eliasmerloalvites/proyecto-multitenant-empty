<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Galería adicional del producto (hasta 4 fotos más, sumadas a
     * PRO_Imagen que sigue siendo la principal — 5 en total). Solo existe
     * en tallermoto: ver ProductoController::tenantTieneGaleriaProducto().
     * Mismo shape que mgc_imagen (tabla hija de imágenes de mantenimiento).
     */
    public function up(): void
    {
        Schema::create('producto_imagen', function (Blueprint $table) {

            $table->unsignedInteger('PRO_Id');
            $table->unsignedTinyInteger('PROI_Item');
            $table->text('PROI_url')->nullable();
            $table->text('PROI_Thumb')->nullable();
            $table->string('PROI_Nombre', 100)->nullable();
            $table->string('PROI_Peso', 20)->nullable();
            $table->primary(['PRO_Id', 'PROI_Item']);

            $table->foreign('PRO_Id', 'RPROI_1')
                ->references('PRO_Id')
                ->on('producto')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('producto_imagen');
    }
};
