@extends('themes.classic.layout')

@section('title', $post['title'])
@section('description', $post['excerpt'] ?: Str::limit(strip_tags($post['content']), 160))

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-8">
        <a href="{{ route('blog.index') }}" class="text-gray-600 hover:text-amber-600 text-sm serif-text flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            記事一覧に戻る
        </a>
    </nav>

    <!-- Article Header -->
    <header class="mb-12 text-center">
        <!-- Post Meta -->
        <div class="flex items-center justify-center text-sm text-gray-600 mb-6 serif-text">
            <svg class="w-4 h-4 mr-2 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <time datetime="{{ $post['date'] }}">
                {{ date('Y年n月j日', strtotime($post['date'])) }}
            </time>
            <span class="mx-3">•</span>
            <span>{{ $post['author'] }}</span>
            @if(!empty($post['tags']))
                <span class="mx-3">•</span>
                <span>タグ: 
                @foreach($post['tags'] as $tag)
                    <a href="{{ route('blog.index', ['tag' => $tag]) }}" 
                       class="classic-accent hover:text-amber-700 underline">{{ $tag }}</a>{{ !$loop->last ? ', ' : '' }}
                @endforeach
                </span>
            @endif
        </div>

        <!-- Decorative Element -->
        <div class="ornament mb-8 pt-4"></div>

        <!-- Title -->
        <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-8 leading-tight serif-text">
            {{ $post['title'] }}
        </h1>
        
        <!-- Excerpt if available -->
        @if(!empty($post['excerpt']))
            <p class="text-xl text-gray-600 leading-relaxed max-w-3xl mx-auto italic">
                {{ $post['excerpt'] }}
            </p>
        @endif
    </header>

    <!-- Article Content -->
    <article class="prose prose-lg max-w-none mb-16 classic-prose classic-card p-8 rounded-lg classic-shadow bg-white">
        {!! (new \Parsedown())->text($post['content']) !!}
    </article>

    <!-- Related Posts -->
    @if(count($relatedPosts) > 0)
        <section class="border-t classic-border pt-12">
            <div class="text-center mb-8">
                <div class="ornament mb-4 pt-4"></div>
                <h2 class="text-2xl font-semibold text-gray-800 serif-text">
                    関連記事
                </h2>
            </div>
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                @foreach($relatedPosts as $relatedPost)
                    <article class="classic-card rounded-lg overflow-hidden classic-shadow">
                        <!-- Post Image -->
                        <div class="h-32 bg-gradient-to-br from-amber-50 to-orange-100 relative">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="classic-accent text-4xl serif-text">❦</span>
                            </div>
                        </div>
                        
                        <div class="p-4">
                            <!-- Post Meta -->
                            <div class="text-xs text-gray-600 mb-2 serif-text">
                                {{ date('Y年n月j日', strtotime($relatedPost['date'])) }}
                            </div>
                            
                            <!-- Post Title -->
                            <h3 class="font-semibold text-gray-800 mb-2 line-clamp-2 serif-text">
                                <a href="{{ route('blog.show', $relatedPost['slug']) }}" class="classic-hover">
                                    {{ $relatedPost['title'] }}
                                </a>
                            </h3>
                            
                            <!-- Post Excerpt -->
                            <p class="text-sm text-gray-600 line-clamp-2">
                                {{ Str::limit(strip_tags($relatedPost['content']), 80) }}
                            </p>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <!-- Navigation -->
    <nav class="border-t classic-border pt-8 mt-12">
        <div class="flex justify-between items-center">
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center classic-accent hover:text-amber-700 font-medium transition-colors serif-text">
                <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
                記事一覧に戻る
            </a>
            
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" 
                    class="text-gray-600 hover:text-amber-600 transition-colors serif-text flex items-center">
                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                </svg>
                ページトップへ
            </button>
        </div>
    </nav>
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Classic prose styles */
    .classic-prose h1, .classic-prose h2, .classic-prose h3, .classic-prose h4, .classic-prose h5, .classic-prose h6 {
        color: #8b4513;
        font-weight: 600;
        margin-top: 2.5rem;
        margin-bottom: 1.5rem;
        font-family: 'Crimson Text', serif;
    }
    
    .classic-prose h1 { 
        font-size: 2.25rem; 
        border-bottom: 3px solid #e5d5c8;
        padding-bottom: 0.5rem;
    }
    .classic-prose h2 { 
        font-size: 1.875rem;
        border-bottom: 2px solid #e5d5c8;
        padding-bottom: 0.25rem;
    }
    .classic-prose h3 { font-size: 1.5rem; }
    .classic-prose h4 { font-size: 1.25rem; }
    
    .classic-prose p {
        margin-bottom: 1.75rem;
        line-height: 1.8;
        color: #374151;
        text-align: justify;
    }
    
    .classic-prose ul, .classic-prose ol {
        margin-bottom: 1.75rem;
        padding-left: 2rem;
    }
    
    .classic-prose li {
        margin-bottom: 0.5rem;
        color: #374151;
    }
    
    .classic-prose blockquote {
        border-left: 4px solid #8b4513;
        padding-left: 2rem;
        margin: 2.5rem 0;
        font-style: italic;
        color: #6b7280;
        background: #faf8f5;
        padding: 2rem;
        border-radius: 0.5rem;
        position: relative;
    }
    
    .classic-prose blockquote::before {
        content: "“";
        font-size: 4rem;
        color: #8b4513;
        position: absolute;
        top: -0.5rem;
        left: 1rem;
        font-family: 'Crimson Text', serif;
        opacity: 0.3;
    }
    
    .classic-prose a {
        color: #8b4513;
        text-decoration: underline;
        font-weight: 500;
    }
    
    .classic-prose a:hover {
        color: #654321;
    }
    
    .classic-prose code {
        background: #f3f4f6;
        color: #8b4513;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
        font-size: 0.875em;
        font-family: 'Courier New', monospace;
        border: 1px solid #e5d5c8;
    }
    
    .classic-prose pre {
        background: #faf8f5;
        border-radius: 0.75rem;
        padding: 2rem;
        margin: 2rem 0;
        border: 2px solid #e5d5c8;
        overflow-x: auto;
    }
    
    .classic-prose pre code {
        background: transparent;
        padding: 0;
        border: none;
        color: #374151;
    }
    
    .classic-prose table {
        width: 100%;
        border-collapse: collapse;
        margin: 2rem 0;
        border: 2px solid #e5d5c8;
    }
    
    .classic-prose th, .classic-prose td {
        border: 1px solid #e5d5c8;
        padding: 0.75rem;
        text-align: left;
    }
    
    .classic-prose th {
        background: #faf8f5;
        color: #8b4513;
        font-weight: 600;
        font-family: 'Crimson Text', serif;
    }
    
    .classic-prose td {
        color: #374151;
    }
    
    .classic-prose img {
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        margin: 2rem auto;
    }
</style>
@endsection