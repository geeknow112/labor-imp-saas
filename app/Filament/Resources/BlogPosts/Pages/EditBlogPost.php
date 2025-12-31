<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostsResource;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function resolveRecord($key): \Illuminate\Database\Eloquent\Model
    {
        $posts = BlogPost::getAllPosts();
        $post = $posts->firstWhere('filename', $key);
        
        if (!$post) {
            abort(404);
        }
        
        return $post;
    }
    
    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        // 古いファイルを削除
        Storage::disk('blog')->delete('blog/posts/' . $record->filename);

        // 新しいファイル名
        $newFilename = $data['slug'] . '.md';
        
        // 日付をstring形式に変換
        $date = $data['date'];
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d');
        } elseif (is_string($date)) {
            $date = date('Y-m-d', strtotime($date));
        }
        
        $post = new BlogPost([
            'filename' => $newFilename,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'date' => $date,
            'author' => $data['author'] ?? '',
            'content' => $data['content'],
        ]);

        $post->saveToFile();
        
        return $post;
    }
}