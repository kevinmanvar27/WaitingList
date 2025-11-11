@extends('admin.layout')

@section('title', 'Restaurants')

@section('content')
<div class="app-container">
    <!-- Page Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Restaurant Management</h1>
        <p class="mt-1 text-gray-600">Manage all restaurants and their owners in the system.</p>
    </div>

    <!-- Filters and Actions (unchanged) -->
    <div class="section">
        <div class="card">
            <div class="card-content">
                <div class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="flex-1">
                        <label class="form-label">Search Restaurants</label>
                        <input type="text" id="searchInput" placeholder="Search by name, email, or phone..." 
                               class="form-input" onkeyup="filterRestaurants()">
                    </div>
                    <div>
                        <label class="form-label">Status</label>
                        <select id="statusFilter" class="form-input" onchange="filterRestaurants()">
                            <option value="">All Status</option>
                            <option value="active">Open</option>
                            <option value="inactive">Closed</option>
                            <option value="suspended">Suspended</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Sort By</label>
                        <select id="sortFilter" class="form-input" onchange="filterRestaurants()">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name">Name A-Z</option>
                            <option value="revenue">Revenue High-Low</option>
                        </select>
                    </div>
                    <a href="{{ route('admin.restaurants.create') }}" class="btn btn-primary">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                        Add Restaurant
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Restaurants List Table -->
    <div class="section p-0">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 bg-white rounded-lg shadow">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sr. No.</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Profile Image</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Operational Status</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Contact</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="restaurant-table-body">
                    @php $srNo = ($restaurants->currentPage() - 1) * $restaurants->perPage() + 1; @endphp
                    @forelse($restaurants as $restaurant)
                        <tr id="restaurant-row-{{ $restaurant->id }}" data-name="{{ strtolower($restaurant->name) }}" data-email="{{ strtolower($restaurant->owner->email ?? '') }}" data-phone="{{ strtolower($restaurant->phone ?? '') }}" data-status="{{ strtolower($restaurant->status) }}">
                            <td class="px-2 py-2 text-gray-700">{{ $srNo++ }}</td>
                            <td class="px-2 py-2">
                                @if($restaurant->profile)
                                    <img src="{{ asset('storage/' . $restaurant->profile) }}" alt="Profile" class="h-12 w-12 object-cover rounded-full border border-gray-200">
                                @else
                                    <span class="inline-block h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-400">
                                        <i data-lucide="image" class="w-6 h-6"></i>
                                    </span>
                                @endif
                            </td>
                            <td class="px-2 py-2">
                                <div class="font-semibold text-gray-900">{{ $restaurant->name }}</div>
                                <div class="text-sm text-gray-500">{{ $restaurant->owner->name ?? 'No Owner' }}</div>
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="toggleOperationalStatus(this, {{ $restaurant->id }})" class="relative inline-flex h-6 w-20 items-center rounded-full transition-colors duration-200 focus:outline-none {{ $restaurant->operational_status === '1' ? 'bg-green-500' : 'bg-red-500' }}" aria-pressed="{{ $restaurant->operational_status === '1' ? 'true' : 'false' }}" id="toggle-operational-btn-{{ $restaurant->id }}">
                                        <span class="sr-only">Toggle Enabled</span>
                                        <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200 {{ $restaurant->operational_status === '1' ? 'translate-x-12' : 'translate-x-1' }}"></span>
                                        <span class="absolute left-2 text-xs font-semibold text-white">{{ $restaurant->operational_status === '1' ? 'Open' : '' }}</span>
                                        <span class="absolute right-2 text-xs font-semibold text-white">{{ $restaurant->operational_status === '0' ? 'Closed' : '' }}</span>
                                    </button>
                                </div>
                            </td>
                            <td class="px-2 py-2">
                                <div>{{ $restaurant->owner->email ?? 'No Email' }}</div>
                                <div class="text-sm text-gray-500">{{ $restaurant->phone ?? 'No Phone' }}</div>
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex items-center space-x-2">
                                    <button type="button" onclick="toggleSwitch(this, {{ $restaurant->id }})" class="relative inline-flex h-6 w-24 items-center rounded-full transition-colors duration-200 focus:outline-none {{ $restaurant->is_active ? 'bg-green-500' : 'bg-red-500' }}" aria-pressed="{{ $restaurant->is_active ? 'true' : 'false' }}" id="toggle-btn-{{ $restaurant->id }}">
                                       <span class="sr-only">Toggle Enabled</span>
                                       <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow transition-transform duration-200 {{ $restaurant->is_active ? 'translate-x-16' : 'translate-x-1' }}"></span>
                                       <span class="absolute left-2 text-xs font-semibold text-white">{{ $restaurant->is_active ? 'Active' : '' }}</span>
                                       <span class="absolute right-2 text-xs font-semibold text-white">{{ !$restaurant->is_active ? 'Suspended' : '' }}</span>
                                       <input type="hidden" id="toggle-state-{{ $restaurant->id }}" value="{{ $restaurant->is_active ? 'active' : 'inactive' }}">
                                   </button>
                                </div>
                            </td>
                            <td class="px-2 py-2 space-x-2">
                                <button onclick="viewRestaurant({{ $restaurant->id }})" class="btn btn-outline btn-sm">
                                    <i data-lucide="eye" class="w-4 h-4"></i> View
                                </button>
                                <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="btn btn-outline btn-sm">
                                    <i data-lucide="edit" class="w-4 h-4"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-8 text-gray-500">
                                No restaurants found. <a href="{{ route('admin.restaurants.create') }}" class="text-primary underline">Add Restaurant</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($restaurants->hasPages())
            <div class="mt-8">
                {{ $restaurants->links() }}
            </div>
        @endif
    </div>

    <!-- Restaurant Details Modal -->
    <div id="restaurant-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center bg-black bg-opacity-40">
        <div class="bg-white rounded-lg shadow-lg max-w-3xl w-full relative">
            <button onclick="closeRestaurantModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-6 h-6"></i>
            </button>
            <div id="restaurant-modal-content" class="p-6">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
