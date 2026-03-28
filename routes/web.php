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
use App\Http\Controllers\MK\InstagramOverviewController;
use App\Http\Controllers\MK\YoutubeOverviewController;
use App\Http\Controllers\MK\TiktokOverviewController;
use App\Http\Controllers\MK\NewsController;
use App\Http\Controllers\MK\CompareProjectController;
use App\Http\Controllers\MK\TrendingTopicController;
use App\Http\Controllers\MK\SearchTopicController;
use App\Http\Controllers\MK\MediaStatisticController;
use App\Http\Controllers\MK\AllPlatformAiController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('user')->name('user.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [UserAuthController::class, 'showLoginForm'])->name('login');
        Route::post('/login', [UserAuthController::class, 'login'])->name('login.submit');
    });
    Route::post('/logout', [UserAuthController::class, 'logout'])->middleware('auth')->name('logout');
});

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

Route::prefix('mk')->name('mk.')->middleware('auth')->group(function () {

    // ═══════════════════════════════════════════════════════
    // API ROUTES
    // ═══════════════════════════════════════════════════════
    Route::prefix('api')->name('api.')->group(function () {

        Route::get('/trending-topics',       [DataOverviewApiController::class, 'trendingTopics'])->name('trending-topics');
        Route::get('/top-hashtags-overview', [DataOverviewApiController::class, 'topHashtags'])->name('top-hashtags-overview');
        Route::get('/mention-counts',        [DataOverviewApiController::class, 'mentionCounts'])->name('mention-counts');
        Route::get('/sentiment-by-media',    [DataOverviewApiController::class, 'sentimentByMedia'])->name('sentiment-by-media');
        Route::get('/active-users',          [DataOverviewApiController::class, 'activeUsers'])->name('active-users');
        Route::get('/sentiment-timeline',    [DataOverviewApiController::class, 'sentimentTimeline'])->name('sentiment-timeline');
        Route::get('/geo-users',             [DataOverviewApiController::class, 'geoUsers'])->name('geo-users');

        Route::get('/topic-map', [TopicMapController::class, 'getTopicMap'])->name('topic-map');

        Route::get('/top-hashtags',   [TopAnalyticsController::class, 'getHashtagsData'])->name('top-hashtags');
        Route::get('/top-locations',  [TopAnalyticsController::class, 'getLocationsData'])->name('top-locations');
        Route::get('/top-influencers',[TopAnalyticsController::class, 'getInfluencersData'])->name('top-influencers');

        // ─────────────────────────────────────────────────────
        // NEWS API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/word-cloud',        [NewsController::class, 'newsWordCloudData'])->name('word-cloud-api');
            Route::get('/top-publisher',     [NewsController::class, 'topPublisherData'])->name('top-publisher-api');
            Route::get('/mentions',          [NewsController::class, 'newsMentionsData'])->name('mentions-api');
            Route::get('/articles',          [NewsController::class, 'articlesData'])->name('articles-api');
            Route::get('/tiktok-top-status', [NewsController::class, 'tiktokTopStatus'])->name('tiktok-top-status');
            Route::get('/ig-top-status',     [NewsController::class, 'igTopStatus'])->name('ig-top-status');
            Route::get('/fb-top-status',     [NewsController::class, 'fbTopStatusApi'])->name('fb-top-status');
            Route::get('/ytb-top-status',    [NewsController::class, 'ytbTopStatus'])->name('ytb-top-status');
            Route::get('/ai-analysis-data',  [NewsController::class, 'aiAnalysisData'])->name('ai-analysis-data');
            Route::post('/ai-proxy',         [NewsController::class, 'aiAnalysisProxy'])->name('ai-proxy');
        });

        // ─────────────────────────────────────────────────────
        // MEDIA STATISTIC API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('media-statistic')->name('media-statistic.')->group(function () {
            Route::get('/mention-by-platform',  [MediaStatisticController::class, 'mentionByPlatform'])->name('mention-by-platform');
            Route::get('/trend-by-media',       [MediaStatisticController::class, 'trendByMedia'])->name('trend-by-media');
            Route::get('/trend-mentions',       [MediaStatisticController::class, 'trendMentions'])->name('trend-mentions');
            Route::get('/sentiment-engagement', [MediaStatisticController::class, 'sentimentEngagement'])->name('sentiment-engagement');
            Route::get('/locations',            [MediaStatisticController::class, 'locations'])->name('locations');
            Route::get('/mentions-by-weekday',  [MediaStatisticController::class, 'mentionsByWeekday'])->name('mentions-by-weekday');
            Route::get('/mentions-by-hour',     [MediaStatisticController::class, 'mentionsByHour'])->name('mentions-by-hour');
            Route::get('/x-interaction',        [MediaStatisticController::class, 'xInteraction'])->name('x-interaction');
        });

        Route::prefix('sentiment')->name('sentiment.')->group(function () {
            Route::get('/totals',             [MediaStatisticController::class, 'sentimentTotals'])->name('totals');
            Route::get('/by-time',            [MediaStatisticController::class, 'sentimentByTime'])->name('by-time');
            Route::get('/interaction-totals', [MediaStatisticController::class, 'interactionSentimentTotals'])->name('interaction-totals');
        });

        // ─────────────────────────────────────────────────────
        // X (TWITTER) API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('x')->name('x.')->group(function () {
            Route::get('/total-users',            [XOverviewController::class, 'totalUsers'])->name('total-users');
            Route::get('/total-authors',          [XOverviewController::class, 'totalAuthors'])->name('total-authors');
            Route::get('/volume-total',           [XOverviewController::class, 'volumeTotal'])->name('volume-total');
            Route::get('/sentiment-total',        [XOverviewController::class, 'sentimentTotal'])->name('sentiment-total');
            Route::get('/most-active-users',      [XOverviewController::class, 'mostActiveUsers'])->name('most-active-users');
            Route::get('/most-retweets',          [XOverviewController::class, 'mostRetweets'])->name('most-retweets');
            Route::get('/most-status',            [XOverviewController::class, 'mostStatus'])->name('most-status');
            Route::get('/user-mentions',          [XOverviewController::class, 'userMentions'])->name('user-mentions');
            Route::get('/top-hashtags-data',      [XOverviewController::class, 'topHashtagsData'])->name('top-hashtags-data');
            Route::get('/trending-topics',        [XOverviewController::class, 'trendingTopicsData'])->name('trending-topics');
            Route::get('/shared-urls',            [XOverviewController::class, 'sharedUrls'])->name('shared-urls');
            Route::get('/post-with-location',     [XOverviewController::class, 'postWithLocation'])->name('post-with-location');
            Route::get('/geo-user',               [XOverviewController::class, 'geoUser'])->name('geo-user');
            Route::get('/geo-sentiment',          [XOverviewController::class, 'geoSentiment'])->name('geo-sentiment');
            Route::get('/top-locations',          [XOverviewController::class, 'topLocations'])->name('top-locations');
            Route::get('/authors-age',            [XOverviewController::class, 'authorsAgeData'])->name('authors-age');
            Route::get('/authors-gender',         [XOverviewController::class, 'authorsGenderData'])->name('authors-gender');
            Route::get('/authors-type',           [XOverviewController::class, 'authorsTypeData'])->name('authors-type');
            Route::get('/user-detailed-mentions', [XOverviewController::class, 'userDetailedMentions'])->name('user-detailed-mentions');
            Route::get('/top-influencers',        [XOverviewController::class, 'topInfluencersData'])->name('top-influencers');
            Route::get('/emotion-analysis',       [XOverviewController::class, 'emotionAnalysisData'])->name('emotion-analysis');
            Route::get('/most-engagement',        [XOverviewController::class, 'mostEngagementData'])->name('most-engagement');
            Route::get('/ai-analysis-data',       [XOverviewController::class, 'aiAnalysisData'])->name('ai-analysis-data');
            Route::post('/ai-proxy',              [XOverviewController::class, 'aiAnalysisProxy'])->name('ai-proxy');
        });

        // ─────────────────────────────────────────────────────
        // FACEBOOK API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('facebook')->name('facebook.')->group(function () {
            Route::get('/total-users',      [FacebookOverviewController::class, 'totalUsers'])->name('total-users');
            Route::get('/total-authors',    [FacebookOverviewController::class, 'totalAuthors'])->name('total-authors');
            Route::get('/volume-total',     [FacebookOverviewController::class, 'volumeTotal'])->name('volume-total');
            Route::get('/sentiment-total',  [FacebookOverviewController::class, 'sentimentTotal'])->name('sentiment-total');
            Route::get('/most-active-users',[FacebookOverviewController::class, 'mostActiveUsers'])->name('most-active-users');
            Route::get('/trending-topics',  [FacebookOverviewController::class, 'trendingTopicsData'])->name('trending-topics');
            Route::get('/most-viewed-posts',[FacebookOverviewController::class, 'mostViewedPostsData'])->name('most-viewed-posts');
            Route::get('/most-engagement',  [FacebookOverviewController::class, 'mostEngagementData'])->name('most-engagement');
            Route::get('/authors-age',      [FacebookOverviewController::class, 'authorsAgeData'])->name('authors-age');
            Route::get('/authors-gender',   [FacebookOverviewController::class, 'authorsGenderData'])->name('authors-gender');
            Route::get('/authors-type',     [FacebookOverviewController::class, 'authorsTypeData'])->name('authors-type');
            Route::get('/geo-user',         [FacebookOverviewController::class, 'geoUser'])->name('geo-user');
            Route::get('/geo-sentiment',    [FacebookOverviewController::class, 'geoSentiment'])->name('geo-sentiment');
            Route::get('/top-locations',    [FacebookOverviewController::class, 'topLocations'])->name('top-locations');
            Route::get('/ai-analysis-data', [FacebookOverviewController::class, 'aiAnalysisData'])->name('ai-analysis-data');
            Route::post('/ai-proxy',        [FacebookOverviewController::class, 'aiAnalysisProxy'])->name('ai-proxy');
        });

        // ─────────────────────────────────────────────────────
        // INSTAGRAM API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('instagram')->name('instagram.')->group(function () {
            Route::get('/total-users',      [InstagramOverviewController::class, 'totalUsers'])->name('total-users');
            Route::get('/total-authors',    [InstagramOverviewController::class, 'totalAuthors'])->name('total-authors');
            Route::get('/volume-total',     [InstagramOverviewController::class, 'volumeTotal'])->name('volume-total');
            Route::get('/sentiment-total',  [InstagramOverviewController::class, 'sentimentTotal'])->name('sentiment-total');
            Route::get('/most-active-users',[InstagramOverviewController::class, 'mostActiveUsers'])->name('most-active-users');
            Route::get('/trending-topics',  [InstagramOverviewController::class, 'trendingTopicsData'])->name('trending-topics');
            Route::get('/most-viewed-posts',[InstagramOverviewController::class, 'mostViewedPostsData'])->name('most-viewed-posts');
            Route::get('/authors-age',      [InstagramOverviewController::class, 'authorsAgeData'])->name('authors-age');
            Route::get('/authors-gender',   [InstagramOverviewController::class, 'authorsGenderData'])->name('authors-gender');
            Route::get('/authors-type',     [InstagramOverviewController::class, 'authorsTypeData'])->name('authors-type');
            Route::get('/geo-user',         [InstagramOverviewController::class, 'geoUser'])->name('geo-user');
            Route::get('/geo-sentiment',    [InstagramOverviewController::class, 'geoSentiment'])->name('geo-sentiment');
            Route::get('/top-locations',    [InstagramOverviewController::class, 'topLocations'])->name('top-locations');
            Route::get('/ai-analysis-data', [InstagramOverviewController::class, 'aiAnalysisData'])->name('ai-analysis-data');
            Route::post('/ai-proxy',        [InstagramOverviewController::class, 'aiAnalysisProxy'])->name('ai-proxy');
        });

        // ─────────────────────────────────────────────────────
        // YOUTUBE API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('youtube')->name('youtube.')->group(function () {
            Route::get('/total-users',      [YoutubeOverviewController::class, 'totalUsers'])->name('total-users');
            Route::get('/total-authors',    [YoutubeOverviewController::class, 'totalAuthors'])->name('total-authors');
            Route::get('/volume-total',     [YoutubeOverviewController::class, 'volumeTotal'])->name('volume-total');
            Route::get('/sentiment-total',  [YoutubeOverviewController::class, 'sentimentTotal'])->name('sentiment-total');
            Route::get('/most-active-users',[YoutubeOverviewController::class, 'mostActiveUsers'])->name('most-active-users');
            Route::get('/trending-topics',  [YoutubeOverviewController::class, 'trendingTopicsData'])->name('trending-topics');
            Route::get('/most-viewed-posts',[YoutubeOverviewController::class, 'mostViewedPostsData'])->name('most-viewed-posts');
            Route::get('/most-engagement',  [YoutubeOverviewController::class, 'mostEngagementData'])->name('most-engagement');
            Route::get('/emotion-analysis', [YoutubeOverviewController::class, 'emotionAnalysisData'])->name('emotion-analysis');
            Route::get('/ai-analysis-data', [YoutubeOverviewController::class, 'aiAnalysisData'])->name('ai-analysis-data');
            Route::post('/ai-proxy',        [YoutubeOverviewController::class, 'aiAnalysisProxy'])->name('ai-proxy');
            Route::get('/top-locations',    [YoutubeOverviewController::class, 'topLocations'])->name('top-locations');
        });

        // ─────────────────────────────────────────────────────
        // TIKTOK API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('tiktok')->name('tiktok.')->group(function () {
            Route::get('/volume-total',     [TiktokOverviewController::class, 'volumeTotal'])->name('volume-total');
            Route::get('/sentiment-total',  [TiktokOverviewController::class, 'sentimentTotal'])->name('sentiment-total');
            Route::get('/most-active-users',[TiktokOverviewController::class, 'mostActiveUsers'])->name('most-active-users');
            Route::get('/trending-topics',  [TiktokOverviewController::class, 'trendingTopicsData'])->name('trending-topics');
            Route::get('/most-viewed-posts',[TiktokOverviewController::class, 'mostViewedPostsData'])->name('most-viewed-posts');
            Route::get('/most-engagement',  [TiktokOverviewController::class, 'mostEngagementData'])->name('most-engagement');
            Route::get('/ai-analysis-data', [TiktokOverviewController::class, 'aiAnalysisData'])->name('ai-analysis-data');
            Route::post('/ai-proxy',        [TiktokOverviewController::class, 'aiAnalysisProxy'])->name('ai-proxy');
            Route::get('/emotion-analysis', [TiktokOverviewController::class, 'emotionAnalysisData'])->name('emotion-analysis');
        });

        // ─────────────────────────────────────────────────────
        // COMPARE API ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('compare')->name('compare.')->group(function () {
            Route::get('/projects', [CompareProjectController::class, 'projectsList'])->name('projects');
            Route::get('/volume',   [CompareProjectController::class, 'compareVolumeTotal'])->name('volume');
            Route::get('/sentiment',[CompareProjectController::class, 'compareSentiment'])->name('sentiment');
            Route::get('/authors',  [CompareProjectController::class, 'compareAuthors'])->name('authors');
            Route::get('/all',      [CompareProjectController::class, 'compareAll'])->name('all');
        });

        // Trending Topic API
        Route::get('/trending-topics-twitter', [TrendingTopicController::class, 'getData'])->name('trending-topics-twitter');

        // ─────────────────────────────────────────────────────
        // ALL PLATFORM AI ROUTES
        // ─────────────────────────────────────────────────────
        Route::prefix('all-platform')->name('all-platform.')->group(function () {
            Route::get('/ai-analysis-data', [AllPlatformAiController::class, 'data'])->name('ai-analysis-data');
            Route::post('/ai-proxy',        [AllPlatformAiController::class, 'proxy'])->name('ai-proxy');
        });
    });

    // ═══════════════════════════════════════════════════════
    // PAGE ROUTES
    // ═══════════════════════════════════════════════════════
    Route::get('/dashboard',        [MkController::class, 'dashboard'])->name('dashboard');
    Route::get('/dashboard/chart-data', [MkController::class, 'chartData'])->name('dashboard.chart-data'); // ← ADDED
    Route::get('/profile',          [MkController::class, 'profile'])->name('profile');
    Route::post('/profile/avatar',  [MkController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/data-overview',    [MkController::class, 'dataOverview'])->name('data-overview');
    Route::get('/projects',         [MkController::class, 'projects'])->name('projects');
    Route::get('/media-statistic',       [MediaStatisticController::class, 'index'])->name('media-statistic');
    Route::get('/media-statistic/trend', [MediaStatisticController::class, 'trendPage'])->name('media-statistic.trend');

    Route::get('/sentiment',             [MediaStatisticController::class, 'sentimentPage'])->name('sentiment');
    Route::get('/net-sentiment-score',   [MediaStatisticController::class, 'netSentimentScorePage'])->name('net-sentiment-score');
    Route::get('/engagement',            [MediaStatisticController::class, 'engagementPage'])->name('engagement');
    Route::get('/interaction-sentiment', [MediaStatisticController::class, 'interactionSentimentPage'])->name('interaction-sentiment');
    Route::get('/compare',               [CompareProjectController::class, 'index'])->name('compare.index');
    Route::get('/topic-map',             [TopicMapController::class, 'index'])->name('topic-map');
    Route::get('/trending-topic',          [TrendingTopicController::class, 'index'])->name('trending-topic');
    Route::get('/search-topic',            [SearchTopicController::class, 'index'])->name('search-topic');

    Route::prefix('top-analytics')->name('top-analytics.')->group(function () {
        Route::get('/hashtags',    [TopAnalyticsController::class, 'hashtags'])->name('hashtags');
        Route::get('/locations',   [TopAnalyticsController::class, 'locations'])->name('locations');
        Route::get('/influencers', [TopAnalyticsController::class, 'influencers'])->name('influencers');
    });

    // ─────────────────────────────────────────────────────
    // NEWS PAGE ROUTES
    // ─────────────────────────────────────────────────────
    Route::prefix('news')->name('news.')->group(function () {
        Route::get('/word-cloud',    [NewsController::class, 'newsWordCloudPage'])->name('word-cloud');
        Route::get('/top-publishers',[NewsController::class, 'topPublisherPage'])->name('top-publishers');
        Route::get('/timeline',      [NewsController::class, 'newsTimelinePage'])->name('timeline');
        Route::get('/articles',      [NewsController::class, 'articlesPage'])->name('articles');
        Route::get('/topic-map',     [NewsController::class, 'newsTopicMapPage'])->name('topic-map');
        Route::get('/ai-analysis',   [NewsController::class, 'aiAnalysisPage'])->name('ai-analysis');
    });

    // ─────────────────────────────────────────────────────
    // X (TWITTER) PAGE ROUTES
    // ─────────────────────────────────────────────────────
    Route::prefix('x')->name('x.')->group(function () {
        Route::get('/overview',             [XOverviewController::class, 'index'])->name('overview');
        Route::get('/trending-topics',      [XOverviewController::class, 'trendingTopicsPage'])->name('trending-topics');
        Route::get('/trending-word-cloud',  [XOverviewController::class, 'trendingWordCloudPage'])->name('trending-word-cloud');
        Route::get('/top-hashtags',         [XOverviewController::class, 'topHashtagsPage'])->name('top-hashtags');
        Route::get('/shared-urls',          [XOverviewController::class, 'sharedUrlsPage'])->name('shared-urls');
        Route::get('/most-status',          [XOverviewController::class, 'mostStatusPage'])->name('most-status');
        Route::get('/most-retweets',        [XOverviewController::class, 'mostRetweetsPage'])->name('most-retweets');
        Route::get('/most-engagement',      [XOverviewController::class, 'mostEngagementPage'])->name('most-engagement');
        Route::get('/most-active-users',    [XOverviewController::class, 'mostActiveUsersPage'])->name('most-active-users');
        Route::get('/authors-demographics', [XOverviewController::class, 'authorsDemographicsPage'])->name('authors.demographics');
        Route::get('/geographic',           [XOverviewController::class, 'geographicPage'])->name('geographic');
        Route::get('/post-with-location',   [XOverviewController::class, 'postWithLocationPage'])->name('post-with-location');
        Route::get('/top-influencers',      [XOverviewController::class, 'topInfluencersPage'])->name('top-influencers');
        Route::get('/emotion-analysis',     [XOverviewController::class, 'emotionAnalysisPage'])->name('emotion-analysis');
        Route::get('/ai-analysis',          [XOverviewController::class, 'aiAnalysisPage'])->name('ai-analysis');
    });

    // ─────────────────────────────────────────────────────
    // FACEBOOK PAGE ROUTES
    // ─────────────────────────────────────────────────────
    Route::prefix('facebook')->name('facebook.')->group(function () {
        Route::get('/overview',             [FacebookOverviewController::class, 'index'])->name('overview');
        Route::get('/trending-topics',      [FacebookOverviewController::class, 'trendingTopicsPage'])->name('trending-topics');
        Route::get('/most-viewed-posts',    [FacebookOverviewController::class, 'mostViewedPostsPage'])->name('most-viewed-posts');
        Route::get('/top-hashtags',         [FacebookOverviewController::class, 'topHashtagsPage'])->name('top-hashtags');
        Route::get('/authors-demographics', [FacebookOverviewController::class, 'authorsDemographicsPage'])->name('authors.demographics');
        Route::get('/geographic',           [FacebookOverviewController::class, 'geographicPage'])->name('geographic');
        Route::get('/trending-word-cloud',  [FacebookOverviewController::class, 'trendingWordCloudPage'])->name('trending-word-cloud');
        Route::get('/most-engagement',      [FacebookOverviewController::class, 'mostEngagementPage'])->name('most-engagement');
        Route::get('/emotion-analysis',     [FacebookOverviewController::class, 'emotionAnalysisPage'])->name('emotion-analysis');
        Route::get('/ai-analysis',          [FacebookOverviewController::class, 'aiAnalysisPage'])->name('ai-analysis');
    });

    // ─────────────────────────────────────────────────────
    // INSTAGRAM PAGE ROUTES
    // ─────────────────────────────────────────────────────
    Route::prefix('instagram')->name('instagram.')->group(function () {
        Route::get('/overview',             [InstagramOverviewController::class, 'index'])->name('overview');
        Route::get('/trending-topics',      [InstagramOverviewController::class, 'trendingTopicsPage'])->name('trending-topics');
        Route::get('/most-viewed-posts',    [InstagramOverviewController::class, 'mostViewedPostsPage'])->name('most-viewed-posts');
        Route::get('/authors-demographics', [InstagramOverviewController::class, 'authorsDemographicsPage'])->name('authors.demographics');
        Route::get('/geographic',           [InstagramOverviewController::class, 'geographicPage'])->name('geographic');
        Route::get('/trending-word-cloud',  [InstagramOverviewController::class, 'trendingWordCloudPage'])->name('trending-word-cloud');
        Route::get('/most-engagement',      [InstagramOverviewController::class, 'mostEngagementPage'])->name('most-engagement');
        Route::get('/emotion-analysis',     [InstagramOverviewController::class, 'emotionAnalysisPage'])->name('emotion-analysis');
        Route::get('/ai-analysis',          [InstagramOverviewController::class, 'aiAnalysisPage'])->name('ai-analysis');
    });

    // ─────────────────────────────────────────────────────
    // YOUTUBE PAGE ROUTES
    // ─────────────────────────────────────────────────────
    Route::prefix('youtube')->name('youtube.')->group(function () {
        Route::get('/overview',             [YoutubeOverviewController::class, 'index'])->name('overview');
        Route::get('/trending-topics',      [YoutubeOverviewController::class, 'trendingTopicsPage'])->name('trending-topics');
        Route::get('/most-viewed-posts',    [YoutubeOverviewController::class, 'mostViewedPostsPage'])->name('most-viewed-posts');
        Route::get('/most-engagement',      [YoutubeOverviewController::class, 'mostEngagementPage'])->name('most-engagement');
        Route::get('/emotion-analysis',     [YoutubeOverviewController::class, 'emotionAnalysisPage'])->name('emotion-analysis');
        Route::get('/authors-demographics', [YoutubeOverviewController::class, 'authorsDemographicsPage'])->name('authors.demographics');
        Route::get('/geographic',           [YoutubeOverviewController::class, 'geographicPage'])->name('geographic');
        Route::get('/trending-word-cloud',  [YoutubeOverviewController::class, 'trendingWordCloudPage'])->name('trending-word-cloud');
        Route::get('/ai-analysis',          [YoutubeOverviewController::class, 'aiAnalysisPage'])->name('ai-analysis');
    });

    // ─────────────────────────────────────────────────────
    // TIKTOK PAGE ROUTES
    // ─────────────────────────────────────────────────────
    Route::prefix('tiktok')->name('tiktok.')->group(function () {
        Route::get('/overview',            [TiktokOverviewController::class, 'index'])->name('overview');
        Route::get('/trending-topics',     [TiktokOverviewController::class, 'trendingTopicsPage'])->name('trending-topics');
        Route::get('/most-viewed-posts',   [TiktokOverviewController::class, 'mostViewedPostsPage'])->name('most-viewed-posts');
        Route::get('/trending-word-cloud', [TiktokOverviewController::class, 'trendingWordCloudPage'])->name('trending-word-cloud');
        Route::get('/most-engagement',     [TiktokOverviewController::class, 'mostEngagementPage'])->name('most-engagement');
        Route::get('/emotion-analysis',    [TiktokOverviewController::class, 'emotionAnalysisPage'])->name('emotion-analysis');
        Route::get('/ai-analysis',         [TiktokOverviewController::class, 'aiAnalysisPage'])->name('ai-analysis');
    });

    Route::get('/geographic', [MkController::class, 'geographic'])->name('geographic');
    Route::get('/all-ai-analysis', [AllPlatformAiController::class, 'page'])->name('all-ai-analysis');

    Route::prefix('authors')->name('authors.')->group(function () {
        Route::get('/age',    [MkController::class, 'authorsAge'])->name('age');
        Route::get('/gender', [MkController::class, 'authorsGender'])->name('gender');
        Route::get('/type',   [MkController::class, 'authorsType'])->name('type');
    });

    Route::get('/categories', [MkController::class, 'categories'])->name('categories');

    Route::prefix('engagement')->name('engagement.')->group(function () {
        Route::get('/reach',    [MkController::class, 'reach'])->name('reach');
        Route::get('/urls',     [MkController::class, 'sharedUrls'])->name('urls');
        Route::get('/users',    [MkController::class, 'activeUsers'])->name('users');
        Route::get('/retweets', [MkController::class, 'mostRetweets'])->name('retweets');
    });

    Route::get('/publisher', [MkController::class, 'publisherStats'])->name('publisher');
    Route::get('/topics',    [MkController::class, 'recentTopics'])->name('topics');

    Route::prefix('data-source')->name('data-source.')->group(function () {
        Route::get('/users',   [DataSourceController::class, 'users'])->name('users');
        Route::get('/authors', [DataSourceController::class, 'authors'])->name('authors');
        Route::get('/volume',  [DataSourceController::class, 'volume'])->name('volume');
        Route::get('/trends',  [DataSourceController::class, 'trends'])->name('trends');
    });
});

Route::fallback(function () {
    return redirect()->route('user.login');
});