<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Agrega 'pasarela' al enum metodo_pago para los pagos que crea
     * automáticamente el webhook de Culqi (ver CulqiWebhookController),
     * distinguiéndolos de los que registra un admin a mano.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE pagos MODIFY metodo_pago ENUM('efectivo', 'transferencia', 'yape_plin', 'tarjeta', 'pasarela', 'otro') DEFAULT 'transferencia'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE pagos MODIFY metodo_pago ENUM('efectivo', 'transferencia', 'yape_plin', 'tarjeta', 'otro') DEFAULT 'transferencia'");
    }
};
