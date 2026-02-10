<?php

namespace Database\Seeders\Central;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'Abner Elias Merlo Alvites',
                'email' => 'merloalviteselias@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$10$QIRp4TJ1IyW4WHxUZnCd3eOmNqpldLkUUepaedJP/lMcNpIQTQCsa',
                'photo_extension' => null,
                'provider' => null,
                'provider_id' => null,
                'remember_token' => 'v6GbOTVXfxpiKud9IFub1q6KIXC5h1J75N6pYz8M0jBYlQZwj0rA5W1tl1Ke',
                'created_at' => Carbon::parse('2023-11-02 02:23:21'),
                'updated_at' => Carbon::parse('2024-04-03 22:02:46'),
                'estadousuario' => 1,
                'avatar' => '',
                'tipousuario' => 0,
                'numerodocumento' => null,
                'PER_Id' => 1,
            ],
            [
                'id' => 2,
                'name' => 'Manuel Margas Azañero',
                'email' => 'manuel@gmail.com',
                'email_verified_at' => null,
                'password' => '$2y$12$IUcxZoouOi71BIa10F.iouUSviAcSfXn6s5x9dlMLzQvpJOka26mi',
                'photo_extension' => null,
                'provider' => null,
                'provider_id' => null,
                'remember_token' => 'DMdfeMEE2T',
                'created_at' => Carbon::parse('2024-04-01 23:07:44'),
                'updated_at' => Carbon::parse('2024-04-01 23:07:44'),
                'estadousuario' => 1,
                'avatar' => null,
                'tipousuario' => 0,
                'numerodocumento' => null,
                'PER_Id' => 2,
            ]
        ]);
    }
}
