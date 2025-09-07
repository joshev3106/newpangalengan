<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'), // password default
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // User biasa
        DB::table('users')->insert([
            'name' => 'Regular User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'), // password default
            'role' => 'user',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
