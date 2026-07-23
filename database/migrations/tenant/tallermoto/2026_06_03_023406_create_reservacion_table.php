<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('reservacion', function (Blueprint $table) {

            $table->bigIncrements('RES_Id');

            $table->unsignedInteger('ALM_Id');
            $table->unsignedBigInteger('TUR_Id');
            $table->unsignedBigInteger('BAH_Id');

            $table->string('RES_Moto', 150);
            $table->string('RES_Cliente', 120);
            $table->string('RES_Celular', 12);

            $table->string('RES_Detalle', 250)->nullable();
            $table->string('RES_Adicional', 250)->nullable();

            $table->string('RES_FechaProgramada', 120);

            $table->string('RES_State', 10)
                  ->default('PENDIENTE');

            $table->string('RES_Estado', 10)
                  ->default('ACT');

            $table->foreign('ALM_Id', 'RRES1')
                ->references('ALM_Id')
                ->on('almacen')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('reservacion', function (Blueprint $table) {
            $table->dropForeign('RRES1');
        });

        Schema::dropIfExists('reservacion');
    }
};
