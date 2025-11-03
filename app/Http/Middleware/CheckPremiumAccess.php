<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\SubscriptionPlan;

class CheckPremiumAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!$request->user()) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required.',
                'premium_required' => true,
            ], 401);
        }

        $user = $request->user();

        // Admin users always have premium access
        if ($user->is_admin) {
            return $next($request);
        }

        // Check if user has premium access
        if (!$user->hasPremiumAccess()) {
            // Get available subscription plans for the response
            $availablePlans = SubscriptionPlan::enabled()->ordered()->get();

            // If no plans are enabled, allow free access to premium features
            if ($availablePlans->count() === 0) {
                // Optionally, you can set a flag/message in the request for downstream use
                $request->attributes->set('premium_free', true);
                return $next($request);
            }

            return response()->json([
                'success' => false,
                'message' => 'Premium subscription required to access this feature.',
                'premium_required' => true,
                'subscription_status' => [
                    'has_premium_access' => false,
                    'active_subscription' => null,
                    'available_plans' => $availablePlans,
                ],
            ], 403);
        }

        return $next($request);
    }
}
