<?php

namespace Database\Seeders\Tenant\tallermoto;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Marcas de motocicletas que se ofrecen por defecto al elegir la marca del
 * vehiculo (reservas y mantenimientos). El taller puede agregar, renombrar
 * o desactivar desde Mantenimientos > Marcas de Vehiculo. Es idempotente:
 * solo agrega las que falten, sin tocar las que el taller ya edito.
 */
class MarcaVehiculoSeeder extends Seeder
{
    public const MARCAS = [
        // Japonesas
        'Honda', 'Yamaha', 'Suzuki', 'Kawasaki',
        // India
        'Bajaj', 'TVS', 'Hero', 'Royal Enfield',
        // Chinas (muy comunes en Peru)
        'Lifan', 'Zongshen', 'Loncin', 'Haojue', 'Dayun', 'Wanxin', 'Jialing',
        'Shineray', 'Zontes', 'Voge', 'QJ Motor', 'CFMoto', 'Keeway', 'Benelli',
        'Kymco', 'SYM', 'Sanya', 'Jincheng', 'Qingqi', 'Lingken', 'Motokar',
        // Marcas y ensambladoras de la region
        'Ronco', 'Italika', 'Wanxin Ronco', 'Gilera', 'Zanella', 'Corbeta',
        // Europeas y americanas
        'KTM', 'Husqvarna', 'BMW', 'Ducati', 'Triumph', 'Aprilia', 'Piaggio',
        'Vespa', 'Moto Guzzi', 'MV Agusta', 'Harley-Davidson', 'Indian',
    ];

    public function run(): void
    {
        $existentes = DB::table('marca_vehiculo')->pluck('MAR_Nombre')->map(fn ($n) => mb_strtolower($n))->all();

        foreach (self::MARCAS as $marca) {
            if (! in_array(mb_strtolower($marca), $existentes, true)) {
                DB::table('marca_vehiculo')->insert([
                    'MAR_Nombre' => $marca,
                    'MAR_Estado' => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
