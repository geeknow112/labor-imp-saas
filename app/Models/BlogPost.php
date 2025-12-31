<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class BlogPost extends Model
{
    // データベースを使用しない設定
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'filename';
    
    protected $fillable = [
        'filename',
        'title',
        'slug',
        'date',
        'author',
        'content'
    ];

    protected $guarded = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->exists = true; // ファイルベースなので常に存在する
    }

    public function getKey()
    {
        return $this->getAttribute('filename');
    }

    public function getKeyName()
    {
        return 'filename';
    }

    // データベース操作を無効化
    public function save(array $options = [])
    {
        $this->saveToFile();
        return true;
    }

    public function delete()
    {
        $this->deleteFile();
        return true;
    }

    public static function getAllPosts()
    {
        $posts = [];
        $files = Storage::disk('blog')->files('blog/posts');
        
        \Log::info('BlogPost getAllPosts - found files:', $files);
        
        foreach ($files as $file) {
            \Log::info('Processing file:', ['file' => $file]);
            if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $content = Storage::disk('blog')->get($file);
                \Log::info('File content length:', ['file' => $file, 'length' => strlen($content)]);
                
                $document = YamlFrontMatter::parse($content);
                
                $filename = pathinfo($file, PATHINFO_BASENAME);
                
                $post = new self();
                $post->filename = $filename;
                $post->title = $document->matter('title', 'Untitled');
                $post->slug = $document->matter('slug', '');
                $post->date = $document->matter('date', '');
                $post->author = $document->matter('author', '');
                $post->content = $document->body();
                
                \Log::info('Created post in getAllPosts:', [
                    'filename' => $post->filename,
                    'title' => $post->title,
                    'original_file' => $file,
                    'extracted_filename' => $filename
                ]);
                
                \Log::info('Created post:', ['title' => $post->title, 'filename' => $post->filename]);
                $posts[] = $post;
            }
        }
        
        \Log::info('Total posts found:', ['count' => count($posts)]);
        
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
        
        \Log::info('BlogPost saveToFile - attributes:', $this->attributes);
        \Log::info('BlogPost saveToFile - content:', ['content' => $this->content]);
        
        $yamlContent = "---\n";
        foreach ($frontMatter as $key => $value) {
            $yamlContent .= "{$key}: {$value}\n";
        }
        $yamlContent .= "---\n\n";
        $yamlContent .= $this->content;
        
        \Log::info('BlogPost saveToFile - final content:', ['yamlContent' => $yamlContent]);
        
        Storage::disk('blog')->put('blog/posts/' . $this->filename, $yamlContent);
    }

    public function deleteFile()
    {
        Storage::disk('blog')->delete('blog/posts/' . $this->filename);
    }
}