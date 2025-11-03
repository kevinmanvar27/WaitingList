<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Transaction;
use App\Models\User;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing users and plans
        $users = User::all();
        $plans = SubscriptionPlan::all();

        if ($users->isEmpty() || $plans->isEmpty()) {
            $this->command->info('No users or subscription plans found. Please seed them first.');
            return;
        }

        // Create sample transactions
        $transactions = [
            [
                'user_id' => $users->random()->id,
                'subscription_plan_id' => $plans->random()->id,
                'restaurant_name' => 'Pizza Palace',
                'plan_name' => 'Basic Plan',
                'amount' => 799.00,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'transaction_id' => 'txn_' . uniqid(),
                'razorpay_payment_id' => 'pay_' . uniqid(),
                'razorpay_order_id' => 'order_' . uniqid(),
                'status' => 'completed',
                'payment_date' => now()->subDays(5),
                'created_at' => now()->subDays(5),
            ],
            [
                'user_id' => $users->random()->id,
                'subscription_plan_id' => $plans->random()->id,
                'restaurant_name' => 'Burger Barn',
                'plan_name' => 'Premium Plan',
                'amount' => 1499.00,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'transaction_id' => 'txn_' . uniqid(),
                'razorpay_payment_id' => 'pay_' . uniqid(),
                'razorpay_order_id' => 'order_' . uniqid(),
                'status' => 'completed',
                'payment_date' => now()->subDays(3),
                'created_at' => now()->subDays(3),
            ],
            [
                'user_id' => $users->random()->id,
                'subscription_plan_id' => $plans->random()->id,
                'restaurant_name' => 'Coffee Corner',
                'plan_name' => 'Basic Plan',
                'amount' => 799.00,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'transaction_id' => 'txn_' . uniqid(),
                'razorpay_payment_id' => 'pay_' . uniqid(),
                'status' => 'failed',
                'payment_date' => null,
                'created_at' => now()->subDays(2),
            ],
            [
                'user_id' => $users->random()->id,
                'subscription_plan_id' => $plans->random()->id,
                'restaurant_name' => 'Sushi Spot',
                'plan_name' => 'Enterprise Plan',
                'amount' => 2999.00,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'transaction_id' => 'txn_' . uniqid(),
                'razorpay_payment_id' => 'pay_' . uniqid(),
                'razorpay_order_id' => 'order_' . uniqid(),
                'status' => 'completed',
                'payment_date' => now()->subDay(),
                'created_at' => now()->subDay(),
            ],
            [
                'user_id' => $users->random()->id,
                'subscription_plan_id' => $plans->random()->id,
                'restaurant_name' => 'Mountain View Cafe',
                'plan_name' => 'Basic Plan',
                'amount' => 799.00,
                'currency' => 'INR',
                'payment_method' => 'razorpay',
                'transaction_id' => 'txn_' . uniqid(),
                'status' => 'pending',
                'payment_date' => null,
                'created_at' => now(),
            ],
        ];

        foreach ($transactions as $transactionData) {
            Transaction::create($transactionData);
        }

        $this->command->info('Sample transactions created successfully!');
    }
}
