<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Agrega el metodo de pago sintetico "Credito" (mismo patron que
 * seed_metodo_pago_mixto.php), usado como venta.MEP_Id cuando una venta
 * al credito no tiene ningun adelanto (0 metodos reales usados). Solo
 * afecta tenants de 'generico' ya existentes; los nuevos lo reciben via
 * MetodoPagoTableSeeder.
 */
return new class extends Migration
{
    public function up(): void
    {
        // Tabla vacia = tenant nuevo recien provisionandose: MetodoPagoTableSeeder
        // (que corre despues de las migraciones) ya va a insertar "Crédito" con
        // MEP_Id=7 explicito. Insertarlo aqui tambien le robaria un auto_increment
        // bajo y el seeder chocaria por duplicado de llave primaria.
        if (DB::table('metodo_pago')->count() === 0) {
            return;
        }

        if (!DB::table('metodo_pago')->where('MEP_Pago', 'Crédito')->exists()) {
            DB::table('metodo_pago')->insert([
                'MEP_Pago' => 'Crédito',
                'MEP_Status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('metodo_pago')->where('MEP_Pago', 'Crédito')->delete();
    }
};
