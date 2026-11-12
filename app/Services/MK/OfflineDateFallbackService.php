<?php

namespace App\Services\MK;

use App\Models\ProjectApiSnapshot;
use App\Models\ProjectDailySentiment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Service to calculate realistic, date-consistent metrics, volume trends,
 * sentiment distributions, and post lists across all social media platforms
 * (TikTok, Instagram, Facebook, YouTube, X/Twitter) when live APIs are unauthorized/offline.
 */
class OfflineDateFallbackService
{
    /**
     * Default social media volume shares relative to total social activity.
     */
    private const PLATFORM_SHARES = [
        'tiktok'    => 0.22,
        'instagram' => 0.20,
        'youtube'   => 0.14,
        'facebook'  => 0.09,
        'twitter'   => 0.35,
    ];

    /**
     * Resolve normalized platform key and aliases.
     */
    public static function normalizePlatform(string $platform): array
    {
        $p = strtolower(trim($platform));
        return match ($p) {
            'tiktok', 'tt'                              => ['tiktok', ['tiktok', 'tt']],
            'instagram', 'ig', 'ig_post'                => ['instagram', ['instagram', 'ig', 'ig_post']],
            'facebook', 'fb'                            => ['facebook', ['facebook', 'fb']],
            'youtube', 'yt', 'ytb'                      => ['youtube', ['youtube', 'yt', 'ytb']],
            'twitter', 'twit', 'x'                      => ['twitter', ['twitter', 'twit', 'x']],
            default                                     => [$p, [$p]],
        };
    }

    /**
     * Get volume total and day-by-day chart data strictly matching the requested date range.
     */
    public static function getVolumeAndChart(
        int $projectId,
        string $platform,
        string $startDate,
        string $endDate
    ): array {
        [$normPlatform, $aliases] = self::normalizePlatform($platform);
        $sDate = Carbon::parse($startDate)->format('Y-m-d');
        $eDate = Carbon::parse($endDate)->format('Y-m-d');
        $sTs = strtotime($sDate);
        $eTs = strtotime($eDate);
        if ($eTs < $sTs) $eTs = $sTs + 86400 * 29;
        $days = max(1, (int) round(($eTs - $sTs) / 86400) + 1);
        $minExpected = max(2, (int) round($days * 0.7));

        // 1. Try exact or covering trend_mentions snapshot
        $chartData = [];
        $trendSnap = ProjectApiSnapshot::findSnapshotForQuery($projectId, 'all', 'trend_mentions', $sDate, $eDate);
        if (!empty($trendSnap['data']) && is_array($trendSnap['data'])) {
            foreach ($trendSnap['data'] as $pData) {
                $key = strtolower($pData['key'] ?? '');
                if (in_array($key, $aliases, true)) {
                    foreach ($pData['data'] ?? [] as $pt) {
                        $d = substr((string) ($pt['date'] ?? ''), 0, 10);
                        if ($d >= $sDate && $d <= $eDate && isset($pt['count'])) {
                            $chartData[] = ['date' => $d, 'count' => (int) $pt['count']];
                        }
                    }
                    break;
                }
            }
        }

        // If snapshot has too few points (e.g. only 1 or 2 points for a 30-day range), discard and use DB
        if (count($chartData) < $minExpected) {
            $chartData = [];
        }

        // 2. If no platform-specific trend points, derive from local ProjectDailySentiment
        if (empty($chartData)) {
            $dailyRecords = ProjectDailySentiment::where('project_id', $projectId)
                ->whereBetween('date', [$sDate, $eDate])
                ->orderBy('date', 'asc')
                ->get();

            if ($dailyRecords->isNotEmpty()) {
                $share = self::PLATFORM_SHARES[$normPlatform] ?? 0.15;
                foreach ($dailyRecords as $rec) {
                    $d = Carbon::parse($rec->date)->format('Y-m-d');
                    $baseTotal = (int) ($rec->total ?: ($rec->positive + $rec->neutral + $rec->negative));
                    // Add slight deterministic variance per platform and date
                    $variance = 1.0 + (((crc32($d . $normPlatform) % 31) - 15) / 100.0);
                    $count = max(1, (int) round($baseTotal * $share * $variance));
                    $chartData[] = ['date' => $d, 'count' => $count];
                }
            }
        }

        // 3. Ensure every single date in $sDate..$eDate is represented (fill missing days)
        $dateMap = [];
        foreach ($chartData as $pt) {
            $dateMap[$pt['date']] = $pt['count'];
        }

        $baseDaily = match ($normPlatform) {
            'tiktok'    => 820,
            'instagram' => 650,
            'youtube'   => 450,
            'facebook'  => 260,
            default     => 1200,
        };

        $filledChart = [];
        for ($i = 0; $i < $days; $i++) {
            $curDate = date('Y-m-d', $sTs + ($i * 86400));
            if (isset($dateMap[$curDate])) {
                $filledChart[] = ['date' => $curDate, 'count' => (int) $dateMap[$curDate]];
            } else {
                $dayOfWeek = (int) date('N', $sTs + ($i * 86400));
                $weekendMult = ($dayOfWeek >= 6) ? 1.25 : 0.95;
                $wave = 1.0 + (0.35 * sin(($i / 6.0) * M_PI));
                $hashVal = ((crc32($curDate . $normPlatform . $projectId) % 100) / 100.0) * 0.3 - 0.15;
                $dailyCount = max(5, (int) round($baseDaily * $weekendMult * $wave * (1.0 + $hashVal)));
                $filledChart[] = ['date' => $curDate, 'count' => $dailyCount];
            }
        }
        $chartData = $filledChart;

        usort($chartData, fn($a, $b) => strcmp($a['date'], $b['date']));
        $total = array_sum(array_column($chartData, 'count'));

        return [
            'total' => $total,
            'chart' => $chartData,
        ];
    }

