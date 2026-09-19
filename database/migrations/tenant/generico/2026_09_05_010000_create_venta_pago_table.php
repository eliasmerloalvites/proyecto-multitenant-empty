<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Detalle de pago por venta: exclusivo de 'generico' para poder dividir
     * el cobro de una venta entre varios metodos (parte en Efectivo, parte
     * en Yape, etc.) sin cambiar el formato de venta.MEP_Id/VEN_Pagado que
     * ya usan VentaController, HomeController y CajaSesionController
     * (compartidos con tallermoto y NO tocados por esta funcionalidad).
     *
     * Invariante que debe respetar quien inserte aqui: la suma de
     * VPG_Monto de una misma VEN_Id debe ser exactamente igual al total de
     * esa venta (nunca el efectivo bruto entregado, ya con el vuelto
     * descontado de la linea en efectivo) - de eso depende que el arqueo de
     * caja de generico cuadre.
     */
    public function up(): void
    {
        Schema::create('venta_pago', function (Blueprint $table) {
            $table->increments('VPG_Id');

            $table->unsignedInteger('VEN_Id');
            $table->unsignedInteger('MEP_Id');
            $table->decimal('VPG_Monto', 10, 2);

            $table->timestamps();

            $table->index('VEN_Id', 'VPG_KFR1');
            $table->index('MEP_Id', 'VPG_KFR2');

            $table->foreign('VEN_Id', 'VPG_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'VPG_KFR2')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_pago');
    }
};
