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
        Schema::create('personal', function (Blueprint $table) {
            $table->id('PER_Id');
            $table->string('PER_Nombre', 50);
            $table->string('PER_Apellido', 50);
            $table->string('PER_TipoDocumento', 20);
            $table->string('PER_NumeroDocumento', 11);
            $table->date('PER_FechaNacimiento')->nullable();
            $table->integer('PER_Edad');
            $table->string('PER_Sexo', 20);
            $table->string('PER_EstadoCivil', 20)->nullable();
            $table->integer('PER_NumeroHijos')->default(0);
            $table->string('PER_Procedencia', 150)->nullable();
            $table->string('PER_Direccion', 250)->nullable();
            $table->string('PER_Referencia', 255)->nullable();
            $table->string('PER_Correo', 100)->nullable();
            $table->char('PER_Celular', 12)->nullable();
            $table->string('PER_Parenteso', 20)->nullable();
            $table->string('PER_PNombre', 50)->nullable();
            $table->char('PER_PCelular', 12)->nullable();
            $table->string('PER_PDireccion', 80)->nullable();
            $table->string('PER_Parenteso2', 40)->nullable();
            $table->string('PER_PNombre2', 150)->nullable();
            $table->string('PER_PCelular2', 20)->nullable();
            $table->string('PER_PDireccion2', 150)->nullable();
            $table->integer('PUE_Id')->nullable();
            $table->string('PER_Carrera', 40)->nullable();
            $table->string('PER_GradoAcademico', 40)->nullable();
            $table->string('PER_EstadoLaboral', 40)->default('ACTIVO');
            $table->integer('ARE_Id')->nullable();
            $table->string('PER_TPolo', 6)->nullable();
            $table->string('PER_TPantalon', 6)->nullable();
            $table->string('PER_TZapatos', 6)->nullable();
            $table->string('PER_Titular', 60)->nullable();
            $table->string('PER_Banco', 30)->nullable();
            $table->string('PER_NumeroCuenta', 20)->nullable();
            $table->string('PER_CCI', 22)->nullable();
            $table->char('PER_Documento', 10)->nullable();
            $table->string('PER_Foto', 10)->nullable();
            $table->char('PER_CV', 20)->nullable();
            $table->char('PER_ListaNegra', 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal');
    }
};
