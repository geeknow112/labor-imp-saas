<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label('タイトル')
                    ->searchable()
                    ->sortable(),
                    
                TextColumn::make('date')
                    ->label('日付')
                    ->date()
                    ->sortable(),
                    
                TextColumn::make('author')
                    ->label('著者')
                    ->searchable(),
                    
                TextColumn::make('filename')
                    ->label('ファイル名')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}