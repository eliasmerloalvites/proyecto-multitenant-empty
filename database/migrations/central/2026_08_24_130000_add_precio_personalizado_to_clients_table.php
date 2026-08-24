<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Precio negociado para un cliente puntual (ej. le subimos límites sin
     * cambiarlo de plan). NULL = usa el precio estándar del plan, igual que
     * hasta ahora — ver Client::montoEsperado().
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->decimal('precio_personalizado', 8, 2)->nullable()->after('billing_day');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('precio_personalizado');
        });
    }
};
