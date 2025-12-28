@extends('blog.layout')

@section('title', $post->title)
@section('description', $post->excerpt ?: Str::limit(strip_tags($post->content), 160))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Main Content -->
    <div class="lg:col-span-3">
        <article class="bg-white rounded-lg shadow-sm border overflow-hidden">
            <!-- Article Header -->
            <div class="p-8 border-b">
                <div class="mb-6">
                    <a 
                        href="{{ route('blog.index') }}" 
                        class="inline-flex items-center text-blue-600 hover:text-blue-800 text-sm font-medium"
                    >
                        <svg class="mr-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        記事一覧に戻る
                    </a>
                </div>
                
                <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4">
                    {{ $post->title }}
                </h1>
                
                @if($post->excerpt)
                    <p class="text-xl text-gray-600 mb-6">{{ $post->excerpt }}</p>
                @endif
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center text-sm text-gray-500">
                        <time datetime="{{ $post->published_at?->format('Y-m-d') }}">
                            {{ $post->published_at?->format('Y年m月d日') }}
                        </time>
                        @if($post->updated_at && $post->updated_at->format('Y-m-d') !== $post->published_at?->format('Y-m-d'))
                            <span class="mx-2">•</span>
                            <span>更新: {{ $post->updated_at->format('Y年m月d日') }}</span>
                        @endif
                    </div>
                    
                    @if(!empty($post->tags))
                        <div class="flex flex-wrap gap-2">
                            @foreach($post->tags as $tag)
                                <a 
                                    href="{{ route('blog.index', ['tag' => $tag]) }}"
                                    class="inline-block px-3 py-1 bg-gray-100 text-gray-700 text-sm rounded-full hover:bg-gray-200 transition-colors"
                                >
                                    #{{ $tag }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Article Content -->
            <div class="p-8">
                <div class="prose prose-lg max-w-none">
                    {!! $post->getRenderedContent() !!}
                </div>
            </div>
        </article>

        <!-- Related Posts -->
        @if($relatedPosts->count() > 0)
            <div class="mt-12">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">関連記事</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($relatedPosts as $relatedPost)
                        <article class="bg-white rounded-lg shadow-sm border p-6 hover:shadow-md transition-shadow">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                <a href="{{ $relatedPost->getUrl() }}" class="hover:text-blue-600 transition-colors">
                                    {{ $relatedPost->title }}
                                </a>
                            </h3>
                            
                            @if($relatedPost->excerpt)
                                <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ $relatedPost->excerpt }}</p>
                            @endif
                            
                            <div class="flex items-center justify-between">
                                <time class="text-xs text-gray-500">
                                    {{ $relatedPost->published_at?->format('Y/m/d') }}
                                </time>
                                
                                @if(!empty($relatedPost->tags))
                                    <div class="flex gap-1">
                                        @foreach(array_slice($relatedPost->tags, 0, 2) as $tag)
                                            <span class="inline-block px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1">
        <div class="space-y-6">
            <!-- Table of Contents (if needed) -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">この記事について</h3>
                <div class="space-y-2 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>公開日:</span>
                        <span>{{ $post->published_at?->format('Y/m/d') }}</span>
                    </div>
                    @if($post->updated_at && $post->updated_at->format('Y-m-d') !== $post->published_at?->format('Y-m-d'))
                        <div class="flex justify-between">
                            <span>更新日:</span>
                            <span>{{ $post->updated_at->format('Y/m/d') }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between">
                        <span>文字数:</span>
                        <span>約{{ number_format(mb_strlen(strip_tags($post->content))) }}文字</span>
                    </div>
                </div>
            </div>

            <!-- Share Buttons -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">シェア</h3>
                <div class="space-y-2">
                    <a 
                        href="https://twitter.com/intent/tweet?text={{ urlencode($post->title) }}&url={{ urlencode(request()->fullUrl()) }}"
                        target="_blank"
                        class="flex items-center w-full px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                        </svg>
                        Twitter
                    </a>
                    
                    <button 
                        onclick="copyToClipboard()"
                        class="flex items-center w-full px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        URLをコピー
                    </button>
                </div>
            </div>

            <!-- Recent Posts -->
            <div class="bg-white rounded-lg shadow-sm border p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">最新記事</h3>
                <div class="space-y-3">
                    @foreach(\App\Models\BlogPost::published()->where('slug', '!=', $post->slug)->take(5) as $recentPost)
                        <div>
                            <a 
                                href="{{ $recentPost->getUrl() }}" 
                                class="block text-sm font-medium text-gray-900 hover:text-blue-600 transition-colors line-clamp-2"
                            >
                                {{ $recentPost->title }}
                            </a>
                            <time class="text-xs text-gray-500">
                                {{ $recentPost->published_at?->format('m/d') }}
                            </time>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyToClipboard() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('URLをクリップボードにコピーしました');
    });
}
</script>
@endsection