<?php

namespace Database\Seeders\Tenant\tallermoto;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            PersonalTableSeeder::class,
            UsersTableSeeder::class,
            RoleAndPermissionSeeder::class,
            ClaseTableSeeder::class,
            TipoGastoTableSeeder::class,
            ProveedorTableSeeder::class,
            MetodoPagoTableSeeder::class,
            ClienteTableSeeder::class,
        ]);
    }
}
