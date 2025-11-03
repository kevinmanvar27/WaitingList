<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RestaurantUser;
use App\Models\Settings;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    /**
     * Show the admin login form
     */
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle admin login
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password) || !$user->is_admin) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect or you are not an admin.'],
            ]);
        }

        Auth::login($user);

        return redirect()->route('admin.dashboard');
    }

    /**
     * Show admin dashboard
     */
    public function dashboard()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        // Get dashboard statistics with month-over-month comparisons
        $stats = $this->getDashboardStats();

        // Get restaurant owners with their user counts
        $restaurantOwners = User::whereHas('restaurantUsers')
            ->withCount('restaurantUsers')
            ->with(['restaurantUsers' => function ($query) {
                $query->latest()->take(5);
            }])
            ->orderBy('restaurant_users_count', 'desc')
            ->paginate(10);

        return view('admin.dashboard', compact('stats', 'restaurantOwners'));
    }

    /**
     * Get dashboard statistics with growth percentages
     */
    private function getDashboardStats()
    {
        $currentMonth = now();
        $lastMonth = now()->subMonth();

        // Current month stats
        $currentStats = [
            'total_restaurant_owners' => User::whereHas('restaurantUsers')->count(),
            'total_restaurant_users' => RestaurantUser::count(),
            'total_users' => User::count(),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(7))->count(),
        ];

        // Last month stats for comparison
        $lastMonthStats = [
            'total_restaurant_owners' => User::whereHas('restaurantUsers')
                ->where('created_at', '<', $currentMonth->startOfMonth())
                ->count(),
            'total_restaurant_users' => RestaurantUser::where('created_at', '<', $currentMonth->startOfMonth())->count(),
            'total_users' => User::where('created_at', '<', $currentMonth->startOfMonth())->count(),
        ];

        // Calculate growth percentages
        $stats = $currentStats;
        $stats['restaurant_owners_growth'] = $this->calculateGrowthPercentage(
            $lastMonthStats['total_restaurant_owners'],
            $currentStats['total_restaurant_owners']
        );
        $stats['restaurant_users_growth'] = $this->calculateGrowthPercentage(
            $lastMonthStats['total_restaurant_users'],
            $currentStats['total_restaurant_users']
        );
        $stats['total_users_growth'] = $this->calculateGrowthPercentage(
            $lastMonthStats['total_users'],
            $currentStats['total_users']
        );

        return $stats;
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

    /**
     * Show restaurant users for a specific owner
     */
    public function showRestaurantUsers($ownerId)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $owner = User::findOrFail($ownerId);

        $restaurantUsers = RestaurantUser::where('added_by', $ownerId)
            ->with('addedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.restaurant-users', compact('owner', 'restaurantUsers'));
    }

    /**
     * Show settings page
     */
    public function settings()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $settings = Settings::getInstance();
        return view('admin.settings', compact('settings'));
    }

    /**
     * Show transactions page
     */
    public function transactions()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        return view('admin.transactions');
    }

    /**
     * Get transactions data (JSON response for AJAX)
     */
    public function transactionsData(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');
            $search = $request->get('search');

            $query = \App\Models\Transaction::with(['user', 'subscriptionPlan', 'userSubscription'])
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
                    'from' => $transactions->firstItem(),
                    'to' => $transactions->lastItem(),
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
     * Get transaction statistics (JSON response for AJAX)
     */
    public function transactionsStats(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $currentMonth = now();
            $lastMonth = now()->subMonth();

            // Current month stats
            $stats = [
                'total_transactions' => \App\Models\Transaction::count(),
                'completed_transactions' => \App\Models\Transaction::where('status', 'completed')->count(),
                'pending_transactions' => \App\Models\Transaction::where('status', 'pending')->count(),
                'failed_transactions' => \App\Models\Transaction::where('status', 'failed')->count(),
                'total_revenue' => \App\Models\Transaction::where('status', 'completed')->sum('amount'),
                'today_revenue' => \App\Models\Transaction::where('status', 'completed')
                    ->whereDate('payment_date', today())
                    ->sum('amount'),
                'this_month_revenue' => \App\Models\Transaction::where('status', 'completed')
                    ->whereMonth('payment_date', now()->month)
                    ->whereYear('payment_date', now()->year)
                    ->sum('amount'),
            ];

            // Last month stats for comparison
            $lastMonthStats = [
                'total_transactions' => \App\Models\Transaction::where('created_at', '<', $currentMonth->startOfMonth())->count(),
                'completed_transactions' => \App\Models\Transaction::where('status', 'completed')
                    ->where('created_at', '<', $currentMonth->startOfMonth())->count(),
                'total_revenue' => \App\Models\Transaction::where('status', 'completed')
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
     * Update settings
     */
    public function updateSettings(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'application_name' => 'required|string|max:255',
            'app_version' => 'required|string|max:50',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,ico|max:1024',
            'app_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $settings = Settings::getInstance();
        $settings->application_name = $request->application_name;
        $settings->app_version = $request->app_version;

        // Handle file uploads
        foreach (['logo', 'favicon', 'app_logo'] as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($settings->$field && Storage::disk('public')->exists($settings->$field)) {
                    Storage::disk('public')->delete($settings->$field);
                }

                // Store new file
                $path = $request->file($field)->store('settings', 'public');
                $settings->$field = $path;
            }
        }

        Settings::where('id', $settings->id)->update([
            'application_name' => $settings->application_name,
            'app_version' => $settings->app_version,
            'logo' => $settings->logo,
            'favicon' => $settings->favicon,
            'app_logo' => $settings->app_logo,
        ]);

        return redirect()->route('admin.settings')->with('success', 'Settings updated successfully!');
    }

    /**
     * Show restaurants page
     */
    public function restaurants(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
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

        return view('admin.restaurants', compact('restaurants'));
    }

    /**
     * Get chart data for dashboard (JSON response for AJAX)
     */
    public function chartData(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $period = $request->get('period', '7days'); // 7days, 30days, 6months, 1year

            $chartData = [];

            switch ($period) {
                case '7days':
                    $chartData = $this->getLast7DaysData();
                    break;
                case '30days':
                    $chartData = $this->getLast30DaysData();
                    break;
                case '6months':
                    $chartData = $this->getLast6MonthsData();
                    break;
                case '1year':
                    $chartData = $this->getLast12MonthsData();
                    break;
                default:
                    $chartData = $this->getLast7DaysData();
            }

            return response()->json([
                'success' => true,
                'data' => $chartData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get last 7 days data for charts
     */
    private function getLast7DaysData()
    {
        $data = [];
        $labels = [];
        $userRegistrations = [];
        $transactions = [];
        $revenue = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M j');

            $userRegistrations[] = User::whereDate('created_at', $date)->count();
            $dayTransactions = \App\Models\Transaction::whereDate('created_at', $date)->count();
            $transactions[] = $dayTransactions;
            $revenue[] = \App\Models\Transaction::where('status', 'completed')
                ->whereDate('payment_date', $date)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Registrations',
                    'data' => $userRegistrations,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Transactions',
                    'data' => $transactions,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'revenue' => [
                'labels' => $labels,
                'data' => $revenue,
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'tension' => 0.4,
            ]
        ];
    }

    /**
     * Get last 30 days data for charts
     */
    private function getLast30DaysData()
    {
        $data = [];
        $labels = [];
        $userRegistrations = [];
        $transactions = [];
        $revenue = [];

        // Group by weeks for 30 days
        for ($i = 3; $i >= 0; $i--) {
            $startDate = now()->subWeeks($i + 1)->startOfWeek();
            $endDate = now()->subWeeks($i)->endOfWeek();
            $labels[] = $startDate->format('M j') . ' - ' . $endDate->format('M j');

            $userRegistrations[] = User::whereBetween('created_at', [$startDate, $endDate])->count();
            $transactions[] = \App\Models\Transaction::whereBetween('created_at', [$startDate, $endDate])->count();
            $revenue[] = \App\Models\Transaction::where('status', 'completed')
                ->whereBetween('payment_date', [$startDate, $endDate])
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Registrations',
                    'data' => $userRegistrations,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Transactions',
                    'data' => $transactions,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'revenue' => [
                'labels' => $labels,
                'data' => $revenue,
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'tension' => 0.4,
            ]
        ];
    }

    /**
     * Get last 6 months data for charts
     */
    private function getLast6MonthsData()
    {
        $data = [];
        $labels = [];
        $userRegistrations = [];
        $transactions = [];
        $revenue = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $userRegistrations[] = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $transactions[] = \App\Models\Transaction::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $revenue[] = \App\Models\Transaction::where('status', 'completed')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Registrations',
                    'data' => $userRegistrations,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Transactions',
                    'data' => $transactions,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'revenue' => [
                'labels' => $labels,
                'data' => $revenue,
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'tension' => 0.4,
            ]
        ];
    }

    /**
     * Get last 12 months data for charts
     */
    private function getLast12MonthsData()
    {
        $data = [];
        $labels = [];
        $userRegistrations = [];
        $transactions = [];
        $revenue = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $labels[] = $date->format('M Y');

            $userRegistrations[] = User::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $transactions[] = \App\Models\Transaction::whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->count();
            $revenue[] = \App\Models\Transaction::where('status', 'completed')
                ->whereMonth('payment_date', $date->month)
                ->whereYear('payment_date', $date->year)
                ->sum('amount');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'User Registrations',
                    'data' => $userRegistrations,
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4,
                ],
                [
                    'label' => 'Transactions',
                    'data' => $transactions,
                    'borderColor' => 'rgb(16, 185, 129)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4,
                ],
            ],
            'revenue' => [
                'labels' => $labels,
                'data' => $revenue,
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'tension' => 0.4,
            ]
        ];
    }

    /**
     * Get transaction-specific chart data (JSON response for AJAX)
     */
    public function transactionChartData(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        try {
            $period = $request->get('period', '7days');

            $chartData = [];

            switch ($period) {
                case '7days':
                    $chartData = $this->getTransactionLast7DaysData();
                    break;
                case '30days':
                    $chartData = $this->getTransactionLast30DaysData();
                    break;
                case '6months':
                    $chartData = $this->getTransactionLast6MonthsData();
                    break;
                case '1year':
                    $chartData = $this->getTransactionLast12MonthsData();
                    break;
                default:
                    $chartData = $this->getTransactionLast7DaysData();
            }

            return response()->json([
                'success' => true,
                'data' => $chartData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch transaction chart data: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get transaction data for last 7 days
     */
    private function getTransactionLast7DaysData()
    {
        $labels = [];
        $totalTransactions = [];
        $completedTransactions = [];
        $revenue = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $labels[] = $date->format('M j');

            $dayTotal = \App\Models\Transaction::whereDate('created_at', $date)->count();
            $dayCompleted = \App\Models\Transaction::where('status', 'completed')
                ->whereDate('created_at', $date)->count();
            $dayRevenue = \App\Models\Transaction::where('status', 'completed')
                ->whereDate('payment_date', $date)
                ->sum('amount');

            $totalTransactions[] = $dayTotal;
            $completedTransactions[] = $dayCompleted;
            $revenue[] = $dayRevenue;
        }

        return [
            'labels' => $labels,
            'transactions' => [
                'total' => $totalTransactions,
                'completed' => $completedTransactions,
            ],
            'revenue' => [
                'labels' => $labels,
                'data' => $revenue,
                'borderColor' => 'rgb(245, 158, 11)',
                'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                'tension' => 0.4,
            ]
        ];
    }

    /**
     * Show create restaurant form
     */
    public function createRestaurant()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $users = User::all();

        return view('admin.create-restaurant', compact('users'));
    }

    /**
     * Store new restaurant
     */
    public function storeRestaurant(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
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
            'owner_id' => 'nullable|exists:users,id',
            'owner_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'operational_status' => 'nullable|in:open,closed',
        ]);

        $data = $request->only([
            'name', 'contact_number', 'location',
            'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code',
            'latitude', 'longitude', 'owner_id', 'owner_name', 'description', 'operational_status'
        ]);
        $data['is_active'] = $request->has('is_active');

        // Handle profile image upload
        if ($request->hasFile('profile')) {
            $path = $request->file('profile')->store('restaurants', 'public');
            $data['profile'] = $path;
        }

        Restaurant::create($data);

        return redirect()->route('admin.restaurants')->with('success', 'Restaurant created successfully!');
    }

    /**
     * Toggle restaurant status
     */
    public function toggleRestaurantStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:active,inactive',
        ]);

        try {
            $restaurant = Restaurant::findOrFail($id);
            $restaurant->is_active = $request->status === 'active';
            $restaurant->save();

            return response()->json([
                'success' => true,
                'message' => 'Restaurant status updated successfully.',
                'status' => $restaurant->is_active ? 'active' : 'inactive'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle restaurant operational status
     */
    public function toggleOperationalStatus(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:open,closed',
        ]);

        try {
            $restaurant = Restaurant::findOrFail($id);
            $restaurant->operational_status = $request->status;
            $restaurant->save();

            return response()->json([
                'success' => true,
                'message' => 'Restaurant operational status updated successfully.',
                'status' => $restaurant->operational_status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update restaurant operational status.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Edit restaurant form
     */
    public function editRestaurant($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $restaurant = \App\Models\Restaurant::findOrFail($id);
        $users = User::all();

        return view('admin.create-restaurant', compact('restaurant', 'users'));
    }

    /**
     * Update restaurant
     */
    public function updateRestaurant(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
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
            'owner_id' => 'required',
            'owner_name' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'profile' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'operational_status' => 'nullable|in:open,closed',
        ]);

        $restaurant = \App\Models\Restaurant::findOrFail($id);
        $data = $request->only([
            'name', 'contact_number', 'location',
            'address_line_1', 'address_line_2', 'city', 'state', 'country', 'postal_code',
            'latitude', 'longitude', 'owner_id', 'owner_name', 'description', 'operational_status'
        ]);
        $data['is_active'] = $request->has('is_active');

        // Handle profile image upload
        if ($request->hasFile('profile')) {
            $path = $request->file('profile')->store('restaurants', 'public');
            $data['profile'] = $path;
        }

        $restaurant->update($data);

        return redirect()->route('admin.restaurants')->with('success', 'Restaurant updated successfully!');
    }

    /**
     * Show restaurant details for modal view
     */
    public function showRestaurant($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $restaurant = Restaurant::with('owner')->findOrFail($id);

        return view('admin.view-restaurant-partial', compact('restaurant'));
    }

    /**
     * Logout admin
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    /**
     * Clear the database (except admin users)
     */
    public function clearDatabase()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        try {
            // Disable foreign key checks for bulk operations
            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            // Truncate all tables except protected ones
            $tables = DB::select('SHOW TABLES');
            $protected = ['users', 'settings', 'migrations'];
            foreach ($tables as $table) {
                $table = reset($table);
                if (!in_array($table, $protected)) {
                    DB::table($table)->truncate();
                }
            }

            // Remove all non-admin users (preserve admin accounts)
            DB::table('users')->where('is_admin', false)->delete();

            // Re-enable foreign key checks
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            return response()->json(['success' => true, 'message' => 'Database cleared successfully!', 'redirect' => route('admin.login')]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Failed to clear database.', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Change admin password
     */
    public function changePassword(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }

        // Update password
        User::where('id', $user->id)->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->route('admin.settings', ['tab' => 'password_change', 'password_changed' => 'true'])->with('success', 'Password changed successfully!');
    }

    
}
