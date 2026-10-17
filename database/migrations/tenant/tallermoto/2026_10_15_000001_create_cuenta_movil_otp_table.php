<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Codigos de verificacion (OTP) de la app movil. Solo se guarda el HASH del
 * codigo, con vencimiento, contador de intentos y marca de consumido.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuenta_movil_otp', function (Blueprint $table) {
            $table->id('CMO_Id');
            $table->string('CMO_Celular', 15)->index();
            $table->string('CMO_CodigoHash', 64);
            $table->unsignedTinyInteger('CMO_Intentos')->default(0);
            $table->dateTime('CMO_ExpiraEn');
            $table->dateTime('CMO_ConsumidoEn')->nullable();
            $table->string('CMO_Ip', 45)->nullable();
            $table->string('CMO_DispositivoId', 100)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuenta_movil_otp');
    }
};
