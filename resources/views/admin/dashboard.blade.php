@extends('admin.layout')

@section('title', 'Dashboard')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="app-container">
    <!-- Page Header -->
    <div class="section">
        <h1>Admin Dashboard</h1>
        <p class="mt-2 text-lg text-gray-600">Welcome back! Here's what's happening with your restaurant management system.</p>
    </div>

    <!-- Statistics Cards -->
    <div class="section">
        <h2 class="mb-4">System Overview</h2>
        <div class="grid-stats">
            <!-- Restaurant Owners Card -->
            <div class="card animate-scale-in">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Restaurant Owners</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_restaurant_owners'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <i data-lucide="users" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        @if($stats['restaurant_owners_growth'] >= 0)
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+{{ $stats['restaurant_owners_growth'] }}%</span>
                        @else
                            <i data-lucide="trending-down" class="w-4 h-4 text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ $stats['restaurant_owners_growth'] }}%</span>
                        @endif
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Restaurant Users Card -->
            <div class="card animate-scale-in" style="animation-delay: 0.1s">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Restaurant Users</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_restaurant_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-secondary rounded-lg flex items-center justify-center">
                            <i data-lucide="user-check" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        @if($stats['restaurant_users_growth'] >= 0)
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+{{ $stats['restaurant_users_growth'] }}%</span>
                        @else
                            <i data-lucide="trending-down" class="w-4 h-4 text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ $stats['restaurant_users_growth'] }}%</span>
                        @endif
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Total Users Card -->
            <div class="card animate-scale-in" style="animation-delay: 0.2s">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Users</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_users'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-primary rounded-lg flex items-center justify-center">
                            <i data-lucide="user-plus" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        @if($stats['total_users_growth'] >= 0)
                            <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1"></i>
                            <span class="text-green-600 font-medium">+{{ $stats['total_users_growth'] }}%</span>
                        @else
                            <i data-lucide="trending-down" class="w-4 h-4 text-red-500 mr-1"></i>
                            <span class="text-red-600 font-medium">{{ $stats['total_users_growth'] }}%</span>
                        @endif
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <!-- Recent Registrations Card -->
            <div class="card animate-scale-in" style="animation-delay: 0.3s">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">This Week</p>
                            <p class="text-3xl font-bold text-gray-900">{{ $stats['recent_registrations'] }}</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <i data-lucide="clock" class="w-4 h-4 text-gray-500 mr-1"></i>
                        <span class="text-gray-600">Last 7 days</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Statistics -->
    <div class="section">
        <div class="flex justify-between items-center mb-4">
            <h2>Financial Overview</h2>
            <a href="{{ route('admin.transactions') }}" class="btn btn-outline">
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                View All Transactions
            </a>
        </div>
        
        <div class="grid-stats">
            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalTransactions">Loading...</p>
                        </div>
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="credit-card" class="w-5 h-5 text-green-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl font-bold text-gray-900" id="completedTransactions">Loading...</p>
                        </div>
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-5 h-5 text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-2xl font-bold text-gray-900" id="totalRevenue">Loading...</p>
                        </div>
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="dollar-sign" class="w-5 h-5 text-yellow-600"></i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Today's Revenue</p>
                            <p class="text-2xl font-bold text-gray-900" id="todayRevenue">Loading...</p>
                        </div>
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-5 h-5 text-purple-600"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="section">
        <div class="flex justify-between items-center mb-4">
            <h2>Analytics Overview</h2>
            <div class="flex space-x-2">
                <select id="chartPeriod" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="6months">Last 6 Months</option>
                    <option value="1year">Last Year</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- User & Transaction Trends Chart -->
            <div class="card">
                <div class="card-header">
                    <h3>User & Transaction Trends</h3>
                    <p class="text-sm text-gray-600 mt-1">Track user registrations and transaction volume</p>
                </div>
                <div class="card-content">
                    <div class="h-80">
                        <canvas id="trendsChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="card">
                <div class="card-header">
                    <h3>Revenue Trends</h3>
                    <p class="text-sm text-gray-600 mt-1">Monitor revenue growth over time</p>
                </div>
                <div class="card-content">
                    <div class="h-80">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurant Owners Section -->
    <div class="section">
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h3>Restaurant Owners</h3>
                        <p class="text-sm text-gray-600 mt-1">Manage restaurant owners and their associated users</p>
                    </div>
                    <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add New Owner
                    </a>
                </div>
            </div>
            <div class="card-content">
                @if($restaurantOwners->isEmpty())
                    <div class="text-center py-12">
                        <i data-lucide="users" class="w-12 h-12 text-gray-400 mx-auto mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No restaurant owners yet</h3>
                        <p class="text-gray-600 mb-4">Get started by adding your first restaurant owner.</p>
                        <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add Restaurant Owner
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Owner</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Email</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Users</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                                    <th class="text-right py-3 px-4 font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($restaurantOwners as $owner)
                                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                        <td class="py-4 px-4">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                                                    <span class="text-sm font-medium text-gray-700">
                                                        {{ substr($owner->name, 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ $owner->name }}</p>
                                                    <p class="text-sm text-gray-600">Owner</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-4 px-4 text-gray-600">{{ $owner->email }}</td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                {{ $owner->restaurant_users_count }} users
                                            </span>
                                        </td>
                                        <td class="py-4 px-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        </td>
                                        <td class="py-4 px-4 text-right">
                                            <a href="{{ route('admin.restaurant-users', $owner->id) }}" 
                                               class="btn btn-ghost text-sm">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    @if($restaurantOwners->hasPages())
                        <div class="mt-6">
                            {{ $restaurantOwners->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="section">
        <h2 class="mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <a href="{{ route('admin.restaurants.create') }}" class="card hover:shadow-xl transition-all duration-200 group">
                <div class="card-content text-center">
                    <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="building" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">Add Restaurant</h3>
                    <p class="text-gray-600 text-sm">Create a new restaurant and assign an owner</p>
                </div>
            </a>

            <a href="{{ route('admin.transactions') }}" class="card hover:shadow-xl transition-all duration-200 group">
                <div class="card-content text-center">
                    <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="credit-card" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">View Transactions</h3>
                    <p class="text-gray-600 text-sm">Monitor all financial transactions</p>
                </div>
            </a>

            <a href="{{ route('admin.settings') }}" class="card hover:shadow-xl transition-all duration-200 group">
                <div class="card-content text-center">
                    <div class="w-16 h-16 bg-purple-500 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform">
                        <i data-lucide="settings" class="w-8 h-8 text-white"></i>
                    </div>
                    <h3 class="text-lg font-semibold mb-2">System Settings</h3>
                    <p class="text-gray-600 text-sm">Configure system preferences</p>
                </div>
            </a>
        </div>
    </div>
</div>

<script>
let trendsChart = null;
let revenueChart = null;

// Load transaction statistics
async function loadTransactionStats() {
    try {
        const response = await fetch('/admin/transactions/stats', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        const data = await response.json();

        if (data.success) {
            const stats = data.data;
            document.getElementById('totalTransactions').textContent = stats.total_transactions || 0;
            document.getElementById('completedTransactions').textContent = stats.completed_transactions || 0;
            document.getElementById('totalRevenue').textContent = `₹${(stats.total_revenue || 0).toLocaleString()}`;
            document.getElementById('todayRevenue').textContent = `₹${(stats.today_revenue || 0).toLocaleString()}`;
        }
    } catch (error) {
        console.error('Error loading transaction stats:', error);
        // Set fallback values
        document.getElementById('totalTransactions').textContent = '0';
        document.getElementById('completedTransactions').textContent = '0';
        document.getElementById('totalRevenue').textContent = '₹0';
        document.getElementById('todayRevenue').textContent = '₹0';
    }
}

// Load chart data
async function loadChartData(period = '7days') {
    try {
        const response = await fetch(`/admin/chart-data?period=${period}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        const data = await response.json();

        if (data.success) {
            updateCharts(data.data);
        }
    } catch (error) {
        console.error('Error loading chart data:', error);
    }
}

// Update charts with new data
function updateCharts(data) {
    // Update trends chart (User Registrations & Transactions)
    if (trendsChart) {
        trendsChart.destroy();
    }

    const trendsCtx = document.getElementById('trendsChart').getContext('2d');
    trendsChart = new Chart(trendsCtx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: data.datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });

    // Update revenue chart
    if (revenueChart) {
        revenueChart.destroy();
    }

    const revenueCtx = document.getElementById('revenueChart').getContext('2d');
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: data.revenue.labels,
            datasets: [{
                label: 'Revenue (₹)',
                data: data.revenue.data,
                borderColor: data.revenue.borderColor,
                backgroundColor: data.revenue.backgroundColor,
                tension: data.revenue.tension,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₹' + value.toLocaleString();
                        }
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
}

// Handle period change
function handlePeriodChange() {
    const period = document.getElementById('chartPeriod').value;
    loadChartData(period);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadTransactionStats();
    loadChartData();
    lucide.createIcons();

    // Add event listener for period change
    document.getElementById('chartPeriod').addEventListener('change', handlePeriodChange);
});
</script>
@endsection
