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
        Schema::create('bahia', function (Blueprint $table) {
            $table->id('BAH_Id');
            $table->unsignedBigInteger('USU_Id');
            $table->unsignedInteger('ALM_Id');
            $table->string('BAH_Nombre', 120);
            $table->string('BAH_Estado', 10)->default('ACT');
            $table->timestamps();
            // FOREIGN KEYS
            $table->foreign('ALM_Id', 'RVAH1')->references('ALM_Id')->on('almacen')->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('USU_Id', 'RVAH2')->references('id')->on('users')->restrictOnDelete()->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bahia');
    }
};
