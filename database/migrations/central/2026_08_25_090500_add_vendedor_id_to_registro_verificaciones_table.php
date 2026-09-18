<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVendedorIdToRegistroVerificacionesTable extends Migration
{
    /**
     * El registro público captura ?ref=CODIGO (ver RegistroController) y lo
     * guarda aquí, porque el tenant recién se crea en verificar() (otra
     * request, solo con el token) — así el vendedor referente sobrevive el
     * viaje por correo hasta que se confirma el email.
     */
    public function up(): void
    {
        Schema::table('registro_verificaciones', function (Blueprint $table) {
            $table->foreignId('vendedor_id')->nullable()->after('plan')
                ->constrained('vendedores')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('registro_verificaciones', function (Blueprint $table) {
            $table->dropConstrainedForeignId('vendedor_id');
        });
    }
}
