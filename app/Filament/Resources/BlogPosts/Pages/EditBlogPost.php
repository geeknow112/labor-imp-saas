<?php

namespace App\Filament\Resources\BlogPosts\Pages;

use App\Filament\Resources\BlogPosts\BlogPostsResource;
use App\Models\BlogPost;
use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\RichEditor;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Storage;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostsResource::class;
    
    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('タイトル')
                    ->required()
                    ->maxLength(255),
                    
                TextInput::make('slug')
                    ->label('スラッグ')
                    ->required()
                    ->maxLength(255),
                    
                DatePicker::make('date')
                    ->label('日付')
                    ->required(),
                    
                TextInput::make('author')
                    ->label('著者')
                    ->required()
                    ->maxLength(255),
                    
                RichEditor::make('content')
                    ->label('内容')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // ファイルからデータを読み込み
        $filename = $this->record . '.md';
        $posts = BlogPost::getAllPosts();
        $post = $posts->firstWhere('filename', $filename);
        
        if ($post) {
            return [
                'title' => $post->title,
                'slug' => $post->slug,
                'date' => $post->date,
                'author' => $post->author,
                'content' => $post->content,
            ];
        }
        
        return $data;
    }
    
    public function save(bool $shouldRedirect = true, bool $shouldSendSavedNotification = true): void
    {
        $data = $this->form->getState();
        
        // 古いファイルを削除
        $oldFilename = $this->record . '.md';
        Storage::disk('blog')->delete('blog/posts/' . $oldFilename);
        
        // 新しいファイル名を生成
        $newFilename = $data['slug'] . '.md';
        
        // 日付を適切な形式に変換
        $date = $data['date'];
        if ($date instanceof \DateTime) {
            $date = $date->format('Y-m-d');
        }
        
        // 新しいBlogPostインスタンスを作成
        $post = new BlogPost([
            'filename' => $newFilename,
            'title' => $data['title'],
            'slug' => $data['slug'],
            'date' => $date,
            'author' => $data['author'],
            'content' => $data['content'],
        ]);
        
        // ファイルに保存
        $post->saveToFile();
        
        if ($shouldRedirect) {
            $this->redirect(BlogPostsResource::getUrl('index'));
        }
    }
}