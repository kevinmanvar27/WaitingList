@extends('admin.layout')

@section('title', 'Transactions')

@section('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endsection

@section('content')
<div class="app-container">
    <!-- Page Header -->
    <div class="section">
        <h1>Transaction Management</h1>
        <p class="mt-2 text-lg text-gray-600">Monitor and manage all financial transactions across the platform</p>
    </div>

    <!-- Statistics Overview -->
    <div class="section">
        <div class="grid-stats">
            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                            <p class="text-3xl font-bold text-gray-900" id="totalRevenue">₹0</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="dollar-sign" class="w-6 h-6 text-green-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1" id="revenueGrowthIcon"></i>
                        <span class="text-green-600 font-medium" id="revenueGrowthText">+0%</span>
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                            <p class="text-3xl font-bold text-gray-900" id="totalTransactions">0</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="credit-card" class="w-6 h-6 text-primary"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1" id="transactionsGrowthIcon"></i>
                        <span class="text-green-600 font-medium" id="transactionsGrowthText">+0%</span>
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Success Rate</p>
                            <p class="text-3xl font-bold text-gray-900" id="successRate">0%</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-6 h-6 text-purple-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1" id="completedGrowthIcon"></i>
                        <span class="text-green-600 font-medium" id="completedGrowthText">+0%</span>
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Avg. Transaction</p>
                            <p class="text-3xl font-bold text-gray-900" id="avgTransaction">₹0</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-6 h-6 text-orange-600"></i>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-sm">
                        <i data-lucide="trending-up" class="w-4 h-4 text-green-500 mr-1" id="avgGrowthIcon"></i>
                        <span class="text-green-600 font-medium" id="avgGrowthText">+0%</span>
                        <span class="text-gray-500 ml-2">from last month</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="section">
        <div class="card">
            <div class="card-content">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label class="form-label">Search</label>
                        <input type="text" id="searchInput" placeholder="Search transactions..." 
                               class="form-input" onkeyup="filterTransactions()">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select id="statusFilter" class="form-input" onchange="filterTransactions()">
                            <option value="">All Status</option>
                            <option value="completed">Completed</option>
                            <option value="pending">Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Date Range</label>
                        <select id="dateFilter" class="form-input" onchange="filterTransactions()">
                            <option value="all">All Time</option>
                            <option value="today">Today</option>
                            <option value="week">This Week</option>
                            <option value="month">This Month</option>
                            <option value="year">This Year</option>
                        </select>
                    </div>
                    <button onclick="resetFilters()" class="btn btn-outline">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                        Reset
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="section">
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <div>
                        <h3>All Transactions</h3>
                        <p class="text-sm text-gray-600 mt-1">Showing <span id="transactionCount">0</span> transactions</p>
                    </div>
                    <button onclick="exportTransactions()" class="btn btn-outline">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export
                    </button>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-gray-200">
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Transaction ID</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">User</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Amount</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Status</th>
                            <th class="text-left py-3 px-4 font-medium text-gray-700">Date</th>
                            <th class="text-right py-3 px-4 font-medium text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="transactionsTableBody">
                        <tr>
                            <td colspan="6" class="text-center py-8">
                                <i data-lucide="loader" class="w-8 h-8 animate-spin mx-auto text-gray-400"></i>
                                <p class="text-gray-500 mt-2">Loading transactions...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <div class="flex justify-between items-center">
                    <p class="text-sm text-gray-600">
                        Showing <span id="showingFrom">0</span> to <span id="showingTo">0</span> of <span id="totalResults">0</span> results
                    </p>
                    <div id="pagination" class="flex space-x-2">
                        <!-- Pagination will be inserted here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Transaction Analytics Chart -->
    <div class="section">
        <div class="flex justify-between items-center mb-4">
            <h2>Transaction Analytics</h2>
            <div class="flex space-x-2">
                <select id="transactionChartPeriod" class="px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
                    <option value="7days">Last 7 Days</option>
                    <option value="30days">Last 30 Days</option>
                    <option value="6months">Last 6 Months</option>
                    <option value="1year">Last Year</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Transaction Volume Chart -->
            <div class="card">
                <div class="card-header">
                    <h3>Transaction Volume</h3>
                    <p class="text-sm text-gray-600 mt-1">Daily transaction counts and success rates</p>
                </div>
                <div class="card-content">
                    <div class="h-80">
                        <canvas id="transactionVolumeChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Revenue Breakdown Chart -->
            <div class="card">
                <div class="card-header">
                    <h3>Revenue Breakdown</h3>
                    <p class="text-sm text-gray-600 mt-1">Revenue distribution over time</p>
                </div>
                <div class="card-content">
                    <div class="h-80">
                        <canvas id="revenueBreakdownChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="section">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Recent Transactions -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Transactions</h3>
                    <p class="text-sm text-gray-600 mt-1">Latest 5 transactions</p>
                </div>
                <div class="card-content">
                    <div id="recentTransactionsList">
                        <div class="text-center py-8">
                            <i data-lucide="loader" class="w-8 h-8 animate-spin mx-auto text-gray-400"></i>
                            <p class="text-gray-500 mt-2">Loading recent transactions...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Top Users -->
            <div class="card">
                <div class="card-header">
                    <h3>Top Spenders</h3>
                    <p class="text-sm text-gray-600 mt-1">Users with highest transaction amounts</p>
                </div>
                <div class="card-content">
                    <div id="topUsersList">
                        <div class="text-center py-8">
                            <i data-lucide="loader" class="w-8 h-8 animate-spin mx-auto text-gray-400"></i>
                            <p class="text-gray-500 mt-2">Loading top users...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let allTransactions = [];
let currentPage = 1;
const itemsPerPage = 20;

// Load transaction data
async function loadTransactions() {
    try {
        const response = await fetch('/admin/transactions/data', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        const data = await response.json();
        
        if (data.success) {
            allTransactions = data.data;
            updateStatistics();
            renderTransactions();
            renderRecentActivity();
        }
    } catch (error) {
        console.error('Error loading transactions:', error);
    }
}

// Update statistics with growth percentages
async function updateStatistics() {
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

            // Update main statistics
            document.getElementById('totalRevenue').textContent = `₹${(stats.total_revenue || 0).toLocaleString()}`;
            document.getElementById('totalTransactions').textContent = (stats.total_transactions || 0).toLocaleString();
            document.getElementById('successRate').textContent = `${Math.round((stats.completed_transactions / stats.total_transactions) * 100) || 0}%`;
            document.getElementById('avgTransaction').textContent = `₹${(stats.avg_transaction || 0).toLocaleString()}`;

            // Update growth percentages
            updateGrowthIndicator('revenueGrowth', stats.revenue_growth || 0);
            updateGrowthIndicator('transactionsGrowth', stats.total_transactions_growth || 0);
            updateGrowthIndicator('completedGrowth', stats.completed_transactions_growth || 0);

            // Use the calculated average transaction growth from API
            updateGrowthIndicator('avgGrowth', stats.avg_transaction_growth || 0);
        }
    } catch (error) {
        console.error('Error loading transaction stats:', error);
        // Fallback to local calculation
        const totalRevenue = allTransactions.reduce((sum, t) => sum + (t.amount || 0), 0);
        const totalTransactions = allTransactions.length;
        const completedTransactions = allTransactions.filter(t => t.status === 'completed').length;
        const successRate = totalTransactions > 0 ? Math.round((completedTransactions / totalTransactions) * 100) : 0;
        const avgTransaction = totalTransactions > 0 ? Math.round(totalRevenue / totalTransactions) : 0;

        document.getElementById('totalRevenue').textContent = `₹${totalRevenue.toLocaleString()}`;
        document.getElementById('totalTransactions').textContent = totalTransactions.toLocaleString();
        document.getElementById('successRate').textContent = `${successRate}%`;
        document.getElementById('avgTransaction').textContent = `₹${avgTransaction.toLocaleString()}`;
    }
}

