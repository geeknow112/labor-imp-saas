<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class BlogPost extends Model
{
    protected $fillable = [
        'filename', 'title', 'slug', 'date', 'author', 'content'
    ];

    protected $casts = [
        'date' => 'date',
    ];

    // ファイルに保存
    public function saveToFile()
    {
        $frontMatter = [
            'title' => $this->title,
            'slug' => $this->slug,
            'date' => $this->date->format('Y-m-d'),
            'author' => $this->author,
        ];
        
        $yamlContent = "---\n";
        foreach ($frontMatter as $key => $value) {
            $yamlContent .= "{$key}: {$value}\n";
        }
        $yamlContent .= "---\n\n";
        $yamlContent .= $this->content;
        
        Storage::disk('blog')->put('blog/posts/' . $this->filename, $yamlContent);
    }

    // ファイルから読み込み
    public function loadFromFile()
    {
        if (Storage::disk('blog')->exists('blog/posts/' . $this->filename)) {
            $content = Storage::disk('blog')->get('blog/posts/' . $this->filename);
            $document = YamlFrontMatter::parse($content);
            
            $this->title = $document->matter('title', 'Untitled');
            $this->slug = $document->matter('slug', '');
            $this->date = $document->matter('date', '');
            $this->author = $document->matter('author', '');
            $this->content = $document->body();
        }
    }

    // 保存時にファイルも更新
    protected static function booted()
    {
        static::saved(function ($blogPost) {
            $blogPost->saveToFile();
        });

        static::deleted(function ($blogPost) {
            Storage::disk('blog')->delete('blog/posts/' . $blogPost->filename);
        });
    }

    // 既存ファイルをDBに同期
    public static function syncFromFiles()
    {
        $files = Storage::disk('blog')->files('blog/posts');
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $filename = pathinfo($file, PATHINFO_BASENAME);
                $content = Storage::disk('blog')->get($file);
                $document = YamlFrontMatter::parse($content);
                
                $date = $document->matter('date', '');
                if (empty($date)) {
                    $date = now()->format('Y-m-d');
                }
                
                static::updateOrCreate(
                    ['filename' => $filename],
                    [
                        'title' => $document->matter('title', 'Untitled'),
                        'slug' => $document->matter('slug', str_replace('.md', '', $filename)),
                        'date' => $date,
                        'author' => $document->matter('author', 'Unknown'),
                        'content' => $document->body(),
                    ]
                );
            }
        }
    }
}