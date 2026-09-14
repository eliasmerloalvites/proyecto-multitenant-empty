<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Items dentro de una recepcion_categoria. RIT_TipoCampo decide como se
 * pinta y que valores acepta (ver EstadoRecepcionController/el partial
 * estado-recepcion.blade.php, que arman el formulario a partir de esta
 * tabla, sin nada hardcodeado en el frontend):
 *   BOOLEAN    -> "SI"/"NO"
 *   STATUS     -> "BUENO"/"REGULAR"/"MALO"
 *   INSPECTION -> "OK"/"REEMPLAZAR"
 *   SELECT     -> una de RIT_Opciones (json)
 *   TEXT       -> texto libre
 * Nunca se borran (solo RIT_Activo = 0): recepcion_respuesta guarda su FK
 * a RIT_Id, asi que una recepcion vieja sigue mostrando la respuesta de un
 * item ya desactivado aunque los formularios nuevos ya no lo muestren.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recepcion_item', function (Blueprint $table) {
            $table->id('RIT_Id');
            $table->unsignedBigInteger('RCT_Id');
            $table->string('RIT_Codigo', 60)->unique();
            $table->string('RIT_Etiqueta', 150);
            $table->string('RIT_TipoCampo', 20); // BOOLEAN | STATUS | INSPECTION | SELECT | TEXT
            $table->json('RIT_Opciones')->nullable();
            $table->unsignedInteger('RIT_Orden')->default(0);
            $table->boolean('RIT_Activo')->default(1);
            $table->timestamps();

            $table->foreign('RCT_Id', 'RIT_KFR1')->references('RCT_Id')->on('recepcion_categoria')->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recepcion_item');
    }
};
