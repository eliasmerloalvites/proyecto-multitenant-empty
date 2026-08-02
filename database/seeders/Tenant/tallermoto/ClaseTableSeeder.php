<?php

namespace Database\Seeders\Tenant\tallermoto;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClaseTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('clase')->insert([
            [
                'CLA_Id' => 1,
                'CLA_Nombre' => 'REPUESTOS Y MECÁNICA',
            ],
            [
                'CLA_Id' => 2,
                'CLA_Nombre' => 'SISTEMA ELÉCTRICO E ILUMINACIÓN',
            ],
            [
                'CLA_Id' => 3,
                'CLA_Nombre' => 'FLUIDOS Y QUÍMICOS',
            ],
            [
                'CLA_Id' => 4,
                'CLA_Nombre' => 'CARROCERÍA, PINTURA Y ESTÉTICA',
            ],
            [
                'CLA_Id' => 5,
                'CLA_Nombre' => 'HERRAMIENTAS E INSUMOS DE TALLER',
            ],
            [
                'CLA_Id' => 6,
                'CLA_Nombre' => 'SERVICIOS Y MANO DE OBRA',
            ]
        ]);
    }
}
