<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class RestaurantController extends Controller
{
    /**
     * Get all restaurants for admin
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $query = Restaurant::with('owner');

            // Add search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->search($search);
            }

            // Add filter by status
            if ($request->has('status')) {
                $status = $request->get('status');
                if ($status === 'active') {
                    $query->where('is_active', true);
                } elseif ($status === 'inactive') {
                    $query->where('is_active', false);
                }
            }

            $restaurants = $query->orderBy('created_at', 'desc')->paginate(15);

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => 'Restaurants retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurants: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get public restaurants list (for non-authenticated users)
     */
    public function publicList(Request $request): JsonResponse
    {
        try {
            // New filtering logic: Show restaurants that are either:
            // 1. Open (is_active = true), OR
            // 2. Closed but with waiting customers (is_active = false AND current_waiting_count > 0)
            $query = Restaurant::where(function ($q) {
                $q->where('is_active', true)
                  ->orWhere(function ($subQ) {
                      $subQ->where('is_active', false)
                           ->where('current_waiting_count', '>', 0);
                  });
            })->with('owner:id,name');

            // Add search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->search($search);
            }

            // Add location-based filtering
            if ($request->has('latitude') && $request->has('longitude')) {
                $latitude = $request->get('latitude');
                $longitude = $request->get('longitude');
                $radius = $request->get('radius', 10); // Default 10km radius

                // Using Haversine formula for distance calculation
                $query->selectRaw("
                    *,
                    (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance
                ", [$latitude, $longitude, $latitude])
                ->having('distance', '<=', $radius)
                ->orderBy('distance');
            } else {
                $query->orderBy('name');
            }

            $restaurants = $query->paginate(20);

            // Transform data for public consumption
            $restaurants->getCollection()->transform(function ($restaurant) {
                return [
                    'id' => $restaurant->id,
                    'name' => $restaurant->name,
                    'location' => $restaurant->location,
                    'full_address' => $restaurant->full_address,
                    'address_line_1' => $restaurant->address_line_1,
                    'address_line_2' => $restaurant->address_line_2,
                    'city' => $restaurant->city,
                    'state' => $restaurant->state,
                    'country' => $restaurant->country,
                    'postal_code' => $restaurant->postal_code,
                    'contact_number' => $restaurant->contact_number,
                    'profile' => $restaurant->profile ? Storage::url($restaurant->profile) : null,
                    'current_waiting_count' => $restaurant->current_waiting_count,
                    'distance' => isset($restaurant->distance) ? round($restaurant->distance, 2) : null,
                    'owner_name' => $restaurant->display_owner_name,
                    'description' => $restaurant->description,
                    'is_active' => $restaurant->is_active,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $restaurants,
                'message' => 'Public restaurants retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurants: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a new restaurant
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'location' => 'sometimes|string',
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'owner_id' => 'required|exists:users,id',
                'owner_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'boolean',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->only([
                'name', 'contact_number', 'location',
                'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code',
                'latitude', 'longitude', 'owner_id', 'owner_name', 'description'
            ]);
            // Ensure 'location' is always set (concatenate address if missing)
            if (empty($data['location'])) {
                $parts = array_filter([
                    $data['address_line_1'] ?? '',
                    $data['city'] ?? '',
                    $data['state'] ?? '',
                    $data['country'] ?? ''
                ]);
                $data['location'] = implode(', ', $parts);
            }
            $data['is_active'] = $request->get('is_active', true);

            // Handle profile image upload
            if ($request->hasFile('profile')) {
                $path = $request->file('profile')->store('restaurants', 'public');
                $data['profile'] = $path;
            }

            $restaurant = Restaurant::create($data);
            $restaurant->load('owner');

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => 'Restaurant created successfully',
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create restaurant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show a specific restaurant
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $restaurant = Restaurant::with('owner')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => 'Restaurant retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update a restaurant
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $restaurant = Restaurant::findOrFail($id);

            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|string|max:255',
                'contact_number' => 'sometimes|string|max:20',
                'location' => 'sometimes|string',
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'owner_id' => 'sometimes|exists:users,id',
                'owner_name' => 'nullable|string|max:255',
                'description' => 'nullable|string',
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
                'is_active' => 'boolean',
                'current_waiting_count' => 'integer|min:0',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->only([
                'name', 'contact_number', 'location',
                'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code',
                'latitude', 'longitude', 'owner_id', 'owner_name', 'description',
                'is_active', 'current_waiting_count'
            ]);

            // Handle profile image upload
            if ($request->hasFile('profile')) {
                // Delete old profile if exists
                if ($restaurant->profile && Storage::disk('public')->exists($restaurant->profile)) {
                    Storage::disk('public')->delete($restaurant->profile);
                }

                $path = $request->file('profile')->store('restaurants', 'public');
                $data['profile'] = $path;
            }

            $restaurant->update($data);
            $restaurant->load('owner');

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => 'Restaurant updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete a restaurant
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $restaurant = Restaurant::findOrFail($id);

            // Delete profile image if exists
            if ($restaurant->profile && Storage::disk('public')->exists($restaurant->profile)) {
                Storage::disk('public')->delete($restaurant->profile);
            }

            $restaurant->delete();

            return response()->json([
                'success' => true,
                'message' => 'Restaurant deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete restaurant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get current user's restaurant
     */
    public function getMyRestaurant(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $restaurant = Restaurant::where('owner_id', $user->id)->first();

            if (!$restaurant) {
                return response()->json([
                    'success' => true,
                    'message' => 'No restaurant found',
                    'data' => null,
                ], 200);
            }

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => 'Restaurant retrieved successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve restaurant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create or update current user's restaurant
     */
    public function saveMyRestaurant(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $restaurant = Restaurant::where('owner_id', $user->id)->first();

            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'contact_number' => 'required|string|max:20',
                'location' => 'nullable|string',
                'address_line_1' => 'nullable|string|max:255',
                'address_line_2' => 'nullable|string|max:255',
                'city' => 'nullable|string|max:100',
                'state' => 'nullable|string|max:100',
                'country' => 'nullable|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'description' => 'nullable|string',
                'operating_hours' => 'nullable|string|max:255',
                'cuisine_type' => 'nullable|string|max:100',
                // 'website' => 'nullable|max:255', // Temporarily removed for testing
                'owner_name' => 'nullable|string|max:255',
                'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($validator->fails()) {
                \Log::error('Restaurant profile validation failed', [
                    'errors' => $validator->errors(),
                    'request_data' => $request->except(['profile']), // Exclude file for logging
                    'has_profile_file' => $request->hasFile('profile')
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            $data = $request->only([
                'name', 'contact_number', 'location',
                'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code',
                'latitude', 'longitude', 'description', 'operating_hours', 'cuisine_type', 'owner_name'
            ]);
            // Ensure 'location' is always set (concatenate address if missing)
            if (empty($data['location'])) {
                $parts = array_filter([
                    $data['address_line_1'] ?? '',
                    $data['city'] ?? '',
                    $data['state'] ?? '',
                    $data['country'] ?? ''
                ]);
                $data['location'] = implode(', ', $parts);
            }

            // Set owner information
            $data['owner_id'] = $user->id;

            // Handle owner_name carefully
            $requestOwnerName = $request->input('owner_name');
            if (!$request->has('owner_name') || empty(trim($requestOwnerName ?? ''))) {
                $data['owner_name'] = $user->name;
            } else {
                $data['owner_name'] = trim($requestOwnerName);
            }

            $data['is_active'] = true; // Default to active for user-created restaurants

            // Handle profile image upload
            if ($request->hasFile('profile')) {
                // Delete old profile if exists and updating
                if ($restaurant && $restaurant->profile && Storage::disk('public')->exists($restaurant->profile)) {
                    Storage::disk('public')->delete($restaurant->profile);
                }

                $path = $request->file('profile')->store('restaurants', 'public');
                $data['profile'] = $path;
            }

            if ($restaurant) {
                // Update existing restaurant
                $restaurant->update($data);
                $message = 'Restaurant updated successfully';
            } else {
                // Create new restaurant
                $restaurant = Restaurant::create($data);
                $message = 'Restaurant created successfully';
            }

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => $message,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to save restaurant: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle restaurant active status (Admin only)
     */
    public function toggleStatus(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $restaurant = Restaurant::findOrFail($id);
            $restaurant->update(['is_active' => !$restaurant->is_active]);
            $restaurant->load('owner');

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => 'Restaurant status updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant status: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle own restaurant status (Restaurant owner)
     */
    public function toggleMyRestaurantStatus(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            // Find restaurant owned by this user
            $restaurant = Restaurant::where('owner_id', $user->id)->first();

            if (!$restaurant) {
                return response()->json([
                    'success' => false,
                    'message' => 'No restaurant found for this user.',
                ], 404);
            }

            $restaurant->update(['operational_status' => !$restaurant->operational_status]);
            $restaurant->load('owner');

            return response()->json([
                'success' => true,
                'data' => $restaurant,
                'message' => 'Restaurant status updated successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant status: ' . $e->getMessage(),
            ], 500);
        }
    }
}
