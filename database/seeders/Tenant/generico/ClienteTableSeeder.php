<?php

namespace Database\Seeders\Tenant\generico;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClienteTableSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('cliente')->insert([
            [
                'CLI_Id' => 1,
                'CLI_TipoDocumento' => 'DNI',
                'CLI_NumDocumento' => '00000000',
                'CLI_Nombre' => 'Cliente Generico',
                'CLI_Direccion' => '',
                'CLI_Celular' => '',
                'CLI_Correo' => '',
                'CLI_Status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}