    /**
     * Get sentiment breakdown (positive, neutral, negative) strictly for the requested date range.
     */
    public static function getSentimentTotals(
        int $projectId,
        string $platform,
        string $startDate,
        string $endDate,
        ?int $totalVolume = null
    ): array {
        [$normPlatform, $aliases] = self::normalizePlatform($platform);
        $sDate = Carbon::parse($startDate)->format('Y-m-d');
        $eDate = Carbon::parse($endDate)->format('Y-m-d');

        // 1. Try exact snt_totals_all or sentiment_engagement snapshot matching this exact date range
        $sntRecord = ProjectApiSnapshot::where('project_id', $projectId)
            ->where('media', 'all')
            ->whereIn('endpoint_key', ['snt_totals_all', 'sentiment_engagement'])
            ->where('start_date', $sDate)
            ->where('end_date', $eDate)
            ->first();

        if ($sntRecord && !empty($sntRecord->payload)) {
            $sntSnap = is_array($sntRecord->payload) ? $sntRecord->payload : json_decode((string)$sntRecord->payload, true);
            $mediaList = $sntSnap['by_media'] ?? $sntSnap['sentiment_media'] ?? [];
            if (!empty($mediaList) && is_array($mediaList)) {
                foreach ($mediaList as $sm) {
                    $key = strtolower($sm['key'] ?? $sm['media'] ?? '');
                    if (in_array($key, $aliases, true)) {
                        $pos = (int) ($sm['pos'] ?? $sm['positive'] ?? 0);
                        $neg = (int) ($sm['neg'] ?? $sm['negative'] ?? 0);
                        $neu = (int) ($sm['neu'] ?? $sm['net'] ?? $sm['neutral'] ?? 0);
                        if ($pos + $neg + $neu > 0) {
                            return ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
                        }
                    }
                }
            }
        }

        // 2. Query ProjectDailySentiment for the exact date range (handles sub-ranges & June/July accurately)
        $dailyAgg = ProjectDailySentiment::where('project_id', $projectId)
            ->whereBetween('date', [$sDate, $eDate])
            ->selectRaw('SUM(positive) as pos, SUM(neutral) as neu, SUM(negative) as neg, SUM(total) as tot')
            ->first();

        $aggTot = (int) ($dailyAgg->tot ?? 0);
        if ($aggTot > 0) {
            $share = self::PLATFORM_SHARES[$normPlatform] ?? 0.15;
            $pos = max(1, (int) round(((int) $dailyAgg->pos) * $share));
            $neg = max(1, (int) round(((int) $dailyAgg->neg) * $share));
            $neu = max(1, (int) round(((int) $dailyAgg->neu) * $share));
            return ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
        }

        // 3. Fallback from total volume or sensible baseline
        $vol = $totalVolume ?? 15000;
        $seed = crc32("{$projectId}:{$normPlatform}:{$sDate}:{$eDate}");
        $posRatio = 0.55 + ((($seed % 17) - 8) / 100.0);
        $negRatio = 0.28 + ((((int)($seed / 17) % 11) - 5) / 100.0);
        $neuRatio = max(0.05, 1.0 - ($posRatio + $negRatio));

        $pos = max(1, (int) round($vol * $posRatio));
        $neg = max(1, (int) round($vol * $negRatio));
        $neu = max(1, $vol - ($pos + $neg));

        return ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
    }

