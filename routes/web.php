<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test', function () {
    return 'Test route works!';
});

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/tag/{tag}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');

// 管理画面のブログ管理
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('blog', \App\Http\Controllers\Admin\BlogController::class)->parameters(['blog' => 'filename']);
});