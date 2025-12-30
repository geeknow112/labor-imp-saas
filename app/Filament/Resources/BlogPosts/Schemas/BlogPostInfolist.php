<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextEntry;

class BlogPostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextEntry::make('title')
                    ->label('タイトル'),
                    
                TextEntry::make('slug')
                    ->label('スラッグ'),
                    
                TextEntry::make('date')
                    ->label('日付')
                    ->date(),
                    
                TextEntry::make('author')
                    ->label('著者'),
                    
                TextEntry::make('content')
                    ->label('内容')
                    ->markdown(),
            ]);
    }
}