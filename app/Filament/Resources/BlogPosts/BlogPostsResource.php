<?php

namespace App\Filament\Resources\BlogPosts;

use App\Filament\Resources\BlogPosts\Pages\CreateBlogPost;
use App\Filament\Resources\BlogPosts\Pages\EditBlogPost;
use App\Filament\Resources\BlogPosts\Pages\ListBlogPosts;
use App\Models\BlogPost;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BlogPostsResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    
    protected static ?string $navigationLabel = 'Blog Posts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                \Filament\Schemas\Components\TextInput::make('title')
                    ->label('タイトル')
                    ->required(),
                    
                \Filament\Schemas\Components\TextInput::make('slug')
                    ->label('スラッグ')
                    ->required(),
                    
                \Filament\Schemas\Components\DatePicker::make('date')
                    ->label('日付')
                    ->required(),
                    
                \Filament\Schemas\Components\TextInput::make('author')
                    ->label('著者')
                    ->required(),
                    
                \Filament\Schemas\Components\Textarea::make('content')
                    ->label('内容')
                    ->required()
                    ->rows(10),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->records(fn (): \Illuminate\Support\Collection => BlogPost::getAllPosts())
            ->columns([
                \Filament\Tables\Columns\TextColumn::make('title')
                    ->label('タイトル')
                    ->searchable()
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('date')
                    ->label('日付')
                    ->date()
                    ->sortable(),
                    
                \Filament\Tables\Columns\TextColumn::make('author')
                    ->label('著者')
                    ->searchable(),
                    
                \Filament\Tables\Columns\TextColumn::make('filename')
                    ->label('ファイル名')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->bulkActions([
                //
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBlogPosts::route('/'),
            'create' => CreateBlogPost::route('/create'),
        ];
    }
}