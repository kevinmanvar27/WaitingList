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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // User who purchased
            $table->foreignId('subscription_plan_id')->constrained()->onDelete('cascade'); // Plan purchased
            $table->timestamp('starts_at')->nullable(); // Subscription start date
            $table->timestamp('expires_at')->nullable(); // Subscription expiry date
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active'); // Subscription status
            $table->decimal('amount_paid', 10, 2); // Amount paid for this subscription
            $table->string('payment_method')->nullable(); // Payment method used
            $table->string('transaction_id')->nullable(); // Transaction reference
            $table->json('plan_snapshot')->nullable(); // Snapshot of plan details at purchase time
            $table->timestamps();

            // Indexes for better performance
            $table->index(['user_id', 'status']);
            $table->index(['expires_at', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
