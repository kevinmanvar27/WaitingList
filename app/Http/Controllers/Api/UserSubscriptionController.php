<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPlan;
use App\Models\UserSubscription;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Razorpay\Api\Api;

class UserSubscriptionController extends Controller
{
    /**
     * Display user's subscription history.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $subscriptions = $user->subscriptions()
                ->with('subscriptionPlan')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $subscriptions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscriptions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Purchase a subscription plan.
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'subscription_plan_id' => 'required|exists:subscription_plans,id',
                'payment_method' => 'nullable|string',
                'transaction_id' => 'required|string',
                'payment_details' => 'nullable|array',
                'payment_details.razorpay_payment_id' => 'nullable|string',
                'payment_details.razorpay_order_id' => 'nullable|string',
                'payment_details.razorpay_signature' => 'nullable|string',
                'payment_details.amount_paid' => 'nullable|numeric',
                'payment_details.currency' => 'nullable|string',
            ]);

            $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

            // Check if plan is enabled
            if (!$plan->is_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'This subscription plan is not available.',
                ], 422);
            }

            // Check if user already has an active subscription
            $activeSubscription = $user->activeSubscription();
            if ($activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active subscription.',
                    'data' => [
                        'active_subscription' => $activeSubscription,
                    ],
                ], 422);
            }

            // Create the subscription and transaction in a database transaction
            $result = DB::transaction(function () use ($user, $plan, $validated) {
                // Create the subscription
                $subscription = UserSubscription::createForUser($user, $plan, [
                    'payment_method' => $validated['payment_method'] ?? 'razorpay',
                    'transaction_id' => $validated['transaction_id'],
                ]);

                // Create transaction record
                $transaction = Transaction::createForSubscription($user, $plan, $subscription, [
                    'payment_method' => $validated['payment_method'] ?? 'razorpay',
                    'transaction_id' => $validated['transaction_id'],
                    'razorpay_payment_id' => $validated['payment_details']['razorpay_payment_id'] ?? null,
                    'razorpay_order_id' => $validated['payment_details']['razorpay_order_id'] ?? null,
                    'razorpay_signature' => $validated['payment_details']['razorpay_signature'] ?? null,
                    'payment_details' => $validated['payment_details'] ?? null,
                ]);

                return [
                    'subscription' => $subscription->load('subscriptionPlan'),
                    'transaction' => $transaction,
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Subscription purchased successfully!',
                'data' => $result['subscription'],
                'transaction' => $result['transaction'],
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
                'message' => 'Failed to purchase subscription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's current subscription status.
     */
    public function status(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $activeSubscription = $user->activeSubscription();
            $hasPremiumAccess = $user->hasPremiumAccess();

            return response()->json([
                'success' => true,
                'data' => [
                    'has_premium_access' => $hasPremiumAccess,
                    'active_subscription' => $activeSubscription,
                    'subscription_required' => !$hasPremiumAccess,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription status.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancel user's active subscription.
     */
    public function cancel(Request $request): JsonResponse
    {
        try {
            $user = $request->user();
            $activeSubscription = $user->activeSubscription();

            if (!$activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active subscription found.',
                ], 404);
            }

            $activeSubscription->markAsCancelled();

            return response()->json([
                'success' => true,
                'message' => 'Subscription cancelled successfully.',
                'data' => $activeSubscription->fresh(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel subscription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified subscription.
     */
    public function show(Request $request, UserSubscription $userSubscription): JsonResponse
    {
        try {
            // Ensure user can only view their own subscriptions
            if ($userSubscription->user_id !== $request->user()->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized.',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $userSubscription->load('subscriptionPlan'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch subscription.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user's transaction history
     */
    public function getUserTransactions(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $transactions = Transaction::where('user_id', $user->id)
                ->with(['subscriptionPlan', 'userSubscription'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $transactions,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction history.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create Razorpay order for payment
     */
    public function createRazorpayOrder(Request $request): JsonResponse
    {
        try {
            $user = $request->user();

            $validated = $request->validate([
                'subscription_plan_id' => 'required|exists:subscription_plans,id',
            ]);

            $plan = SubscriptionPlan::findOrFail($validated['subscription_plan_id']);

            // Check if plan is enabled
            if (!$plan->is_enabled) {
                return response()->json([
                    'success' => false,
                    'message' => 'This subscription plan is not available.',
                ], 422);
            }

            // Check if user already has an active subscription
            $activeSubscription = $user->activeSubscription();
            if ($activeSubscription) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already have an active subscription.',
                    'data' => [
                        'active_subscription' => $activeSubscription,
                    ],
                ], 422);
            }

            // Initialize Razorpay API
            $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));

            // Create order data
            $orderData = [
                'receipt' => 'txn_' . time() . '_' . $user->id,
                'amount' => $plan->price * 100, // Amount in paise
                'currency' => 'INR',
                'notes' => [
                    'user_id' => $user->id,
                    'plan_id' => $plan->id,
                    'plan_name' => $plan->name,
                ],
            ];

            // Create order
            $razorpayOrder = $api->order->create($orderData);

            return response()->json([
                'success' => true,
                'data' => [
                    'order_id' => $razorpayOrder->id,
                    'amount' => $razorpayOrder->amount,
                    'currency' => $razorpayOrder->currency,
                    'plan' => $plan,
                ],
            ]);

        } catch (\Razorpay\Api\Errors\Error $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order.',
                'error' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create payment order.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
