<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriptionPlan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'duration_days',
        'price',
        'is_enabled',
        'description',
        'features',
        'sort_order',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'price' => 'decimal:2',
        'is_enabled' => 'boolean',
        'features' => 'array',
        'duration_days' => 'integer',
        'sort_order' => 'integer',
    ];

    /**
     * Get the user subscriptions for this plan.
     */
    public function userSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get active user subscriptions for this plan.
     */
    public function activeSubscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class)->where('status', 'active');
    }

    /**
     * Scope to get only enabled plans.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get plans ordered by sort_order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }

    /**
     * Check if the plan is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->is_enabled;
    }

    /**
     * Get formatted price in INR.
     */
    public function getFormattedPriceAttribute(): string
    {
        return '₹' . number_format($this->price, 2);
    }

    /**
     * Get formatted price in USD (for backward compatibility).
     */
    public function getFormattedPriceUsdAttribute(): string
    {
        return '$' . number_format($this->price, 2);
    }

    /**
     * Get duration in human readable format.
     */
    public function getFormattedDurationAttribute(): string
    {
        if ($this->duration_days == 1) {
            return '1 Day';
        } elseif ($this->duration_days == 7) {
            return '1 Week (7 days)';
        } elseif ($this->duration_days == 30) {
            return '1 Month (30 days)';
        } elseif ($this->duration_days == 365) {
            return '1 Year (365 days)';
        } elseif ($this->duration_days == 90) {
            return '3 Months (90 days)';
        } else {
            return $this->duration_days . ' days';
        }
    }
}
