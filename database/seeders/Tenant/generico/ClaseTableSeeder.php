<?php

namespace Database\Seeders\Tenant\generico;

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
                'CLA_Nombre' => 'Abarrotes',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
