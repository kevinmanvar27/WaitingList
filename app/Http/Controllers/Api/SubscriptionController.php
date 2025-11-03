<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    /**
     * Display a listing of subscription plans.
     * For admin: shows all plans
     * For users: shows only enabled plans
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SubscriptionPlan::query();

            // If not admin, only show enabled plans
            if (!$request->user() || !$request->user()->is_admin) {
                $query->enabled();
            }

            $plans = $query->ordered()->get();

            return response()->json([
                'success' => true,
                'data' => $plans,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription plans.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created subscription plan.
     * Admin only.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->is_admin) {
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
    public function show(Request $request, SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            // If not admin and plan is disabled, don't show it
            if ((!$request->user() || !$request->user()->is_admin) && !$subscriptionPlan->is_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'Subscription plan not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $subscriptionPlan,
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
     * Admin only.
     */
    public function update(Request $request, SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'duration_days' => 'sometimes|required|integer|min:1',
                'price' => 'sometimes|required|numeric|min:0',
                'is_enabled' => 'sometimes|boolean',
                'description' => 'nullable|string',
                'features' => 'nullable|array',
                'sort_order' => 'nullable|integer|min:0',
            ]);

            $subscriptionPlan->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Subscription plan updated successfully.',
                'data' => $subscriptionPlan->fresh(),
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
     * Admin only.
     */
    public function destroy(Request $request, SubscriptionPlan $subscriptionPlan): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            // Check if plan has active subscriptions
            $activeSubscriptionsCount = $subscriptionPlan->activeSubscriptions()->count();
            if ($activeSubscriptionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot delete plan. It has {$activeSubscriptionsCount} active subscription(s).",
                ], 422);
            }

            $subscriptionPlan->delete();

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
