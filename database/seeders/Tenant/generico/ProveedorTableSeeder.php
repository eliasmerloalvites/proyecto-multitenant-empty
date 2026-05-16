<?php

namespace Database\Seeders\Tenant\generico;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProveedorTableSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('proveedor')->insert([
            [
                'PROV_Id' => 1,
                'PROV_TipoDocumento' => 'DNI',
                'PROV_NumDocumento' => '00000000',
                'PROV_RazonSocial' => 'Proveedor Generico',
                'PROV_Direccion' => '',
                'PROV_Descripcion' => '',
                'PROV_Celular' => '',
                'PROV_Correo' => '',
                'PROV_Status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}


