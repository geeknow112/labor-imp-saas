<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $posts = BlogPost::orderBy('date', 'desc')->get();
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:blog_posts,slug',
            'date' => 'required|date',
            'author' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Generate filename from slug
        $validated['filename'] = $validated['slug'] . '.md';

        $post = BlogPost::create($validated);

        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost)
    {
        return response()->json($blogPost);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blogPost)
    {
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|required|string|max:255|unique:blog_posts,slug,' . $blogPost->id,
            'date' => 'sometimes|required|date',
            'author' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
        ]);

        // Update filename if slug changed
        if (isset($validated['slug'])) {
            $validated['filename'] = $validated['slug'] . '.md';
        }

        $blogPost->update($validated);

        return response()->json($blogPost);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost)
    {
        $blogPost->delete();

        return response()->json(null, 204);
    }
}