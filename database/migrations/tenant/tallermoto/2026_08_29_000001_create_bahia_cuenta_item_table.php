<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Linea de producto/servicio cargada a una bahia_cuenta mientras esta
 * abierta. Es solo un borrador: no descuenta stock ni genera detalle_venta
 * hasta que la cuenta se cobra (ahi se convierten en DetalleVenta reales).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bahia_cuenta_item', function (Blueprint $table) {
            $table->id('BCI_Id');
            $table->unsignedBigInteger('BCT_Id');
            $table->unsignedInteger('PRO_Id');
            $table->decimal('BCI_Cantidad', 10, 2);
            $table->decimal('BCI_PrecioUnitario', 10, 2);
            $table->unsignedBigInteger('USU_Id_Agrega');
            $table->timestamps();

            $table->foreign('BCT_Id')->references('BCT_Id')->on('bahia_cuenta')->onDelete('cascade');
            $table->foreign('PRO_Id')->references('PRO_Id')->on('producto')->onDelete('cascade');
            $table->foreign('USU_Id_Agrega')->references('id')->on('users')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bahia_cuenta_item');
    }
};
