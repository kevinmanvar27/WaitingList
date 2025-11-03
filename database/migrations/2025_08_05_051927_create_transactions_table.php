<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Restaurant owner
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade'); // Plan purchased
            $table->foreignId('user_subscription_id')->nullable()->constrained()->onDelete('set null'); // Related subscription
            $table->string('restaurant_name'); // Restaurant name at time of purchase
            $table->string('plan_name'); // Plan name at time of purchase
            $table->decimal('amount', 10, 2); // Transaction amount
            $table->string('currency', 3)->default('INR'); // Currency
            $table->string('payment_method'); // razorpay, etc.
            $table->string('transaction_id')->unique(); // Payment gateway transaction ID
            $table->string('razorpay_payment_id')->nullable(); // Razorpay payment ID
            $table->string('razorpay_order_id')->nullable(); // Razorpay order ID
            $table->string('razorpay_signature')->nullable(); // Razorpay signature
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('payment_details')->nullable(); // Additional payment gateway details
            $table->timestamp('payment_date')->nullable(); // When payment was completed
            $table->text('notes')->nullable(); // Additional notes
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['transaction_id']);
            $table->index(['payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
