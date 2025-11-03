@extends('admin.layout')

@section('title', 'Settings')

@section('content')
<div class="app-container">
    <!-- Page Header -->
    <div class="section">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1>Application Settings</h1>
                <p class="mt-2 text-lg text-gray-600">Manage application configuration, branding, and subscriptions</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.transactions') }}" class="btn btn-primary">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    View Transactions
                </a>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <div class="section">
        <div class="tab-container">
            <nav class="tab-navigation" aria-label="Settings Tabs">
                <button onclick="showTab('general')" id="general-tab" class="tab-button-inline" data-tab="general">
                    <i data-lucide="settings" class="w-4 h-4"></i>
                    <span>General Settings</span>
                </button>
                <button onclick="showTab('password_change')" id="password_change-tab" class="tab-button-inline" data-tab="password_change">
                    <i data-lucide="password" class="w-4 h-4"></i>
                    <span>Password Change</span>
                </button>
                <button onclick="showTab('subscriptions')" id="subscriptions-tab" class="tab-button-inline" data-tab="subscriptions">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span>Subscription Plans</span>
                </button>
                <button onclick="showTab('clear_database')" id="clear_database-tab" class="tab-button-inline" data-tab="clear_database">
                    <i data-lucide="database" class="w-4 h-4"></i>
                    <span>Clear Database</span>
                </button>
            </nav>
        </div>
    </div>

    <!-- General Settings Tab -->
    <div id="general-content" class="tab-content section">
        <div class="card">
            <div class="card-header">
                <h3>General Settings</h3>
                <p class="text-sm text-gray-600 mt-1">Configure basic application settings and branding</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data">
                @csrf

                <div class="card-content">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                        <!-- Application Name -->
                        <div>
                            <label for="application_name" class="form-label">
                                Application Name
                            </label>
                            <input type="text"
                                   name="application_name"
                                   id="application_name"
                                   value="{{ old('application_name', $settings->application_name) }}"
                                   class="form-input"
                                   required>
                            @error('application_name')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- App Version -->
                        <div>
                            <label for="app_version" class="form-label">
                                App Version
                            </label>
                            <input type="text"
                                   name="app_version"
                                   id="app_version"
                                   value="{{ old('app_version', $settings->app_version) }}"
                                   class="form-input"
                                   required>
                            @error('app_version')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Logo Upload -->
                        <div class="lg:col-span-2">
                            <label for="logo" class="form-label">
                                Logo
                            </label>
                            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0">
                                <div class="flex-shrink-0">
                                    @if($settings->logo)
                                        <img id="logo-preview"
                                             src="{{ Storage::url($settings->logo) }}"
                                             alt="Current Logo"
                                             class="h-20 w-20 object-cover rounded-lg border border-gray-300 shadow-sm">
                                    @else
                                        <div id="logo-preview"
                                             class="h-20 w-20 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                            <i data-lucide="image" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="file"
                                           name="logo"
                                           id="logo"
                                           accept="image/*"
                                           class="form-file"
                                           onchange="previewImage(this, 'logo-preview')">
                                    <p class="form-help">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            @error('logo')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Favicon Upload -->
                        <div class="lg:col-span-2">
                            <label for="favicon" class="form-label">
                                Favicon
                            </label>
                            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0">
                                <div class="flex-shrink-0">
                                    @if($settings->favicon)
                                        <img id="favicon-preview"
                                             src="{{ Storage::url($settings->favicon) }}"
                                             alt="Current Favicon"
                                             class="h-16 w-16 object-cover rounded-lg border border-gray-300 shadow-sm">
                                    @else
                                        <div id="favicon-preview"
                                             class="h-16 w-16 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                            <i data-lucide="globe" class="w-6 h-6 text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="file"
                                           name="favicon"
                                           id="favicon"
                                           accept="image/*"
                                           class="form-file"
                                           onchange="previewImage(this, 'favicon-preview')">
                                    <p class="form-help">PNG, JPG, GIF, ICO up to 1MB</p>
                                </div>
                            </div>
                            @error('favicon')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- App Logo Upload -->
                        <div class="lg:col-span-2">
                            <label for="app_logo" class="form-label">
                                Application Logo
                            </label>
                            <div class="flex flex-col sm:flex-row items-center sm:space-x-6 space-y-4 sm:space-y-0">
                                <div class="flex-shrink-0">
                                    @if($settings->app_logo)
                                        <img id="app-logo-preview"
                                             src="{{ Storage::url($settings->app_logo) }}"
                                             alt="Current App Logo"
                                             class="h-20 w-20 object-cover rounded-lg border border-gray-300 shadow-sm">
                                    @else
                                        <div id="app-logo-preview"
                                             class="h-20 w-20 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center">
                                            <i data-lucide="smartphone" class="w-8 h-8 text-gray-400"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 w-full">
                                    <input type="file"
                                           name="app_logo"
                                           id="app_logo"
                                           accept="image/*"
                                           class="form-file"
                                           onchange="previewImage(this, 'app-logo-preview')">
                                    <p class="form-help">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            @error('app_logo')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Subscriptions Tab -->
    <div id="subscriptions-content" class="tab-content hidden section">
        <div class="card">
            <div class="card-header">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
                    <div>
                        <h3>Subscription Plans</h3>
                        <p class="text-sm text-gray-600 mt-1">Create and manage subscription plans for premium features</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <button onclick="loadPlans()" class="btn btn-outline" title="Refresh plans">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Refresh
                        </button>
                        <button onclick="showAddPlanModal()" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add New Plan
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-content">
                <!-- Subscription Plans Grid -->
                <div id="plans-container">
                    <div id="plans-loading" class="hidden">
                        <div class="flex items-center justify-center py-12">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mr-3"></div>
                            <span class="text-gray-600">Loading subscription plans...</span>
                        </div>
                    </div>

                    <div id="plans-error" class="hidden">
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-red-100 rounded-full flex items-center justify-center">
                                <i data-lucide="alert-circle" class="w-8 h-8 text-red-600"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">Failed to load subscription plans</h4>
                            <p class="text-gray-600 mb-4" id="error-message">Something went wrong while loading the plans.</p>
                            <button onclick="loadPlans()" class="btn btn-primary">
                                <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                                Try Again
                            </button>
                        </div>
                    </div>

                    <div id="plans-empty" class="hidden">
                        <div class="text-center py-12">
                            <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i data-lucide="credit-card" class="w-8 h-8 text-gray-400"></i>
                            </div>
                            <h4 class="text-lg font-medium text-gray-900 mb-2">No subscription plans yet</h4>
                            <p class="text-gray-600 mb-6">Create your first subscription plan to start offering premium features to your customers.</p>
                            <button onclick="showAddPlanModal()" class="btn btn-primary">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                Create First Plan
                            </button>
                        </div>
                    </div>

                    <div id="plans-grid" class="w-full">
                        <!-- Plans will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Clear Database Tab -->
    <div id="clear_database-content" class="tab-content hidden section">
        <div class="card">
            <div class="card-header">
                <h3>Clear Database</h3>
                <p class="text-sm text-gray-600 mt-1">Remove all data except admin users and settings</p>
            </div>

            <div class="card-content">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                    <div class="flex">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-yellow-600 mr-3 mt-0.5"></i>
                        <div>
                            <h4 class="text-sm font-medium text-yellow-800">Warning</h4>
                            <p class="text-sm text-yellow-700 mt-1">
                                This action will permanently delete all restaurant data, users, and transactions.
                                Only admin users and application settings will be preserved.
                            </p>
                        </div>
                    </div>
                </div>

                <button type="button"
                        class="btn btn-destructive"
                        onclick="clearDatabase()">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                    Clear Database
                </button>
            </div>
        </div>
    </div>

    <!-- Password Change Tab -->
    <div id="password_change-content" class="tab-content hidden section">
        <div class="card">
            <div class="card-header">
                <h3>Change Password</h3>
                <p class="text-sm text-gray-600 mt-1">Update your admin account password</p>
            </div>

            <form method="POST" action="{{ route('admin.settings.change-password') }}">
                @csrf

                <div class="card-content">
                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="form-label">
                                Current Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="current_password"
                                       id="current_password"
                                       class="form-input pr-10"
                                       required>
                                <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                        onclick="togglePasswordVisibility('current_password')">
                                    <i data-lucide="eye" class="w-5 h-5 text-gray-400" id="toggle-current_password"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="form-label">
                                New Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password"
                                       id="new_password"
                                       class="form-input pr-10"
                                       required>
                                <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                        onclick="togglePasswordVisibility('new_password')">
                                    <i data-lucide="eye" class="w-5 h-5 text-gray-400" id="toggle-new_password"></i>
                                </button>
                            </div>
                            <p class="form-help">Password must be at least 8 characters long</p>
                            @error('new_password')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm New Password -->
                        <div>
                            <label for="new_password_confirmation" class="form-label">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input type="password"
                                       name="new_password_confirmation"
                                       id="new_password_confirmation"
                                       class="form-input pr-10"
                                       required>
                                <button type="button" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center"
                                        onclick="togglePasswordVisibility('new_password_confirmation')">
                                    <i data-lucide="eye" class="w-5 h-5 text-gray-400" id="toggle-new_password_confirmation"></i>
                                </button>
                            </div>
                            @error('new_password_confirmation')
                                <p class="form-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Change Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Notification Container -->
