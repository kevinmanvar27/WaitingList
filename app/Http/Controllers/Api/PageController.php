<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PageController extends Controller
{
    /**
     * Display a listing of the pages.
     */
    public function index()
    {
        $pages = Page::orderBy('created_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'data' => $pages
        ]);
    }

    /**
     * Display the specified page for public API access.
     */
    public function publicShow($slug)
    {
        $page = Page::where('slug', $slug)->active()->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Store a newly created page in storage.
     */
    public function store(Request $request)
    {
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
            return response()->json([
                'success' => false,
                'message' => 'The slug has already been taken.',
            ], 422);
        }

        $page = Page::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Page created successfully!',
            'data' => $page
        ], 201);
    }

    /**
     * Display the specified page.
     */
    public function show($slug)
    {
        $page = Page::where('slug', $slug)->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $page
        ]);
    }

    /**
     * Update the specified page in storage.
     */
    public function update(Request $request, $id)
    {
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
            return response()->json([
                'success' => false,
                'message' => 'The slug has already been taken.',
            ], 422);
        }

        $page->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Page updated successfully!',
            'data' => $page
        ]);
    }

    /**
     * Remove the specified page from storage.
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        $page->delete();

        return response()->json([
            'success' => true,
            'message' => 'Page deleted successfully!'
        ]);
    }
}