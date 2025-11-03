<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AdminController;
use App\Http\Controllers\Web\PageController;

// Redirect root to admin login
Route::get('/', [AdminController::class, 'showLogin'])->name('admin.login');;

// Admin routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminController::class, 'login']);
    Route::post('/logout', [AdminController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
        Route::get('/restaurant-users/{owner}', [AdminController::class, 'showRestaurantUsers'])->name('restaurant-users');

        // Settings routes
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
        Route::post('/settings/change-password', [AdminController::class, 'changePassword'])->name('settings.change-password');

        // Transactions route
        Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');

        // Transaction data routes (JSON responses for AJAX)
        Route::get('/transactions/data', [AdminController::class, 'transactionsData'])->name('transactions.data');
        Route::get('/transactions/stats', [AdminController::class, 'transactionsStats'])->name('transactions.stats');

        // Chart data routes
        Route::get('/chart-data', [AdminController::class, 'chartData'])->name('chart.data');
        Route::get('/transaction-chart-data', [AdminController::class, 'transactionChartData'])->name('transaction-chart.data');

        // Restaurant routes
        Route::get('/restaurants', [AdminController::class, 'restaurants'])->name('restaurants');
        Route::get('/restaurants/create', [AdminController::class, 'createRestaurant'])->name('restaurants.create');
        Route::post('/restaurants', [AdminController::class, 'storeRestaurant'])->name('restaurants.store');
        Route::post('/restaurants/{id}/toggle-status', [AdminController::class, 'toggleRestaurantStatus'])->name('restaurants.toggle-status');
        Route::post('/restaurants/{id}/toggle-operational-status', [AdminController::class, 'toggleOperationalStatus'])->name('restaurants.toggle-operational-status');
        Route::get('/restaurants/{id}/edit', [AdminController::class, 'editRestaurant'])->name('restaurants.edit');
        Route::get('/restaurants/{id}', [AdminController::class, 'showRestaurant'])->name('restaurants.show');
        Route::put('/restaurants/{id}', [AdminController::class, 'updateRestaurant'])->name('restaurants.update');

        // Pages routes
        Route::resource('pages', PageController::class);

        // Subscription management routes for admin panel
        Route::prefix('subscription-plans')->name('subscription-plans.')->group(function () {
            Route::get('/', [App\Http\Controllers\Web\AdminSubscriptionController::class, 'index'])->name('index');
            Route::post('/', [App\Http\Controllers\Web\AdminSubscriptionController::class, 'store'])->name('store');
            Route::get('/{id}', [App\Http\Controllers\Web\AdminSubscriptionController::class, 'show'])->name('show');
            Route::put('/{id}', [App\Http\Controllers\Web\AdminSubscriptionController::class, 'update'])->name('update');
            Route::delete('/{id}', [App\Http\Controllers\Web\AdminSubscriptionController::class, 'destroy'])->name('destroy');
        });

        Route::post('/clear-database', [AdminController::class, 'clearDatabase'])->name('clear-database');
    });
});

// Add a login route to prevent authentication redirect errors for API
// NEW
Route::get('/login', function () {
    return response()->json(['message' => 'Unauthenticated'], 401);
})->name('api.login');

// Public route for viewing pages
Route::get('/pages/{slug}', [\App\Http\Controllers\Web\PageController::class, 'show'])->name('pages.show');