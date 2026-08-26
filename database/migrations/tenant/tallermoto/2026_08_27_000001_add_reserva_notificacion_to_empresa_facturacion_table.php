<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Configuracion del recordatorio de reservas del dia siguiente: a que
     * hora se activa el aviso en el panel y el mensaje predeterminado que
     * se usa para armar el link de WhatsApp (el envio sigue siendo manual,
     * un clic por cliente, tal como el resto del sistema).
     */
    public function up(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->boolean('reserva_notif_activo')->default(false)->after('whatsapp');
            $table->time('reserva_notif_hora')->nullable()->after('reserva_notif_activo');
            $table->text('reserva_notif_mensaje')->nullable()->after('reserva_notif_hora');
        });
    }

    public function down(): void
    {
        Schema::table('empresa_facturacion', function (Blueprint $table) {
            $table->dropColumn(['reserva_notif_activo', 'reserva_notif_hora', 'reserva_notif_mensaje']);
        });
    }
};