<div id="notification-container" class="fixed top-6 right-6 z-50"></div>

<!-- Add/Edit Plan Modal -->
<div id="plan-modal" class="fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full hidden flex items-center justify-center p-4 z-50">
    <div class="relative bg-white rounded-lg shadow-xl w-full max-w-md">
        <div class="card">
            <div class="card-header">
                <div class="flex justify-between items-center">
                    <h3 id="modal-title">Add Subscription Plan</h3>
                    <button onclick="closePlanModal()" class="text-gray-400 hover:text-gray-600">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
            </div>

            <form id="plan-form">
                <div class="card-content space-y-4">
                    <div>
                        <label for="plan-name" class="form-label">Plan Name</label>
                        <input type="text" id="plan-name" name="name" required class="form-input">
                    </div>

                    <div>
                        <label for="plan-duration" class="form-label">Duration (Days)</label>
                        <input type="number" id="plan-duration" name="duration_days" required min="1" class="form-input">
                    </div>

                    <div>
                        <label for="plan-price" class="form-label">Price (₹ INR)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500">₹</span>
                            </div>
                            <input type="number" id="plan-price" name="price" required min="0" step="0.01"
                                   class="form-input pl-8" placeholder="0.00">
                        </div>
                        <p class="form-help">Enter amount in Indian Rupees (INR)</p>
                    </div>

                    <div>
                        <label for="plan-description" class="form-label">Description</label>
                        <textarea id="plan-description" name="description" rows="3" class="form-input"></textarea>
                    </div>

                    <div>
                        <label class="flex items-center">
                            <input type="checkbox" id="plan-enabled" name="is_enabled" checked
                                   class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-700">Plan Enabled</span>
                        </label>
                    </div>
                </div>

                <div class="card-footer flex justify-end space-x-3">
                    <button type="button" onclick="closePlanModal()" class="btn btn-outline">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab content sections
    document.querySelectorAll('[id$="-content"]').forEach(content => {
        content.classList.add('hidden');
    });

    // Remove active class from all tabs
    document.querySelectorAll('.tab-button-inline').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');

    // Add active class to selected tab
    document.getElementById(tabName + '-tab').classList.add('active');

    // Update URL without page reload
    const url = new URL(window.location);
    url.searchParams.set('tab', tabName);
    window.history.replaceState({}, '', url);
}

