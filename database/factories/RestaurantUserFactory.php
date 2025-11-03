<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RestaurantUser>
 */
class RestaurantUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'username' => $this->faker->name,
            'mobile_number' => $this->faker->unique()->numerify('##########'),
            'total_users_count' => $this->faker->numberBetween(1, 10),
            'added_by' => \App\Models\User::factory(),
        ];
    }
}
