<?php

namespace Database\Seeders\Tenant\generico;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodoPagoTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('metodo_pago')->insert([
            [
                'MEP_Id' => 1,
                'MEP_Pago' => 'Efectivo',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'MEP_Id' => 2,
                'MEP_Pago' => 'Tarjeta',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'MEP_Id' => 3,
                'MEP_Pago' => 'Transferencia Bancaria',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'MEP_Id' => 4,
                'MEP_Pago' => 'Yape',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'MEP_Id' => 5,
                'MEP_Pago' => 'Plin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // Se usa cuando una venta se paga con 2+ metodos a la vez
                // (el detalle real por metodo vive en venta_pago).
                'MEP_Id' => 6,
                'MEP_Pago' => 'Mixto',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // Se usa como venta.MEP_Id cuando una venta al credito no
                // tiene ningun adelanto (0 metodos reales usados; el saldo
                // completo queda en cuenta_cobrar).
                'MEP_Id' => 7,
                'MEP_Pago' => 'Crédito',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}


