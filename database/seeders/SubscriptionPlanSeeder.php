<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic Plan',
                'duration_days' => 30,
                'price' => 799.00, // ₹799 per month
                'is_enabled' => true,
                'description' => 'Perfect for small restaurants with basic waiting list needs.',
                'features' => [
                    'Person Count Management',
                    'Waiting List Management',
                    'Basic Status Updates',
                    'Mobile App Access'
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Premium Plan',
                'duration_days' => 30,
                'price' => 1499.00, // ₹1,499 per month
                'is_enabled' => true,
                'description' => 'Ideal for busy restaurants with advanced management features.',
                'features' => [
                    'Person Count Management',
                    'Waiting List Management',
                    'Real-time Status Updates',
                    'Advanced Search & Filters',
                    'Mobile App Access',
                    'Priority Support'
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Annual Plan',
                'duration_days' => 365,
                'price' => 14999.00, // ₹14,999 per year (save ₹2,989)
                'is_enabled' => true,
                'description' => 'Best value for restaurants committed to long-term growth.',
                'features' => [
                    'Person Count Management',
                    'Waiting List Management',
                    'Real-time Status Updates',
                    'Advanced Search & Filters',
                    'Mobile App Access',
                    'Priority Support',
                    'Analytics Dashboard',
                    'Custom Branding'
                ],
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['name' => $planData['name']],
                $planData
            );
        }
    }
}
