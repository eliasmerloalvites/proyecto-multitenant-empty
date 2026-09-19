<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cotizaciones: exclusivo de generico (CotizacionController). Es como una
 * venta (mismo cliente, mismo catalogo de productos, mismo almacen) pero NO
 * mueve stock -- es solo una propuesta/presupuesto que el cliente puede
 * aceptar despues. Por eso no tiene MEP_Id/caja/lote: nada de eso aplica
 * hasta que la cotizacion se convierta en una venta real (fuera de este
 * alcance por ahora).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cotizacion', function (Blueprint $table) {
            $table->increments('COT_Id');
            $table->unsignedInteger('CLI_Id')->nullable();
            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->decimal('COT_Total', 10, 2)->default(0);
            $table->date('COT_FechaVencimiento')->nullable();
            $table->string('COT_Observaciones', 500)->nullable();
            /* 1 Pendiente, 2 Aprobada, 3 Rechazada, 0 Anulada */
            $table->tinyInteger('COT_Estado')->default(1);
            $table->timestamps();

            $table->index('CLI_Id', 'COT_KFR1');
            $table->index('ALM_Id', 'COT_KFR2');

            $table->foreign('CLI_Id', 'COT_KFR1')
                  ->references('CLI_Id')
                  ->on('cliente')
                  ->onDelete('set null');

            $table->foreign('ALM_Id', 'COT_KFR2')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('USU_Id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion');
    }
};
