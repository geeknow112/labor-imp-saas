@extends('themes.classic.layout')

@section('title', 'ブログ記事一覧')
@section('description', '伝統的なスタイルで読みやすいブログ記事一覧')

@section('content')
<div class="space-y-12">
    <!-- Hero Section -->
    <div class="text-center py-20 classic-shadow rounded-lg bg-white/70 backdrop-blur-sm">
        <h1 class="text-5xl font-bold classic-accent serif-text mb-6">
            心を込めた記事をお届け
        </h1>
        <p class="text-xl text-gray-700 max-w-3xl mx-auto leading-relaxed mb-8">
            日々の学びや発見を、丁寧に綴った記事の数々。<br>
            ゆっくりとお楽しみください。
        </p>
        
        <!-- Search Form -->
        <div class="mt-12 max-w-lg mx-auto">
            <form method="GET" action="{{ route('blog.index') }}" class="relative">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="記事を検索..." 
                    class="w-full px-6 py-4 text-lg bg-white border classic-border rounded-full focus:outline-none focus:ring-2 focus:ring-amber-300 focus:border-transparent text-gray-800 serif-text classic-shadow"
                >
                <button type="submit" class="absolute right-3 top-3 text-gray-500 hover:text-amber-600 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </form>
        </div>
    </div>

    @if(request('search'))
        <div class="classic-card p-6 rounded-lg classic-shadow">
            <p class="text-gray-700 serif-text text-lg">
                <span class="classic-accent font-semibold">検索結果:</span> "{{ request('search') }}" 
                <span class="text-gray-500">（{{ count($posts) }}件の記事が見つかりました）</span>
                <a href="{{ route('blog.index') }}" class="ml-4 text-red-600 hover:text-red-700 underline">検索をクリア</a>
            </p>
        </div>
    @endif

    @if(isset($tag))
        <div class="classic-card p-6 rounded-lg classic-shadow">
            <p class="text-gray-700 serif-text text-lg">
                <span class="classic-accent font-semibold">タグ絞り込み:</span> "{{ $tag }}" 
                <span class="text-gray-500">（{{ count($posts) }}件の記事）</span>
                <a href="{{ route('blog.index') }}" class="ml-4 text-red-600 hover:text-red-700 underline">絞り込みを解除</a>
            </p>
        </div>
    @endif

    <!-- Blog Posts Grid -->
    @if(count($posts) > 0)
        <div class="grid gap-8 md:grid-cols-2 lg:grid-cols-3">
            @foreach($posts as $post)
                <article class="classic-card rounded-lg overflow-hidden classic-shadow">
                    <!-- Post Header -->
                    <div class="h-48 bg-gradient-to-br from-amber-50 to-orange-100 relative overflow-hidden">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <div class="text-center">
                                <span class="text-6xl classic-accent serif-text">❦</span>
                                <div class="text-amber-700 text-sm serif-text mt-2 italic">記事</div>
                            </div>
                        </div>
                        @if(!empty($post['tags']))
                            <div class="absolute top-4 left-4">
                                @foreach(array_slice($post['tags'], 0, 2) as $tag)
                                    <span class="inline-block bg-amber-100 text-amber-800 text-xs px-3 py-1 rounded-full mr-2 mb-2 border border-amber-200">
                                        {{ $tag }}
                                    </span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <div class="p-6">
                        <!-- Post Meta -->
                        <div class="flex items-center text-sm text-gray-600 mb-4 serif-text">
                            <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <time datetime="{{ $post['date'] }}">
                                {{ date('Y年n月j日', strtotime($post['date'])) }}
                            </time>
                            <span class="mx-2">•</span>
                            <span>{{ $post['author'] }}</span>
                        </div>
                        
                        <!-- Post Title -->
                        <h2 class="text-xl font-semibold text-gray-800 mb-4 leading-tight serif-text">
                            <a href="{{ route('blog.show', $post['slug']) }}" class="classic-hover">
                                {{ $post['title'] }}
                            </a>
                        </h2>
                        
                        <!-- Post Excerpt -->
                        @if(!empty($post['excerpt']))
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ $post['excerpt'] }}
                            </p>
                        @else
                            <p class="text-gray-600 mb-6 leading-relaxed">
                                {{ Str::limit(strip_tags($post['content']), 120) }}
                            </p>
                        @endif
                        
                        <!-- Read More Link -->
                        <a href="{{ route('blog.show', $post['slug']) }}" 
                           class="inline-flex items-center classic-accent hover:text-amber-700 font-medium transition-colors serif-text">
                            続きを読む
                            <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-20 classic-card rounded-lg classic-shadow">
            <div class="text-8xl mb-6 classic-accent serif-text">❦</div>
            <h3 class="text-2xl font-semibold text-gray-800 mb-4 serif-text">記事が見つかりません</h3>
            <p class="text-gray-600 mb-8">
                @if(request('search'))
                    検索条件に一致する記事がありませんでした
                @elseif(isset($tag))
                    「{{ $tag }}」タグの記事がありませんでした
                @else
                    まだ記事が投稿されていません
                @endif
            </p>
            @if(request('search') || isset($tag))
                <a href="{{ route('blog.index') }}" 
                   class="inline-block bg-amber-100 text-amber-800 px-6 py-3 rounded-full border border-amber-200 hover:bg-amber-200 transition-colors serif-text">
                    すべての記事を見る
                </a>
            @endif
        </div>
    @endif
</div>
@endsection