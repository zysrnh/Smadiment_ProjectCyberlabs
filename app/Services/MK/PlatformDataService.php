<?php

namespace App\Services\MK;

use App\Models\ProjectDailySentiment;
use App\Models\ProjectApiSnapshot;
use App\Services\MediaKernelsClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * PlatformDataService
 *
 * Centralizes all platform-specific data fetching, snapshot fallbacks, and normalization.
 * Used by AllPlatformAiController (and any future consumers).
 */
class PlatformDataService
{
    /** Max items fetched per platform per project. */
    private const LIMIT = 35;

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
        $allItems         = [];
        $projectSummaries = [];
        $globalSentiment  = ['positive' => 0, 'negative' => 0, 'neutral' => 0];

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
                $items = $fetcher();
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

        // Supplement sentiment from dedicated API or DB Daily Sentiments
        try {
            $raw = $this->client->sentimentTotal($projectId, $startDate, $endDate);
            $parsed = $this->parseSentiment($raw);
            if (!empty($parsed) && array_sum($parsed) > 0) {
                $sentiment = $parsed;
            }
        } catch (\Throwable $e) {
            Log::warning("PlatformDataService: sentimentTotal API failed for {$projectId}: {$e->getMessage()}");
        }

        // Database Fallback for Sentiment Totals if still 0
        if (array_sum($sentiment) === 0 || $sentiment === ['positive' => 0, 'negative' => 0, 'neutral' => 0]) {
            try {
                $sDate = $startDate ?: now()->subDays(30)->format('Y-m-d');
                $eDate = $endDate   ?: now()->format('Y-m-d');
                $dbSentiments = ProjectDailySentiment::getDailySentiments((int)$projectId, 'all', $sDate, $eDate);
                if (!empty($dbSentiments)) {
                    $pos = array_sum(array_column($dbSentiments, 'positive_count'));
                    $neg = array_sum(array_column($dbSentiments, 'negative_count'));
                    $neu = array_sum(array_column($dbSentiments, 'neutral_count'));
                    if ($pos + $neg + $neu > 0) {
                        $sentiment = ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
                    }
                }
            } catch (\Throwable $dbErr) {
                Log::warning("PlatformDataService: DB sentiment fallback error: {$dbErr->getMessage()}");
            }
        }

