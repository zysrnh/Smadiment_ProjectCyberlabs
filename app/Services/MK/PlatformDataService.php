<?php

namespace App\Services\MK;

use App\Services\MediaKernelsClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * PlatformDataService
 *
 * Centralizes all platform-specific data fetching and normalization.
 * Used by AllPlatformAiController (and any future consumers).
 *
 * Design principles:
 *  - Each platform has one dedicated fetch method.
 *  - All results are normalized into a unified shape.
 *  - Errors are isolated: one failing platform does NOT kill the rest.
 *  - Optional 5-minute cache per (project × platform × date range).
 */
class PlatformDataService
{
    /** Max items fetched per platform per project. */
    private const LIMIT = 15;

    /** Cache TTL in seconds (5 minutes). */
    private const CACHE_TTL = 300;

    public function __construct(private readonly MediaKernelsClient $client) {}

    // =========================================================================
    // PUBLIC: multi-project aggregation
    // =========================================================================

    /**
     * Aggregate data across all given projects.
     *
     * Returns:
     * [
     *   'summary'  => [...],   // global sentiment totals + percentages
     *   'projects' => [...],   // per-project breakdown
     *   'dataset'  => [...],   // flat list of normalized items (all projects)
     * ]
     */
    public function aggregateAll(array $projectIds, ?string $startDate, ?string $endDate): array
    {
        $allItems        = [];
        $projectSummaries = [];
        $globalSentiment = ['positive' => 0, 'negative' => 0, 'neutral' => 0];

        foreach ($projectIds as $projectId) {
            $projectResult = $this->aggregateProject((string) $projectId, $startDate, $endDate);

            // Merge flat dataset
            foreach ($projectResult['dataset'] as $item) {
                $allItems[] = $item;
            }

            // Accumulate global sentiment
            $s = $projectResult['sentiment'];
            $globalSentiment['positive'] += $s['positive'];
            $globalSentiment['negative'] += $s['negative'];
            $globalSentiment['neutral']  += $s['neutral'];

            $projectSummaries[] = [
                'project_id' => $projectId,
                'sentiment'  => $s,
                'counts'     => $projectResult['counts'],
            ];
        }

        $total = max(1, array_sum($globalSentiment));

        return [
            'summary' => [
                'total_positive'  => $globalSentiment['positive'],
                'total_negative'  => $globalSentiment['negative'],
                'total_neutral'   => $globalSentiment['neutral'],
                'total_mentions'  => $total,
                'pct_positive'    => round($globalSentiment['positive'] / $total * 100, 1),
                'pct_negative'    => round($globalSentiment['negative'] / $total * 100, 1),
                'pct_neutral'     => round($globalSentiment['neutral']  / $total * 100, 1),
                'project_count'   => count($projectIds),
            ],
            'projects' => $projectSummaries,
            'dataset'  => $allItems,
        ];
    }

    // =========================================================================
    // PRIVATE: single-project aggregation
    // =========================================================================

