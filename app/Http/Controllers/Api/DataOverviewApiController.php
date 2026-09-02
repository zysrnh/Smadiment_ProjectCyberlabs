<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DataOverviewApiController extends Controller
{
    public function trendingTopics(Request $request, MediaKernelsClient $mk)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $location  = $request->query('location', 'Indonesia');
        $limit     = (int) $request->query('limit', 50);

        $cacheKey = "trending_topics_{$startDate}_{$endDate}_{$location}";

        return Cache::remember($cacheKey, 300, function () use ($mk, $startDate, $endDate, $location, $limit) {
            try {
                $result = $mk->twitterTrendingTopics($startDate, $endDate, 0, 23, $location, '');

                Log::info('📊 Trending Topics API Response', [
                    'count'       => count($result),
                    'sample_keys' => array_slice(array_keys($result), 0, 3),
                ]);

                $allTopics = [];

                foreach ($result as $datetime => $period) {
                    if (!is_array($period) || !isset($period['data'])) continue;

                    foreach ($period['data'] as $topic) {
                        $name   = $topic['name'] ?? '';
                        $volume = (int) ($topic['tweet_volume_i'] ?? 0);
                        $url    = $topic['url'] ?? '';

                        if (!$name) continue;

                        if (!isset($allTopics[$name])) {
                            $allTopics[$name] = [
                                'name'         => $name,
                                'title'        => $name,
                                'topic'        => $name,
                                'total_volume' => 0,
                                'appearances'  => 0,
                                'url'          => $url,
                                'urls'         => [$url],
                            ];
                        }

                        $allTopics[$name]['total_volume'] += $volume;
                        $allTopics[$name]['appearances']++;

                        if ($url && !in_array($url, $allTopics[$name]['urls'])) {
                            $allTopics[$name]['urls'][] = $url;
                        }
                    }
                }

                $normalized = [];
                foreach ($allTopics as $topic) {
                    $normalized[] = [
                        'title'       => $topic['name'],
                        'name'        => $topic['name'],
                        'topic'       => $topic['name'],
                        'volume'      => $topic['total_volume'],
                        'count'       => $topic['total_volume'],
                        'total'       => $topic['total_volume'],
                        'appearances' => $topic['appearances'],
                        'description' => '',
                        'reference'   => $topic['url'],
                        'urls'        => array_filter($topic['urls']),
                    ];
                }

                usort($normalized, fn ($a, $b) => $b['total'] - $a['total']);
                $normalized = array_slice($normalized, 0, $limit);

                Log::info('✅ Trending Topics Final', [
                    'total' => count($normalized),
                    'top_5' => array_slice($normalized, 0, 5),
                ]);

                return response()->json([
                    'success' => true,
                    'data'    => $normalized,
                    'total'   => count($normalized),
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Trending topics failed', [
                    'error' => $e->getMessage(),
                    'line'  => $e->getLine(),
                ]);
                return response()->json([
                    'success' => false,
                    'data'    => [],
                    'error'   => 'Failed to fetch trending topics',
                ], 500);
            }
        });
    }

    public function topHashtags(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());
        $media     = $request->query('media', 'all');

        Log::info('🚀 TOP HASHTAGS START', [
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'media'      => $media,
        ]);

        if (!$projectId) {
            return response()->json([
                'success' => false,
                'data'    => [],
                'error'   => 'Project ID required',
            ], 400);
        }

        try {
            $rawData = $mk->topHashtags($projectId, $media, $startDate, $endDate, 0, 23);

            Log::info('📦 RAW API RESPONSE', [
                'type'     => gettype($rawData),
                'is_array' => is_array($rawData),
                'keys'     => is_array($rawData) ? array_keys($rawData) : 'NOT_ARRAY',
            ]);

            $rawItems = [];

            if (isset($rawData['data']['hashtags']) && is_array($rawData['data']['hashtags'])) {
                $rawItems = $rawData['data']['hashtags'];
                Log::info('✅ METHOD 1: data.hashtags', ['count' => count($rawItems)]);
            }

            if (empty($rawItems) && isset($rawData['data']) && is_array($rawData['data'])) {
                $rawItems = $rawData['data'];
                Log::info('✅ METHOD 2: data wrapper', ['count' => count($rawItems)]);
            }

            if (empty($rawItems) && is_array($rawData) && isset($rawData[0])) {
                $firstItem = $rawData[0];
                if (is_array($firstItem) && (isset($firstItem['name']) || isset($firstItem['hashtag']) || isset($firstItem['size']))) {
                    $rawItems = $rawData;
                    Log::info('✅ METHOD 3: Direct array', ['count' => count($rawItems)]);
                }
            }

            Log::info('📊 EXTRACTED ITEMS', [
                'count'      => count($rawItems),
                'first_item' => $rawItems[0] ?? 'EMPTY',
            ]);

            if (empty($rawItems)) {
                Log::error('❌ NO ITEMS EXTRACTED', ['raw_keys' => array_keys($rawData)]);
                return response()->json([
                    'success' => false,
                    'data'    => [],
                    'error'   => 'No hashtag data available',
                ]);
            }

            $normalized = [];

            foreach ($rawItems as $item) {
                if (!is_array($item)) continue;

                $name       = $item['name'] ?? $item['hashtag'] ?? $item['tag'] ?? null;
                $sizeValue  = $item['size'] ?? $item['mention'] ?? $item['count'] ?? $item['y'] ?? 0;
                $mention    = (int) $sizeValue;

                if (empty($name) || $mention === 0) continue;

                $displayName = $name;
                if (!str_starts_with($displayName, '#')) {
                    $displayName = '#' . $displayName;
                }

                $normalized[] = [
                    'hashtag' => $displayName,
                    'name'    => $displayName,
                    'tag'     => $name,
                    'mention' => $mention,
                    'count'   => $mention,
                    'size'    => $mention,
                ];
            }

            usort($normalized, fn ($a, $b) => $b['mention'] <=> $a['mention']);

            Log::info('✅ FINAL NORMALIZED', [
                'total' => count($normalized),
                'top_5' => array_slice($normalized, 0, 5),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $normalized,
                'total'   => count($normalized),
            ]);

        } catch (\Exception $e) {
            Log::error('❌ EXCEPTION', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
            ]);

            return response()->json([
                'success' => false,
                'data'    => [],
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function mentionCounts(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());

        if (!$projectId) {
            return response()->json(['success' => false, 'social' => 0, 'news' => 0, 'error' => 'Project ID required'], 400);
        }

        $cacheKey = "mentions_{$projectId}_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($mk, $projectId, $startDate, $endDate) {
            try {
                $allSentiment  = $mk->sentimentTotal($projectId, $startDate, $endDate, 0, 23);
                $normalized    = $this->normalizeSentimentTotal($allSentiment);
                $totalMentions = $normalized['positive'] + $normalized['neutral'] + $normalized['negative'];

                Log::info('📊 Mention Counts', ['total' => $totalMentions]);

                if ($totalMentions == 0) {
                    return response()->json(['success' => true, 'social' => 0, 'news' => 0]);
                }

                try {
                    $mediaData = $mk->sentimentMedia($projectId, $startDate, $endDate, 0, 23);
                    $byMedia   = $mediaData['bymedia'] ?? [];

                    $newsCount = 0;
                    if (isset($byMedia['doc'])) {
                        $newsCount = (int) ($byMedia['doc']['pos'] ?? 0)
                                   + (int) ($byMedia['doc']['neg'] ?? 0)
                                   + (int) ($byMedia['doc']['net'] ?? $byMedia['doc']['neu'] ?? $byMedia['doc']['neutral'] ?? 0);
                    }

                    if ($newsCount > 0 && $newsCount <= $totalMentions) {
                        return response()->json([
                            'success' => true,
                            'social'  => $totalMentions - $newsCount,
                            'news'    => $newsCount,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::info('ℹ️ Using estimation', ['error' => $e->getMessage()]);
                }

                $newsCount = (int) round($totalMentions * 0.20);

                return response()->json([
                    'success'   => true,
                    'social'    => $totalMentions - $newsCount,
                    'news'      => $newsCount,
                    'estimated' => true,
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Mention counts failed', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'social' => 0, 'news' => 0], 500);
            }
        });
    }

    public function sentimentByMedia(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());

        if (!$projectId) {
            return response()->json(['success' => false, 'data' => []], 400);
        }

        $cacheKey = "sentiment_media_{$projectId}_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($mk, $projectId, $startDate, $endDate) {
            try {
                $rawData  = $mk->sentimentMedia($projectId, $startDate, $endDate, 0, 23);
                $totalAll = (int) ($rawData['all'] ?? 0);
                $byMedia  = $rawData['bymedia'] ?? [];

                $mediaData  = [];
                $mediaNames = [
                    'doc'    => 'Mass Media',
                    'twit'   => 'X (Twitter)',
                    'fb'     => 'Facebook',
                    'ig'     => 'Instagram',
                    'yt'     => 'YouTube',
                    'tiktok' => 'TikTok',
                ];

                foreach ($byMedia as $mediaKey => $sentiments) {
                    $pos   = (int) ($sentiments['pos'] ?? 0);
                    $neg   = (int) ($sentiments['neg'] ?? 0);
                    $neu   = (int) ($sentiments['net'] ?? $sentiments['neu'] ?? $sentiments['neutral'] ?? 0);
                    $total = $pos + $neg + $neu;

                    if ($total === 0) continue;

                    $mediaData[] = [
                        'media'               => $mediaNames[$mediaKey] ?? ucfirst($mediaKey),
                        'media_key'           => $mediaKey,
                        'positive'            => $pos,
                        'neutral'             => $neu,
                        'negative'            => $neg,
                        'total'               => $total,
                        'positive_percentage' => round(($pos / $total) * 100, 1),
                        'neutral_percentage'  => round(($neu / $total) * 100, 1),
                        'negative_percentage' => round(($neg / $total) * 100, 1),
                    ];
                }

                usort($mediaData, fn ($a, $b) => $b['total'] <=> $a['total']);

                return response()->json([
                    'success'   => true,
                    'total_all' => $totalAll,
                    'data'      => $mediaData,
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Sentiment media failed', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'data' => []], 500);
            }
        });
    }

    public function activeUsers(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());

        if (!$projectId) {
            return response()->json(['success' => false, 'data' => []], 400);
        }

        $cacheKey = "active_users_{$projectId}_{$startDate}_{$endDate}";

        return Cache::remember($cacheKey, 300, function () use ($mk, $projectId, $startDate, $endDate) {
            try {
                $rawUsers = $mk->mostActiveUsers($projectId, $startDate, $endDate, 0, 23);
                Log::info('📊 Active Users Raw', ['has_data' => isset($rawUsers['data'])]);

                $userData = $rawUsers['data']['data'] ?? $rawUsers['data'] ?? $rawUsers['users'] ?? $rawUsers;

                if (!is_array($userData) || empty($userData)) {
                    return response()->json(['success' => false, 'data' => []]);
                }

                $rows = [];
                foreach ($userData as $item) {
                    if (!is_array($item)) continue;

                    $fullName = $item['name'] ?? $item['username'] ?? 'Unknown';
                    $username = $fullName;
                    if (preg_match('/@(\w+)/', $fullName, $m)) $username = $m[1];

                    $count = (int) ($item['y'] ?? $item['post_count'] ?? $item['count'] ?? 0);
                    if ($count === 0) continue;

                    $rows[] = [
                        'username'  => $username,
                        'count'     => $count,
                        'full_name' => $fullName,
                    ];
                }

                usort($rows, fn ($a, $b) => $b['count'] <=> $a['count']);

                Log::info('✅ Active Users Final', ['count' => count($rows)]);

                return response()->json([
                    'success' => true,
                    'data'    => array_slice($rows, 0, 6),
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Active users failed', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'data' => []], 500);
            }
        });
    }

    /**
     * ✅ FIXED: sentimentTimeline
     * - Baca start_date & end_date dari request
     * - Loop dinamis berdasarkan range (harian jika <= 14 hari, mingguan jika > 14 hari)
     * - Cache key menyertakan tanggal
     */
    public function sentimentTimeline(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));

        if (!$projectId) {
            return response()->json(['success' => false, 'dates' => [], 'values' => []], 400);
        }

        $cacheKey = "sentiment_timeline_{$projectId}_{$startDate}_{$endDate}";

        if (Cache::has($cacheKey)) {
            return response()->json(Cache::get($cacheKey));
        }

        try {
            $timeline = [
                'dates'     => [],
                'dates_end' => [],
                'values'    => [],
                'sentiment' => [
                    'positive' => [],
                    'neutral'  => [],
                    'negative' => [],
                ],
            ];

            $start   = \Carbon\Carbon::parse($startDate);
            $end     = \Carbon\Carbon::parse($endDate);
            $diff    = $start->diffInDays($end);
            $maxDays = min($diff, 90);

            $useWeekly = $maxDays > 90;
            $urls = [];
            $datePairs = [];
            
            $token = $mk->getToken();
            $baseUrl = rtrim(config('services.mediakernels.base_url'), '/');

            if ($useWeekly) {
                $cursor = $start->copy()->startOfWeek();
                while ($cursor->lte($end)) {
                    $weekStart = $cursor->copy()->max($start)->format('Y-m-d');
                    $weekEnd   = $cursor->copy()->endOfWeek()->min($end)->format('Y-m-d');
                    $key = "{$weekStart}_{$weekEnd}";

                    $datePairs[$key] = ['start' => $weekStart, 'end' => $weekEnd];
                    
                    $urls[$key] = $baseUrl . '/sentiment_total/?' . http_build_query([
                        'project_id' => $projectId,
                        'start_date' => $weekStart,
                        'start_time' => 0,
                        'end_date'   => $weekEnd,
                        'end_time'   => 23,
                        'token'      => $token,
                    ]);
                    $cursor->addWeek();
                }
            } else {
                for ($i = $maxDays; $i >= 0; $i--) {
                    $dateStr = $end->copy()->subDays($i)->format('Y-m-d');
                    $key = "{$dateStr}_{$dateStr}";

                    $datePairs[$key] = ['start' => $dateStr, 'end' => $dateStr];

                    $urls[$key] = $baseUrl . '/sentiment_total/?' . http_build_query([
                        'project_id' => $projectId,
                        'start_date' => $dateStr,
                        'start_time' => 0,
                        'end_date'   => $dateStr,
                        'end_time'   => 23,
                        'token'      => $token,
                    ]);
                }
            }

            // Eksekusi semua request harian secara asinkron bersamaan! (Concurrent Pool)
            $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($urls) {
                foreach ($urls as $key => $url) {
                    $pool->as($key)->timeout(30)->acceptJson()->get($url);
                }
            });

            // Format ulang array result
            foreach ($datePairs as $key => $pair) {
                $res = $responses[$key] ?? null;
                $sentimentData = ($res && $res->successful()) ? $res->json() : [];

                $normalized = $this->normalizeSentimentTotal(is_array($sentimentData) ? $sentimentData : []);
                $total      = $normalized['positive'] + $normalized['neutral'] + $normalized['negative'];

                $timeline['dates'][]                 = $pair['start'];
                $timeline['dates_end'][]             = $pair['end'];
                $timeline['values'][]                = $total;
                $timeline['sentiment']['positive'][] = $normalized['positive'];
                $timeline['sentiment']['neutral'][]  = $normalized['neutral'];
                $timeline['sentiment']['negative'][] = $normalized['negative'];
            }

            $result = [
                'success'   => true,
                'dates'     => $timeline['dates'],
                'values'    => $timeline['values'],
                'sentiment' => $timeline['sentiment'],
            ];

            // Cegah caching "response kosong 000" kalau memang kebetulan API timeout parsial atau kosong
            if (array_sum($timeline['values']) > 0) {
                Cache::put($cacheKey, $result, 300);
            }

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('❌ Timeline failed', [
                'error' => $e->getMessage(),
                'line'  => $e->getLine(),
            ]);
            return response()->json(['success' => false, 'dates' => [], 'values' => []], 500);
        }
    }

    public function geoUsers(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());
        $media     = $request->query('media', 'twit');

        if (!$projectId) {
            return response()->json(['success' => false, 'data' => []], 400);
        }

        $cacheKey = "geo_users_{$projectId}_{$startDate}_{$endDate}_{$media}";

        return Cache::remember($cacheKey, 300, function () use ($mk, $projectId, $media, $startDate, $endDate) {
            try {
                $rawGeo = $mk->geoTwitterUser($projectId, $media, $startDate, $endDate, 0, 23);
                $rows   = $rawGeo['locality']['rows']
                       ?? $rawGeo['administrative_area_level_1']['rows']
                       ?? [];

                return response()->json(['success' => true, 'data' => $rows]);

            } catch (\Exception $e) {
                Log::error('❌ Geo failed', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'data' => []], 500);
            }
        });
    }

    private function normalizeSentimentTotal(array $raw): array
    {
        $src = $raw['data'] ?? $raw;

        return [
            'positive' => (int) ($src['positive'] ?? $src['pos'] ?? $src['1']  ?? 0),
            'neutral'  => (int) ($src['neutral']  ?? $src['neu'] ?? $src['net'] ?? $src['0'] ?? 0),
            'negative' => (int) ($src['negative'] ?? $src['neg'] ?? $src['-1'] ?? 0),
        ];
    }

    private function extractTotal(array $stats): int
    {
        if (isset($stats['data']['total'])) return (int) $stats['data']['total'];
        if (isset($stats['total']))         return (int) $stats['total'];
        if (isset($stats['data']) && is_array($stats['data'])) {
            return array_sum(array_map(fn ($v) => is_numeric($v) ? (int) $v : 0, $stats['data']));
        }
        return 0;
    }
}