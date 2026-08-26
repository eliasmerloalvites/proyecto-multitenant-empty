<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * La guia de remision electronica usa una plataforma SUNAT distinta a
     * la de factura/boleta/notas: en vez del usuario/clave SOL clasico,
     * pide un client_id/client_secret OAuth2 generado aparte en el menu
     * SOL (Manual de Servicios GRE). No reemplazan a sol_usuario/sol_password
     * (esas tambien se siguen usando para el login), son credenciales
     * adicionales solo para este documento.
     */
    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->string('gre_client_id', 100)->nullable()->after('sol_password');
            $table->text('gre_client_secret')->nullable()->after('gre_client_id');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->dropColumn(['gre_client_id', 'gre_client_secret']);
        });
    }
};
