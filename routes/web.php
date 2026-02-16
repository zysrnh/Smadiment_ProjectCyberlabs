<?php

use App\Http\Controllers\MkController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\UserAuthController;
use App\Http\Controllers\Api\DataOverviewApiController;
use App\Http\Controllers\Api\DataSourceController;
use App\Http\Controllers\Api\TopicMapController;
use App\Http\Controllers\Api\TopAnalyticsController;
use App\Http\Controllers\MK\XOverviewController;
use App\Http\Controllers\MK\FacebookOverviewController;
use App\Http\Controllers\MK\NewsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Authentication Routes
|--------------------------------------------------------------------------
*/

Route::prefix('user')->name('user.')->group(function () {

    Route::middleware('guest')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    });

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

    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.submit');
    });

    Route::middleware('auth:admin')->group(function () {
        Route::get('/dashboard', [MkController::class, 'adminDashboard'])->name('dashboard');
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

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
    // API ENDPOINTS FOR LAZY LOADING (MUST BE BEFORE OTHER ROUTES!)
    // ═══════════════════════════════════════════════════════════
    Route::prefix('api')->name('api.')->group(function () {

        // ─────────────────────────────────────────────────────
        // Data Overview APIs
        // ─────────────────────────────────────────────────────
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

        // ─────────────────────────────────────────────────────
        // Topic Map API
        // ─────────────────────────────────────────────────────
        Route::get('/topic-map', [TopicMapController::class, 'getTopicMap'])
            ->name('topic-map');

        // ─────────────────────────────────────────────────────
        // Top Analytics APIs
        // ─────────────────────────────────────────────────────
        Route::get('/top-hashtags', [TopAnalyticsController::class, 'getHashtagsData'])
            ->name('top-hashtags');

        Route::get('/top-locations', [TopAnalyticsController::class, 'getLocationsData'])
            ->name('top-locations');

        Route::get('/top-influencers', [TopAnalyticsController::class, 'getInfluencersData'])
            ->name('top-influencers');

        // ─────────────────────────────────────────────────────
        // News APIs
        // ─────────────────────────────────────────────────────
        Route::prefix('news')->name('news.')->group(function () {
            // Recent Topics API (for lazy loading)
            Route::get('/recent-topics', [NewsController::class, 'recentTopicsApi'])
                ->name('recent-topics-api');
        });

        // ─────────────────────────────────────────────────────
        // X (Twitter) APIs - CONSOLIDATED
        // ─────────────────────────────────────────────────────
        Route::prefix('x')->name('x.')->group(function () {

            // Overview Stats
            Route::get('/total-users', [XOverviewController::class, 'totalUsers'])
                ->name('total-users');

            Route::get('/total-authors', [XOverviewController::class, 'totalAuthors'])
                ->name('total-authors');

            Route::get('/volume-total', [XOverviewController::class, 'volumeTotal'])
                ->name('volume-total');

            Route::get('/sentiment-total', [XOverviewController::class, 'sentimentTotal'])
                ->name('sentiment-total');

            // Most Active Users API (JSON)
            Route::get('/most-active-users', [XOverviewController::class, 'mostActiveUsers'])
                ->name('most-active-users');

            // Engagement
            Route::get('/most-retweets', [XOverviewController::class, 'mostRetweets'])
                ->name('most-retweets');

            Route::get('/most-status', [XOverviewController::class, 'mostStatus'])
                ->name('most-status');

            Route::get('/user-mentions', [XOverviewController::class, 'userMentions'])
                ->name('user-mentions');

            // Content
            Route::get('/top-hashtags-data', [XOverviewController::class, 'topHashtagsData'])
                ->name('top-hashtags-data');

            // Trending Topics API (used by both table view and word cloud)
            Route::get('/trending-topics', [XOverviewController::class, 'trendingTopicsData'])
                ->name('trending-topics');

            // Shared URLs API
            Route::get('/shared-urls', [XOverviewController::class, 'sharedUrls'])
                ->name('shared-urls');

            // Geographic
            Route::get('/post-with-location', [XOverviewController::class, 'postWithLocation'])
                ->name('post-with-location');

            Route::get('/geo-user', [XOverviewController::class, 'geoUser'])
                ->name('geo-user');

            Route::get('/geo-sentiment', [XOverviewController::class, 'geoSentiment'])
                ->name('geo-sentiment');

            Route::get('/top-locations', [XOverviewController::class, 'topLocations'])
                ->name('top-locations');

            // Demographics
            Route::get('/authors-age', [XOverviewController::class, 'authorsAgeData'])
                ->name('authors-age');

            Route::get('/authors-gender', [XOverviewController::class, 'authorsGenderData'])
                ->name('authors-gender');

            Route::get('/authors-type', [XOverviewController::class, 'authorsTypeData'])
                ->name('authors-type');
        });

        // ─────────────────────────────────────────────────────
        // Facebook APIs
        // ─────────────────────────────────────────────────────
        Route::prefix('facebook')->name('facebook.')->group(function () {
            // Overview Stats
            Route::get('/total-users', [FacebookOverviewController::class, 'totalUsers'])
                ->name('total-users');

            Route::get('/total-authors', [FacebookOverviewController::class, 'totalAuthors'])
                ->name('total-authors');

            Route::get('/volume-total', [FacebookOverviewController::class, 'volumeTotal'])
                ->name('volume-total');

            Route::get('/sentiment-total', [FacebookOverviewController::class, 'sentimentTotal'])
                ->name('sentiment-total');

            Route::get('/most-active-users', [FacebookOverviewController::class, 'mostActiveUsers'])
                ->name('most-active-users');
        });
    });

    // ═══════════════════════════════════════════════════════════
    // REGULAR MK ROUTES (Pages)
    // ═══════════════════════════════════════════════════════════

    // ─────────────────────────────────────────────────────
    // Main Dashboard Pages
    // ─────────────────────────────────────────────────────
    Route::get('/dashboard', [MkController::class, 'dashboard'])->name('dashboard');
    Route::get('/data-overview', [MkController::class, 'dataOverview'])->name('data-overview');
    Route::get('/projects', [MkController::class, 'projects'])->name('projects');

    // ─────────────────────────────────────────────────────
    // Topic Map Page
    // ─────────────────────────────────────────────────────
    Route::get('/topic-map', [TopicMapController::class, 'index'])->name('topic-map');

    // ─────────────────────────────────────────────────────
    // Top Analytics Pages
    // ─────────────────────────────────────────────────────
    Route::prefix('top-analytics')->name('top-analytics.')->group(function () {
        Route::get('/hashtags', [TopAnalyticsController::class, 'hashtags'])->name('hashtags');
        Route::get('/locations', [TopAnalyticsController::class, 'locations'])->name('locations');
        Route::get('/influencers', [TopAnalyticsController::class, 'influencers'])->name('influencers');
    });

    // ─────────────────────────────────────────────────────
    // News Routes
    // ─────────────────────────────────────────────────────
    Route::prefix('news')->name('news.')->group(function () {
        // Recent Topics Page
        Route::get('/recent-topics', [NewsController::class, 'recentTopicsPage'])
            ->name('recent-topics');
    });

    // ─────────────────────────────────────────────────────
    // X (Twitter) Routes - COMPLETE & ORGANIZED
    // ─────────────────────────────────────────────────────
    Route::prefix('x')->name('x.')->group(function () {

        // Overview & Main Stats
        Route::get('/overview', [XOverviewController::class, 'index'])
            ->name('overview');

        // ─────────────────────────────────────────────────
        // Trending & Content Analysis
        // ─────────────────────────────────────────────────

        // Trending Topics - Table View (no project_id required)
        Route::get('/trending-topics', [XOverviewController::class, 'trendingTopicsPage'])
            ->name('trending-topics');

        // Trending Topics - Word Cloud View (no project_id required)
        Route::get('/trending-word-cloud', [XOverviewController::class, 'trendingWordCloudPage'])
            ->name('trending-word-cloud');

        // Top Hashtags
        Route::get('/top-hashtags', [XOverviewController::class, 'topHashtagsPage'])
            ->name('top-hashtags');

        // Shared URLs
        Route::get('/shared-urls', [XOverviewController::class, 'sharedUrlsPage'])
            ->name('shared-urls');

        // ─────────────────────────────────────────────────
        // Engagement & Popular Content
        // ─────────────────────────────────────────────────

        // Most Viewed Posts
        Route::get('/most-status', [XOverviewController::class, 'mostStatusPage'])
            ->name('most-status');

        // Most Retweeted Posts
        Route::get('/most-retweets', [XOverviewController::class, 'mostRetweetsPage'])
            ->name('most-retweets');

        // Most Active Users
        Route::get('/most-active-users', [XOverviewController::class, 'mostActiveUsersPage'])
            ->name('most-active-users');

        // ─────────────────────────────────────────────────
        // Author Demographics
        // ─────────────────────────────────────────────────

        // All Demographics in One Page
        Route::get('/authors-demographics', [XOverviewController::class, 'authorsDemographicsPage'])
            ->name('authors.demographics');

        // ─────────────────────────────────────────────────
        // Geographic Analysis
        // ─────────────────────────────────────────────────

        // Geographic Overview (Map + Stats)
        Route::get('/geographic', [XOverviewController::class, 'geographicPage'])
            ->name('geographic');

        // Posts with Location Data
        Route::get('/post-with-location', [XOverviewController::class, 'postWithLocationPage'])
            ->name('post-with-location');
    });

    // ─────────────────────────────────────────────────────
    // Facebook Routes
    // ─────────────────────────────────────────────────────
    Route::prefix('facebook')->name('facebook.')->group(function () {
        // Overview Page
        Route::get('/overview', [FacebookOverviewController::class, 'index'])
            ->name('overview');
    });

    // ─────────────────────────────────────────────────────
    // Legacy Analytics Pages (from MkController)
    // ─────────────────────────────────────────────────────
    Route::get('/sentiment', [MkController::class, 'sentiment'])->name('sentiment');
    Route::get('/geographic', [MkController::class, 'geographic'])->name('geographic');

    // Authors Demographics (legacy - consider migrating to X routes)
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

    // ─────────────────────────────────────────────────────
    // Data Source Routes
    // ─────────────────────────────────────────────────────
    Route::prefix('data-source')->name('data-source.')->group(function () {
        Route::get('/users', [DataSourceController::class, 'users'])->name('users');
        Route::get('/authors', [DataSourceController::class, 'authors'])->name('authors');
        Route::get('/volume', [DataSourceController::class, 'volume'])->name('volume');
        Route::get('/trends', [DataSourceController::class, 'trends'])->name('trends');
    });
});