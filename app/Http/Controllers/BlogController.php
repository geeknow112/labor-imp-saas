<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Spatie\YamlFrontMatter\YamlFrontMatter;
use App\Models\ThemeSetting;

class BlogController extends Controller
{
    public function index()
    {
        $posts = [];
        // 直接パスを指定してファイルを取得
        $blogPath = storage_path('app/blog/posts');
        
        if (!is_dir($blogPath)) {
            \Log::error('Blog posts directory not found: ' . $blogPath);
            return view('blog.index', compact('posts'));
        }
        
        $files = glob($blogPath . '/*.md');
        \Log::info('Files found: ' . count($files));
        
        foreach ($files as $filePath) {
            \Log::info('Processing file: ' . $filePath);
            
            if (is_file($filePath)) {
                $content = file_get_contents($filePath);
                $document = YamlFrontMatter::parse($content);
                
                $post = [
                    'filename' => basename($filePath),
                    'title' => $document->matter('title', 'Untitled'),
                    'slug' => $document->matter('slug', ''),
                    'date' => $document->matter('date', ''),
                    'author' => $document->matter('author', ''),
                    'content' => $document->body(),
                    'excerpt' => $document->matter('excerpt', ''),
                ];
                
                \Log::info('Post data: ', $post);
                $posts[] = $post;
            }
        }
        
        \Log::info('Total posts processed: ' . count($posts));
        
        // 日付順でソート
        usort($posts, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        // テーマ設定を取得
        $activeTheme = ThemeSetting::getActiveTheme();
        $themeConfig = ThemeSetting::getThemeConfig();
        
        // テーマに応じたビューを選択
        $viewPath = "themes.{$activeTheme}.index";
        
        // テーマビューが存在しない場合はデフォルトを使用
        if (!view()->exists($viewPath)) {
            $viewPath = 'blog.index';
        }
        
        return view($viewPath, compact('posts', 'themeConfig'));
    }
    
    public function show($slug)
    {
        // 直接パスを指定してファイルを取得
        $blogPath = storage_path('app/blog/posts');
        
        if (!is_dir($blogPath)) {
            abort(404);
        }
        
        $files = glob($blogPath . '/*.md');
        
        foreach ($files as $filePath) {
            if (is_file($filePath)) {
                $content = file_get_contents($filePath);
                $document = YamlFrontMatter::parse($content);
                
                $postSlug = $document->matter('slug', '');
                
                if ($postSlug === $slug) {
                    $post = [
                        'filename' => basename($filePath),
                        'title' => $document->matter('title', 'Untitled'),
                        'slug' => $postSlug,
                        'date' => $document->matter('date', ''),
                        'author' => $document->matter('author', ''),
                        'content' => $document->body(),
                        'excerpt' => $document->matter('excerpt', ''),
                    ];
                    
                    // テーマ設定を取得
                    $activeTheme = ThemeSetting::getActiveTheme();
                    $themeConfig = ThemeSetting::getThemeConfig();
                    
                    // 関連記事を取得（同じ作者の他の記事を3件まで）
                    $relatedPosts = [];
                    $relatedCount = 0;
                    foreach ($files as $relatedFilePath) {
                        if ($relatedCount >= 3) break;
                        if ($relatedFilePath === $filePath) continue; // 現在の記事は除外
                        
                        if (is_file($relatedFilePath)) {
                            $relatedContent = file_get_contents($relatedFilePath);
                            $relatedDocument = YamlFrontMatter::parse($relatedContent);
                            
                            // 同じ作者の記事のみ関連記事とする
                            if ($relatedDocument->matter('author', '') === $post['author']) {
                                $relatedPosts[] = [
                                    'filename' => basename($relatedFilePath),
                                    'title' => $relatedDocument->matter('title', 'Untitled'),
                                    'slug' => $relatedDocument->matter('slug', ''),
                                    'date' => $relatedDocument->matter('date', ''),
                                    'author' => $relatedDocument->matter('author', ''),
                                    'content' => $relatedDocument->body(),
                                    'excerpt' => $relatedDocument->matter('excerpt', ''),
                                ];
                                $relatedCount++;
                            }
                        }
                    }
                    
                    // テーマに応じたビューを選択
                    $viewPath = "themes.{$activeTheme}.show";
                    
                    // テーマビューが存在しない場合はデフォルトを使用
                    if (!view()->exists($viewPath)) {
                        $viewPath = 'blog.show';
                    }
                    
                    return view($viewPath, compact('post', 'themeConfig', 'relatedPosts'));
                }
            }
        }
        
        abort(404);
    }
}