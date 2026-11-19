<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite anular un pago a proveedor ya registrado (y con ello el pago de la
 * cuota que cubrio): el abono no se borra, queda marcado como anulado con
 * quien, cuando y por que, y deja de contar en el saldo, en las cuotas y en
 * los reportes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_pagar_abono', function (Blueprint $table) {
            $table->boolean('CXPA_Anulado')->default(false)->after('CXPA_Descripcion');
            $table->dateTime('CXPA_FechaAnulacion')->nullable()->after('CXPA_Anulado');
            $table->unsignedBigInteger('CXPA_UsuarioAnulacion')->nullable()->after('CXPA_FechaAnulacion');
            $table->string('CXPA_MotivoAnulacion', 150)->nullable()->after('CXPA_UsuarioAnulacion');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_pagar_abono', function (Blueprint $table) {
            $table->dropColumn(['CXPA_Anulado', 'CXPA_FechaAnulacion', 'CXPA_UsuarioAnulacion', 'CXPA_MotivoAnulacion']);
        });
    }
};
