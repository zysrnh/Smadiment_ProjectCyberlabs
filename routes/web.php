<?php

use App\Http\Controllers\MkController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MK Analytics Routes
|--------------------------------------------------------------------------
*/

Route::prefix('mk')->name('mk.')->group(function () {
    
    // Main Pages
    Route::get('/dashboard', [MkController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [MkController::class, 'projects'])->name('projects');
    
    // Analytics Pages
    Route::get('/sentiment', [MkController::class, 'sentiment'])->name('sentiment');
    Route::get('/geographic', [MkController::class, 'geographic'])->name('geographic');
    
    // Authors Demographics
    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/age', [MkController::class, 'authorsAge'])->name('age');
        Route::get('/gender', [MkController::class, 'authorsGender'])->name('gender');
        Route::get('/type', [MkController::class, 'authorsType'])->name('type');
    });
    
    // Categories
    Route::get('/categories', [MkController::class, 'categories'])->name('categories');
    
    // Engagement Metrics
    Route::prefix('engagement')->name('engagement.')->group(function () {
        Route::get('/reach', [MkController::class, 'reach'])->name('reach');
        Route::get('/urls', [MkController::class, 'sharedUrls'])->name('urls');
        Route::get('/users', [MkController::class, 'activeUsers'])->name('users');
    });
});