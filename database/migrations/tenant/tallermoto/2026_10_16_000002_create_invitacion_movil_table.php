<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Codigos de invitacion generados por el personal del taller para entregar al
 * cliente en el mostrador: vinculan una cuenta con una moto con alcance
 * COMPLETO, sin depender de que el celular coincida con los registros. Solo
 * se guarda el HASH del codigo; el codigo se muestra una unica vez, al crearlo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitacion_movil', function (Blueprint $table) {
            $table->id('INV_Id');
            $table->unsignedBigInteger('MOT_Id');
            $table->string('INV_CodigoHash', 64)->unique();
            $table->dateTime('INV_ExpiraEn');
            $table->dateTime('INV_UsadaEn')->nullable();
            $table->unsignedBigInteger('INV_UsadaPor')->nullable();
            $table->unsignedBigInteger('INV_CreadaPor')->nullable();
            $table->dateTime('INV_RevocadaEn')->nullable();
            $table->timestamps();

            $table->foreign('MOT_Id', 'INV_FK_MOTO')->references('MOT_Id')->on('moto')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitacion_movil');
    }
};
