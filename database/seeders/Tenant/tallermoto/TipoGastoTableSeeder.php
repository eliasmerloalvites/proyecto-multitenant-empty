<?php

namespace Database\Seeders\Tenant\tallermoto;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoGastoTableSeeder extends Seeder
{
    public function run(): void
    {
        /* - Servicios Básicos
        - Alquiler
        - Transporte
        - Publicidad
        - Sueldos
        - Mantenimiento
        - Compras Varias
        - Impuestos
        - Software
        - Otros Gastos */
        DB::table('tipo_gasto')->insert([
            [
                'TG_Id' => 1,
                'TG_Descripcion' => 'Servicios Básicos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 2,
                'TG_Descripcion' => 'Alquiler',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 3,
                'TG_Descripcion' => 'Transporte',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 4,
                'TG_Descripcion' => 'Publicidad',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 5,
                'TG_Descripcion' => 'Sueldos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 6,
                'TG_Descripcion' => 'Mantenimiento',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 7,
                'TG_Descripcion' => 'Compras Varias',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 8,
                'TG_Descripcion' => 'Impuestos',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 9,
                'TG_Descripcion' => 'Software',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'TG_Id' => 10,
                'TG_Descripcion' => 'Otros Gastos',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}


