<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RestaurantUser;
use App\Models\Transaction;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use Carbon\Carbon;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users or create new ones
        $users = [];
        for ($i = 1; $i <= 20; $i++) {
            $user = User::firstOrCreate(
                ['email' => "user{$i}@demo.com"],
                [
                    'name' => "Demo User {$i}",
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                    'created_at' => now()->subDays(rand(1, 60)),
                ]
            );
            $users[] = $user;
        }

        // Create some restaurants first
        $restaurants = [];
        foreach ($users as $index => $user) {
            if ($index % 3 == 0) { // Every 3rd user becomes a restaurant owner
                $restaurant = \App\Models\Restaurant::create([
                    'name' => "Restaurant " . ($index + 1),
                    'contact_number' => '9876543210',
                    'location' => 'Demo Location ' . ($index + 1),
                    'owner_id' => $user->id,
                    'owner_name' => $user->name,
                    'is_active' => true,
                    'created_at' => $user->created_at,
                ]);
                $restaurants[] = $restaurant;

                // Create some restaurant users for this restaurant
                for ($j = 1; $j <= rand(3, 8); $j++) {
                    RestaurantUser::create([
                        'username' => "Customer " . $j,
                        'mobile_number' => '98765432' . str_pad($j, 2, '0', STR_PAD_LEFT),
                        'total_users_count' => rand(1, 5),
                        'status' => rand(0, 1) ? 'waiting' : 'dine-in',
                        'added_by' => $user->id,
                        'restaurant_id' => $restaurant->id,
                        'created_at' => $user->created_at->addDays(rand(0, 30)),
                    ]);
                }
            }
        }

        // Create subscription plans if they don't exist
        $plans = [
            [
                'name' => 'Basic Plan',
                'description' => 'Basic features for small restaurants',
                'price' => 999.00,
                'duration_days' => 30,
                'features' => json_encode(['Basic menu management', 'Order tracking']),
            ],
            [
                'name' => 'Premium Plan',
                'description' => 'Advanced features for growing restaurants',
                'price' => 1999.00,
                'duration_days' => 30,
                'features' => json_encode(['Advanced analytics', 'Multi-location support', 'Priority support']),
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Full features for large restaurant chains',
                'price' => 4999.00,
                'duration_days' => 30,
                'features' => json_encode(['Custom integrations', 'Dedicated support', 'Advanced reporting']),
            ],
        ];

        $subscriptionPlans = [];
        foreach ($plans as $planData) {
            $plan = SubscriptionPlan::firstOrCreate(
                ['name' => $planData['name']],
                $planData
            );
            $subscriptionPlans[] = $plan;
        }

        // Create demo transactions over the last 60 days
        $statuses = ['completed', 'pending', 'failed'];
        $statusWeights = [0.7, 0.2, 0.1]; // 70% completed, 20% pending, 10% failed

        for ($i = 0; $i < 100; $i++) {
            $user = $users[array_rand($users)];
            $plan = $subscriptionPlans[array_rand($subscriptionPlans)];
            
            // Create more transactions in recent days
            $daysAgo = $this->getWeightedRandomDays();
            $createdAt = now()->subDays($daysAgo);
            
            // Select status based on weights
            $status = $this->getWeightedRandomStatus($statuses, $statusWeights);
            
            $transaction = Transaction::create([
                'user_id' => $user->id,
                'subscription_plan_id' => $plan->id,
                'transaction_id' => 'TXN' . str_pad($i + 1, 6, '0', STR_PAD_LEFT),
                'razorpay_payment_id' => $status === 'completed' ? 'pay_' . uniqid() : null,
                'amount' => $plan->price,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'status' => $status,
                'restaurant_name' => "Restaurant " . rand(1, 20),
                'plan_name' => $plan->name,
                'payment_date' => $status === 'completed' ? $createdAt : null,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Create user subscription for completed transactions
            if ($status === 'completed') {
                UserSubscription::create([
                    'user_id' => $user->id,
                    'subscription_plan_id' => $plan->id,
                    'transaction_id' => $transaction->id,
                    'starts_at' => $createdAt,
                    'expires_at' => $createdAt->copy()->addDays($plan->duration_days),
                    'status' => 'active',
                    'amount_paid' => $plan->price,
                    'payment_method' => 'razorpay',
                    'created_at' => $createdAt,
                ]);
            }
        }

        $this->command->info('Demo data seeded successfully!');
    }

    /**
     * Get weighted random number of days ago (more recent transactions)
     */
    private function getWeightedRandomDays(): int
    {
        $rand = mt_rand() / mt_getrandmax();
        
        if ($rand < 0.4) {
            return rand(0, 7); // 40% in last week
        } elseif ($rand < 0.7) {
            return rand(8, 30); // 30% in last month
        } else {
            return rand(31, 60); // 30% in last 2 months
        }
    }

    /**
     * Get weighted random status
     */
    private function getWeightedRandomStatus(array $statuses, array $weights): string
    {
        $rand = mt_rand() / mt_getrandmax();
        $cumulative = 0;
        
        foreach ($weights as $index => $weight) {
            $cumulative += $weight;
            if ($rand <= $cumulative) {
                return $statuses[$index];
            }
        }
        
        return $statuses[0]; // fallback
    }
}
