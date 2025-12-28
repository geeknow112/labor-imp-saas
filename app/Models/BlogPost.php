<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class BlogPost extends Model
{
    protected $fillable = [
        'filename',
        'title',
        'slug',
        'date',
        'author',
        'content'
    ];

    public static function getAllPosts()
    {
        $posts = [];
        $files = Storage::files('blog/posts');
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $content = Storage::get($file);
                $document = YamlFrontMatter::parse($content);
                
                $posts[] = new self([
                    'filename' => basename($file),
                    'title' => $document->matter('title', 'Untitled'),
                    'slug' => $document->matter('slug', ''),
                    'date' => $document->matter('date', ''),
                    'author' => $document->matter('author', ''),
                    'content' => $document->body(),
                ]);
            }
        }
        
        return collect($posts)->sortByDesc('date');
    }

    public function saveToFile()
    {
        $frontMatter = [
            'title' => $this->title,
            'slug' => $this->slug,
            'date' => $this->date,
            'author' => $this->author,
        ];
        
        $yamlContent = "---\n";
        foreach ($frontMatter as $key => $value) {
            $yamlContent .= "{$key}: {$value}\n";
        }
        $yamlContent .= "---\n\n";
        $yamlContent .= $this->content;
        
        Storage::put('blog/posts/' . $this->filename, $yamlContent);
    }

    public function deleteFile()
    {
        Storage::delete('blog/posts/' . $this->filename);
    }
}