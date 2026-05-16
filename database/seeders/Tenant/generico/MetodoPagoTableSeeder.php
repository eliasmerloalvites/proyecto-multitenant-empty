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
            ]
        ]);
    }
}


