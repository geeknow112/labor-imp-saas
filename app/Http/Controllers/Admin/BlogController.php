<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index()
    {
        $posts = BlogPost::getAllPosts();
        return view('admin.blog.index', compact('posts'));
    }

    public function create()
    {
        return view('admin.blog.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'date' => 'required|date',
            'content' => 'required',
        ]);

        $filename = $request->slug . '.md';
        
        $post = new BlogPost([
            'filename' => $filename,
            'title' => $request->title,
            'slug' => $request->slug,
            'date' => $request->date,
            'author' => $request->author ?? '',
            'content' => $request->content,
        ]);

        $post->saveToFile();

        return redirect()->route('admin.blog.index')->with('success', '記事を作成しました');
    }

    public function edit($filename)
    {
        $posts = BlogPost::getAllPosts();
        $post = $posts->firstWhere('filename', $filename);
        
        if (!$post) {
            abort(404);
        }

        return view('admin.blog.edit', compact('post'));
    }

    public function update(Request $request, $filename)
    {
        $request->validate([
            'title' => 'required',
            'slug' => 'required',
            'date' => 'required|date',
            'content' => 'required',
        ]);

        // 古いファイルを削除
        Storage::delete('blog/posts/' . $filename);

        // 新しいファイル名
        $newFilename = $request->slug . '.md';
        
        $post = new BlogPost([
            'filename' => $newFilename,
            'title' => $request->title,
            'slug' => $request->slug,
            'date' => $request->date,
            'author' => $request->author ?? '',
            'content' => $request->content,
        ]);

        $post->saveToFile();

        return redirect()->route('admin.blog.index')->with('success', '記事を更新しました');
    }

    public function destroy($filename)
    {
        Storage::delete('blog/posts/' . $filename);
        return redirect()->route('admin.blog.index')->with('success', '記事を削除しました');
    }
}