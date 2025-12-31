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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Textarea;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class BlogPostsResource extends Resource
{
    protected static ?string $model = BlogPost::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;
    
    protected static ?string $navigationLabel = 'Blog Posts';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('タイトル')
                    ->required(),
                    
                TextInput::make('slug')
                    ->label('スラッグ')
                    ->required(),
                    
                DateTimePicker::make('date')
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

    public static function table(Table $table): Table
    {
        return $table
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
            ->recordActions([
                EditAction::make()
                    ->url(fn (BlogPost $record): string => static::getUrl('edit', ['record' => pathinfo($record->filename, PATHINFO_FILENAME)])),
                DeleteAction::make()
                    ->action(function (BlogPost $record) {
                        $record->deleteFile();
                        redirect()->to(static::getUrl('index'));
                    }),
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
            'edit' => EditBlogPost::route('/{record}/edit'),
        ];
    }
}