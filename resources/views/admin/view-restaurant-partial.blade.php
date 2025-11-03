<div class="p-2">
    <h2 class="text-2xl font-bold mb-4">
        {{ $restaurant->name }}
        <span class="ml-2 inline-block h-4 w-4 rounded-full {{ $restaurant->operational_status === 'open' ? 'bg-green-500' : 'bg-red-500' }}"></span>
    </h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            @if($restaurant->profile)
                <img src="{{ asset('storage/' . $restaurant->profile) }}" alt="Profile" class="w-full h-auto rounded-lg shadow-md">
            @else
                <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-lg">
                    <span class="text-gray-500">No Image</span>
                </div>
            @endif
        </div>
        <div>
            <h3 class="font-semibold text-lg mb-2">Restaurant Details</h3>
            <div class="space-y-2 text-sm">
                <p><strong>Owner:</strong> {{ $restaurant->owner->name ?? 'N/A' }}</p>
                <p><strong>Contact:</strong> {{ $restaurant->contact_number ?? 'N/A' }}</p>
                <p><strong>Email:</strong> {{ $restaurant->owner->email ?? 'N/A' }}</p>
                <p><strong>Operational Status:</strong> <span class="font-semibold py-1 px-2 rounded-full {{ $restaurant->operational_status === 'open' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ ucfirst($restaurant->operational_status) }}</span></p>
                <p><strong>Account Status:</strong> <span class="font-semibold py-1 px-2 rounded-full {{ $restaurant->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $restaurant->is_active ? 'Active' : 'Suspended' }}</span></p>
                <p><strong>Member Since:</strong> {{ $restaurant->created_at->format('d M, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <h3 class="font-semibold text-lg mb-2">Address</h3>
        <p class="text-sm text-gray-600">
            {{ $restaurant->address_line_1 ?? '' }}
            {{ $restaurant->address_line_2 ? ', ' . $restaurant->address_line_2 : '' }}<br>
            {{ $restaurant->city ?? '' }}{{ $restaurant->state ? ', ' . $restaurant->state : '' }}{{ $restaurant->postal_code ? ' - ' . $restaurant->postal_code : '' }}<br>
            {{ $restaurant->country ?? '' }}
        </p>
    </div>

    @if($restaurant->description)
    <div class="mt-4">
        <h3 class="font-semibold text-lg mb-2">Description</h3>
        <p class="text-sm text-gray-600">{{ $restaurant->description }}</p>
    </div>
    @endif

    <div class="mt-6 flex justify-end space-x-2">
        <a href="{{ route('admin.restaurants.edit', $restaurant->id) }}" class="btn btn-outline">
            <i data-lucide="edit" class="w-4 h-4 mr-1"></i> Edit
        </a>
        <button onclick="closeRestaurantModal()" class="btn">Close</button>
    </div>
</div>