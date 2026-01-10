<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post['title'] }} - ブログ</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Back to blog link -->
            <div class="mb-8">
                <a href="{{ route('blog.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                    ← ブログ一覧に戻る
                </a>
            </div>
            
            <!-- Article -->
            <article class="bg-white rounded-lg shadow p-8">
                <header class="mb-8">
                    <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $post['title'] }}</h1>
                    <div class="flex items-center text-gray-600 text-sm">
                        <span>作成者: {{ $post['author'] }}</span>
                        <span class="mx-2">•</span>
                        <time datetime="{{ $post['date'] }}">
                            {{ date('Y年m月d日', is_numeric($post['date']) ? $post['date'] : strtotime($post['date'])) }}
                        </time>
                        @if($post['slug'])
                            <span class="mx-2">•</span>
                            <span>スラッグ: {{ $post['slug'] }}</span>
                        @endif
                    </div>
                </header>
                
                <div class="prose prose-lg max-w-none">
                    {!! \Parsedown::instance()->text($post['content']) !!}
                </div>
            </article>
        </div>
    </div>
</body>
</html>