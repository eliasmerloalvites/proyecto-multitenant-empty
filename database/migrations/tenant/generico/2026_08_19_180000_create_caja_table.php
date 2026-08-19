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
        Schema::create('caja', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('CAJ_Id');
            /* CAMPOS */
            $table->unsignedInteger('ALM_Id')->nullable();
            $table->string('CAJ_Nombre', 100);
            $table->decimal('CAJ_MontoApertura', 10, 2)->default(0);
            $table->boolean('CAJ_Status')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();

            $table->foreign('ALM_Id')->references('ALM_Id')->on('almacen')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('caja');
    }
};