// Image preview function
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="image-preview">`;
        };
        reader.readAsDataURL(file);
    }
}

// Subscription plan management
let editingPlanId = null;

// Notification system
function showNotification(message, type = 'info') {
    const container = document.getElementById('notification-container');

    // Safety check - if container doesn't exist, create it
    if (!container) {
        console.error('Notification container not found, creating one...');
        const newContainer = document.createElement('div');
        newContainer.id = 'notification-container';
        newContainer.className = 'fixed top-4 right-4 z-50';
        document.body.appendChild(newContainer);
        return showNotification(message, type); // Retry with new container
    }

    const notification = document.createElement('div');

    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-primary';

    notification.className = `${bgColor} text-white px-6 py-3 rounded-lg shadow-lg mb-2 transform transition-all duration-300 translate-x-full opacity-0`;
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;

    container.appendChild(notification);

    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full', 'opacity-0');
    }, 100);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentElement) {
                    notification.remove();
                }
            }, 300);
        }
    }, 5000);
}

function loadPlans() {
    // Hide all states
    document.getElementById('plans-loading').classList.remove('hidden');
    document.getElementById('plans-error').classList.add('hidden');
    document.getElementById('plans-empty').classList.add('hidden');
    document.getElementById('plans-grid').innerHTML = '';

    fetch('/admin/subscription-plans', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok) {
            if (response.status === 401) {
                throw new Error('Please log in to access this feature');
            } else if (response.status === 403) {
                throw new Error('Admin access required');
            } else {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
        }
        return response.json();
    })
    .then(data => {
        document.getElementById('plans-loading').classList.add('hidden');
        if (data.success) {
            renderPlans(data.data);
        } else {
            throw new Error(data.message || 'Failed to load plans');
        }
    })
    .catch(error => {
        console.error('Error loading plans:', error);
        document.getElementById('plans-loading').classList.add('hidden');
        document.getElementById('plans-error').classList.remove('hidden');
        document.getElementById('error-message').textContent = error.message;
    });
}

function renderPlans(plans) {
    const plansGrid = document.getElementById('plans-grid');
    plansGrid.innerHTML = '';

    if (plans.length === 0) {
        document.getElementById('plans-empty').classList.remove('hidden');
        return;
    }

    // Create a scrollable container for the table
    const scrollContainer = document.createElement('div');
    scrollContainer.className = 'overflow-x-auto';
    
    // Create table structure with full width
    const table = document.createElement('table');
    table.className = 'min-w-full divide-y divide-gray-200';
    
    table.innerHTML = `
        <thead class="bg-gray-100">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan Name</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody id="plans-table-body" class="bg-white divide-y divide-gray-200">
        </tbody>
    `;
    
    const tableBody = table.querySelector('#plans-table-body');
    
    plans.forEach((plan, index) => {
        // Format duration for better display
        let durationDisplay = plan.formatted_duration;
        let durationIcon = 'calendar';

        if (plan.duration_days === 30) {
            durationDisplay = '1 Month';
            durationIcon = 'calendar';
        } else if (plan.duration_days === 365) {
            durationDisplay = '1 Year';
            durationIcon = 'calendar';
        } else if (plan.duration_days === 7) {
            durationDisplay = '1 Week';
            durationIcon = 'calendar';
        } else {
            durationDisplay = `${plan.duration_days} Days`;
            durationIcon = 'clock';
        }

        // Format price in INR with proper formatting
        const priceFormatted = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR',
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(plan.price);

        // Determine if this is a popular plan (longest duration or highest price)
        const isPopular = plans.length > 1 && (
            plan.duration_days === Math.max(...plans.map(p => p.duration_days)) ||
            plan.price === Math.max(...plans.map(p => p.price))
        );

        const row = document.createElement('tr');
        row.className = `${!plan.is_enabled ? 'bg-gray-50' : ''} ${isPopular ? 'ring-2 ring-primary ring-opacity-20' : ''}`;
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${index + 1}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center">
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">${plan.name}</div>
                        ${isPopular ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-primary bg-opacity-10 text-primary">Most Popular</span>' : ''}
                    </div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="flex items-center text-sm text-gray-500">
                    <i data-lucide="${durationIcon}" class="w-4 h-4 mr-1"></i>
                    ${durationDisplay}
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                ${priceFormatted}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                ${plan.description || '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <button type="button"
                        onclick="togglePlanStatus(this, ${plan.id})"
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 ${plan.is_enabled ? 'bg-primary' : 'bg-gray-200'}"
                        aria-pressed="${plan.is_enabled ? 'true' : 'false'}"
                        id="plan-toggle-btn-${plan.id}">
                    <span class="sr-only">Toggle plan status</span>
                    <span aria-hidden="true" class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out ${plan.is_enabled ? 'translate-x-5' : 'translate-x-0'}"></span>
                </button>
                <span class="ml-2 text-sm text-gray-500">${plan.is_enabled ? 'Active' : 'Inactive'}</span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editPlan(${plan.id})" class="text-primary hover:text-orange-700 mr-3">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                </button>
                <button onclick="deletePlan(${plan.id})" class="text-red-600 hover:text-red-900">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    // Add the table to the scrollable container
    scrollContainer.appendChild(table);
    
    // Add the scrollable container to plansGrid
    plansGrid.appendChild(scrollContainer);

    // Initialize Lucide icons for the new table
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

function showAddPlanModal() {
    editingPlanId = null;
    document.getElementById('modal-title').textContent = 'Add Subscription Plan';
    document.getElementById('plan-form').reset();
    document.getElementById('plan-enabled').checked = true;
    document.getElementById('plan-modal').classList.remove('hidden');
}

function togglePlanStatus(btn, planId) {
    const isEnabled = btn.getAttribute('aria-pressed') === 'true';
    const newStatus = !isEnabled;

    // Optimistic UI update
    updatePlanToggleUI(btn, newStatus);

    fetch(`/admin/subscription-plans/${planId}`, {
        method: 'PUT',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            is_enabled: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (!data.success) {
            // Revert UI on failure
            updatePlanToggleUI(btn, isEnabled);
            showNotification(data.message || 'Failed to update plan status', 'error');
        } else {
            showNotification('Plan status updated successfully!', 'success');
        }
    })
    .catch(error => {
        console.error('Error updating plan status:', error);
        // Revert UI on fetch error
        updatePlanToggleUI(btn, isEnabled);
        showNotification('Failed to update plan status', 'error');
    });
}

function updatePlanToggleUI(btn, isEnabled) {
    const knob = btn.querySelector('span[aria-hidden="true"]');
    const statusText = btn.nextElementSibling;

    // Update button attributes
    btn.setAttribute('aria-pressed', isEnabled ? 'true' : 'false');
    
    // Update button styling
    if (isEnabled) {
        btn.classList.remove('bg-gray-200');
        btn.classList.add('bg-primary');
    } else {
        btn.classList.remove('bg-primary');
        btn.classList.add('bg-gray-200');
    }
    
    // Update knob position
    if (knob) {
        if (isEnabled) {
            knob.classList.remove('translate-x-0');
            knob.classList.add('translate-x-5');
        } else {
            knob.classList.remove('translate-x-5');
            knob.classList.add('translate-x-0');
        }
    }
    
    // Update status text
    if (statusText) {
        statusText.textContent = isEnabled ? 'Active' : 'Inactive';
    }
}

function editPlan(planId) {
    editingPlanId = planId;
    document.getElementById('modal-title').textContent = 'Edit Subscription Plan';

    // Fetch plan details and populate form
    fetch(`/admin/subscription-plans/${planId}`, {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const plan = data.data;
            document.getElementById('plan-name').value = plan.name;
            document.getElementById('plan-duration').value = plan.duration_days;
            document.getElementById('plan-price').value = plan.price;
            document.getElementById('plan-description').value = plan.description || '';
            document.getElementById('plan-enabled').checked = plan.is_enabled;
            document.getElementById('plan-modal').classList.remove('hidden');
        }
    });
}

function closePlanModal() {
    document.getElementById('plan-modal').classList.add('hidden');
    editingPlanId = null;
}

function deletePlan(planId) {
    if (confirm('Are you sure you want to delete this subscription plan?')) {
        fetch(`/admin/subscription-plans/${planId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Plan deleted successfully!', 'success');
                setTimeout(() => {
                    loadPlans();
                }, 500);
            } else {
                showNotification(data.message || 'Failed to delete plan', 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting plan:', error);
            showNotification('Failed to delete plan', 'error');
        });
    }
}

