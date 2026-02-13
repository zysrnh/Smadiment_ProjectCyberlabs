<?php

namespace App\Http\Controllers\X;

use App\Http\Controllers\Controller;
use App\Services\XAnalyticsClient;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * XController
 *
 * Mengelola seluruh halaman dan API endpoint untuk X (Twitter) Analytics.
 * Setiap halaman menggunakan lazy loading — data dimuat via AJAX setelah page render.
 *
 * Routes yang perlu ditambahkan di web.php:
 * ─────────────────────────────────────────
 * Route::prefix('x')->name('x.')->group(function () {
 *     // Pages
 *     Route::get('/overview',          [XController::class, 'overview'])->name('overview');
 *     Route::get('/sentiment',         [XController::class, 'sentiment'])->name('sentiment');
 *     Route::get('/authors',           [XController::class, 'authors'])->name('authors');
 *     Route::get('/hashtags',          [XController::class, 'hashtags'])->name('hashtags');
 *     Route::get('/influencers',       [XController::class, 'influencers'])->name('influencers');
 *     Route::get('/geographic',        [XController::class, 'geographic'])->name('geographic');
 *     Route::get('/trending',          [XController::class, 'trending'])->name('trending');
 *     Route::get('/most-status',       [XController::class, 'mostStatus'])->name('most-status');
 *     Route::get('/topic-map',         [XController::class, 'topicMap'])->name('topic-map');
 *
 *     // API (lazy loading endpoints)
 *     Route::prefix('api')->name('api.')->group(function () {
 *         Route::get('/overview-stats',       [XController::class, 'apiOverviewStats'])->name('overview-stats');
 *         Route::get('/sentiment',            [XController::class, 'apiSentiment'])->name('sentiment');
 *         Route::get('/sentiment-timeline',   [XController::class, 'apiSentimentTimeline'])->name('sentiment-timeline');
 *         Route::get('/authors-age',          [XController::class, 'apiAuthorsAge'])->name('authors-age');
 *         Route::get('/authors-gender',       [XController::class, 'apiAuthorsGender'])->name('authors-gender');
 *         Route::get('/authors-type',         [XController::class, 'apiAuthorsType'])->name('authors-type');
 *         Route::get('/hashtags',             [XController::class, 'apiHashtags'])->name('hashtags');
 *         Route::get('/influencers',          [XController::class, 'apiInfluencers'])->name('influencers');
 *         Route::get('/geo-users',            [XController::class, 'apiGeoUsers'])->name('geo-users');
 *         Route::get('/geo-sentiment',        [XController::class, 'apiGeoSentiment'])->name('geo-sentiment');
 *         Route::get('/trending-topics',      [XController::class, 'apiTrendingTopics'])->name('trending-topics');
 *         Route::get('/most-status',          [XController::class, 'apiMostStatus'])->name('most-status');
 *         Route::get('/most-retweets',        [XController::class, 'apiMostRetweets'])->name('most-retweets');
 *         Route::get('/active-users',         [XController::class, 'apiActiveUsers'])->name('active-users');
 *         Route::get('/shared-urls',          [XController::class, 'apiSharedUrls'])->name('shared-urls');
 *         Route::get('/topic-map',            [XController::class, 'apiTopicMap'])->name('topic-map');
 *         Route::get('/post-with-location',   [XController::class, 'apiPostWithLocation'])->name('post-with-location');
 *     });
 * });
 */
class XController extends Controller
{
    public function __construct(
        protected XAnalyticsClient $client
    ) {}

    // ══════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════

    /**
     * Ambil common params dari request (project_id, tanggal, waktu).
     */
    private function getParams(Request $request): array
    {
        return [
            'project_id' => $request->query('project_id', ''),
            'start_date' => $request->query('start_date', now()->subDays(7)->format('Y-m-d')),
            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
            'start_time' => (int) $request->query('start_time', 0),
            'end_time'   => (int) $request->query('end_time', 23),
        ];
    }

    /**
     * Shared view data untuk semua halaman X.
     */
    private function sharedViewData(Request $request): array
    {
        return [
            'currentProjectId' => $request->query('project_id', ''),
            'startDate'        => $request->query('start_date', now()->subDays(7)->format('Y-m-d')),
            'endDate'          => $request->query('end_date', now()->format('Y-m-d')),
        ];
    }

    // ══════════════════════════════════════════════════════════════
    // PAGE VIEWS — hanya render blade, data via AJAX
    // ══════════════════════════════════════════════════════════════

    /** Halaman Overview X — ringkasan semua metrik */
    public function overview(Request $request)
    {
        return view('x.overview', $this->sharedViewData($request));
    }

    /** Halaman Analisis Sentimen X */
    public function sentiment(Request $request)
    {
        return view('x.sentiment', $this->sharedViewData($request));
    }

    /** Halaman Demografi Author X (usia, gender, tipe) */
    public function authors(Request $request)
    {
        return view('x.authors', $this->sharedViewData($request));
    }

    /** Halaman Top Hashtag X */
    public function hashtags(Request $request)
    {
        return view('x.hashtags', $this->sharedViewData($request));
    }