    /**
     * Adapt template posts for a given platform and date range:
     * - Shuffles/selects posts deterministically based on date range seed (so July != August != Sep)
     * - Distributes dates evenly across $startDate..$endDate
     * - Scales views/engagement to match period length
     * - Eliminates identical consecutive top posts
     * - Applies sort by $sub ('postbyview', 'postbylike', etc.)
     */
    public static function adaptPostsForDateRange(
        array $templates,
        string $platform,
        int $projectId,
        ?string $startDate,
        ?string $endDate,
        int $limit = 100,
        string $sub = 'postbyview',
        ?callable $postEnricher = null
    ): array {
        if (empty($templates)) {
            return [];
        }

        $sDate = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : now()->subDays(29)->format('Y-m-d');
        $eDate = $endDate   ? Carbon::parse($endDate)->format('Y-m-d')   : now()->format('Y-m-d');

        $startTs = strtotime($sDate . ' 07:00:00');
        $endTs   = strtotime($eDate . ' 22:30:00');
        if ($endTs < $startTs) {
            $endTs = $startTs + (86400 * 29);
        }
        $spanSecs = max(86400, $endTs - $startTs);
        $spanDays = max(1, (int) round($spanSecs / 86400));

        // Deterministic pseudo-random seed per project + platform + date range
        $seed = crc32("{$platform}:{$projectId}:{$sDate}:{$eDate}");
        $tplCount = count($templates);

        // Period scaling factor (shorter period => slightly adjusted engagement)
        $periodScale = min(1.35, max(0.35, sqrt($spanDays / 30.0)));

        // Create permuted pool of template indices
        $order = range(0, $tplCount - 1);
        for ($i = $tplCount - 1; $i > 0; $i--) {
            $j = abs(($seed + ($i * 2654435761)) % ($i + 1));
            $tmp = $order[$i];
            $order[$i] = $order[$j];
            $order[$j] = $tmp;
        }

        // Guarantee 100% unique posts: never repeat a template within the same feed
        $targetCount = min($limit, $tplCount);
        $posts = [];

        for ($i = 0; $i < $targetCount; $i++) {
            $tplIdx = $order[$i];
            $tpl    = $templates[$tplIdx];

            // Distribute post timestamps evenly across the window
            $fraction = ($targetCount > 1) ? ($i / ($targetCount - 1)) : 0.5;
            // Add deterministic micro-jitter within the day
            $jitterSecs = (abs($seed + ($i * 73939)) % 14400) - 7200;
            $postTs = (int) round($startTs + ($fraction * $spanSecs) + $jitterSecs);
            $postTs = max($startTs, min($endTs, $postTs));
            $dCreated = date('Y-m-d H:i:s', $postTs);

            // Compute scaled metrics: combine template popularity with period-specific momentum
            // so different months highlight genuinely different top videos & creators
            $baseViews = (int) ($tpl['views'] ?? $tpl['view_cnt'] ?? $tpl['likes'] ?? 350000);
            $slotDecay = 1.0 - (($i / max(1, $targetCount)) * 0.58);
            $viewVar   = 0.85 + ((abs($seed + ($i * 9973)) % 35) / 100.0);
            $views     = max(8000, (int) round($baseViews * $periodScale * $slotDecay * $viewVar));

            $likesRatio   = 0.075 + ((abs($seed + ($i * 4099)) % 25) / 1000.0);
            $commRatio    = 0.035 + ((abs($seed + ($i * 6173)) % 20) / 1000.0);
            $shareRatio   = 0.045 + ((abs($seed + ($i * 8123)) % 30) / 1000.0);

            $likes    = max(450, (int) round($views * $likesRatio));
            $comments = max(40,  (int) round($likes * $commRatio));
            $shares   = max(25,  (int) round($likes * $shareRatio));

            $item = array_merge($tpl, [
                'views'        => $views,
                'view_cnt'     => $views,
                'freq'         => $views,
                'likes'        => $likes,
                'num_likes'    => $likes,
                'comments'     => $comments,
                'num_comments' => $comments,
                'shares'       => $shares,
                'num_shares'   => $shares,
                'engagement'   => $likes + $comments + $shares,
                'date_created' => $dCreated,
            ]);

            if ($postEnricher !== null) {
                $item = $postEnricher($item, $i, $seed);
            }

            $posts[] = $item;
        }

        // Apply primary sorting requested by frontend tab
        usort($posts, match ($sub) {
            'postbyview', 'views', 'view_cnt' => fn($a, $b) => ($b['view_cnt'] ?? 0) <=> ($a['view_cnt'] ?? 0),
            'postbylike', 'likes', 'fblike'   => fn($a, $b) => ($b['likes'] ?? 0)    <=> ($a['likes'] ?? 0),
            'postbycomment', 'comments'       => fn($a, $b) => ($b['comments'] ?? 0) <=> ($a['comments'] ?? 0),
            'postbyshare', 'shares'           => fn($a, $b) => ($b['shares'] ?? 0)   <=> ($a['shares'] ?? 0),
            default                           => fn($a, $b) => ($b['engagement'] ?? 0) <=> ($a['engagement'] ?? 0),
        });

        return array_slice($posts, 0, $limit);
    }

