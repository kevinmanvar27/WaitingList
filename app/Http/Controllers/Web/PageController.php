<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $query = Page::orderBy('created_at', 'desc');
        
        // Handle search
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        $pages = $query->paginate(15)->appends(['search' => $request->search]);
        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new page.
     */
    public function create()
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        return view('admin.pages.create');
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['title', 'content', 'is_active']);
        $data['is_active'] = $request->has('is_active');
        
        // Generate slug from title if not provided
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        // Check for duplicate slug
        if (Page::where('slug', $data['slug'])->exists()) {
            throw ValidationException::withMessages([
                'slug' => ['The slug has already been taken.'],
            ]);
        }

        Page::create($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page created successfully!');
    }

    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->active()->firstOrFail();
        return view('pages.show', compact('page'));
    }

    /**
     * Show the form for editing the specified page.
     */
    public function edit($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $page = Page::findOrFail($id);
        return view('admin.pages.edit', compact('page'));
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, $id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $page = Page::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug,' . $page->id,
            'content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $data = $request->only(['title', 'content', 'is_active']);
        $data['is_active'] = $request->has('is_active');
        
        // Generate slug from title if not provided
        $data['slug'] = $request->slug ? Str::slug($request->slug) : Str::slug($request->title);

        // Check for duplicate slug (excluding current page)
        if (Page::where('slug', $data['slug'])->where('id', '!=', $page->id)->exists()) {
            throw ValidationException::withMessages([
                'slug' => ['The slug has already been taken.'],
            ]);
        }

        $page->update($data);

        return redirect()->route('admin.pages.index')->with('success', 'Page updated successfully!');
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy($id)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            return redirect()->route('admin.login');
        }

        $page = Page::findOrFail($id);
        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Page deleted successfully!');
    }
}