<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostsResource;
use App\Models\BlogPost;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Collection;

class ListBlogPosts extends ListRecords
{
    protected static string $resource = BlogPostsResource::class;

    public function getTableRecords(): \Illuminate\Support\Collection
    {
        $posts = BlogPost::all();
        
        // デバッグ用ログ
        \Log::info('getTableRecords - posts count:', ['count' => $posts->count()]);
        
        // 各投稿にIDを設定
        $posts->each(function ($post, $index) {
            $post->id = $index + 1;
            \Log::info('Post details:', [
                'id' => $post->id,
                'title' => $post->title,
                'filename' => $post->filename,
                'date' => $post->date
            ]);
        });
        
        return $posts;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    // レコードキーを取得
    public function getTableRecordKey($record): string
    {
        return $record->filename ?? 'unknown';
    }
}