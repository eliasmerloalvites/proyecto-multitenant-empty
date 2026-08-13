<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEmailToClientsTable extends Migration
{
    public function up(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Correo de contacto para facturación (recordatorios/avisos de
            // cobro). Distinto del correo del admin del tenant: este vive en
            // la BD central y es a quien se le cobra, no a quien opera el panel.
            $table->string('email')->nullable()->after('ruc');
        });
    }

    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn('email');
        });
    }
}
