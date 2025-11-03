<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'user_subscription_id',
        'restaurant_name',
        'plan_name',
        'amount',
        'currency',
        'payment_method',
        'transaction_id',
        'razorpay_payment_id',
        'razorpay_order_id',
        'razorpay_signature',
        'status',
        'payment_details',
        'payment_date',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'payment_details' => 'array',
        'payment_date' => 'datetime',
    ];

    /**
     * Get the user that owns the transaction.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subscription plan.
     */
    public function subscriptionPlan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class);
    }

    /**
     * Get the user subscription.
     */
    public function userSubscription(): BelongsTo
    {
        return $this->belongsTo(UserSubscription::class);
    }

    /**
     * Scope to get completed transactions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get pending transactions.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get failed transactions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Get formatted amount.
     */
    public function getFormattedAmountAttribute(): string
    {
        return '₹' . number_format($this->amount, 2);
    }

    /**
     * Mark transaction as completed.
     */
    public function markAsCompleted(): bool
    {
        return $this->update([
            'status' => 'completed',
            'payment_date' => now(),
        ]);
    }

    /**
     * Mark transaction as failed.
     */
    public function markAsFailed(): bool
    {
        return $this->update(['status' => 'failed']);
    }

    /**
     * Create a new transaction record.
     */
    public static function createForSubscription(
        User $user,
        SubscriptionPlan $plan,
        UserSubscription $subscription,
        array $paymentData
    ): self {
        return self::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'user_subscription_id' => $subscription->id,
            'restaurant_name' => $user->restaurant_name ?? $user->name,
            'plan_name' => $plan->name,
            'amount' => $plan->price,
            'currency' => 'INR',
            'payment_method' => $paymentData['payment_method'] ?? 'razorpay',
            'transaction_id' => $paymentData['transaction_id'],
            'razorpay_payment_id' => $paymentData['razorpay_payment_id'] ?? null,
            'razorpay_order_id' => $paymentData['razorpay_order_id'] ?? null,
            'razorpay_signature' => $paymentData['razorpay_signature'] ?? null,
            'status' => 'completed',
            'payment_details' => $paymentData['payment_details'] ?? null,
            'payment_date' => now(),
        ]);
    }
}