// Handle form submission
document.getElementById('plan-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const data = Object.fromEntries(formData.entries());
    data.is_enabled = document.getElementById('plan-enabled').checked;

    const url = editingPlanId ? `/admin/subscription-plans/${editingPlanId}` : '/admin/subscription-plans';
    const method = editingPlanId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const action = editingPlanId ? 'updated' : 'created';
            showNotification(`Plan ${action} successfully!`, 'success');
            closePlanModal();
            setTimeout(() => {
                loadPlans();
            }, 500);
        } else {
            showNotification(data.message || `Failed to ${editingPlanId ? 'update' : 'create'} plan`, 'error');
        }
    })
    .catch(error => {
        console.error('Error saving plan:', error);
        showNotification(`Failed to ${editingPlanId ? 'update' : 'create'} plan`, 'error');
    });
});

// Load plans when subscriptions tab is shown
document.addEventListener('DOMContentLoaded', function() {
    // Activate tab from URL parameter if present
    const urlParams = new URLSearchParams(window.location.search);
    const tab = urlParams.get('tab');
    if (tab === 'subscriptions') {
        showTab('subscriptions');
    } else {
        showTab('general');
    }
    // Load plans initially if subscriptions tab is active
    if (!document.getElementById('subscriptions-content').classList.contains('hidden')) {
        loadPlans();
    }
});

