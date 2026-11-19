<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite anular un abono ya registrado: el abono no se borra, queda
 * marcado como anulado con quien, cuando y por que, y deja de contar en el
 * saldo de la cuenta, en el arqueo de caja y en los reportes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_por_cobrar_abono', function (Blueprint $table) {
            $table->boolean('CPA_Anulado')->default(false)->after('CPA_Observacion');
            $table->dateTime('CPA_FechaAnulacion')->nullable()->after('CPA_Anulado');
            $table->unsignedBigInteger('CPA_UsuarioAnulacion')->nullable()->after('CPA_FechaAnulacion');
            $table->string('CPA_MotivoAnulacion', 150)->nullable()->after('CPA_UsuarioAnulacion');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_por_cobrar_abono', function (Blueprint $table) {
            $table->dropColumn(['CPA_Anulado', 'CPA_FechaAnulacion', 'CPA_UsuarioAnulacion', 'CPA_MotivoAnulacion']);
        });
    }
};
