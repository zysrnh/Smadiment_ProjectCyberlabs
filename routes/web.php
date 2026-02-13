<?php

use App\Http\Controllers\MkController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Api\DataOverviewApiController;
use App\Http\Controllers\Api\DataSourceController;
use App\Http\Controllers\Api\TopicMapController;
use App\Http\Controllers\Api\TopAnalyticsController;
use App\Http\Controllers\Api\XOverviewApiController; // 🔥 NEW
use App\Http\Controllers\MK\XOverviewController; // 🔥 NEW
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
    // 🔥 API ENDPOINTS FOR LAZY LOADING (MUST BE BEFORE OTHER ROUTES!)
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
        
        // Topic Map API
        Route::get('/topic-map', [TopicMapController::class, 'getTopicMap'])
            ->name('topic-map');
        
        // Top Analytics APIs
        Route::get('/top-hashtags', [TopAnalyticsController::class, 'getHashtagsData'])
            ->name('top-hashtags');
        
        Route::get('/top-locations', [TopAnalyticsController::class, 'getLocationsData'])
            ->name('top-locations');
        
        Route::get('/top-influencers', [TopAnalyticsController::class, 'getInfluencersData'])
            ->name('top-influencers');
        
        // 🔥 NEW: X Overview APIs
        Route::prefix('x')->name('x.')->group(function () {
            Route::get('/total-users', [XOverviewApiController::class, 'totalUsers'])
                ->name('total-users');
            
            Route::get('/total-authors', [XOverviewApiController::class, 'totalAuthors'])
                ->name('total-authors');
            
            Route::get('/volume-total', [XOverviewApiController::class, 'volumeTotal'])
                ->name('volume-total');
            
            Route::get('/sentiment-total', [XOverviewApiController::class, 'sentimentTotal'])
                ->name('sentiment-total');
            
            Route::get('/top-hashtags', [XOverviewApiController::class, 'topHashtags'])
                ->name('top-hashtags');
            
            Route::get('/most-active-users', [XOverviewApiController::class, 'mostActiveUsers'])
                ->name('most-active-users');
        });
    });
    
    // ═══════════════════════════════════════════════════════════
    // REGULAR MK ROUTES
    // ═══════════════════════════════════════════════════════════
    
    // Main Pages
    Route::get('/data-overview', [MkController::class, 'dataOverview'])->name('data-overview');
    Route::get('/dashboard', [MkController::class, 'dashboard'])->name('dashboard');
    Route::get('/projects', [MkController::class, 'projects'])->name('projects');
    
    // Topic Map Page
    Route::get('/topic-map', [TopicMapController::class, 'index'])->name('topic-map');
    
    // Top Analytics Pages
    Route::prefix('top-analytics')->name('top-analytics.')->group(function () {
        Route::get('/hashtags', [TopAnalyticsController::class, 'hashtags'])->name('hashtags');
        Route::get('/locations', [TopAnalyticsController::class, 'locations'])->name('locations');
        Route::get('/influencers', [TopAnalyticsController::class, 'influencers'])->name('influencers');
    });
    
    // 🔥 NEW: X (Twitter) Routes
    Route::prefix('x')->name('x.')->group(function () {
        Route::get('/overview', [XOverviewController::class, 'index'])->name('overview');
    });
    
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
        Route::get('/retweets', [MkController::class, 'mostRetweets'])->name('retweets');
    });
    
    // Content
    Route::get('/publisher', [MkController::class, 'publisherStats'])->name('publisher');
    Route::get('/topics', [MkController::class, 'recentTopics'])->name('topics');
    
    // Data Source Routes
    Route::prefix('data-source')->name('data-source.')->group(function () {
        Route::get('/users', [DataSourceController::class, 'users'])->name('users');
        Route::get('/authors', [DataSourceController::class, 'authors'])->name('authors');
        Route::get('/volume', [DataSourceController::class, 'volume'])->name('volume');
        Route::get('/trends', [DataSourceController::class, 'trends'])->name('trends');
    });
});