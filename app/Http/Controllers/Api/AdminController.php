<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RestaurantUser;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AdminController extends Controller
{
    /**
     * Get all restaurant owners (users who have added restaurant users)
     */
    public function getRestaurantOwners(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $query = User::whereHas('restaurantUsers')
                ->withCount('restaurantUsers')
                ->with(['restaurantUsers' => function ($query) {
                    $query->latest()->take(5); // Get latest 5 restaurant users for preview
                }]);

            // Add search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            }

            // Add sorting
            $sortBy = $request->get('sort_by', 'restaurant_users_count');
            $sortOrder = $request->get('sort_order', 'desc');

            if ($sortBy === 'restaurant_users_count') {
                $query->orderBy('restaurant_users_count', $sortOrder);
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }

            // Paginate results
            $perPage = $request->get('per_page', 20);
            $restaurantOwners = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurantOwners->items(),
                'meta' => [
                    'current_page' => $restaurantOwners->currentPage(),
                    'last_page' => $restaurantOwners->lastPage(),
                    'per_page' => $restaurantOwners->perPage(),
                    'total' => $restaurantOwners->total(),
                ],
                'message' => 'Restaurant owners retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurant owners: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get restaurant users for a specific owner
     */
    public function getRestaurantUsersByOwner(Request $request, $ownerId): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $owner = User::findOrFail($ownerId);

            $query = RestaurantUser::where('added_by', $ownerId)->with('addedBy');

            // Add search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('username', 'like', "%{$search}%")
                      ->orWhere('mobile_number', 'like', "%{$search}%");
                });
            }

            // Add sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Paginate results
            $perPage = $request->get('per_page', 20);
            $restaurantUsers = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $restaurantUsers->items(),
                'owner' => [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'email' => $owner->email,
                ],
                'meta' => [
                    'current_page' => $restaurantUsers->currentPage(),
                    'last_page' => $restaurantUsers->lastPage(),
                    'per_page' => $restaurantUsers->perPage(),
                    'total' => $restaurantUsers->total(),
                ],
                'message' => 'Restaurant users retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurant users: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get admin dashboard statistics
     */
    public function getDashboardStats(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $totalRestaurantOwners = User::whereHas('restaurantUsers')->count();
            $totalRestaurantUsers = RestaurantUser::count();
            $totalUsers = User::count();
            $recentRegistrations = User::where('created_at', '>=', now()->subDays(7))->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'total_restaurant_owners' => $totalRestaurantOwners,
                    'total_restaurant_users' => $totalRestaurantUsers,
                    'total_users' => $totalUsers,
                    'recent_registrations' => $recentRegistrations,
                ],
                'message' => 'Dashboard statistics retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve dashboard statistics: ' . $e->getMessage(),
            ], 500);
        }
    }
}
