<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\YamlFrontMatter\YamlFrontMatter;

class BlogPost extends Model
{
    public $timestamps = false;
    public $incrementing = false;
    protected $keyType = 'string';
    protected $primaryKey = 'filename';
    
    protected $fillable = [
        'filename', 'title', 'slug', 'date', 'author', 'content'
    ];

    public function getConnection()
    {
        // ダミーの接続オブジェクトを返す
        return app('db')->connection('mysql');
    }

    // クエリメソッドをオーバーライド
    public function newQuery()
    {
        // 実際のEloquent Builderを返すが、データベースクエリは実行しない
        $query = parent::newQuery();
        
        // クエリの実行をオーバーライドするためのカスタムビルダーを作成
        return new class($query->getQuery(), $this) extends \Illuminate\Database\Eloquent\Builder {
            public function __construct($query, $model) {
                parent::__construct($query);
                $this->setModel($model);
            }
            
            public function get($columns = ['*']) {
                return BlogPost::getAllPosts();
            }
            
            public function paginate($perPage = null, $columns = ['*'], $pageName = 'page', $page = null, $total = null) {
                $posts = BlogPost::getAllPosts();
                $perPage = $perPage ?: 15;
                $page = $page ?: request()->get($pageName, 1);
                return new \Illuminate\Pagination\LengthAwarePaginator(
                    $posts->forPage($page, $perPage),
                    $posts->count(),
                    $perPage,
                    $page,
                    ['path' => request()->url(), 'pageName' => $pageName]
                );
            }
            
            public function first($columns = ['*']) {
                return BlogPost::getAllPosts()->first();
            }
            
            public function count() {
                return BlogPost::getAllPosts()->count();
            }
        };
    }

    public static function query()
    {
        return (new static)->newQuery();
    }

    public function resolveRouteBinding($value, $field = null)
    {
        $posts = static::getAllPosts();
        $post = $posts->firstWhere('filename', $value . '.md');
        if (!$post) {
            $post = $posts->firstWhere('slug', $value);
        }
        return $post;
    }

    public static function getAllPosts()
    {
        $posts = [];
        $files = Storage::disk('blog')->files('blog/posts');
        
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'md') {
                $content = Storage::disk('blog')->get($file);
                $document = YamlFrontMatter::parse($content);
                $filename = pathinfo($file, PATHINFO_BASENAME);
                
                $post = new self();
                $post->filename = $filename;
                $post->title = $document->matter('title', 'Untitled');
                $post->slug = $document->matter('slug', '');
                $post->date = $document->matter('date', '');
                $post->author = $document->matter('author', '');
                $post->content = $document->body();
                $post->exists = true;
                
                $posts[] = $post;
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
        
        Storage::disk('blog')->put('blog/posts/' . $this->filename, $yamlContent);
    }

    public function save(array $options = [])
    {
        $this->saveToFile();
        return true;
    }

    public function delete()
    {
        Storage::disk('blog')->delete('blog/posts/' . $this->filename);
        return true;
    }
}