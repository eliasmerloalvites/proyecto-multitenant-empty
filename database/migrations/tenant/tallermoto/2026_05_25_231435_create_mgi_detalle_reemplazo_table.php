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
        Schema::create('mgi_detalle_reemplazo', function (Blueprint $table) {
            $table->unsignedBigInteger('MGI_Id');
            $table->unsignedBigInteger('MGID_Item');
            $table->string('MGID_Descripcion', 100);
            $table->decimal('MGI_Precio', 10, 2)->nullable();
            $table->primary(['MGI_Id', 'MGID_Item']);
            $table->foreign('MGI_Id', 'RMGID_1')
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
        Schema::dropIfExists('mgi_detalle_reemplazo');
    }
};
