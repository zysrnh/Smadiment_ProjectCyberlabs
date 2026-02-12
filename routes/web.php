<?php

use App\Http\Controllers\MkController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Api\DataOverviewApiController;
use App\Http\Controllers\Api\DataSourceController;
use App\Http\Controllers\Api\AnalyticsOverviewController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->group(function () {
    
    // Login routes (accessible without authentication)
    Route::middleware('guest')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    });
    
    // Logout (require authentication)
    Route::post('/logout', [UserAuthController::class, 'logout'])
        ->middleware('auth')
        ->name('logout');
});

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
        
        // User Management Routes
        Route::resource('users', AdminUserController::class);
    });
});

/*
|--------------------------------------------------------------------------
| MK Analytics Routes (Protected by User Auth)
|--------------------------------------------------------------------------
*/

Route::prefix('mk')->name('mk.')->middleware('auth')->group(function () {
    
    // ═══════════════════════════════════════════════════════════
    // API ENDPOINTS FOR LAZY LOADING
    // ═══════════════════════════════════════════════════════════
    Route::prefix('api')->name('api.')->group(function () {
        // Data Overview APIs
        Route::get('/trending-topics', [DataOverviewApiController::class, 'trendingTopics'])
            ->name('trending-topics');
        
        Route::get('/top-hashtags-overview', [DataOverviewApiController::class, 'topHashtags'])
            ->name('top-hashtags-overview');
        
        Route::get('/mention-counts', [DataOverviewApiController::class, 'mentionCounts'])
            ->name('mention-counts');
        
        Route::get('/sentiment-by-media', [DataOverviewApiController::class, 'sentimentByMedia'])
            ->name('sentiment-by-media');
        
        Route::get('/active-users', [DataOverviewApiController::class, 'activeUsers'])
            ->name('active-users');
        
        Route::get('/sentiment-timeline', [DataOverviewApiController::class, 'sentimentTimeline'])
            ->name('sentiment-timeline');
        
        Route::get('/geo-users', [DataOverviewApiController::class, 'geoUsers'])
            ->name('geo-users');
        
        // Analytics Overview APIs
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/topic-map', [AnalyticsOverviewController::class, 'getTopicMap'])
                ->name('topic-map');
            
            Route::get('/hashtags', [AnalyticsOverviewController::class, 'getHashtags'])
                ->name('hashtags');
            
            Route::get('/locations', [AnalyticsOverviewController::class, 'getLocations'])
                ->name('locations');
            
            Route::get('/influencers', [AnalyticsOverviewController::class, 'getInfluencers'])
                ->name('influencers');
        });
    });
    
    // ═══════════════════════════════════════════════════════════
    // MAIN PAGES
    // ═══════════════════════════════════════════════════════════
    
    Route::get('/dashboard', [MkController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-overview', [MkController::class, 'dataOverview'])->name('data-overview');
    Route::get('/analytics-overview', [AnalyticsOverviewController::class, 'index'])->name('analytics-overview');
    
    // Data Source Routes
    Route::prefix('data-source')->name('data-source.')->group(function () {
        Route::get('/users', [DataSourceController::class, 'users'])->name('users');
        Route::get('/authors', [DataSourceController::class, 'authors'])->name('authors');
        Route::get('/volume', [DataSourceController::class, 'volume'])->name('volume');
        Route::get('/trends', [DataSourceController::class, 'trends'])->name('trends');
    });
});