    /** Halaman Top Influencer X */
    public function influencers(Request $request)
    {
        return view('x.influencers', $this->sharedViewData($request));
    }

    /** Halaman Geographic — peta sebaran user & sentimen */
    public function geographic(Request $request)
    {
        return view('x.geographic', $this->sharedViewData($request));
    }

    /** Halaman Twitter Trending Topics */
    public function trending(Request $request)
    {
        return view('x.trending', $this->sharedViewData($request));
    }

    /** Halaman Most Status / Tweet Terpopuler */
    public function mostStatus(Request $request)
    {
        return view('x.most-status', $this->sharedViewData($request));
    }

    /** Halaman Topic Map X */
    public function topicMap(Request $request)
    {
        return view('x.topic-map', $this->sharedViewData($request));
    }

    // ══════════════════════════════════════════════════════════════
    // API ENDPOINTS — lazy loading (return JSON)
    // ══════════════════════════════════════════════════════════════

    /**
     * API: Statistik ringkasan untuk halaman Overview.
     * Memanggil: volumeTotal + sentimentTotal + totalAuthors secara paralel.
     */
    public function apiOverviewStats(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $volume    = $this->client->getVolumeTotal($p['project_id'], $p['start_date'], $p['end_date'], $p['start_time'], $p['end_time']);
            $sentiment = $this->client->getSentimentTotal($p['project_id'], $p['start_date'], $p['end_date'], $p['start_time'], $p['end_time']);
            $authors   = $this->client->getTotalAuthors($p['project_id'], $p['start_date'], $p['end_date'], $p['start_time'], $p['end_time']);
            $retweets  = $this->client->getMostRetweets($p['project_id'], $p['start_date'], $p['end_date'], $p['start_time'], $p['end_time']);

            return response()->json([
                'volume'    => $volume,
                'sentiment' => $sentiment,
                'authors'   => $authors,
                'retweets'  => $retweets,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Breakdown sentimen (positif / negatif / netral).
     */
    public function apiSentiment(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getSentimentX(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Timeline sentimen per hari.
     */
    public function apiSentimentTimeline(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getSentimentTotal(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Demografi usia author X.
     */
    public function apiAuthorsAge(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getAuthorsAge(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Demografi gender author X.
     */
    public function apiAuthorsGender(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getAuthorsGender(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Tipe author X (personal / organisasi).
     */
    public function apiAuthorsType(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getAuthorsType(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Top hashtag di X.
     */
    public function apiHashtags(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getTopHashtags(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Top influencer X.
     */
    public function apiInfluencers(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getTopInfluencers(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Sebaran geografis user X.
     */
    public function apiGeoUsers(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getGeoUsers(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Sebaran geografis user X berdasarkan sentimen.
     *
     * @query int $sentiment  1 = positif, -1 = negatif, 0 = netral
     */
    public function apiGeoSentiment(Request $request): JsonResponse
    {
        $p         = $this->getParams($request);
        $sentiment = (int) $request->query('sentiment', 1);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getGeoUserSentiment(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time'],
                $sentiment
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Twitter trending topics.
     * Tidak butuh project_id.
     *
     * @query string $location  Default: Indonesia
     */
    public function apiTrendingTopics(Request $request): JsonResponse
    {
        $p        = $this->getParams($request);
        $location = $request->query('location', 'Indonesia');
        $topics   = $request->query('topics', '');

        try {
            $data = $this->client->getTrendingTopics(
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time'],
                $location,
                $topics
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Tweet/status terpopuler.
     *
     * @query string $mention_type  view_all | retweet | reply | quote | original
     */
    public function apiMostStatus(Request $request): JsonResponse
    {
        $p           = $this->getParams($request);
        $mentionType = $request->query('mention_type', 'view_all');

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getMostStatus(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time'],
                $mentionType
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Tweet paling banyak di-retweet.
     */
    public function apiMostRetweets(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getMostRetweets(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: User paling aktif di X.
     */
    public function apiActiveUsers(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getMostActiveUsers(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: URL yang sering dishare di X.
     */
    public function apiSharedUrls(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getSharedUrlFreq(
                $p['project_id'],
                $p['start_date'],
                $p['end_date']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Topic map X.
     */
    public function apiTopicMap(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getTopicMap(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Post X dengan data lokasi (geo-tagged).
     *
     * @query int $start  Offset pagination
     * @query int $rows   Jumlah data per halaman
     */
    public function apiPostWithLocation(Request $request): JsonResponse
    {
        $p     = $this->getParams($request);
        $start = (int) $request->query('start', 0);
        $rows  = (int) $request->query('rows', 10);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getPostWithLocation(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time'],
                $start,
                $rows
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Top author location X.
     */
    public function apiTopAuthorLocation(Request $request): JsonResponse
    {
        $p = $this->getParams($request);

        if (empty($p['project_id'])) {
            return response()->json(['error' => 'project_id wajib diisi'], 422);
        }

        try {
            $data = $this->client->getTopAuthorLocation(
                $p['project_id'],
                $p['start_date'],
                $p['end_date'],
                $p['start_time'],
                $p['end_time']
            );
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}