        // If dataset items exist but sentiment totals still 0, aggregate from dataset
        if (array_sum($sentiment) === 0 && !empty($dataset)) {
            foreach ($dataset as $item) {
                $sentiment[$item['sentiment']]++;
            }
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
            $items = [];
            try {
                $raw   = $this->client->articles($projectId, 'doc', $startDate, $endDate, 0, 23, 0, self::LIMIT, true);
                $items = $this->toArray($raw);
            } catch (\Throwable $e) {
                $items = [];
            }

            // Database Snapshot Fallback
            if (empty($items)) {
                $items = $this->getSnapshotItems($projectId, 'news', $startDate, $endDate);
            }

            return array_map(fn ($a) => $this->normalizeItem($projectId, 'news', [
                'author'   => $a['publisher_name'] ?? $a['publisher'] ?? $a['hostname'] ?? $a['media'] ?? $a['author_name'] ?? 'Media Publikasi',
                'content'  => $a['title']     ?? $a['content'] ?? '',
                'body'     => $a['content']   ?? $a['title'] ?? '',
                'date'     => $a['date']      ?? $a['date_created'] ?? $a['created_at'] ?? '',
                'sentiment'=> $a['sentiment_str'] ?? $a['sentiment'] ?? $a['class_sentiment'] ?? '',
                'metrics'  => ['likes' => (int)($a['likes'] ?? 0), 'views' => (int)($a['views'] ?? $a['num_views_i'] ?? 0), 'comments' => (int)($a['comments'] ?? 0), 'shares' => 0],
                'url'      => $a['url'] ?? $a['link'] ?? '',
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
            $items = [];
            try {
                $raw   = $this->client->mostStatus($projectId, 'twitter', $startDate, $endDate, 0, 23, self::LIMIT, 'postbyview');
                $items = $this->toArray($raw);

                if (empty($items)) {
                    $raw   = $this->client->mostStatus($projectId, 'twitter', $startDate, $endDate, 0, 23, self::LIMIT, 'postbyrt');
                    $items = $this->toArray($raw);
                }

                if (empty($items)) {
                    $raw   = $this->client->mostRetweets($projectId, $startDate, $endDate);
                    $items = array_slice($this->toArray($raw), 0, self::LIMIT);
                }

                if (empty($items)) {
                    $raw      = $this->client->mentions($projectId, $startDate, $endDate, 0, 23, true, 0, self::LIMIT);
                    $allItems = $this->toArray($raw);
                    $items    = array_values(array_filter($allItems, function ($item) {
                        $tc = strtolower($item['tcode'] ?? $item['media_type'] ?? $item['media'] ?? '');
                        return str_starts_with($tc, 'tw') || in_array($tc, ['twitter', 'x', 'rt']);
                    }));
                }
            } catch (\Throwable $e) {
                $items = [];
            }

            // Database Snapshot Fallback
            if (empty($items)) {
                $items = $this->getSnapshotItems($projectId, 'twitter', $startDate, $endDate);
            }

            return array_map(fn ($t) => $this->normalizeItem($projectId, 'twitter', [
                'author'   => $t['author']['scr_name'] ?? $t['author_name'] ?? $t['author_scr_name'] ?? $t['scr_name'] ?? $t['name'] ?? '@user',
                'content'  => $t['content'] ?? $t['title'] ?? $t['text'] ?? '',
                'body'     => $t['content'] ?? $t['title'] ?? '',
                'date'     => $t['date_created'] ?? $t['date'] ?? $t['created_at'] ?? '',
                'sentiment'=> $t['sentiment_str'] ?? $t['sentiment'] ?? $t['class_sentiment'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($t['fav_count'] ?? $t['num_likes'] ?? $t['likes'] ?? 0),
                    'views'    => (int) ($t['view_cnt']  ?? $t['num_views_i'] ?? $t['freq'] ?? 0),
                    'comments' => (int) ($t['reply_cnt'] ?? $t['num_replied'] ?? $t['num_comments'] ?? $t['comments'] ?? 0),
                    'shares'   => (int) ($t['rt']        ?? $t['shares'] ?? 0),
                ],
                'url' => $t['url'] ?? $t['link'] ?? '',
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
            $items = [];
            try {
                $raw   = $this->client->fbTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'fblike');
                $items = $this->toArray($raw);
            } catch (\Throwable $e) {
                $items = [];
            }

            // Database Snapshot Fallback
            if (empty($items)) {
                $items = $this->getSnapshotItems($projectId, 'facebook', $startDate, $endDate);
            }

            return array_map(fn ($p) => $this->normalizeItem($projectId, 'facebook', [
                'author'   => $p['contentJson']['from']['name'] ?? $p['author_name'] ?? $p['name'] ?? 'Facebook User',
                'content'  => $this->stripFbHtml($p['content'] ?? $p['title'] ?? $p['name'] ?? ''),
                'body'     => $this->stripFbHtml($p['content'] ?? $p['title'] ?? ''),
                'date'     => $p['date_created'] ?? $p['date'] ?? $p['created_at'] ?? '',
                'sentiment'=> $p['sentiment_str'] ?? $p['sentiment'] ?? $p['class_sentiment'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($p['num_likes']    ?? $p['likes']    ?? 0),
                    'views'    => (int) ($p['view_cnt']     ?? $p['freq']     ?? 0),
                    'comments' => (int) ($p['num_comments'] ?? $p['comments'] ?? 0),
                    'shares'   => (int) ($p['num_shares']   ?? $p['shares']   ?? 0),
                ],
                'url' => $p['url'] ?? $p['link'] ?? '',
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
            $items = [];
            try {
                $raw   = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'postbylike');
                $items = $this->toArray($raw);
            } catch (\Throwable $e) {
                $items = [];
            }

            // Database Snapshot Fallback
            if (empty($items)) {
                $items = $this->getSnapshotItems($projectId, 'instagram', $startDate, $endDate);
            }

            return array_map(fn ($p) => $this->normalizeItem($projectId, 'instagram', [
                'author'   => $p['author_scr_name'] ?? $p['author_name'] ?? $p['name'] ?? '@instagram_user',
                'content'  => $p['content'] ?? $p['title'] ?? $p['caption'] ?? '',
                'body'     => $p['content'] ?? $p['title'] ?? '',
                'date'     => $p['date_created'] ?? $p['date'] ?? $p['created_at'] ?? '',
                'sentiment'=> $p['sentiment_str'] ?? $p['sentiment'] ?? $p['class_sentiment'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($p['num_likes']    ?? $p['likes']    ?? 0),
                    'views'    => 0,
                    'comments' => (int) ($p['num_comments'] ?? $p['comments'] ?? 0),
                    'shares'   => 0,
                ],
                'url' => $p['url'] ?? $p['link'] ?? '',
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
            $items = [];
            try {
                $raw   = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'postbyview');
                $items = $this->toArray($raw);
            } catch (\Throwable $e) {
                $items = [];
            }

            // Database Snapshot Fallback
            if (empty($items)) {
                $items = $this->getSnapshotItems($projectId, 'youtube', $startDate, $endDate);
            }

            return array_map(fn ($v) => $this->normalizeItem($projectId, 'youtube', [
                'author'   => $v['author_name']   ?? $v['channel_title'] ?? $v['name'] ?? 'YouTube Creator',
                'content'  => $v['title']          ?? $v['content']       ?? '',
                'body'     => $v['content']        ?? $v['title']         ?? '',
                'date'     => $v['date_created']   ?? $v['date']          ?? $v['created_at'] ?? '',
                'sentiment'=> $v['sentiment_str']  ?? $v['sentiment']     ?? $v['class_sentiment'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($v['num_likes']    ?? $v['likes']    ?? 0),
                    'views'    => (int) ($v['num_views_i']  ?? $v['num_views'] ?? $v['view_cnt'] ?? $v['views'] ?? 0),
                    'comments' => (int) ($v['num_comments'] ?? $v['comments'] ?? 0),
                    'shares'   => 0,
                ],
                'url' => $v['url'] ?? $v['link'] ?? '',
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
            $items = [];
            try {
                $raw   = $this->client->tiktokTopStatus($projectId, $startDate, $endDate, 0, 23, self::LIMIT, 'postbylike');
                $items = $this->toArray($raw);
            } catch (\Throwable $e) {
                $items = [];
            }

            // Database Snapshot Fallback
            if (empty($items)) {
                $items = $this->getSnapshotItems($projectId, 'tiktok', $startDate, $endDate);
            }

            return array_map(fn ($p) => $this->normalizeItem($projectId, 'tiktok', [
                'author'   => $p['author_scr_name'] ?? $p['author_nickname'] ?? $p['nickname'] ?? $p['author_name'] ?? '@tiktok_user',
                'content'  => $p['content']          ?? $p['desc']           ?? $p['title'] ?? '',
                'body'     => $p['content']          ?? $p['desc']           ?? '',
                'date'     => $p['date_created']     ?? $p['date']           ?? $p['created_at'] ?? '',
                'sentiment'=> $p['sentiment_str']    ?? $p['sentiment']      ?? $p['class_sentiment'] ?? '',
                'metrics'  => [
                    'likes'    => (int) ($p['digg_count']    ?? $p['num_likes']    ?? $p['likes']    ?? 0),
                    'views'    => (int) ($p['play_count']    ?? $p['num_views']    ?? 0),
                    'comments' => (int) ($p['comment_count'] ?? $p['num_comments'] ?? 0),
                    'shares'   => (int) ($p['share_count']   ?? $p['shares']       ?? 0),
                ],
                'url' => $p['url'] ?? $p['link'] ?? '',
            ]), $items);
        });
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Retrieve platform items from Database Snapshots.
     */
    private function getSnapshotItems(string $projectId, string $platform, ?string $startDate, ?string $endDate): array
    {
        $pid   = (int) $projectId;
        $sDate = $startDate ?: now()->subDays(30)->format('Y-m-d');
        $eDate = $endDate   ?: now()->format('Y-m-d');

        $snap = ProjectApiSnapshot::findSnapshotForQuery($pid, 'all', 'news_mentions_0_1200', $sDate, $eDate)
             ?? ProjectApiSnapshot::findSnapshotForQuery($pid, 'all', 'news_mentions_0_500', $sDate, $eDate)
             ?? ProjectApiSnapshot::findSnapshotForQuery($pid, 'doc', 'articles_doc_all_0', $sDate, $eDate);

        if (!$snap) return [];

        $all = $this->toArray($snap);
        $filtered = [];

        foreach ($all as $item) {
            if (!is_array($item)) continue;
            $type = strtolower($item['type'] ?? $item['media_type'] ?? $item['media'] ?? '');

            $match = match($platform) {
                'news'      => in_array($type, ['doc', 'news', 'article', 'online_news', 'portal']),
                'twitter'   => in_array($type, ['twit', 'twitter', 'x', 'tweet']),
                'facebook'  => in_array($type, ['fb', 'facebook']),
                'instagram' => in_array($type, ['instagram', 'ig']),
                'youtube'   => in_array($type, ['youtube', 'yt', 'ytb']),
                'tiktok'    => in_array($type, ['tiktok', 'tt', 'vt']),
                default     => false,
            };

            if ($match) {
                $filtered[] = $item;
                if (count($filtered) >= self::LIMIT) break;
            }
        }

        return $filtered;
    }

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
     * Clean text and decode Mojibake / HTML entities.
     */
    private function cleanText(string $text): string
    {
        if (empty($text)) return '';

        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Fix common Mojibake artifacts
        $mojibakeMap = [
            'aEURoe' => '“',
            'aEUR'   => '”',
            'aEUR"'  => '—',
            'aEUR™'  => '’',
            'â€™'    => '’',
            'â€œ'    => '“',
            'â€ '    => '”',
            'â€"'    => '—',
            'â€“'    => '–',
            'â€¦'    => '…',
            'Ã©'     => 'é',
            'Ã '     => 'à',
            'Ã¨'     => 'è',
            'ðŸ'     => '',
        ];
        $text = str_replace(array_keys($mojibakeMap), array_values($mojibakeMap), $text);

        // Strip non-printable control characters
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $text);

        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        return trim($text);
    }

    /**
     * Normalize a raw platform item into the unified schema.
     */
    private function normalizeItem(string $projectId, string $platform, array $raw): array
    {
        $author  = $this->cleanText(strip_tags((string) ($raw['author'] ?? '')));
        $content = $this->cleanText(strip_tags((string) ($raw['content'] ?? '')));
        $body    = $this->cleanText(strip_tags((string) ($raw['body'] ?? '')));

        return [
            'project_id' => $projectId,
            'platform'   => $platform,
            'author'     => mb_substr($author, 0, 80),
            'content'    => mb_substr($content, 0, 200),
            'body'       => mb_substr($body, 0, 300),
            'metrics'    => array_map('intval', $raw['metrics'] ?? ['likes' => 0, 'views' => 0, 'comments' => 0, 'shares' => 0]),
            'date'       => substr((string) ($raw['date'] ?? ''), 0, 10),
            'sentiment'  => $this->normalizeSentiment((string) ($raw['sentiment'] ?? '')),
            'url'        => (string) ($raw['url'] ?? ''),
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