<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserSubscription extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'subscription_plan_id',
        'starts_at',
        'expires_at',
        'status',
        'amount_paid',
        'payment_method',
        'transaction_id',
        'plan_snapshot',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount_paid' => 'decimal:2',
        'plan_snapshot' => 'array',
    ];

    /**
     * Get the user that owns the subscription.
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
     * Scope to get only active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope to get expired subscriptions.
     */
    public function scopeExpired($query)
    {
        return $query->where('status', 'active')
                    ->where('expires_at', '<=', now());
    }

    /**
     * Check if the subscription is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at > now();
    }

    /**
     * Check if the subscription is expired.
     */
    public function isExpired(): bool
    {
        return $this->expires_at <= now();
    }

    /**
     * Get days remaining in subscription.
     */
    public function getDaysRemainingAttribute(): int
    {
        if ($this->isExpired()) {
            return 0;
        }

        return now()->diffInDays($this->expires_at);
    }

    /**
     * Mark subscription as expired.
     */
    public function markAsExpired(): bool
    {
        return $this->update(['status' => 'expired']);
    }

    /**
     * Mark subscription as cancelled.
     */
    public function markAsCancelled(): bool
    {
        return $this->update(['status' => 'cancelled']);
    }

    /**
     * Create a new subscription for a user.
     */
    public static function createForUser(User $user, SubscriptionPlan $plan, array $paymentData = []): self
    {
        $startsAt = now();
        $expiresAt = $startsAt->copy()->addDays($plan->duration_days);

        return self::create([
            'user_id' => $user->id,
            'subscription_plan_id' => $plan->id,
            'starts_at' => $startsAt,
            'expires_at' => $expiresAt,
            'status' => 'active',
            'amount_paid' => $plan->price,
            'payment_method' => $paymentData['payment_method'] ?? null,
            'transaction_id' => $paymentData['transaction_id'] ?? null,
            'plan_snapshot' => [
                'name' => $plan->name,
                'duration_days' => $plan->duration_days,
                'price' => $plan->price,
                'features' => $plan->features,
            ],
        ]);
    }
}