// Override showTab to load plans when subscriptions tab is selected
const originalShowTab = showTab;
showTab = function(tabName) {
    originalShowTab(tabName);
    if (tabName === 'subscriptions') {
        // Add a small delay to ensure the tab content is visible
        setTimeout(() => {
            loadPlans();
        }, 100);
    }
};

// Show success notification if present in URL
function showPasswordChangeNotification() {
    const urlParams = new URLSearchParams(window.location.search);
    // if (urlParams.has('password_changed')) {
    //     showNotification('Password changed successfully!', 'success');
    // }
}

// Call on page load
showPasswordChangeNotification();

// Auto-refresh plans every 30 seconds when on subscriptions tab
let refreshInterval;
function startAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
    refreshInterval = setInterval(() => {
        if (!document.getElementById('subscriptions-content').classList.contains('hidden')) {
            loadPlans();
        }
    }, 30000); // 30 seconds
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        refreshInterval = null;
    }
}

// Start auto-refresh when page loads
document.addEventListener('DOMContentLoaded', function() {
    startAutoRefresh();

    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

function clearDatabase() {
    if (confirm('Are you sure you want to clear the database? This will remove all data except admin users.')) {
        fetch('/admin/clear-database', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.redirect) {
                showNotification('Database Cleared Successfully!', 'success');
                setTimeout(() => {
                    window.location.href = data.redirect;
                }, 1500);
            }
            else {
                showNotification('Failed to clear database.', 'error');
            }
        })
        .catch(error => {
            console.error('Error clearing database:', error);
            showNotification('Failed to clear database.', 'error');
        });
    }
}

