<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalle_cotizacion', function (Blueprint $table) {
            $table->unsignedInteger('COT_Id');
            $table->unsignedInteger('PRO_Id');
            $table->integer('DCOT_Item');
            $table->double('DCOT_Cantidad');
            $table->decimal('DCOT_PrecioUnitario', 10, 2);
            $table->decimal('DCOT_Descuento', 10, 2)->default(0);
            $table->timestamps();

            $table->primary(['COT_Id', 'DCOT_Item']);
            $table->index('COT_Id', 'DCOT_KFR1');
            $table->index('PRO_Id', 'DCOT_KFR2');

            $table->foreign('COT_Id', 'DCOT_KFR1')
                  ->references('COT_Id')
                  ->on('cotizacion')
                  ->onDelete('cascade');

            $table->foreign('PRO_Id', 'DCOT_KFR2')
                  ->references('PRO_Id')
                  ->on('producto')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalle_cotizacion');
    }
};
