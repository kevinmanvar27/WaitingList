<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRestaurantUserRequest;
use App\Http\Requests\UpdateRestaurantUserRequest;
use App\Http\Resources\RestaurantUserResource;
use App\Models\RestaurantUser;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RestaurantUserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = RestaurantUser::with('addedBy');

            // Filter by status (default to waiting only)
            $status = $request->get('status', 'waiting');
            if ($status === 'all') {
                // Show all statuses
            } elseif (in_array($status, ['waiting', 'dine-in'])) {
                $query->where('status', $status);
            } else {
                $query->waiting(); // Default to waiting
            }

            // Filter by today's date only
            $query->today();

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
                'data' => RestaurantUserResource::collection($restaurantUsers),
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
     * Store a newly created resource in storage.
     */
    public function store(StoreRestaurantUserRequest $request): JsonResponse
    {
        try {
            // Get the first restaurant for this owner (temporary solution)
            $restaurant = \App\Models\Restaurant::where('owner_id', $request->user()->id)->first();

            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'No restaurant found for this user. Please create a restaurant first.',
                ], 400);
            }

            $restaurantUser = RestaurantUser::create([
                'username' => $request->username,
                'mobile_number' => $request->mobile_number,
                'total_users_count' => $request->total_users_count,
                'status' => 'waiting', // Default status
                'added_by' => $request->user()->id,
                'restaurant_id' => $restaurant->id, // Assign to first restaurant
            ]);

            $restaurantUser->load('addedBy');

            // Update restaurant waiting count
            $this->updateRestaurantWaitingCountByRestaurantId($restaurantUser->restaurant_id);

            return response()->json([
                'success' => true,
                'data' => new RestaurantUserResource($restaurantUser),
                'message' => 'Restaurant user created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create restaurant user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(RestaurantUser $restaurantUser): JsonResponse
    {
        try {
            $restaurantUser->load('addedBy');

            return response()->json([
                'success' => true,
                'data' => new RestaurantUserResource($restaurantUser),
                'message' => 'Restaurant user retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurant user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRestaurantUserRequest $request, RestaurantUser $restaurantUser): JsonResponse
    {
        try {
            $restaurantUser->update($request->validated());
            $restaurantUser->load('addedBy');

            // Update restaurant waiting count after user update
            $this->updateRestaurantWaitingCountByRestaurantId($restaurantUser->restaurant_id);

            return response()->json([
                'success' => true,
                'data' => new RestaurantUserResource($restaurantUser),
                'message' => 'Restaurant user updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(RestaurantUser $restaurantUser): JsonResponse
    {
        try {
            $restaurantId = $restaurantUser->restaurant_id; // Store before deletion
            $restaurantUser->delete();

            // Update restaurant waiting count after user deletion
            $this->updateRestaurantWaitingCountByRestaurantId($restaurantId);

            return response()->json([
                'success' => true,
                'message' => 'Restaurant user deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete restaurant user: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Search for users by phone number for auto-fill functionality
     */
    public function searchByPhone(string $phone): JsonResponse
    {
        try {
            // Clean the phone number (remove spaces, dashes, etc.)
            $cleanPhone = preg_replace('/[^0-9+]/', '', $phone);

            // Search for users with the same phone number
            // Order by created_at desc to get the most recent user first
            $user = RestaurantUser::where('mobile_number', $cleanPhone)
                ->orWhere('mobile_number', $phone) // Also search with original format
                ->orderBy('created_at', 'desc')
                ->first();

            if ($user) {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'username' => $user->username,
                        'phone_number' => $user->mobile_number,
                        'found' => true,
                        'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    ],
                    'message' => 'User found with this phone number',
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => [
                        'username' => null,
                        'phone_number' => $phone,
                        'found' => false,
                    ],
                    'message' => 'No user found with this phone number',
                ]);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to search user by phone: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user status to dine-in
     */
    public function markAsDineIn(RestaurantUser $restaurantUser): JsonResponse
    {
        try {
            $restaurantUser->markAsDineIn();
            $restaurantUser->load('addedBy');

            // Update restaurant waiting count
            $this->updateRestaurantWaitingCountByRestaurantId($restaurantUser->restaurant_id);

            return response()->json([
                'success' => true,
                'data' => new RestaurantUserResource($restaurantUser),
                'message' => 'User marked as dine-in successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark user as dine-in: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user status to waiting (undo dine-in)
     */
    public function markAsWaiting(RestaurantUser $restaurantUser): JsonResponse
    {
        try {
            $restaurantUser->markAsWaiting();
            $restaurantUser->load('addedBy');

            // Update restaurant waiting count
            $this->updateRestaurantWaitingCountByRestaurantId($restaurantUser->restaurant_id);

            return response()->json([
                'success' => true,
                'data' => new RestaurantUserResource($restaurantUser),
                'message' => 'User marked as waiting successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to mark user as waiting: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get waiting count for current user's restaurant
     */
    public function getWaitingCount(Request $request): JsonResponse
    {
        try {
            $count = RestaurantUser::where('added_by', $request->user()->id)
                ->waiting()
                ->today()
                ->count();

            return response()->json([
                'success' => true,
                'data' => ['waiting_count' => $count],
                'message' => 'Waiting count retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get waiting count: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update restaurant waiting count by restaurant ID (NEW PROPER METHOD)
     * This fixes the issue where multiple restaurants shared the same owner_id
     */
    private function updateRestaurantWaitingCountByRestaurantId(int $restaurantId): void
    {
        try {
            $restaurant = \App\Models\Restaurant::find($restaurantId);

            if (!$restaurant) {
                return;
            }

            // Count users for this specific restaurant only
            $count = RestaurantUser::where('restaurant_id', $restaurantId)
                ->waiting()
                ->today()
                ->sum('total_users_count') ?? 0;

            // Update only this specific restaurant's current_waiting_count
            $restaurant->update(['current_waiting_count' => $count]);

        } catch (\Exception $e) {
            \Log::error('Failed to update restaurant waiting count by restaurant ID: ' . $e->getMessage());
        }
    }

    /**
     * Update restaurant waiting count by owner ID (LEGACY METHOD - DEPRECATED)
     * This method is kept for backward compatibility but should not be used
     * as it causes issues when multiple restaurants share the same owner
     */
    private function updateRestaurantWaitingCount(int $ownerId): void
    {
        try {
            // Get the first restaurant for this owner (temporary fix)
            $restaurant = \App\Models\Restaurant::where('owner_id', $ownerId)->first();

            if (!$restaurant) {
                return;
            }

            $count = RestaurantUser::where('added_by', $ownerId)
                ->waiting()
                ->today()
                ->sum('total_users_count') ?? 0;

            // Update only this specific restaurant's current_waiting_count
            $restaurant->update(['current_waiting_count' => $count]);

        } catch (\Exception $e) {
            \Log::error('Failed to update restaurant waiting count: ' . $e->getMessage());
        }
    }

    public function specificRestaurant(Request $request)
    {
        $restaurant = \App\Models\Restaurant::where('id', $request->user()->id)->first();
        return response()->json([
            'success' => true,
            'data' => $restaurant,
            'message' => 'Restaurant retrieved successfully',
        ]);
    }
}
