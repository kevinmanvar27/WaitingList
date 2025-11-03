<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::where('email', 'rektech.uk@gmail.com')->first();

        if (!$admin) {
            User::create([
                'name' => 'RekTech Admin',
                'email' => 'rektech.uk@gmail.com',
                'password' => Hash::make('RekTech@27'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);

            $this->command->info('Admin user created successfully');
        } else {
            $admin->update([
                'password' => Hash::make('RekTech@27'),
                'is_admin' => true,
            ]);

            $this->command->info('Admin user updated successfully');
        }
    }
}