// Password visibility toggle function
function togglePasswordVisibility(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = document.getElementById('toggle-' + fieldId);
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.setAttribute('data-lucide', 'eye-off');
    } else {
        passwordField.type = 'password';
        toggleIcon.setAttribute('data-lucide', 'eye');
    }
    
    // Reinitialize Lucide icons to update the icon
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
}

</script>

<style>
/* Tab Navigation Styles */
.tab-container {
    @apply bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden;
}

.tab-navigation {
    display: flex !important;
    flex-direction: row !important;
    flex-wrap: nowrap !important;
    @apply bg-gray-50 border-b border-gray-200;
    overflow-x: auto;
}

.tab-button-inline {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    justify-content: center;
    gap: 0.5rem;
    flex: 1 1 0%;
    min-width: 0;
    padding: 1rem 1.5rem;
    font-size: 0.875rem;
    font-weight: 500;
    color: #6b7280;
    background: transparent;
    border: none;
    border-bottom: 2px solid transparent;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.tab-button-inline:hover {
    color: #374151;
    border-bottom-color: #d1d5db;
}

.tab-button-inline.active {
    color: var(--color-primary);
    border-bottom-color: var(--color-primary);
    background: white;
    font-weight: 600;
    position: relative;
    z-index: 1;
}

.tab-button-inline.active::after {
    content: '';
    position: absolute;
    bottom: -1px;
    left: 0;
    right: 0;
    height: 2px;
    background: var(--color-primary);
}

/* Tab loading state */
.tab-button-inline.loading i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .tab-navigation {
        display: flex !important;
        flex-direction: row !important;
        flex-wrap: nowrap !important;
        overflow-x: auto;
    }

    .tab-button-inline {
        padding: 0.75rem 1rem;
        flex: 0 0 auto;
        min-width: max-content;
        display: flex !important;
        flex-direction: row !important;
        align-items: center !important;
    }

    .tab-button-inline i {
        width: 1rem;
        height: 1rem;
    }

    .tab-button-inline span {
        font-size: 0.75rem;
    }
}

