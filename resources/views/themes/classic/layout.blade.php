<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'ブログ') - {{ config('app.name') }}</title>
    <meta name="description" content="@yield('description', 'ブログ記事一覧')">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts - Classic Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Crimson+Text:ital,wght@0,400;0,600;1,400&family=Lora:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    
    <!-- Highlight.js Classic Theme -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
    
    <style>
        :root {
            --primary-color: {{ $themeConfig['primary_color'] ?? '#8b4513' }};
            --secondary-color: {{ $themeConfig['secondary_color'] ?? '#654321' }};
            --font-family: {{ $themeConfig['font_family'] ?? 'Lora' }}, serif;
        }
        
        body {
            font-family: var(--font-family);
            background: #faf8f5;
            color: #2c1810;
            line-height: 1.7;
        }
        
        .classic-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        
        .classic-border {
            border-color: #e5d5c8;
        }
        
        .classic-hover:hover {
            color: var(--primary-color);
            transition: all 0.3s ease;
        }
        
        .classic-card {
            background: #ffffff;
            border: 1px solid #e5d5c8;
            transition: all 0.3s ease;
        }
        
        .classic-card:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            transform: translateY(-2px);
        }
        
        .serif-text {
            font-family: 'Crimson Text', serif;
        }
        
        .classic-accent {
            color: var(--primary-color);
        }
        
        .classic-bg {
            background: linear-gradient(135deg, #faf8f5 0%, #f5f1eb 100%);
        }
        
        .ornament {
            position: relative;
        }
        
        .ornament::before {
            content: "❦";
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            color: var(--primary-color);
            font-size: 1.5rem;
            opacity: 0.6;
        }
    </style>
</head>
<body class="classic-bg min-h-screen">
    <!-- Header -->
    <header class="border-b classic-border bg-white/80 backdrop-blur-sm sticky top-0 z-50 classic-shadow">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('blog.index') }}" class="text-3xl font-semibold classic-accent serif-text">
                        {{ config('app.name') }}
                    </a>
                    <span class="text-sm text-gray-600 hidden sm:block italic">
                        ~ 伝統的なブログスタイル ~
                    </span>
                </div>
                <nav class="flex items-center space-x-8">
                    <a href="{{ route('blog.index') }}" class="text-gray-700 classic-hover font-medium">
                        記事一覧
                    </a>
                    <a href="{{ route('filament.admin.pages.dashboard') }}" class="bg-amber-100 text-amber-800 px-4 py-2 rounded-full border border-amber-200 hover:bg-amber-200 transition-colors">
                        管理画面
                    </a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="border-t classic-border mt-20 bg-white/50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="text-center">
                <div class="ornament mb-8 pt-6"></div>
                <p class="text-gray-600 mb-6 serif-text text-lg">
                    © {{ date('Y') }} {{ config('app.name') }} - 心を込めて書かれた記事をお届けします
                </p>
                <div class="flex justify-center space-x-8">
                    <a href="#" class="text-gray-500 classic-hover">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84"/>
                        </svg>
                    </a>
                    <a href="#" class="text-gray-500 classic-hover">
                        <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                            <path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        hljs.highlightAll();
    </script>
</body>
</html>