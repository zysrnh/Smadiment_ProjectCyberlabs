<?php

use App\Http\Controllers\MkController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminAuthController;


/*
|--------------------------------------------------------------------------
| Admin Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->group(function () {
    
    // Login routes (accessible without authentication)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });
    
    // Protected admin routes (require authentication)
    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [MkController::class, 'adminDashboard'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    });
    
});

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
        Route::get('/retweets', [MkController::class, 'mostRetweets'])->name('retweets'); // ← PINDAHIN KE SINI
    });
    
    // Content
    Route::get('/publisher', [MkController::class, 'publisherStats'])->name('publisher'); // ← PINDAHIN KE SINI (hapus /mk/)
    Route::get('/topics', [MkController::class, 'recentTopics'])->name('topics'); // ← PINDAHIN KE SINI (hapus /mk/)
});