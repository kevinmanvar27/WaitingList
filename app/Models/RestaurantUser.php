<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestaurantUser extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'mobile_number',
        'total_users_count',
        'status',
        'added_by',
        'restaurant_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total_users_count' => 'integer',
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the user who added this restaurant user.
     */
    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    /**
     * Get the restaurant this user belongs to.
     */
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    /**
     * Scope to get only waiting users.
     */
    public function scopeWaiting($query)
    {
        return $query->where('status', 'waiting');
    }

    /**
     * Scope to get only dine-in users.
     */
    public function scopeDineIn($query)
    {
        return $query->where('status', 'dine-in');
    }

    /**
     * Scope to get users for today only.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Mark user as dine-in.
     */
    public function markAsDineIn(): bool
    {
        return $this->update(['status' => 'dine-in']);
    }

    /**
     * Mark user as waiting.
     */
    public function markAsWaiting(): bool
    {
        return $this->update(['status' => 'waiting']);
    }

    /**
     * Check if user is waiting.
     */
    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    /**
     * Check if user is dine-in.
     */
    public function isDineIn(): bool
    {
        return $this->status === 'dine-in';
    }
}
