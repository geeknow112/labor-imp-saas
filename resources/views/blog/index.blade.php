<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ブログ記事管理</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-gray-900">ブログ記事管理</h1>
            <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                新規記事作成
            </button>
        </div>

        <div class="grid gap-6">
            @forelse($posts as $post)
                <div class="bg-white rounded-lg shadow p-6 border">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900">{{ $post['title'] }}</h3>
                            <p class="text-sm text-gray-500">
                                作成者: {{ $post['author'] }} | 
                                日付: {{ $post['date'] }} | 
                                ファイル: {{ $post['filename'] }}
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-1 px-3 rounded text-sm">
                                編集
                            </button>
                            <button class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded text-sm">
                                削除
                            </button>
                        </div>
                    </div>
                    
                    <div class="prose prose-sm max-w-none">
                        {!! \Illuminate\Support\Str::limit(strip_tags(\Parsedown::instance()->text($post['content'])), 200) !!}
                    </div>
                    
                    @if($post['slug'])
                        <div class="mt-4 pt-4 border-t">
                            <span class="text-xs text-gray-500">スラッグ: {{ $post['slug'] }}</span>
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-12">
                    <div class="text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">記事がありません</h3>
                        <p class="mt-1 text-sm text-gray-500">最初の記事を作成してください。</p>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</body>
</html>