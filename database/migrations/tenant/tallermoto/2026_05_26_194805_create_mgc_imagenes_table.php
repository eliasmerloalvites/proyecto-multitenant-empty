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
        Schema::create('mgc_imagen', function (Blueprint $table) {

            $table->unsignedBigInteger('MGC_Id');
            $table->unsignedBigInteger('MGCI_Item');
            $table->text('MGCI_url')->nullable();
            $table->text('MGCI_Thumb')->nullable();
            $table->string('MGCI_Nombre', 100)->nullable();
            $table->string('MGCI_Peso', 20)->nullable();
            $table->primary(['MGC_Id', 'MGCI_Item']);

            $table->foreign('MGC_Id', 'RMGCI_1')
                ->references('MGC_Id')
                ->on('mantenimiento_general_carburada')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mgc_imagenes');
    }
};
