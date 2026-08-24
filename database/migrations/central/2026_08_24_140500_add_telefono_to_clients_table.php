<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Culqi exige phone_number en client_details al crear una orden de
     * pago (ver CulqiOrderService) — no había ningún campo de contacto
     * telefónico en clients hasta ahora.
     */
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->string('telefono', 20)->nullable()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('telefono');
        });
    }
};
