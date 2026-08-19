<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTrialEndsAtToClientsTable extends Migration
{
    /**
     * Días de prueba gratis antes del primer cobro: mientras hoy sea antes
     * de trial_ends_at, el cliente no entra al cálculo normal de ciclo de
     * facturación (Client::estadoCicloActual) y por lo tanto no puede
     * marcarse como vencido ni suspenderse por mora. Null = sin trial
     * (clientes creados antes de este cambio, o cargados manualmente por
     * staff con cobro inmediato).
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->date('trial_ends_at')->nullable()->after('billing_day');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('trial_ends_at');
        });
    }
}
