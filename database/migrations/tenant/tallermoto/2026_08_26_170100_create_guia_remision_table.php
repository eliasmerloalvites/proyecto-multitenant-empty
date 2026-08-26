<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Guia de remision electronica (traslado de productos vendidos). Vive
     * en su propia tabla -no en documento_venta- porque no tiene montos
     * ni IGV: lo que describe es el traslado (motivo, peso, origen/destino,
     * transporte), no un comprobante de pago.
     */
    public function up(): void
    {
        Schema::create('guia_remision', function (Blueprint $table) {
            $table->increments('GRM_Id');

            $table->string('GRM_Serie', 13);
            $table->integer('GRM_Numero', false, true);

            // Venta cuyos productos se estan trasladando.
            $table->unsignedInteger('VEN_Id');
            $table->foreign('VEN_Id', 'GRM_KFR1')->references('VEN_Id')->on('venta')->onDelete('cascade');

            // Traslado.
            $table->string('GRM_MotivoTraslado', 2)->default('01');
            $table->string('GRM_DesMotivo', 100)->nullable();
            $table->dateTime('GRM_FechaTraslado');
            $table->decimal('GRM_PesoTotal', 10, 3);
            $table->string('GRM_UndPeso', 3)->default('KGM');

            // Origen y destino.
            $table->string('GRM_UbigeoPartida', 6);
            $table->string('GRM_DireccionPartida', 200);
            $table->string('GRM_UbigeoLlegada', 6);
            $table->string('GRM_DireccionLlegada', 200);

            // Transporte: '01' publico (transportista) o '02' privado (vehiculo propio).
            $table->string('GRM_ModoTransporte', 2);

            $table->string('GRM_TransportistaTipoDoc', 1)->nullable();
            $table->string('GRM_TransportistaNumero', 15)->nullable();
            $table->string('GRM_TransportistaRazonSocial', 150)->nullable();

            $table->string('GRM_VehiculoPlaca', 10)->nullable();

            $table->string('GRM_ConductorTipoDoc', 1)->nullable();
            $table->string('GRM_ConductorNumero', 15)->nullable();
            $table->string('GRM_ConductorNombres', 100)->nullable();
            $table->string('GRM_ConductorApellidos', 100)->nullable();
            $table->string('GRM_ConductorLicencia', 20)->nullable();

            // Estado y resultado del envio a SUNAT (mismo espiritu que documento_venta).
            $table->string('GRM_Estado', 20)->default('PENDIENTE');
            $table->string('GRM_EstadoSunat', 20)->nullable();
            $table->string('GRM_Ticket', 50)->nullable();
            $table->string('GRM_CodigoSunat', 20)->nullable();
            $table->text('GRM_DescripcionSunat')->nullable();
            $table->longText('GRM_ResponseSunat')->nullable();
            $table->boolean('GRM_Anulado')->default(false);
            $table->tinyInteger('GRM_IntentosSunat')->default(0);
            $table->dateTime('GRM_FechaEnvioSunat')->nullable();
            $table->dateTime('GRM_FechaRespuestaSunat')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guia_remision');
    }
};
