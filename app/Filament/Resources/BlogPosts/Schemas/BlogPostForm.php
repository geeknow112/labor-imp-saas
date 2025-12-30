<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\TextInput;
use Filament\Schemas\Components\DatePicker;
use Filament\Schemas\Components\Textarea;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                TextInput::make('title')
                    ->label('タイトル')
                    ->required(),
                    
                TextInput::make('slug')
                    ->label('スラッグ')
                    ->required(),
                    
                DatePicker::make('date')
                    ->label('日付')
                    ->required(),
                    
                TextInput::make('author')
                    ->label('著者')
                    ->required(),
                    
                Textarea::make('content')
                    ->label('内容')
                    ->required()
                    ->rows(10),
            ]);
    }
}