<?php

// EditBlogPostページの動作をテストするスクリプト

$baseUrl = 'http://localhost:8000';
$editUrl = $baseUrl . '/admin/blog-posts/1/edit';

// セッションを維持するためのCookieJar
$cookieFile = tempnam(sys_get_temp_dir(), 'cookies');

// まずログインページにアクセスしてセッションを取得
$loginUrl = $baseUrl . '/admin/login';
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $loginUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_exec($ch);
curl_close($ch);

// EditBlogPostページにアクセス
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $editUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HEADER, true);
curl_setopt($ch, CURLOPT_NOBODY, false);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);

curl_close($ch);

$headers = substr($response, 0, $headerSize);
$body = substr($response, $headerSize);

echo "=== EditBlogPost Page Test ===\n";
echo "URL: $editUrl\n";
echo "HTTP Status: $httpCode\n";

if ($httpCode === 200) {
    echo "✅ Page loaded successfully\n";
    
    // HTMLからタイトルを抽出
    if (preg_match('/<title>(.*?)<\/title>/i', $body, $matches)) {
        echo "Page Title: " . trim($matches[1]) . "\n";
    }
    
    // エラーメッセージを詳細に検索
    $errorPatterns = [
        '/Livewire\\\\Exceptions\\\\[^\\n]+/',
        '/Symfony\\\\Component\\\\ErrorHandler[^\\n]+/',
        '/TypeError[^\\n]+/',
        '/InvalidArgumentException[^\\n]+/',
        '/PropertyNotFoundException[^\\n]+/',
        '/Fatal error[^\\n]+/',
        '/Parse error[^\\n]+/',
        '/Error[^\\n]+/',
        '/Exception[^\\n]+/',
    ];
    
    $errorFound = false;
    foreach ($errorPatterns as $pattern) {
        if (preg_match($pattern, $body, $matches)) {
            echo "❌ Error detected: " . trim($matches[0]) . "\n";
            $errorFound = true;
        }
    }
    
    // HTMLソース内のエラーメッセージを検索
    if (preg_match('/(TypeError|Error|Exception)[^<]*/', $body, $matches)) {
        echo "❌ Error in HTML: " . trim($matches[0]) . "\n";
        $errorFound = true;
    }
    
    // より詳細なエラー情報を抽出
    if (preg_match('/TypeError[^<]*vendor\/filament[^<]*/', $body, $matches)) {
        echo "🔍 Detailed Error: " . trim($matches[0]) . "\n";
    }
    
    // Filamentエラーを特別に検索
    if (preg_match('/Return value must be of type[^<]*/', $body, $matches)) {
        echo "🔍 Type Error Details: " . trim($matches[0]) . "\n";
    }
    
    // エラーページの詳細を抽出
    if (strpos($body, 'Whoops, looks like something went wrong') !== false) {
        echo "❌ Laravel error page detected\n";
        
        // エラーメッセージを抽出
        if (preg_match('/<h1[^>]*class="exception_title"[^>]*>(.*?)<\/h1>/s', $body, $matches)) {
            echo "Error Title: " . strip_tags($matches[1]) . "\n";
        }
        
        if (preg_match('/<p[^>]*class="break-long-words exception_message"[^>]*>(.*?)<\/p>/s', $body, $matches)) {
            echo "Error Message: " . strip_tags($matches[1]) . "\n";
        }
        
        // ファイル名と行番号を抽出
        if (preg_match('/in file <strong>(.*?)<\/strong> on line <strong>(\d+)<\/strong>/', $body, $matches)) {
            echo "Error Location: " . $matches[1] . ":" . $matches[2] . "\n";
        }
    }
    
    if (!$errorFound && strpos($body, 'Whoops') === false) {
        echo "✅ No errors detected\n";
    }
    
    // フォームが表示されているかチェック
    if (strpos($body, '<form') !== false) {
        echo "✅ Form found in response\n";
    } else {
        echo "❌ No form found in response\n";
    }
    
} elseif ($httpCode === 404) {
    echo "❌ Page not found (404)\n";
} elseif ($httpCode === 302) {
    echo "🔄 Redirect detected\n";
    if (preg_match('/Location: (.+)/i', $headers, $matches)) {
        echo "Redirect to: " . trim($matches[1]) . "\n";
    }
} else {
    echo "❌ HTTP Error: $httpCode\n";
}

// 最新のログも確認
echo "\n=== Latest Laravel Log (Last 5 lines) ===\n";
$logFile = 'storage/logs/laravel.log';
if (file_exists($logFile)) {
    $logLines = file($logFile);
    $recentLines = array_slice($logLines, -5);
    foreach ($recentLines as $line) {
        if (strpos($line, 'ERROR') !== false || strpos($line, 'Exception') !== false) {
            echo "❌ " . $line;
        } else {
            echo $line;
        }
    }
} else {
    echo "Log file not found\n";
}

// 一時ファイルを削除
unlink($cookieFile);

?>