@extends('admin.layout')

@section('title', 'Edit Page')

@section('content')
<div class="app-container">
    <div class="mb-6">
        <a href="{{ route('admin.pages.index') }}" class="btn btn-ghost">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            Back to Pages
        </a>
    </div>

    <div class="card">
        <div class="card-header">
            <h2 class="text-xl font-semibold">Edit Page</h2>
        </div>
        <div class="card-content">
            <form action="{{ route('admin.pages.update', $page->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="title" class="form-label">Page Title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $page->title) }}" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Page Slug</label>
                        <input type="text" name="slug" id="slug" class="form-input" value="{{ old('slug', $page->slug) }}">
                        <p class="text-sm text-muted mt-1">Will be automatically generated from title if left empty</p>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group mt-6">
                    <label for="content" class="form-label">Page Content</label>
                    <div id="editor" style="height: 400px;"></div>
                    <input type="hidden" name="content" id="content" value="{{ old('content', $page->content) }}">
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ old('is_active', $page->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                    <p class="text-sm text-muted mt-1">Inactive pages will not be visible to users</p>
                </div>
                
                <div class="flex justify-end mt-8">
                    <a href="{{ route('admin.pages.index') }}" class="btn btn-outline mr-3">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Update Page
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Collapsible API Information Section -->
    <div class="card mt-6">
        <div class="card-header cursor-pointer" onclick="toggleApiInfo()">
            <div class="flex justify-between items-center">
                <h2 class="text-xl font-semibold">API Information</h2>
                <i data-lucide="chevron-down" class="w-5 h-5 transition-transform duration-200" id="api-chevron"></i>
            </div>
        </div>
        <div class="card-content hidden" id="api-content">
            <p class="text-muted mb-4">Use the following API endpoint to access this page's content for mobile applications:</p>
            
            <div class="bg-gray-50 p-4 rounded-lg mb-4">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-medium text-sm text-gray-500">API Endpoint</p>
                        <p class="font-mono text-sm break-all mt-1">
                            {{ url('/api/pages/' . $page->slug) }}
                        </p>
                    </div>
                    <button onclick="copyToClipboard('{{ url('/api/pages/' . $page->slug) }}'); event.stopPropagation();" class="btn btn-outline btn-sm ml-2">
                        <i data-lucide="copy" class="w-4 h-4"></i>
                        Copy
                    </button>
                </div>
            </div>
            
            <div class="bg-gray-50 p-4 rounded-lg">
                <p class="font-medium text-sm text-gray-500 mb-2">Response Format</p>
                <pre class="text-sm bg-white p-3 rounded border overflow-x-auto">{
  "success": true,
  "data": {
    "id": {{ $page->id }},
    "title": "{{ $page->title }}",
    "slug": "{{ $page->slug }}",
    "content": "{{ substr(str_replace('"', '\"', $page->content), 0, 50) }}...",
    "is_active": {{ $page->is_active ? 'true' : 'false' }},
    "created_at": "{{ $page->created_at->toISOString() }}",
    "updated_at": "{{ $page->updated_at->toISOString() }}"
  }
}</pre>
            </div>
            
            <div class="mt-4 p-4 bg-blue-50 rounded-lg">
                <p class="text-sm text-blue-800">
                    <i data-lucide="info" class="w-4 h-4 inline mr-1"></i>
                    Note: This endpoint is publicly accessible and does not require authentication. Only active pages can be accessed via this API.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function() {
        const title = this.value;
        const slug = title.toLowerCase()
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
        
        // Only update slug if it hasn't been manually changed
        if (!document.getElementById('slug').value || document.getElementById('slug').value === '{{ $page->slug }}') {
            document.getElementById('slug').value = slug;
        }
    });
    
    // Initialize Quill editor
    const quill = new Quill('#editor', {
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, false] }],
                ['bold', 'italic', 'underline'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image'],
                ['clean']
            ]
        },
        placeholder: 'Enter page content here...',
        theme: 'snow'
    });
    
    // Sync Quill content to hidden input
    const contentInput = document.getElementById('content');
    quill.on('text-change', function() {
        contentInput.value = quill.root.innerHTML;
    });
    
    // Set initial content
    var initialContent = `{!! old('content', $page->content) !!}`;
    if (initialContent) {
        quill.root.innerHTML = initialContent;
    }
    
    // Toggle API information section
    function toggleApiInfo() {
        const content = document.getElementById('api-content');
        const chevron = document.getElementById('api-chevron');
        
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
        }
    }
    
    // Copy to clipboard function
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            // Show feedback
            const originalText = event.target.innerHTML;
            event.target.innerHTML = '<i data-lucide="check" class="w-4 h-4"></i> Copied!';
            lucide.createIcons();
            
            // Reset after 2 seconds
            setTimeout(function() {
                event.target.innerHTML = originalText;
                lucide.createIcons();
            }, 2000);
        });
    }
    
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endsection