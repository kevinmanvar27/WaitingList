<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RestaurantUserController;
use App\Http\Controllers\Api\SettingsController;
use App\Http\Controllers\Api\RestaurantController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\UserSubscriptionController;
use App\Http\Controllers\Api\TransactionController;

// Authentication routes
Route::prefix('auth')->middleware('throttle:auth')->group(function () {
    Route::post('/google', [AuthController::class, 'googleAuth']);
    Route::post('/admin-login', [AuthController::class, 'adminLogin']);
     Route::post('/request.otp', [\App\Http\Controllers\Api\OtpController::class, 'requestOtp']);
    Route::post('/verify-otp', [\App\Http\Controllers\Api\OtpController::class, 'verifyOtp']);
    Route::post('/pin-login', [AuthController::class, 'pinLogin']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/set-pin', [AuthController::class, 'setPin']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
    });
});

// Public routes (no authentication required)
Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working!',
        'timestamp' => now(),
    ]);
});
Route::get('/restaurants/public', [RestaurantController::class, 'publicList']);
Route::get('/settings/public', [SettingsController::class, 'public']);
Route::get('/pages/{slug}', [\App\Http\Controllers\Api\PageController::class, 'publicShow']);

// Public subscription routes (for viewing enabled plans)
Route::get('/subscription-plans', [SubscriptionController::class, 'index']);

// Protected routes
Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    // Premium features - require subscription
    Route::middleware(['premium'])->group(function () {
        Route::apiResource('restaurant-users', RestaurantUserController::class);

        // Auto-fill functionality
        Route::get('/restaurant-users/search/by-phone/{phone}', [RestaurantUserController::class, 'searchByPhone']);

        // Status management
        Route::post('/restaurant-users/{restaurantUser}/mark-dine-in', [RestaurantUserController::class, 'markAsDineIn']);
        Route::post('/restaurant-users/{restaurantUser}/mark-waiting', [RestaurantUserController::class, 'markAsWaiting']);
        Route::get('/restaurant-users/waiting-count', [RestaurantUserController::class, 'getWaitingCount']);
    });

    // User's restaurant routes
    Route::get('/restaurants/my-restaurant', [RestaurantController::class, 'getMyRestaurant']);
    Route::post('/restaurants/my-restaurant', [RestaurantController::class, 'saveMyRestaurant']);
    Route::post('/restaurants/my-restaurant/toggle-status', [RestaurantController::class, 'toggleMyRestaurantStatus']);

    // User subscription routes
    Route::prefix('subscriptions')->group(function () {
        Route::get('/', [UserSubscriptionController::class, 'index']); // User's subscription history
        Route::post('/purchase', [UserSubscriptionController::class, 'store']); // Purchase subscription
        Route::post('/create-razorpay-order', [UserSubscriptionController::class, 'createRazorpayOrder']); // Create Razorpay order
        Route::get('/status', [UserSubscriptionController::class, 'status']); // Current subscription status
        Route::post('/cancel', [UserSubscriptionController::class, 'cancel']); // Cancel subscription
        Route::get('/{userSubscription}', [UserSubscriptionController::class, 'show']); // View specific subscription
    });

    // User transaction routes
    Route::get('/user-transactions', [UserSubscriptionController::class, 'getUserTransactions']);

    // Admin routes
    Route::prefix('admin')->middleware('admin')->group(function () {
        Route::get('/restaurant-owners', [App\Http\Controllers\Api\AdminController::class, 'getRestaurantOwners']);
        Route::get('/restaurant-owners/{ownerId}/users', [App\Http\Controllers\Api\AdminController::class, 'getRestaurantUsersByOwner']);
        Route::get('/dashboard-stats', [App\Http\Controllers\Api\AdminController::class, 'getDashboardStats']);

        // Settings routes
        Route::get('/settings', [SettingsController::class, 'index']);
        Route::post('/settings', [SettingsController::class, 'update']);

        // Restaurant routes
        Route::apiResource('restaurants', RestaurantController::class);
        Route::post('/restaurants/{id}/toggle-status', [RestaurantController::class, 'toggleStatus']);

        // Admin subscription management routes
        Route::apiResource('subscription-plans', SubscriptionController::class);

        // Admin transaction management routes
        Route::prefix('transactions')->group(function () {
            Route::get('/', [TransactionController::class, 'index']);
            Route::get('/stats', [TransactionController::class, 'stats']);
            Route::get('/{transaction}', [TransactionController::class, 'show']);
        });

        // Admin pages management routes
        Route::apiResource('pages', \App\Http\Controllers\Api\PageController::class);
    });
});