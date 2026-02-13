<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

/**
 * XAnalyticsClient
 *
 * Service khusus untuk X (Twitter) analytics.
 * Extends MediaKernelsClient agar semua method dasar (getToken, parseJson, dll)
 * bisa dipakai ulang, dengan media di-hardcode ke 'twitter' secara default.
 *
 * API yang dikelola di sini:
 * ─────────────────────────────────────────────────────
 * ✅ REUSE from parent (force media='twitter'):
 *   - authorsAge()           → /authors_age/
 *   - authorsGender()        → /authors_gender/
 *   - authorsType()          → /authors_type/
 *   - getSentiment()         → /get_sentiment/
 *   - sentimentTotal()       → /sentiment_total/
 *   - topHashtags()          → /top_hashtags/
 *   - topAuthorLocation()    → /top_author_location/
 *   - topInfluencers()       → /top_influencers/
 *   - volumeTotal()          → /volume_total/
 *   - totalAuthors()         → /total_authors/
 *   - geoTwitterUser()       → /get_geo_twitter_user/
 *   - geoTwitterUserSentiment() → /get_geo_twitter_user_sentiment/
 *   - mostActiveUsers()      → /most_active_users/
 *   - mostRetweets()         → /most_retweets/
 *   - sharedUrlFreq()        → /get_shared_url_freq/
 *   - topicMap()             → /topic_map/
 *
 * 🔥 NEW X-specific (belum ada di parent):
 *   - twitterTrendingTopics()  → /twitter_trending_topics/
 *   - twitterMostStatus()      → /twitter_most_status/
 *   - postWithLocation()       → /post_with_location/
 */
class XAnalyticsClient extends MediaKernelsClient
{
    /** Media identifier untuk seluruh X API */
    private const MEDIA = 'twitter';

    // ══════════════════════════════════════════════════════════════
    // WRAPPER METHODS — Reuse parent, paksa media = 'twitter'
    // ══════════════════════════════════════════════════════════════

    /**
     * Demografi usia author X.
     * Parent: authorsAge(projectId, media, ...)
     */
    public function getAuthorsAge(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $sort = 'post_freq desc'
    ): array {
        return parent::authorsAge($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime, $sort);
    }

    /**
     * Demografi gender author X.
     * Parent: authorsGender(projectId, media, ...)
     */
    public function getAuthorsGender(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $sort = 'post_freq desc'
    ): array {
        return parent::authorsGender($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime, $sort);
    }