/* Tab content transitions */
[id$="-content"] {
    transition: opacity 0.2s ease-in-out;
}

/* Force horizontal layout for all screen sizes */
.tab-navigation,
.tab-navigation * {
    box-sizing: border-box;
}

.tab-navigation {
    width: 100%;
}

.tab-button-inline {
    flex-shrink: 1;
    min-width: 120px;
}

/* Ensure icons stay inline */
.tab-button-inline i {
    flex-shrink: 0;
    margin-right: 0.5rem;
}

.tab-button-inline span {
    flex-shrink: 1;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Form Styles */
.form-label {
    @apply block text-sm font-medium text-gray-700 mb-2;
}

.form-input {
    @apply block w-full px-3 py-2 border border-gray-300 rounded-lg shadow-sm focus:ring-primary focus:border-primary text-sm;
}

.form-file {
    @apply block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-primary file:text-white hover:file:bg-opacity-90 file:cursor-pointer;
}

.form-help {
    @apply mt-1 text-xs text-gray-500;
}

.form-error {
    @apply mt-1 text-sm text-red-600;
}

/* Button Styles - Override to match design system */
.btn-destructive {
    @apply inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-lg shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200;
}

/* Image preview improvements */
.image-preview {
    @apply h-20 w-20 object-cover rounded-lg border border-gray-300 shadow-sm;
}

.image-placeholder {
    @apply h-20 w-20 bg-gray-100 rounded-lg border border-gray-300 flex items-center justify-center;
}

/* Subscription Plan Cards */
.plan-card {
    @apply bg-white rounded-lg border border-gray-200 shadow-sm hover:shadow-md transition-all duration-200;
}

.plan-card:hover {
    @apply border-gray-300 transform -translate-y-1;
}

.plan-card.inactive {
    @apply opacity-60;
}

.plan-price {
    @apply text-3xl font-bold text-gray-900;
}

.plan-duration {
    @apply text-sm text-gray-600 flex items-center;
}

.plan-status-badge {
    @apply inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium;
}

.plan-status-active {
    @apply bg-green-100 text-green-800;
}

.plan-status-inactive {
    @apply bg-red-100 text-red-800;
}

/* Enhanced toggle switches */
.plan-toggle {
    @apply relative inline-flex h-6 w-11 items-center rounded-full transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2;
}

.plan-toggle.active {
    @apply bg-primary;
}

.plan-toggle.inactive {
    @apply bg-gray-200;
}

.plan-toggle-knob {
    @apply inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200;
}

.plan-toggle-knob.active {
    @apply translate-x-6;
}

.plan-toggle-knob.inactive {
    @apply translate-x-1;
}

/* Tab content transitions */
[id$="-content"] {
    transition: opacity 0.2s ease-in-out;
}
</style>
@endsection
