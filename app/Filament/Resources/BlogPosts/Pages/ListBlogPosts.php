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