<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class BlogController extends Controller
{
    public function index()
    {
        $posts = [];
        $files = Storage::files('blog/posts');
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $content = Storage::get($file);
                $document = YamlFrontMatter::parse($content);
                
                $posts[] = [
                    'filename' => basename($file),
                    'title' => $document->matter('title', 'Untitled'),
                    'slug' => $document->matter('slug', ''),
                    'date' => $document->matter('date', ''),
                    'author' => $document->matter('author', ''),
                    'content' => $document->body(),
                ];
            }
        }
        
        // 日付順でソート
        usort($posts, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return view('blog.index', compact('posts'));
    }
}