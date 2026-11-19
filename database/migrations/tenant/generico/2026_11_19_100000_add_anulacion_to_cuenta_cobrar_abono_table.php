<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Permite anular un abono ya registrado (y con ello el pago de la cuota que
 * cubrio): el abono no se borra, queda marcado como anulado con quien, cuando
 * y por que, y deja de contar en el saldo, en las cuotas y en los reportes.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cuenta_cobrar_abono', function (Blueprint $table) {
            $table->boolean('CCA_Anulado')->default(false)->after('CCA_Descripcion');
            $table->dateTime('CCA_FechaAnulacion')->nullable()->after('CCA_Anulado');
            $table->unsignedBigInteger('CCA_UsuarioAnulacion')->nullable()->after('CCA_FechaAnulacion');
            $table->string('CCA_MotivoAnulacion', 150)->nullable()->after('CCA_UsuarioAnulacion');
        });
    }

    public function down(): void
    {
        Schema::table('cuenta_cobrar_abono', function (Blueprint $table) {
            $table->dropColumn(['CCA_Anulado', 'CCA_FechaAnulacion', 'CCA_UsuarioAnulacion', 'CCA_MotivoAnulacion']);
        });
    }
};
