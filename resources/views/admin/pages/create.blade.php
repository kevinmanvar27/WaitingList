@extends('admin.layout')

@section('title', 'Add New Page')

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
            <h2 class="text-xl font-semibold">Add New Page</h2>
        </div>
        <div class="card-content">
            <form action="{{ route('admin.pages.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label for="title" class="form-label">Page Title</label>
                        <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" required>
                        @error('title')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="form-group">
                        <label for="slug" class="form-label">Page Slug</label>
                        <input type="text" name="slug" id="slug" class="form-input" value="{{ old('slug') }}">
                        <p class="text-sm text-muted mt-1">Will be automatically generated from title if left empty</p>
                        @error('slug')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="form-group mt-6">
                    <label for="content" class="form-label">Page Content</label>
                    <div id="editor" style="height: 400px;"></div>
                    <input type="hidden" name="content" id="content" value="{{ old('content') }}">
                    @error('content')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="form-group mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="is_active" class="rounded border-gray-300 text-primary shadow-sm focus:border-primary focus:ring focus:ring-primary focus:ring-opacity-50" {{ old('is_active', true) ? 'checked' : '' }}>
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
                        Save Page
                    </button>
                </div>
            </form>
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
        
        if (!document.getElementById('slug').value) {
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
    
    // Set initial content if exists
    var initialContent = `{!! old('content') !!}`;
    if (initialContent) {
        quill.root.innerHTML = initialContent;
    }
    
    // Initialize Lucide icons
    lucide.createIcons();
</script>
@endsection