@extends('admin.layout')

@section('title', isset($restaurant) ? 'Edit Restaurant' : 'Create Restaurant')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center">
            <a href="{{ route('admin.restaurants') }}" 
               class="mr-4 text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ isset($restaurant) ? 'Edit Restaurant' : 'Create Restaurant' }}</h1>
                <p class="mt-2 text-gray-600">{{ isset($restaurant) ? 'Edit restaurant details' : 'Add a new restaurant to the system' }}</p>
            </div>
        </div>
    </div>

    <!-- Create/Edit Form -->
    <div class="bg-white shadow overflow-hidden sm:rounded-lg card-shadow">
        <form method="POST" action="{{ isset($restaurant) ? route('admin.restaurants.update', $restaurant->id) : route('admin.restaurants.store') }}" enctype="multipart/form-data">
            @csrf
            @if(isset($restaurant))
                @method('PUT')
            @endif
            <div class="px-4 py-5 sm:p-6">
                <div class="grid grid-cols-1 gap-6">
                    <!-- Profile Image -->
                    <div>
                        <label for="profile" class="form-label">
                            Restaurant Profile Image
                        </label>
                        <div class="mt-1 flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div id="profile-preview"
                                     class="h-20 w-20 bg-gray-200 rounded-lg border border-gray-300 flex items-center justify-center">
                                    @if(isset($restaurant) && $restaurant->profile)
                                        <img src="{{ asset('storage/' . $restaurant->profile) }}" alt="Profile" class="h-full w-full object-cover rounded-lg">
                                    @else
                                        <span class="text-gray-400 text-xs">No Image</span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-1">
                                <input type="file"
                                       name="profile"
                                       id="profile"
                                       accept="image/*"
                                       class="form-file"
                                       onchange="previewImage(this, 'profile-preview')">
                                <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                            </div>
                        </div>
                        @error('profile')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Restaurant Name -->
                    <div>
                        <label for="name" class="form-label">
                            Restaurant Name *
                        </label>
                        <div class="mt-1">
                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', isset($restaurant) ? $restaurant->name : '') }}"
                                   class="form-input"
                                   required>
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Contact Number -->
                    <div>
                        <label for="contact_number" class="form-label">
                            Contact Number *
                        </label>
                        <div class="mt-1">
                            <input type="tel"
                                   name="contact_number"
                                   id="contact_number"
                                   value="{{ old('contact_number', isset($restaurant) ? $restaurant->contact_number : '') }}"
                                   class="form-input"
                                   required>
                        </div>
                        @error('contact_number')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Address Fields -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="address_line_1" class="form-label">
                                Address Line 1
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                       name="address_line_1"
                                       id="address_line_1"
                                       value="{{ old('address_line_1', isset($restaurant) ? $restaurant->address_line_1 : '') }}"
                                       class="form-input">
                            </div>
                            @error('address_line_1')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="address_line_2" class="form-label">
                                Address Line 2
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                       name="address_line_2"
                                       id="address_line_2"
                                       value="{{ old('address_line_2', isset($restaurant) ? $restaurant->address_line_2 : '') }}"
                                       class="form-input">
                            </div>
                            @error('address_line_2')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label for="city" class="form-label">
                                City
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                       name="city"
                                       id="city"
                                       value="{{ old('city', isset($restaurant) ? $restaurant->city : '') }}"
                                       class="form-input">
                            </div>
                            @error('city')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="state" class="form-label">
                                State
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                       name="state"
                                       id="state"
                                       value="{{ old('state', isset($restaurant) ? $restaurant->state : '') }}"
                                       class="form-input">
                            </div>
                            @error('state')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="postal_code" class="form-label">
                                Postal Code
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                       name="postal_code"
                                       id="postal_code"
                                       value="{{ old('postal_code', isset($restaurant) ? $restaurant->postal_code : '') }}"
                                       class="form-input">
                            </div>
                            @error('postal_code')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div>
                        <label for="country" class="form-label">
                            Country
                        </label>
                        <div class="mt-1">
                            <input type="text"
                                   name="country"
                                   id="country"
                                   value="{{ old('country', isset($restaurant) ? $restaurant->country : 'India') }}"
                                   class="form-input">
                        </div>
                        @error('country')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Legacy Location (Optional) -->
                    <div>
                        <label for="location" class="form-label">
                            Legacy Location (Optional)
                        </label>
                        <div class="mt-1">
                            <textarea name="location"
                                      id="location"
                                      rows="2"
                                      class="form-input"
                                      placeholder="Full address as single text (for backward compatibility)">{{ old('location', isset($restaurant) ? $restaurant->location : '') }}</textarea>
                        </div>
                        @error('location')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Coordinates -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="latitude" class="form-label">
                                Latitude
                            </label>
                            <div class="mt-1">
                                <input type="number"
                                       name="latitude"
                                       id="latitude"
                                       step="any"
                                       value="{{ old('latitude', isset($restaurant) ? $restaurant->latitude : '') }}"
                                       class="form-input"
                                       placeholder="e.g., 40.7128">
                            </div>
                            @error('latitude')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="longitude" class="form-label">
                                Longitude
                            </label>
                            <div class="mt-1">
                                <input type="number" 
                                       name="longitude" 
                                       id="longitude" 
                                       step="any"
                                       value="{{ old('longitude', isset($restaurant) ? $restaurant->longitude : '') }}"
                                       class="form-input"
                                       placeholder="e.g., -74.0060">
                            </div>
                            @error('longitude')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <!-- Owner -->
                    <div>
                        <div>
                            <label for="owner_id" class="form-label">
                                Restaurant Owner *
                            </label>
                            <div class="mt-1">
                                <!-- <input type="text"
                                       name="owner_id"
                                       id="owner_id"
                                       value="{{ old('owner_id', isset($restaurant) ? $restaurant->owner_id : '') }}"
                                       class="form-input"
                                       placeholder="Enter Owner ID"> -->

                                <select name="owner_id" id="owner_id" class="form-input">
                                    <option value="">Select an Owner</option>
                                    @if(isset($users))
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (isset($restaurant) && $restaurant->owner_id == $user->id) || old('owner_id') == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            @error('owner_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <!-- <div>
                            <label for="owner_name" class="form-label">
                                Display Owner Name
                            </label>
                            <div class="mt-1">
                                <input type="text"
                                       name="owner_name"
                                       id="owner_name"
                                       value="{{ old('owner_name', isset($restaurant) ? $restaurant->owner_name : '') }}"
                                       class="form-input"
                                       placeholder="Leave empty to use account name">
                            </div>
                            @error('owner_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div> -->
                    </div>
                    <!-- Description -->
                    <div>
                        <label for="description" class="form-label">
                            Restaurant Description
                        </label>
                        <div class="mt-1">
                            <textarea name="description"
                                      id="description"
                                      rows="3"
                                      class="form-input"
                                      placeholder="Brief description of the restaurant">{{ old('description', isset($restaurant) ? $restaurant->description : '') }}</textarea>
                        </div>
                        @error('description')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Operational Status -->
                    <div>
                        <label for="operational_status" class="form-label">
                            Operational Status
                        </label>
                        <div class="mt-1">
                            <select name="operational_status"
                                    id="operational_status"
                                    class="form-input">
                                <option value="open" {{ old('operational_status', isset($restaurant) ? $restaurant->operational_status : 'open') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="closed" {{ old('operational_status', isset($restaurant) ? $restaurant->operational_status : 'closed') == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        @error('operational_status')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <!-- Active Status -->
                    <div class="flex items-center">
                        <input type="checkbox"
                               name="is_active"
                               id="is_active"
                               value="1"
                               {{ old('is_active', isset($restaurant) ? $restaurant->is_active : true) ? 'checked' : '' }}
                               class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Restaurant is Active
                        </label>
                    </div>
                </div>
            </div>
            <!-- Form Actions -->
            <div class="card-footer text-right space-x-3">
                <a href="{{ route('admin.restaurants') }}" class="btn btn-outline">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    {{ isset($restaurant) ? 'Update Restaurant' : 'Create Restaurant' }}
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    const file = input.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" alt="Preview" class="h-full w-full object-cover rounded-lg">`;
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
