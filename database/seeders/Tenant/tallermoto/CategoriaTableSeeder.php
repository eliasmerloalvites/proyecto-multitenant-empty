<?php

namespace Database\Seeders\Tenant\tallermoto;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categoria')->insert([
            ['CAT_Id' => 1,  'CLA_Id' => 1, 'CAT_Nombre' => 'MOTOR'],
            ['CAT_Id' => 2,  'CLA_Id' => 1, 'CAT_Nombre' => 'FRENOS'],
            ['CAT_Id' => 3,  'CLA_Id' => 1, 'CAT_Nombre' => 'SUSPENSIÓN Y DIRECCIÓN'],
            ['CAT_Id' => 4,  'CLA_Id' => 2, 'CAT_Nombre' => 'BATERÍAS'],
            ['CAT_Id' => 5,  'CLA_Id' => 2, 'CAT_Nombre' => 'ILUMINACIÓN'],
            ['CAT_Id' => 6,  'CLA_Id' => 2, 'CAT_Nombre' => 'SENSORES Y ENCENDIDO'],
            ['CAT_Id' => 7,  'CLA_Id' => 3, 'CAT_Nombre' => 'ACEITES DE MOTOR'],
            ['CAT_Id' => 8,  'CLA_Id' => 3, 'CAT_Nombre' => 'REFRIGERANTES'],
            ['CAT_Id' => 9,  'CLA_Id' => 3, 'CAT_Nombre' => 'LIMPIADORES Y SELLADORES'],
            ['CAT_Id' => 10, 'CLA_Id' => 4, 'CAT_Nombre' => 'PINTURAS Y PREPARACIÓN'],
            ['CAT_Id' => 11, 'CLA_Id' => 4, 'CAT_Nombre' => 'COLISIÓN'],
            ['CAT_Id' => 12, 'CLA_Id' => 4, 'CAT_Nombre' => 'DETAILING / LIMPIEZA'],
            ['CAT_Id' => 13, 'CLA_Id' => 5, 'CAT_Nombre' => 'HERRAMIENTAS MANUALES'],
            ['CAT_Id' => 14, 'CLA_Id' => 5, 'CAT_Nombre' => 'CONSUMIBLES DE TRABAJO'],
            ['CAT_Id' => 15, 'CLA_Id' => 5, 'CAT_Nombre' => 'EPP (SEGURIDAD)'],
            ['CAT_Id' => 16, 'CLA_Id' => 6, 'CAT_Nombre' => 'MANTENIMIENTO'],
            ['CAT_Id' => 17, 'CLA_Id' => 6, 'CAT_Nombre' => 'REPARACIONES COMPLEJAS'],
            ['CAT_Id' => 18, 'CLA_Id' => 6, 'CAT_Nombre' => 'DIAGNÓSTICOS'],
            ['CAT_Id' => 19, 'CLA_Id' => 1, 'CAT_Nombre' => 'SISTEMAS DE ARRASTRE'],
            ['CAT_Id' => 20, 'CLA_Id' => 1, 'CAT_Nombre' => 'RODAMIENTOS'],
            ['CAT_Id' => 21, 'CLA_Id' => 1, 'CAT_Nombre' => 'CABLES Y SENSORES DE VELOCIDAD'],
        ]);
    }
}
