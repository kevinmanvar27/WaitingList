<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AdminSubscriptionController extends Controller
{
    /**
     * Display a listing of subscription plans.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please log in to access this feature.',
                ], 401);
            }

            if (!Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin access required.',
                ], 403);
            }

            $plans = SubscriptionPlan::ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $plans,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching subscription plans: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription plans.',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    /**
     * Store a newly created subscription plan.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'duration_days' => 'required|integer|min:1',
                'price' => 'required|numeric|min:0',
                'is_enabled' => 'boolean',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $plan = SubscriptionPlan::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan created successfully.',
                'data' => $plan,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create subscription plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified subscription plan.
     */
    public function show(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $plan = SubscriptionPlan::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $plan,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified subscription plan.
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $plan = SubscriptionPlan::findOrFail($id);

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'duration_days' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0',
                'is_enabled' => 'sometimes|boolean',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $plan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan updated successfully.',
                'data' => $plan->fresh(),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update subscription plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified subscription plan.
     */
    public function destroy(Request $request, $id): JsonResponse
    {
        try {
            // Check if user is admin
            if (!Auth::check() || !Auth::user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $plan = SubscriptionPlan::findOrFail($id);

            // Check if plan has active subscriptions
            $activeSubscriptionsCount = $plan->activeSubscriptions()->count();
            if ($activeSubscriptionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete plan. It has {$activeSubscriptionsCount} active subscription(s).",
                ], 422);
            }

            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete subscription plan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
