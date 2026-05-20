<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */

    /*
        |       DOV_StateToRes        |

        | Valor | Significado         |
        | ----- | ------------------- |
        | 0     | pendiente evaluar   |
        | 1     | agregado a resumen  |
        | 2     | enviado resumen     |
        | 3     | no requiere resumen |
        | 4     | rechazado resumen   |

    */
    public function up(): void
    {
        Schema::create('documento_venta', function (Blueprint $table) {
             /* PRIMARY KEY */
            $table->increments('DOV_Id');
            /* CAMPOS */
            $table->string('DOV_Tipo', 3);
            $table->string('DOV_TipoOriginal', 3)->nullable();
            $table->string('DOV_Serie', 13)->nullable();
            $table->integer('DOV_Numero', false, true);
            /* FOREIGN KEY */
            $table->unsignedInteger('VEN_Id');
            /* ESTADO */
            $table->string('DOV_Nombre', 100)->nullable();
            $table->string('DOV_Estado', 20)->default('PENDIENTE');
            $table->string('DOV_EstadoSunat', 20)->nullable();
            $table->string('DOV_CodigoSunat', 20)->nullable();
            $table->text('DOV_DescripcionSunat')->nullable();
            $table->longText('DOV_ResponseSunat')->nullable();
            $table->string('DOV_Hash', 255)->nullable();
            $table->string('DOV_Pdf', 255)->nullable();
            $table->boolean('DOV_Anulado')->default(0);
            $table->tinyInteger('DOV_IntentosSunat')->default(0);
            $table->tinyInteger('DOV_Vista')->nullable();
            $table->dateTime('DOV_FechaEnvioSunat')->nullable();
            $table->dateTime('DOV_FechaRespuestaSunat')->nullable();
            /* ESTADO */
            $table->integer('DOV_IdRes')->nullable();
            $table->tinyInteger('DOV_StateToRes')->default(0);
            /* INDEX */
            $table->index('VEN_Id', 'R_DOV_KFR1');
            /* FOREIGN KEY */
            $table->foreign('VEN_Id', 'R_DOV_KFR1')
                  ->references('VEN_Id')
                  ->on('venta')
                  ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_venta');
    }
};