// Update growth indicator with proper styling
function updateGrowthIndicator(prefix, growthValue) {
    const iconElement = document.getElementById(prefix + 'Icon');
    const textElement = document.getElementById(prefix + 'Text');

    if (!iconElement || !textElement) return;

    if (growthValue >= 0) {
        iconElement.setAttribute('data-lucide', 'trending-up');
        iconElement.className = 'w-4 h-4 text-green-500 mr-1';
        textElement.className = 'text-green-600 font-medium';
        textElement.textContent = `+${growthValue}%`;
    } else {
        iconElement.setAttribute('data-lucide', 'trending-down');
        iconElement.className = 'w-4 h-4 text-red-500 mr-1';
        textElement.className = 'text-red-600 font-medium';
        textElement.textContent = `${growthValue}%`;
    }

    // Refresh lucide icons
    lucide.createIcons();
}

// Render transactions table
function renderTransactions() {
    const tbody = document.getElementById('transactionsTableBody');
    const filteredTransactions = filterTransactionData();
    const startIndex = (currentPage - 1) * itemsPerPage;
    const endIndex = startIndex + itemsPerPage;
    const paginatedTransactions = filteredTransactions.slice(startIndex, endIndex);

    if (paginatedTransactions.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center py-8">
                    <i data-lucide="search" class="w-12 h-12 mx-auto text-gray-400"></i>
                    <p class="text-gray-500 mt-2">No transactions found</p>
                </td>
            </tr>
        `;
    } else {
        tbody.innerHTML = paginatedTransactions.map(transaction => `
            <tr class="border-b border-gray-100 hover:bg-gray-50 transition-colors">
                <td class="py-4 px-4">
                    <span class="font-mono text-sm">${transaction.id}</span>
                </td>
                <td class="py-4 px-4">
                    <div class="flex items-center">
                        <div class="w-8 h-8 rounded-full bg-gray-300 flex items-center justify-center mr-2">
                            <span class="text-xs font-medium text-gray-700">
                                ${transaction.user?.name?.charAt(0) || 'U'}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">${transaction.user?.name || 'Unknown User'}</p>
                            <p class="text-sm text-gray-600">${transaction.user?.email || ''}</p>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-4 font-semibold text-gray-900">
                    ₹${(transaction.amount || 0).toLocaleString()}
                </td>
                <td class="py-4 px-4">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                        ${transaction.status === 'completed' ? 'bg-green-100 text-green-800' : 
                          transaction.status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                          'bg-red-100 text-red-800'}">
                        ${transaction.status || 'unknown'}
                    </span>
                </td>
                <td class="py-4 px-4 text-gray-600">
                    ${new Date(transaction.created_at).toLocaleDateString()}
                </td>
                <td class="py-4 px-4 text-right">
                    <button class="btn btn-ghost text-sm" onclick="viewTransaction(${transaction.id})">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                        View
                    </button>
                </td>
            </tr>
        `).join('');
    }

    updatePagination(filteredTransactions.length);
    updateShowingInfo(filteredTransactions.length);
}

// Filter transaction data
function filterTransactionData() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const dateFilter = document.getElementById('dateFilter').value;

    return allTransactions.filter(transaction => {
        const matchesSearch = !searchTerm || 
            transaction.id.toString().includes(searchTerm) ||
            (transaction.user?.name || '').toLowerCase().includes(searchTerm) ||
            (transaction.user?.email || '').toLowerCase().includes(searchTerm);
        
        const matchesStatus = !statusFilter || transaction.status === statusFilter;
        
        let matchesDate = true;
        if (dateFilter !== 'all') {
            const transactionDate = new Date(transaction.created_at);
            const today = new Date();
            
            switch(dateFilter) {
                case 'today':
                    matchesDate = transactionDate.toDateString() === today.toDateString();
                    break;
                case 'week':
                    const weekAgo = new Date(today.getTime() - 7 * 24 * 60 * 60 * 1000);
                    matchesDate = transactionDate >= weekAgo;
                    break;
                case 'month':
                    const monthAgo = new Date(today.getTime() - 30 * 24 * 60 * 60 * 1000);
                    matchesDate = transactionDate >= monthAgo;
                    break;
                case 'year':
                    const yearAgo = new Date(today.getTime() - 365 * 24 * 60 * 60 * 1000);
                    matchesDate = transactionDate >= yearAgo;
                    break;
            }
        }
        
        return matchesSearch && matchesStatus && matchesDate;
    });
}

// Update pagination
function updatePagination(totalItems) {
    const totalPages = Math.ceil(totalItems / itemsPerPage);
    const pagination = document.getElementById('pagination');
    
    if (totalPages <= 1) {
        pagination.innerHTML = '';
        return;
    }

    let paginationHTML = '';
    
    // Previous button
    if (currentPage > 1) {
        paginationHTML += `
            <button onclick="changePage(${currentPage - 1})" class="btn btn-outline">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
                Previous
            </button>
        `;
    }
    
    // Page numbers
    for (let i = 1; i <= totalPages; i++) {
        if (i === currentPage) {
            paginationHTML += `
                <button class="btn btn-primary">${i}</button>
            `;
        } else {
            paginationHTML += `
                <button onclick="changePage(${i})" class="btn btn-outline">${i}</button>
            `;
        }
    }
    
    // Next button
    if (currentPage < totalPages) {
        paginationHTML += `
            <button onclick="changePage(${currentPage + 1})" class="btn btn-outline">
                Next
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </button>
        `;
    }
    
    pagination.innerHTML = paginationHTML;
}

// Update showing info
function updateShowingInfo(totalItems) {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(currentPage * itemsPerPage, totalItems);
    
    document.getElementById('showingFrom').textContent = startIndex;
    document.getElementById('showingTo').textContent = endIndex;
    document.getElementById('totalResults').textContent = totalItems;
    document.getElementById('transactionCount').textContent = totalItems;
}

// Filter transactions
function filterTransactions() {
    currentPage = 1;
    renderTransactions();
}

// Change page
function changePage(page) {
    currentPage = page;
    renderTransactions();
}

// Reset filters
function resetFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('dateFilter').value = 'all';
    filterTransactions();
}

// Export transactions
function exportTransactions() {
    const filteredTransactions = filterTransactionData();
    const csvContent = "data:text/csv;charset=utf-8," 
        + "ID,User,Amount,Status,Date\n"
        + filteredTransactions.map(t => 
            `${t.id},${t.user?.name || ''},${t.amount},${t.status},${t.created_at}`
        ).join("\n");
    
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "transactions.csv");
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// View transaction details
function viewTransaction(id) {
    // Implementation for viewing transaction details
    alert(`Viewing transaction ${id}`);
}

// Render recent activity
function renderRecentActivity() {
    const recentTransactions = allTransactions.slice(0, 5);
    const topUsers = getTopUsers();

    // Recent transactions list
    const recentList = document.getElementById('recentTransactionsList');
    if (recentTransactions.length > 0) {
        recentList.innerHTML = recentTransactions.map(t => `
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                        <span class="text-sm font-medium text-gray-700">
                            ${t.user?.name?.charAt(0) || 'U'}
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">${t.user?.name || 'Unknown'}</p>
                        <p class="text-sm text-gray-600">₹${t.amount?.toLocaleString() || 0}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">${t.status}</p>
                    <p class="text-xs text-gray-500">${new Date(t.created_at).toLocaleDateString()}</p>
                </div>
            </div>
        `).join('');
    } else {
        recentList.innerHTML = '<p class="text-center text-gray-500 py-4">No recent transactions</p>';
    }

    // Top users list
    const topUsersList = document.getElementById('topUsersList');
    if (topUsers.length > 0) {
        topUsersList.innerHTML = topUsers.map(u => `
            <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-300 flex items-center justify-center mr-3">
                        <span class="text-sm font-medium text-gray-700">
                            ${u.name?.charAt(0) || 'U'}
                        </span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900">${u.name || 'Unknown'}</p>
                        <p class="text-sm text-gray-600">${u.email || ''}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-sm font-medium text-gray-900">₹${u.total?.toLocaleString() || 0}</p>
                    <p class="text-xs text-gray-500">${u.count || 0} transactions</p>
                </div>
            </div>
        `).join('');
    } else {
        topUsersList.innerHTML = '<p class="text-center text-gray-500 py-4">No user data available</p>';
    }
}

// Get top users by transaction amount
function getTopUsers() {
    const userTotals = {};
    allTransactions.forEach(t => {
        if (t.user) {
            const userId = t.user.id;
            if (!userTotals[userId]) {
                userTotals[userId] = {
                    id: userId,
                    name: t.user.name,
                    email: t.user.email,
                    total: 0,
                    count: 0
                };
            }
            userTotals[userId].total += t.amount || 0;
            userTotals[userId].count += 1;
        }
    });
    
    return Object.values(userTotals)
        .sort((a, b) => b.total - a.total)
        .slice(0, 5);
}

// Chart variables
let transactionVolumeChart = null;
let revenueBreakdownChart = null;

// Load transaction chart data
async function loadTransactionChartData(period = '7days') {
    try {
        const response = await fetch(`/admin/transaction-chart-data?period=${period}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });
        const data = await response.json();

        if (data.success) {
            updateTransactionCharts(data.data);
        }
    } catch (error) {
        console.error('Error loading transaction chart data:', error);
    }
}

