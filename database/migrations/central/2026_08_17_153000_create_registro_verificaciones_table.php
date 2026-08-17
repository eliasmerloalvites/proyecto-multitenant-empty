<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroVerificacionesTable extends Migration
{
    /**
     * Registros de autoregistro pendientes de confirmar por correo. El
     * tenant (y su BD) recién se crea cuando se visita el link con el
     * token válido — así una avalancha de POSTs de un bot solo genera
     * filas baratas acá, nunca bases de datos reales.
     */
    public function up(): void
    {
        Schema::create('registro_verificaciones', function (Blueprint $table) {
            $table->id();

            $table->string('token', 64)->unique();

            $table->string('razon_social');
            $table->string('ruc', 11);
            $table->string('email');
            $table->string('password'); // ya hasheado antes de guardarse
            $table->string('subdomain');
            $table->string('plan', 20);

            $table->timestamp('expira_en');
            $table->timestamp('verificado_en')->nullable();

            $table->timestamps();

            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registro_verificaciones');
    }
}
