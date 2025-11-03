<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TransactionController extends Controller
{
    /**
     * Display a listing of transactions.
     * Admin only.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');
            $search = $request->get('search');

            $query = Transaction::with(['user', 'subscriptionPlan', 'userSubscription'])
                ->orderBy('created_at', 'desc');

            // Filter by status
            if ($status) {
                $query->where('status', $status);
            }

            // Search by restaurant name, plan name, or transaction ID
            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('restaurant_name', 'like', "%{$search}%")
                      ->orWhere('plan_name', 'like', "%{$search}%")
                      ->orWhere('transaction_id', 'like', "%{$search}%")
                      ->orWhere('razorpay_payment_id', 'like', "%{$search}%");
                });
            }

            $transactions = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $transactions->items(),
                'pagination' => [
                    'current_page' => $transactions->currentPage(),
                    'last_page' => $transactions->lastPage(),
                    'per_page' => $transactions->perPage(),
                    'total' => $transactions->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transactions.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified transaction.
     * Admin only.
     */
    public function show(Request $request, Transaction $transaction): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $transaction->load(['user', 'subscriptionPlan', 'userSubscription']);

            return response()->json([
                'success' => true,
                'data' => $transaction,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transaction statistics.
     * Admin only.
     */
    public function stats(Request $request): JsonResponse
    {
        try {
            // Check if user is admin
            if (!$request->user() || !$request->user()->is_admin) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized. Admin access required.',
                ], 403);
            }

            $currentMonth = now();
            $lastMonth = now()->subMonth();

            // Current month stats
            $stats = [
                'total_transactions' => Transaction::count(),
                'completed_transactions' => Transaction::completed()->count(),
                'pending_transactions' => Transaction::pending()->count(),
                'failed_transactions' => Transaction::failed()->count(),
                'total_revenue' => Transaction::completed()->sum('amount'),
                'today_revenue' => Transaction::completed()
                    ->whereDate('payment_date', today())
                    ->sum('amount'),
                'this_month_revenue' => Transaction::completed()
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
            ];

            // Last month stats for comparison
            $lastMonthStats = [
                'total_transactions' => Transaction::where('created_at', '<', $currentMonth->startOfMonth())->count(),
                'completed_transactions' => Transaction::completed()
                    ->where('created_at', '<', $currentMonth->startOfMonth())->count(),
                'total_revenue' => Transaction::completed()
                    ->whereMonth('payment_date', $lastMonth->month)
                    ->whereYear('payment_date', $lastMonth->year)
                    ->sum('amount'),
            ];

            // Calculate growth percentages
            $stats['total_transactions_growth'] = $this->calculateGrowthPercentage(
                $lastMonthStats['total_transactions'],
                $stats['total_transactions']
            );
            $stats['completed_transactions_growth'] = $this->calculateGrowthPercentage(
                $lastMonthStats['completed_transactions'],
                $stats['completed_transactions']
            );
            $stats['revenue_growth'] = $this->calculateGrowthPercentage(
                $lastMonthStats['total_revenue'],
                $stats['this_month_revenue']
            );

            // Calculate average transaction value
            $stats['avg_transaction'] = $stats['completed_transactions'] > 0
                ? round($stats['total_revenue'] / $stats['completed_transactions'], 2)
                : 0;

            // Calculate last month's average for comparison
            $lastMonthAvg = $lastMonthStats['completed_transactions'] > 0
                ? round($lastMonthStats['total_revenue'] / $lastMonthStats['completed_transactions'], 2)
                : 0;

            $stats['avg_transaction_growth'] = $this->calculateGrowthPercentage(
                $lastMonthAvg,
                $stats['avg_transaction']
            );

            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction statistics.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Calculate growth percentage between two values
     */
    private function calculateGrowthPercentage($oldValue, $newValue)
    {
        if ($oldValue == 0) {
            return $newValue > 0 ? 100 : 0;
        }

        $growth = (($newValue - $oldValue) / $oldValue) * 100;
        return round($growth, 1);
    }
}