// Update transaction charts
function updateTransactionCharts(data) {
    // Update transaction volume chart
    if (transactionVolumeChart) {
        transactionVolumeChart.destroy();
    }

    const volumeCtx = document.getElementById('transactionVolumeChart').getContext('2d');
    transactionVolumeChart = new Chart(volumeCtx, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [
                {
                    label: 'Total Transactions',
                    data: data.transactions.total,
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgb(59, 130, 246)',
                    borderWidth: 1
                },
                {
                    label: 'Completed Transactions',
                    data: data.transactions.completed,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1
                }
            ]
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
            }
        }
    });

    // Update revenue breakdown chart
    if (revenueBreakdownChart) {
        revenueBreakdownChart.destroy();
    }

    const revenueCtx = document.getElementById('revenueBreakdownChart').getContext('2d');
    revenueBreakdownChart = new Chart(revenueCtx, {
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

// Handle transaction chart period change
function handleTransactionChartPeriodChange() {
    const period = document.getElementById('transactionChartPeriod').value;
    loadTransactionChartData(period);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadTransactions();
    loadTransactionChartData();
    lucide.createIcons();

    // Add event listener for chart period change
    document.getElementById('transactionChartPeriod').addEventListener('change', handleTransactionChartPeriodChange);
});
</script>
@endsection
