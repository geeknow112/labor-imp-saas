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
        $filename = $data['slug'] . '.md';
        
        $post = new BlogPost([
            'filename' => $filename,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'date' => $data['date'],
            'author' => $data['author'] ?? '',
            'content' => $data['content'],
        ]);

        $post->saveToFile();
        
        return $post;
    }
}