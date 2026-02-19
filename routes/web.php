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

            // ── Existing ──────────────────────────────────────
            Route::get('/word-cloud', [NewsController::class, 'newsWordCloudData'])
                ->name('word-cloud-api');

            Route::get('/top-publisher', [NewsController::class, 'topPublisherData'])
                ->name('top-publisher-api');

            Route::get('/mentions', [NewsController::class, 'newsMentionsData'])
                ->name('mentions-api');

            Route::get('/articles', [NewsController::class, 'articlesData'])
                ->name('articles-api');

            // ── NEW: Platform Top Status APIs ─────────────────
            Route::get('/tiktok-top-status', [NewsController::class, 'tiktokTopStatus'])
                ->name('tiktok-top-status');

            Route::get('/ig-top-status', [NewsController::class, 'igTopStatus'])
                ->name('ig-top-status');

            Route::get('/fb-top-status', [NewsController::class, 'fbTopStatusApi'])
                ->name('fb-top-status');

            Route::get('/ytb-top-status', [NewsController::class, 'ytbTopStatus'])
                ->name('ytb-top-status');

            // ── NEW: AI Analysis Proxy ─────────────────────────
            Route::post('/ai-proxy', [NewsController::class, 'aiAnalysisProxy'])
                ->name('ai-proxy');
        });

        // ─────────────────────────────────────────────────────
        // X (Twitter) APIs - CONSOLIDATED
        // ─────────────────────────────────────────────────────
        Route::prefix('x')->name('x.')->group(function () {

            Route::get('/total-users', [XOverviewController::class, 'totalUsers'])
                ->name('total-users');

            Route::get('/total-authors', [XOverviewController::class, 'totalAuthors'])
                ->name('total-authors');

            Route::get('/volume-total', [XOverviewController::class, 'volumeTotal'])
                ->name('volume-total');

            Route::get('/sentiment-total', [XOverviewController::class, 'sentimentTotal'])
                ->name('sentiment-total');

            Route::get('/most-active-users', [XOverviewController::class, 'mostActiveUsers'])
                ->name('most-active-users');

            Route::get('/most-retweets', [XOverviewController::class, 'mostRetweets'])
                ->name('most-retweets');

            Route::get('/most-status', [XOverviewController::class, 'mostStatus'])
                ->name('most-status');

            Route::get('/user-mentions', [XOverviewController::class, 'userMentions'])
                ->name('user-mentions');

            Route::get('/top-hashtags-data', [XOverviewController::class, 'topHashtagsData'])
                ->name('top-hashtags-data');

            Route::get('/trending-topics', [XOverviewController::class, 'trendingTopicsData'])
                ->name('trending-topics');

            Route::get('/shared-urls', [XOverviewController::class, 'sharedUrls'])
                ->name('shared-urls');

            Route::get('/post-with-location', [XOverviewController::class, 'postWithLocation'])
                ->name('post-with-location');

            Route::get('/geo-user', [XOverviewController::class, 'geoUser'])
                ->name('geo-user');

            Route::get('/geo-sentiment', [XOverviewController::class, 'geoSentiment'])
                ->name('geo-sentiment');

            Route::get('/top-locations', [XOverviewController::class, 'topLocations'])
                ->name('top-locations');

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

            Route::get('/trending-topics', [FacebookOverviewController::class, 'trendingTopicsData'])
                ->name('trending-topics');

            Route::get('/most-viewed-posts', [FacebookOverviewController::class, 'mostViewedPostsData'])
                ->name('most-viewed-posts');

            Route::get('/authors-age', [FacebookOverviewController::class, 'authorsAgeData'])
                ->name('authors-age');

            Route::get('/authors-gender', [FacebookOverviewController::class, 'authorsGenderData'])
                ->name('authors-gender');

            Route::get('/authors-type', [FacebookOverviewController::class, 'authorsTypeData'])
                ->name('authors-type');

            // ✅ FIXED: geo routes sekarang benar di dalam prefix facebook
            Route::get('/geo-user', [FacebookOverviewController::class, 'geoUser'])
                ->name('geo-user');

            Route::get('/geo-sentiment', [FacebookOverviewController::class, 'geoSentiment'])
                ->name('geo-sentiment');

            // ✅ FIXED: rename agar tidak conflict dengan TopAnalyticsController top-locations
            Route::get('/top-locations', [FacebookOverviewController::class, 'topLocations'])
                ->name('top-locations');
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
        Route::get('/word-cloud', [NewsController::class, 'newsWordCloudPage'])
            ->name('word-cloud');

        Route::get('/top-publishers', [NewsController::class, 'topPublisherPage'])
            ->name('top-publishers');

        Route::get('/timeline', [NewsController::class, 'newsTimelinePage'])
            ->name('timeline');

        Route::get('/articles', [NewsController::class, 'articlesPage'])
            ->name('articles');

        // ── NEW: AI Analysis Page ──────────────────────────
        Route::get('/ai-analysis', [NewsController::class, 'aiAnalysisPage'])
            ->name('ai-analysis');
    });

    // ─────────────────────────────────────────────────────
    // X (Twitter) Routes
    // ─────────────────────────────────────────────────────
    Route::prefix('x')->name('x.')->group(function () {

        Route::get('/overview', [XOverviewController::class, 'index'])
            ->name('overview');

        Route::get('/trending-topics', [XOverviewController::class, 'trendingTopicsPage'])
            ->name('trending-topics');

        Route::get('/trending-word-cloud', [XOverviewController::class, 'trendingWordCloudPage'])
            ->name('trending-word-cloud');

        Route::get('/top-hashtags', [XOverviewController::class, 'topHashtagsPage'])
            ->name('top-hashtags');

        Route::get('/shared-urls', [XOverviewController::class, 'sharedUrlsPage'])
            ->name('shared-urls');

        Route::get('/most-status', [XOverviewController::class, 'mostStatusPage'])
            ->name('most-status');

        Route::get('/most-retweets', [XOverviewController::class, 'mostRetweetsPage'])
            ->name('most-retweets');

        Route::get('/most-active-users', [XOverviewController::class, 'mostActiveUsersPage'])
            ->name('most-active-users');

        Route::get('/authors-demographics', [XOverviewController::class, 'authorsDemographicsPage'])
            ->name('authors.demographics');

        Route::get('/geographic', [XOverviewController::class, 'geographicPage'])
            ->name('geographic');

        Route::get('/post-with-location', [XOverviewController::class, 'postWithLocationPage'])
            ->name('post-with-location');
    });

    // ─────────────────────────────────────────────────────
    // Facebook Routes
    // ─────────────────────────────────────────────────────
    Route::prefix('facebook')->name('facebook.')->group(function () {

        Route::get('/overview', [FacebookOverviewController::class, 'index'])
            ->name('overview');

        Route::get('/trending-topics', [FacebookOverviewController::class, 'trendingTopicsPage'])
            ->name('trending-topics');

        Route::get('/most-viewed-posts', [FacebookOverviewController::class, 'mostViewedPostsPage'])
            ->name('most-viewed-posts');

        Route::get('/top-hashtags', [FacebookOverviewController::class, 'topHashtagsPage'])
            ->name('top-hashtags');

        Route::get('/authors-demographics', [FacebookOverviewController::class, 'authorsDemographicsPage'])
            ->name('authors.demographics');

        Route::get('/geographic', [FacebookOverviewController::class, 'geographicPage'])
            ->name('geographic');

        // ✅ NEW: Facebook Trending Word Cloud
        Route::get('/trending-word-cloud', [FacebookOverviewController::class, 'trendingWordCloudPage'])
            ->name('trending-word-cloud');
    });

    // ─────────────────────────────────────────────────────
    // Legacy Analytics Pages (from MkController)
    // ─────────────────────────────────────────────────────
    Route::get('/sentiment', [MkController::class, 'sentiment'])->name('sentiment');
    Route::get('/geographic', [MkController::class, 'geographic'])->name('geographic');

    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/age', [MkController::class, 'authorsAge'])->name('age');
        Route::get('/gender', [MkController::class, 'authorsGender'])->name('gender');
        Route::get('/type', [MkController::class, 'authorsType'])->name('type');
    });

    Route::get('/categories', [MkController::class, 'categories'])->name('categories');

    Route::prefix('engagement')->name('engagement.')->group(function () {
        Route::get('/reach', [MkController::class, 'reach'])->name('reach');
        Route::get('/urls', [MkController::class, 'sharedUrls'])->name('urls');
        Route::get('/users', [MkController::class, 'activeUsers'])->name('users');
        Route::get('/retweets', [MkController::class, 'mostRetweets'])->name('retweets');
    });

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