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
        Schema::create('gasto', function (Blueprint $table) {
             /* PRIMARY KEY */
            $table->increments('GAS_Id');
            /* FOREIGN KEYS */
            $table->unsignedInteger('PROV_Id');
            $table->unsignedInteger('MEP_Id');
            $table->unsignedInteger('TG_Id');
            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('USU_Id');
            /* CAMPOS */
            $table->string('GAS_Descripcion', 100)->nullable();
            $table->double('GAS_Monto');
            $table->dateTime('GAS_Fecha')->nullable();
            $table->string('TG_Comprobante', 10);
            $table->string('TG_ComprobanteNum', 20);
            $table->boolean('GAS_Status')->default(1);
            $table->string('GAS_Observacion', 100)->nullable();
            $table->string('GAS_Afecta', 4)->default('SI');
            $table->string('GAS_Documento', 500)->nullable();
            /* INDEX */
            $table->index('PROV_Id', 'GAS_KFR1');
            $table->index('MEP_Id', 'GAS_KFR2');
            $table->index('TG_Id', 'GAS_KFR3');
            $table->index('ALM_Id', 'GAS_KFR4');
            $table->index('USU_Id', 'GAS_KFR5');
            /* FOREIGN KEY CONSTRAINTS */
            $table->foreign('PROV_Id', 'GAS_KFR1')
                  ->references('PROV_Id')
                  ->on('proveedor')
                  ->onDelete('cascade');

            $table->foreign('MEP_Id', 'GAS_KFR2')
                  ->references('MEP_Id')
                  ->on('metodo_pago')
                  ->onDelete('cascade');

            $table->foreign('TG_Id', 'GAS_KFR3')
                  ->references('TG_Id')
                  ->on('tipo_gasto')
                  ->onDelete('cascade');

            $table->foreign('ALM_Id', 'GAS_KFR4')
                  ->references('ALM_Id')
                  ->on('almacen')
                  ->onDelete('cascade');

            $table->foreign('USU_Id', 'GAS_KFR5')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gasto');
    }
};
