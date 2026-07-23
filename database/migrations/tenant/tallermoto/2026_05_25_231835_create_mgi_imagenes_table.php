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
        Schema::create('mgi_imagen', function (Blueprint $table) {
            $table->unsignedBigInteger('MGI_Id');
            $table->unsignedBigInteger('MGII_Item');
            $table->text('MGII_url')->nullable();
            $table->text('MGII_Thumb')->nullable();
            $table->string('MGII_Nombre', 100)->nullable();
            $table->string('MGII_Peso', 20)->nullable();
            $table->primary(['MGI_Id', 'MGII_Item']);
            $table->foreign('MGI_Id', 'RMGII_1')
                ->references('MGI_Id')
                ->on('mantenimiento_general_inyectada')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mgi_imagenes');
    }
};
