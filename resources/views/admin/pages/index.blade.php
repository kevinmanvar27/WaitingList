@extends('admin.layout')

@section('title', 'Pages')

@section('content')
<div class="app-container">
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary">Pages</h1>
            <p class="text-muted mt-1">Manage your static content pages</p>
        </div>
        <div class="mt-4 sm:mt-0">
            <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Add New Page
            </a>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-6">
        <div class="card-content">
            <form method="GET" action="{{ route('admin.pages.index') }}">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                            </div>
                            <input 
                                type="text" 
                                name="search" 
                                value="{{ request('search') }}" 
                                placeholder="Search by title or slug..." 
                                class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm"
                            >
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <button type="submit" class="btn btn-primary">
                            <i data-lucide="search" class="w-4 h-4"></i>
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('admin.pages.index') }}" class="btn btn-outline">
                                <i data-lucide="x" class="w-4 h-4"></i>
                                Clear
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pages Table -->
    <div class="card">
        <div class="card-content">
            @if($pages->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Slug</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($pages as $index => $page)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $pages->firstItem() + $index }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $page->title }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-500">{{ $page->slug }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($page->is_active)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $page->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" class="text-blue-600" title="View Page">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <a href="{{ route('admin.pages.edit', $page->id) }}" class="text-primary" title="Edit Page">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </a>
                                            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this page?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600" title="Delete Page">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($pages->hasPages())
                    <div class="mt-6">
                        {{ $pages->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i data-lucide="file-text" class="w-12 h-12 text-gray-400 mx-auto"></i>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No pages found</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        @if(request('search'))
                            No pages match your search "{{ request('search') }}". 
                            <a href="{{ route('admin.pages.index') }}" class="text-primary hover:underline">Clear search</a> to see all pages.
                        @else
                            Get started by creating a new page.
                        @endif
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
                            <i data-lucide="plus" class="w-4 h-4"></i>
                            Add New Page
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endsection