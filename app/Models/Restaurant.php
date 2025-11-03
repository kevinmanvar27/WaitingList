<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Restaurant extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'profile',
        'name',
        'contact_number',
        'location',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'is_active',
        'current_waiting_count',
        'owner_id',
        'owner_name',
        'description',
        'operating_hours',
        'cuisine_type',
        'website',
        'operational_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'current_waiting_count' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the owner of the restaurant.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Get the restaurant users for this restaurant.
     */
    public function restaurantUsers(): HasMany
    {
        return $this->hasMany(RestaurantUser::class, 'added_by', 'owner_id');
    }

    /**
     * Scope to get only active restaurants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to search restaurants by name or location.
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('location', 'like', "%{$search}%")
              ->orWhere('address_line_1', 'like', "%{$search}%")
              ->orWhere('city', 'like', "%{$search}%")
              ->orWhere('state', 'like', "%{$search}%");
        });
    }

    /**
     * Get formatted full address
     */
    public function getFullAddressAttribute()
    {
        $addressParts = array_filter([
            $this->address_line_1,
            $this->address_line_2,
            $this->city,
            $this->state,
            $this->country,
            $this->postal_code,
        ]);

        return implode(', ', $addressParts);
    }

    /**
     * Get display owner name (use owner_name field or fallback to user relationship)
     */
    public function getDisplayOwnerNameAttribute()
    {
        return $this->owner_name ?: ($this->owner ? $this->owner->name : 'Unknown');
    }

    /**
     * Update the current waiting count based on today's waiting users
     * Aggregates the total_users_count field from all waiting users
     * Note: No auto-close functionality - restaurants only close manually
     */
    public function updateWaitingCount(): void
    {
        $count = \App\Models\RestaurantUser::where('added_by', $this->owner_id)
            ->waiting()
            ->today()
            ->sum('total_users_count') ?? 0;

        $this->update(['current_waiting_count' => $count]);
    }

    /**
     * Get today's waiting count without updating the database
     * Aggregates the total_users_count field from all waiting users
     */
    public function getTodaysWaitingCount(): int
    {
        return \App\Models\RestaurantUser::where('added_by', $this->owner_id)
            ->waiting()
            ->today()
            ->sum('total_users_count') ?? 0;
    }

    /**
     * Get today's waiting entries count (number of records, not aggregated)
     */
    public function getTodaysWaitingEntriesCount(): int
    {
        return \App\Models\RestaurantUser::where('added_by', $this->owner_id)
            ->waiting()
            ->today()
            ->count();
    }
}