    private function aggregateProject(string $projectId, ?string $startDate, ?string $endDate): array
    {
        // Fetch all platforms in "parallel" via concurrent calls.
        // Laravel's Http::pool is only available for outbound HTTP; since
        // MediaKernelsClient wraps internal calls, we run them sequentially
        // but isolate each failure independently.
        $platforms = [
            'news'      => fn () => $this->getNewsData($projectId, $startDate, $endDate),
            'twitter'   => fn () => $this->getTwitterData($projectId, $startDate, $endDate),
            'facebook'  => fn () => $this->getFacebookData($projectId, $startDate, $endDate),
            'instagram' => fn () => $this->getInstagramData($projectId, $startDate, $endDate),
            'youtube'   => fn () => $this->getYoutubeData($projectId, $startDate, $endDate),
            'tiktok'    => fn () => $this->getTiktokData($projectId, $startDate, $endDate),
        ];

        $dataset   = [];
        $counts    = [];
        $sentiment = ['positive' => 0, 'negative' => 0, 'neutral' => 0];

        foreach ($platforms as $platform => $fetcher) {
            try {
                $items          = $fetcher();
                $counts[$platform] = count($items);

                foreach ($items as $item) {
                    $sentiment[$item['sentiment']]++;
                    $dataset[] = $item;
                }
            } catch (\Throwable $e) {
                Log::warning("PlatformDataService: {$platform} failed for project {$projectId}", [
                    'error' => $e->getMessage(),
                ]);
                $counts[$platform] = 0;
            }
        }

        // Supplement sentiment from the dedicated API (more accurate totals).
        try {
            $raw = $this->client->sentimentTotal($projectId, $startDate, $endDate);
            $sentiment = $this->parseSentiment($raw) ?: $sentiment;
        } catch (\Throwable $e) {
            Log::warning("PlatformDataService: sentimentTotal failed for {$projectId}", [
                'error' => $e->getMessage(),
            ]);
        }

        return compact('dataset', 'counts', 'sentiment');
    }

    // =========================================================================
    // PUBLIC: per-platform fetch methods (dedicated, reusable)
    // =========================================================================

    /**
     * Fetch Online News articles for a project.
     *
     * @return array<int, array> Normalized items.
     */
    public function getNewsData(string $projectId, ?string $startDate, ?string $endDate): array
    {
        return $this->cached($projectId, 'news', $startDate, $endDate, function () use ($projectId, $startDate, $endDate) {
            $raw   = $this->client->articles($projectId, 'doc', $startDate, $endDate, 0, 23, 0, self::LIMIT, true);
            $items = $this->toArray($raw);

            return array_map(fn ($a) => $this->normalizeItem($projectId, 'news', [
                'author'   => $a['publisher'] ?? $a['hostname'] ?? '',
                'content'  => $a['title']     ?? '',
                'body'     => $a['content']   ?? '',
                'date'     => $a['date_created'] ?? '',
                'sentiment'=> $a['sentiment_str'] ?? $a['sentiment'] ?? '',
                'metrics'  => ['likes' => 0, 'views' => 0, 'comments' => 0, 'shares' => 0],
                'url'      => $a['url'] ?? '',
            ]), $items);
        });
    }

    /**
     * Fetch Twitter/X posts for a project.
     *
     * @return array<int, array> Normalized items.
     */
    public function getTwitterData(string $projectId, ?string $startDate, ?string $endDate): array
    {
        return $this->cached($projectId, 'twitter', $startDate, $endDate, function () use ($projectId, $startDate, $endDate) {
            $raw   = $this->client->mostStatus($projectId, 'twitter', $startDate, $endDate, 0, 23, self::LIMIT, 'postbyview');
            $items = $this->toArray($raw);

            return array_map(fn ($t) => $this->normalizeItem($projectId, 'twitter', [
                'author'   => $t['author']['scr_name'] ?? $t['name'] ?? '',
                'content'  => $t['content'] ?? '',
                'date'     => $t['date_created'] ?? '',
                'sentiment'=> $t['sentiment_str'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($t['fav_count'] ?? $t['likes'] ?? 0),
                    'views'    => (int) ($t['view_cnt']  ?? $t['freq']  ?? 0),
                    'comments' => (int) ($t['reply_cnt'] ?? 0),
                    'shares'   => (int) ($t['rt']        ?? 0),
                ],
                'url' => $t['url'] ?? '',
            ]), $items);
        });
    }