// Toggle restaurant status with AJAX (toggle button version)
function toggleSwitch(btn, id) {
    const isActive = btn.getAttribute('aria-pressed') === 'true';
    const newStatus = isActive ? 'inactive' : 'active';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Optimistic UI update
    updateToggleUI(btn, id, newStatus);

    fetch(`/admin/restaurants/${id}/toggle-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            // Revert UI on failure
            updateToggleUI(btn, id, isActive ? 'active' : 'inactive');
            alert('Error: ' + (data.message || 'Could not update status.'));
        }
        // UI is already updated, so no action needed on success
    })
    .catch(error => {
        // Revert UI on fetch error
        updateToggleUI(btn, id, isActive ? 'active' : 'inactive');
        console.error('AJAX error:', error);
        alert('An unexpected error occurred. Please try again.');
    });
}

function updateToggleUI(btn, id, status) {
    const isActive = status === 'active';
    const knob = btn.querySelector('span:not(.sr-only)');
    const statusLabel = btn.querySelector('.text-sm');
    const hiddenInput = document.getElementById(`toggle-state-${id}`);

    btn.setAttribute('aria-pressed', isActive);

    if (isActive) {
        btn.classList.remove('bg-red-500');
        btn.classList.add('bg-green-500');
        knob.classList.remove('translate-x-1');
        knob.classList.add('translate-x-6');
        const activeText = btn.querySelector('.left-2');
        const suspendedText = btn.querySelector('.right-2');
        activeText.textContent = 'Active';
        suspendedText.textContent = '';
    } else {
        btn.classList.remove('bg-green-500');
        btn.classList.add('bg-red-500');
        knob.classList.remove('translate-x-6');
        knob.classList.add('translate-x-1');
        const activeText = btn.querySelector('.left-2');
        const suspendedText = btn.querySelector('.right-2');
        activeText.textContent = '';
        suspendedText.textContent = 'Suspended';
    }

    if (hiddenInput) {
        hiddenInput.value = status;
    }
}

function toggleOperationalStatus(btn, id) {
    const isOpen = btn.getAttribute('aria-pressed') === 'true';
    const newStatus = isOpen ? '0' : '1';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    updateOperationalToggleUI(btn, id, newStatus);

    fetch(`/admin/restaurants/${id}/toggle-operational-status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw err; });
        }
        return response.json();
    })
    .then(data => {
        if (!data.success) {
            updateOperationalToggleUI(btn, id, isOpen ? '1' : '0');
            alert('Error: ' + (data.message || 'Could not update status.'));
        }
    })
    .catch(error => {
        updateOperationalToggleUI(btn, id, isOpen ? '1' : '0');
        console.error('AJAX error:', error);
        alert('An unexpected error occurred. Please try again.');
    });
}

function updateOperationalToggleUI(btn, id, status) {
    const isOpen = status === '1';
    const knob = btn.querySelector('span:not(.sr-only)');
    const openText = btn.querySelector('.left-2');
    const closedText = btn.querySelector('.right-2');

    btn.setAttribute('aria-pressed', isOpen);

    if (isOpen) {
        btn.classList.remove('bg-red-500');
        btn.classList.add('bg-green-500');
        knob.classList.remove('translate-x-1');
        knob.classList.add('translate-x-12');
        openText.textContent = 'Open';
        closedText.textContent = '';
    } else {
        btn.classList.remove('bg-green-500');
        btn.classList.add('bg-red-500');
        knob.classList.remove('translate-x-12');
        knob.classList.add('translate-x-1');
        openText.textContent = '';
        closedText.textContent = 'Closed';
    }
}

// View restaurant details in modal (AJAX)
function viewRestaurant(id) {
    const modal = document.getElementById('restaurant-modal');
    const content = document.getElementById('restaurant-modal-content');
    content.innerHTML = '<div class="text-center py-8"><i class="animate-spin w-8 h-8 text-primary" data-lucide="loader"></i></div>';
    modal.classList.remove('hidden');
    fetch(`/admin/restaurants/${id}`)
        .then(response => response.text())
        .then(html => {
            content.innerHTML = html;
            lucide.createIcons(); // Re-render icons
        })
        .catch(error => {
            console.error('Error loading restaurant details:', error);
            content.innerHTML = '<p class="text-red-500">Could not load details.</p>';
        });
}
function closeRestaurantModal() {
    document.getElementById('restaurant-modal').classList.add('hidden');
}
// Improved Filter/Search for table
function filterRestaurants() {
    const searchTerm = document.getElementById('searchInput').value.trim().toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const rows = document.querySelectorAll('#restaurant-table-body tr');
    rows.forEach(row => {
        const name = row.getAttribute('data-name');
        const email = row.getAttribute('data-email');
        const phone = row.getAttribute('data-phone');
        const status = row.getAttribute('data-status');
        const matchesSearch = !searchTerm || (name && name.includes(searchTerm)) || (email && email.includes(searchTerm)) || (phone && phone.includes(searchTerm));
        const matchesStatus = !statusFilter || status === statusFilter;
        row.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
    });
}
document.addEventListener('DOMContentLoaded', function() {
    lucide.createIcons();
});
</script>
@endsection
