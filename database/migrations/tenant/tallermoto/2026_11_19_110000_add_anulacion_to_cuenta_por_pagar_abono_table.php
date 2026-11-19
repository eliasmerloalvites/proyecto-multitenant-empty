<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite anular un pago a proveedor ya registrado: el abono no se borra,
 * queda marcado como anulado con quien, cuando y por que, y deja de contar
 * en el saldo de la cuenta, en el arqueo de caja y en los reportes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_por_pagar_abono', function (Blueprint $table) {
            $table->boolean('CPPA_Anulado')->default(false)->after('CPPA_Observacion');
            $table->dateTime('CPPA_FechaAnulacion')->nullable()->after('CPPA_Anulado');
            $table->unsignedBigInteger('CPPA_UsuarioAnulacion')->nullable()->after('CPPA_FechaAnulacion');
            $table->string('CPPA_MotivoAnulacion', 150)->nullable()->after('CPPA_UsuarioAnulacion');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_por_pagar_abono', function (Blueprint $table) {
            $table->dropColumn(['CPPA_Anulado', 'CPPA_FechaAnulacion', 'CPPA_UsuarioAnulacion', 'CPPA_MotivoAnulacion']);
        });
    }
};
