<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Restaurant;
use App\Models\User;

class RestaurantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users or create some
        $users = User::all();

        if ($users->isEmpty()) {
            // Create some sample users if none exist
            $user1 = User::create([
                'name' => 'John Doe',
                'email' => 'john@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]);

            $user2 = User::create([
                'name' => 'Jane Smith',
                'email' => 'jane@example.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]);

            $users = collect([$user1, $user2]);
        }

        // Create sample restaurants
        $restaurants = [
            [
                'name' => 'Pizza Palace',
                'contact_number' => '+1-555-0101',
                'location' => '123 Main St, New York, NY 10001',
                'latitude' => 40.7128,
                'longitude' => -74.0060,
                'is_active' => true,
                'current_waiting_count' => 5,
                'owner_id' => $users->first()->id,
            ],
            [
                'name' => 'Burger Barn',
                'contact_number' => '+1-555-0102',
                'location' => '456 Oak Ave, Los Angeles, CA 90210',
                'latitude' => 34.0522,
                'longitude' => -118.2437,
                'is_active' => true,
                'current_waiting_count' => 3,
                'owner_id' => $users->count() > 1 ? $users->skip(1)->first()->id : $users->first()->id,
            ],
            [
                'name' => 'Sushi Spot',
                'contact_number' => '+1-555-0103',
                'location' => '789 Pine Rd, Chicago, IL 60601',
                'latitude' => 41.8781,
                'longitude' => -87.6298,
                'is_active' => true,
                'current_waiting_count' => 8,
                'owner_id' => $users->first()->id,
            ],
            [
                'name' => 'Taco Town',
                'contact_number' => '+1-555-0104',
                'location' => '321 Elm St, Houston, TX 77001',
                'latitude' => 29.7604,
                'longitude' => -95.3698,
                'is_active' => false,
                'current_waiting_count' => 0,
                'owner_id' => $users->count() > 1 ? $users->skip(1)->first()->id : $users->first()->id,
            ],
            [
                'name' => 'Coffee Corner',
                'contact_number' => '+1-555-0105',
                'location' => '654 Maple Dr, Phoenix, AZ 85001',
                'latitude' => 33.4484,
                'longitude' => -112.0740,
                'is_active' => true,
                'current_waiting_count' => 2,
                'owner_id' => $users->first()->id,
            ],
        ];

        foreach ($restaurants as $restaurant) {
            Restaurant::create($restaurant);
        }
    }
}
