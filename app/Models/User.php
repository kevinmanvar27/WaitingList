<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'google_id',
        'name',
        'email',
        'password',
        'profile_picture',
        'is_admin',
        'pin',
        'pin_set_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'pin',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
            'pin_set_at' => 'datetime',
        ];
    }

    /**
     * Get the restaurant users added by this user.
     */
    public function restaurantUsers(): HasMany
    {
        return $this->hasMany(RestaurantUser::class, 'added_by');
    }

    /**
     * Get the user's subscriptions.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get the user's active subscription.
     */
    public function activeSubscription()
    {
        return $this->subscriptions()
                    ->where('status', 'active')
                    ->where('expires_at', '>', now())
                    ->latest('created_at')
                    ->first();
    }

    /**
     * Check if user has an active subscription.
     */
    public function hasActiveSubscription(): bool
    {
        return $this->activeSubscription() !== null;
    }

    /**
     * Check if user has premium access.
     * Returns true only if user has active subscription.
     */
    public function hasPremiumAccess(): bool
    {
        // Check for active subscription only
        return $this->hasActiveSubscription();
    }
}
