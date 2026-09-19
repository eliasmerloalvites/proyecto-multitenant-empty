<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * "Mixto" es el metodo_pago que se guarda en venta.MEP_Id cuando una
     * venta se paga con 2+ metodos a la vez (el detalle real de cuanto se
     * pago con cada uno vive en venta_pago). Se agrega via migracion (no
     * solo en el seeder) para que tenants generico ya existentes, como
     * cocacola, tambien lo tengan sin recrear el tenant.
     */
    public function up(): void
    {
        // Tabla vacia = tenant nuevo recien provisionandose: MetodoPagoTableSeeder
        // (que corre despues de las migraciones) ya va a insertar "Mixto" con
        // MEP_Id=6 explicito. Insertarlo aqui tambien le robaria el
        // auto_increment (MEP_Id=1) y el seeder chocaria al insertar su propio
        // MEP_Id=1 ("Efectivo") por duplicado de llave primaria.
        if (DB::table('metodo_pago')->count() === 0) {
            return;
        }

        $existe = DB::table('metodo_pago')->where('MEP_Pago', 'Mixto')->exists();

        if (! $existe) {
            DB::table('metodo_pago')->insert([
                'MEP_Pago'   => 'Mixto',
                'MEP_Status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('metodo_pago')->where('MEP_Pago', 'Mixto')->delete();
    }
};