    /**
     * Fetch Facebook posts for a project.
     *
     * @return array<int, array> Normalized items.
     */
    public function getFacebookData(string $projectId, ?string $startDate, ?string $endDate): array
    {
        return $this->cached($projectId, 'facebook', $startDate, $endDate, function () use ($projectId, $startDate, $endDate) {
            $raw   = $this->client->fbTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'fblike');
            $items = $this->toArray($raw);

            return array_map(fn ($p) => $this->normalizeItem($projectId, 'facebook', [
                'author'   => $p['contentJson']['from']['name'] ?? $p['author_name'] ?? $p['name'] ?? '',
                'content'  => $this->stripFbHtml($p['content'] ?? $p['name'] ?? ''),
                'date'     => $p['date_created'] ?? '',
                'sentiment'=> $p['sentiment_str'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($p['num_likes']    ?? $p['likes']    ?? 0),
                    'views'    => (int) ($p['view_cnt']     ?? $p['freq']     ?? 0),
                    'comments' => (int) ($p['num_comments'] ?? $p['comments'] ?? 0),
                    'shares'   => (int) ($p['num_shares']   ?? $p['shares']   ?? 0),
                ],
                'url' => $p['url'] ?? '',
            ]), $items);
        });
    }

    /**
     * Fetch Instagram posts for a project.
     *
     * @return array<int, array> Normalized items.
     */
    public function getInstagramData(string $projectId, ?string $startDate, ?string $endDate): array
    {
        return $this->cached($projectId, 'instagram', $startDate, $endDate, function () use ($projectId, $startDate, $endDate) {
            $raw   = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'postbylike');
            $items = $this->toArray($raw);

            return array_map(fn ($p) => $this->normalizeItem($projectId, 'instagram', [
                'author'   => $p['author_scr_name'] ?? $p['author_id'] ?? '',
                'content'  => $p['content'] ?? $p['caption'] ?? '',
                'date'     => $p['date_created'] ?? '',
                'sentiment'=> $p['sentiment_str'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($p['num_likes']    ?? $p['likes']    ?? 0),
                    'views'    => 0,
                    'comments' => (int) ($p['num_comments'] ?? $p['comments'] ?? 0),
                    'shares'   => 0,
                ],
                'url' => $p['url'] ?? '',
            ]), $items);
        });
    }

    /**
     * Fetch YouTube videos for a project.
     *
     * @return array<int, array> Normalized items.
     */
    public function getYoutubeData(string $projectId, ?string $startDate, ?string $endDate): array
    {
        return $this->cached($projectId, 'youtube', $startDate, $endDate, function () use ($projectId, $startDate, $endDate) {
            $raw   = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'postbyview');
            $items = $this->toArray($raw);

            return array_map(fn ($v) => $this->normalizeItem($projectId, 'youtube', [
                'author'   => $v['author_name']   ?? $v['channel_title'] ?? '',
                'content'  => $v['title']          ?? $v['content']       ?? '',
                'date'     => $v['date_created']   ?? '',
                'sentiment'=> $v['sentiment_str']  ?? '',
                'metrics'  => [
                    'likes'    => (int) ($v['num_likes']    ?? $v['likes']    ?? 0),
                    'views'    => (int) ($v['num_views']    ?? $v['view_cnt'] ?? 0),
                    'comments' => (int) ($v['num_comments'] ?? $v['comments'] ?? 0),
                    'shares'   => 0,
                ],
                'url' => $v['url'] ?? '',
            ]), $items);
        });
    }

    /**
     * Fetch TikTok posts for a project.
     *
     * @return array<int, array> Normalized items.
     */
    public function getTiktokData(string $projectId, ?string $startDate, ?string $endDate): array
    {
        return $this->cached($projectId, 'tiktok', $startDate, $endDate, function () use ($projectId, $startDate, $endDate) {
            $raw   = $this->client->tiktokTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'postbylike');
            $items = $this->toArray($raw);

            return array_map(fn ($p) => $this->normalizeItem($projectId, 'tiktok', [
                'author'   => $p['author_scr_name'] ?? $p['author_nickname'] ?? $p['nickname'] ?? '',
                'content'  => $p['content']          ?? $p['desc']           ?? '',
                'date'     => $p['date_created']     ?? '',
                'sentiment'=> $p['sentiment_str']    ?? '',
                'metrics'  => [
                    'likes'    => (int) ($p['digg_count']    ?? $p['num_likes']    ?? $p['likes']    ?? 0),
                    'views'    => (int) ($p['play_count']    ?? $p['num_views']    ?? 0),
                    'comments' => (int) ($p['comment_count'] ?? $p['num_comments'] ?? 0),
                    'shares'   => (int) ($p['share_count']   ?? $p['shares']       ?? 0),
                ],
                'url' => $p['url'] ?? '',
            ]), $items);
        });
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Wrap a fetcher in a cache layer keyed by project + platform + dates.
     */
    private function cached(
        string $projectId,
        string $platform,
        ?string $startDate,
        ?string $endDate,
        \Closure $fetcher
    ): array {
        $key = "mk_platform_{$projectId}_{$platform}_{$startDate}_{$endDate}";
        return Cache::remember($key, self::CACHE_TTL, $fetcher);
    }

    /**
     * Normalize a raw platform item into the unified schema.
     *
     * Unified shape:
     * {
     *   project_id, platform, author, content, body,
     *   metrics: { likes, views, comments, shares },
     *   date, sentiment, url
     * }
     */
    private function normalizeItem(string $projectId, string $platform, array $raw): array
    {
        return [
            'project_id' => $projectId,
            'platform'   => $platform,
            'author'     => substr(strip_tags($raw['author'] ?? ''), 0, 80),
            'content'    => substr(strip_tags($raw['content'] ?? ''), 0, 200),
            'body'       => substr(strip_tags($raw['body']    ?? ''), 0, 300),
            'metrics'    => array_map('intval', $raw['metrics'] ?? ['likes' => 0, 'views' => 0, 'comments' => 0, 'shares' => 0]),
            'date'       => substr($raw['date'] ?? '', 0, 10),
            'sentiment'  => $this->normalizeSentiment($raw['sentiment'] ?? ''),
            'url'        => $raw['url'] ?? '',
        ];
    }

    /**
     * Map any sentiment string to 'positive' | 'negative' | 'neutral'.
     */
    private function normalizeSentiment(string $raw): string
    {
        $lower = strtolower($raw);
        if (str_contains($lower, 'pos') || $lower === '1')                    return 'positive';
        if (str_contains($lower, 'neg') || $lower === '-1' || $lower === '2') return 'negative';
        return 'neutral';
    }

    /**
     * Parse a sentimentTotal API response into ['positive', 'negative', 'neutral'].
     */
    private function parseSentiment(mixed $raw): array
    {
        if (isset($raw['pos'], $raw['neg'], $raw['net'])) {
            return [
                'positive' => (int) $raw['pos'],
                'negative' => (int) $raw['neg'],
                'neutral'  => (int) $raw['net'],
            ];
        }

        if (isset($raw['bymedia']) && is_array($raw['bymedia'])) {
            $pos = $neg = $neu = 0;
            foreach ($raw['bymedia'] as $d) {
                $pos += (int) ($d['pos'] ?? 0);
                $neg += (int) ($d['neg'] ?? 0);
                $neu += (int) ($d['net'] ?? 0);
            }
            return ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
        }

        return [];
    }

    /**
     * Safely coerce any API response to a plain indexed array.
     * Handles: raw [], {data:[]}, {success, data}.
     */
    private function toArray(mixed $response): array
    {
        if (!is_array($response)) return [];
        if (empty($response) || isset($response[0])) return $response;
        if (isset($response['data']) && is_array($response['data'])) return $response['data'];
        return is_array($response) ? $response : [];
    }

    /**
     * Strip Facebook's HTML-formatted author/content fields.
     */
    private function stripFbHtml(string $text): string
    {
        $text = preg_replace('/<b>.*?<\/b>\s*/', '', $text);
        return trim(strip_tags($text));
    }
}