    /**
     * Tipe author X (personal / organisasi / bot).
     * Parent: authorsType(projectId, media, ...)
     */
    public function getAuthorsType(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::authorsType($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Sentimen per post X (positif / negatif / netral).
     * Parent: getSentiment(projectId, media, ...)
     */
    public function getSentimentX(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::getSentiment($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Total sentimen X.
     * Parent: sentimentTotal(projectId, ...) — tidak ada param media,
     * tapi data sudah bisa di-filter dari response jika perlu.
     */
    public function getSentimentTotal(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::sentimentTotal($projectId, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Top hashtag di X.
     * Parent: topHashtags(projectId, media, ...)
     */
    public function getTopHashtags(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::topHashtags($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Lokasi author X terbanyak.
     * Parent: topAuthorLocation(projectId, media, ...)
     */
    public function getTopAuthorLocation(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::topAuthorLocation($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Top influencer X.
     * Parent: topInfluencers(projectId, ...) — tanpa param media
     */
    public function getTopInfluencers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::topInfluencers($projectId, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Total volume mention di X.
     * Parent: volumeTotal(projectId, media, ...)
     */
    public function getVolumeTotal(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::volumeTotal($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Total author X.
     * Parent: totalAuthors(projectId, media, ...)
     */
    public function getTotalAuthors(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::totalAuthors($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Sebaran geografis user X (peta).
     * Parent: geoTwitterUser(projectId, media, ...)
     */
    public function getGeoUsers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::geoTwitterUser($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Sebaran geografis user X berdasarkan sentimen.
     * Parent: geoTwitterUserSentiment(projectId, media, ..., sentiment)
     *
     * @param int $sentiment  1 = positif, -1 = negatif, 0 = netral
     */
    public function getGeoUserSentiment(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $sentiment = 1
    ): array {
        return parent::geoTwitterUserSentiment($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime, $sentiment);
    }

    /**
     * User paling aktif di X.
     * Parent: mostActiveUsers(projectId, ...) — tanpa param media
     */
    public function getMostActiveUsers(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::mostActiveUsers($projectId, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * Tweet paling banyak di-retweet.
     * Parent: mostRetweets(projectId, ...) — tanpa param media
     */
    public function getMostRetweets(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::mostRetweets($projectId, $startDate, $endDate, $startTime, $endTime);
    }

    /**
     * URL yang sering dishare di X.
     * Parent: sharedUrlFreq(projectId, ...) — tanpa param media
     */
    public function getSharedUrlFreq(
        string $projectId,
        string $startDate,
        string $endDate
    ): array {
        return parent::sharedUrlFreq($projectId, $startDate, $endDate);
    }

    /**
     * Topic map / peta topik diskusi di X.
     * Parent: topicMap(projectId, media, ...)
     */
    public function getTopicMap(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23
    ): array {
        return parent::topicMap($projectId, self::MEDIA, $startDate, $endDate, $startTime, $endTime);
    }

    // ══════════════════════════════════════════════════════════════
    // NEW X-SPECIFIC METHODS — Belum ada di parent
    // ══════════════════════════════════════════════════════════════

    /**
     * Topik yang sedang trending di Twitter/X.
     *
     * GET /twitter_trending_topics/
     * Params: start_date, start_time, end_date, end_time, location, topics, token
     *
     * Catatan: endpoint ini TIDAK butuh project_id
     *
     * @param string $location  Nama kota/negara, contoh: 'Indonesia', 'Jakarta'
     * @param string $topics    Keyword filter (opsional, bisa kosong)
     */
    public function getTrendingTopics(
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $location = 'Indonesia',
        string $topics = ''
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(30)->acceptJson()->get(
                $this->baseUrl() . '/twitter_trending_topics/',
                [
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'location'   => $location,
                    'topics'     => $topics,
                    'token'      => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('XAnalytics twitterTrendingTopics response', [
                'location'   => $location,
                'top_keys'   => array_keys($json),
                'count'      => is_array($json) ? count($json) : 0,
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::warning('XAnalytics twitterTrendingTopics error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    /**
     * Status/tweet terbanyak di X.
     *
     * GET /twitter_most_status/
     * Params: project_id, media, start_date, start_time, end_date, end_time, mention_type, token
     *
     * @param string $mentionType  'view_all' | 'retweet' | 'reply' | 'quote' | 'original'
     */
    public function getMostStatus(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        string $mentionType = 'view_all'
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/twitter_most_status/',
                [
                    'project_id'   => $projectId,
                    'media'        => self::MEDIA,
                    'start_date'   => $startDate,
                    'end_date'     => $endDate,
                    'start_time'   => $startTime,
                    'end_time'     => $endTime,
                    'mention_type' => $mentionType,
                    'token'        => $token,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('XAnalytics twitterMostStatus response', [
                'project_id'   => $projectId,
                'mention_type' => $mentionType,
                'top_keys'     => array_keys($json),
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::warning('XAnalytics twitterMostStatus error', ['error' => $e->getMessage()]);
            return ['data' => []];
        }
    }

    /**
     * Post X yang menyertakan data lokasi (geo-tagged tweet).
     *
     * GET /post_with_location/
     * Params: project_id, start_date, start_time, end_date, end_time, token, start, rows
     *
     * @param int $start  Offset pagination (default 0)
     * @param int $rows   Jumlah data per halaman (default 10)
     */
    public function getPostWithLocation(
        string $projectId,
        string $startDate,
        string $endDate,
        int $startTime = 0,
        int $endTime = 23,
        int $start = 0,
        int $rows = 10
    ): array {
        try {
            $token = $this->getToken();

            $res = Http::timeout(60)->acceptJson()->get(
                $this->baseUrl() . '/post_with_location/',
                [
                    'project_id' => $projectId,
                    'start_date' => $startDate,
                    'end_date'   => $endDate,
                    'start_time' => $startTime,
                    'end_time'   => $endTime,
                    'token'      => $token,
                    'start'      => $start,
                    'rows'       => $rows,
                ]
            );

            $res->throw();

            $json = $this->parseJson($res);

            Log::info('XAnalytics postWithLocation response', [
                'project_id' => $projectId,
                'top_keys'   => array_keys($json),
                'total'      => $json['total'] ?? 'unknown',
            ]);

            return $json;

        } catch (\Exception $e) {
            Log::warning('XAnalytics postWithLocation error', ['error' => $e->getMessage()]);
            return ['data' => [], 'total' => 0];
        }
    }
}