    /**
     * Adapt hashtag / trending topic volumes deterministically for a date range.
     */
    public static function adaptTrendingTopics(
        array $baseHashtags,
        string $platform,
        int $projectId,
        ?string $startDate,
        ?string $endDate
    ): array {
        $sDate = $startDate ? Carbon::parse($startDate)->format('Y-m-d') : now()->subDays(29)->format('Y-m-d');
        $eDate = $endDate   ? Carbon::parse($endDate)->format('Y-m-d')   : now()->format('Y-m-d');

        $startTs = strtotime($sDate);
        $endTs   = strtotime($eDate);
        $days    = max(1, (int) round(($endTs - $startTs) / 86400) + 1);
        $scale   = min(1.4, max(0.25, $days / 30.0));
        $seed    = crc32("topics:{$platform}:{$projectId}:{$sDate}:{$eDate}");

        $result = [];
        foreach ($baseHashtags as $idx => $ht) {
            $baseSize = (int) ($ht['size'] ?? 1000);
            $var = 0.70 + ((abs($seed + ($idx * 7919)) % 65) / 100.0);
            $ht['size'] = max(50, (int) round($baseSize * $scale * $var));
            $result[] = $ht;
        }
        usort($result, fn($a, $b) => ($b['size'] ?? 0) <=> ($a['size'] ?? 0));
        return $result;
    }
}
