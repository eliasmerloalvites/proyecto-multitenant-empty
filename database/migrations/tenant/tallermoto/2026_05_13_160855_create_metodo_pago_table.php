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
        Schema::create('metodo_pago', function (Blueprint $table) {
            /* PRIMARY KEY */
            $table->increments('MEP_Id');
            /* CAMPOS */
            $table->string('MEP_Pago', 50);
            $table->boolean('MEP_Status')->default(1);
            /* TIMESTAMPS */
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodo_pago');
    }
};
