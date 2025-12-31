<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostsResource;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostsResource::class;
    
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        // デバッグ用ログ
        \Log::info('CreateBlogPost handleRecordCreation called', $data);
        
        $filename = $data['slug'] . '.md';
        
        // 日付をstring形式に変換
        $date = $data['date'];
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d');
        } elseif (is_string($date)) {
            $date = date('Y-m-d', strtotime($date));
        }
        
        \Log::info('Processed date: ' . $date);
        \Log::info('Filename: ' . $filename);
        
        $post = new BlogPost([
            'filename' => $filename,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'date' => $date,
            'author' => $data['author'] ?? '',
            'content' => $data['content'],
        ]);

        \Log::info('About to save file');
        $post->saveToFile();
        \Log::info('File saved successfully');
        
        return $post;
    }
}