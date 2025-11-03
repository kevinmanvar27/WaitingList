<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ensure admin user exists via dedicated seeder
        $this->call([
            AdminUserSeeder::class,
            SettingsSeeder::class,
            PageSeeder::class,
        ]);
    }
}