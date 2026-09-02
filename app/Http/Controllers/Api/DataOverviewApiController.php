<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProjectDailySentiment;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DataOverviewApiController extends Controller
{
    public function trendingTopics(Request $request, MediaKernelsClient $mk)
    {
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $location  = $request->query('location', 'Indonesia');
        $limit     = (int) $request->query('limit', 50);

        $cacheKey = "trending_topics_{$startDate}_{$endDate}_{$location}";

        return Cache::remember($cacheKey, 1800, function () use ($mk, $startDate, $endDate, $location, $limit) {
            try {
                $result = $mk->twitterTrendingTopics($startDate, $endDate, 0, 23, $location, '');

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

                usort($normalized, fn ($a, $b) => $b['total'] <=> $a['total']);
                $normalized = array_slice($normalized, 0, $limit);

                return response()->json([
                    'success' => true,
                    'data'    => $normalized,
                    'total'   => count($normalized),
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Trending topics failed', [
                    'error' => $e->getMessage(),
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

        if (!$projectId) {
            return response()->json([
                'success' => false,
                'data'    => [],
                'error'   => 'Project ID required',
            ], 400);
        }

        $cacheKey = "top_hashtags_{$projectId}_{$startDate}_{$endDate}_{$media}";

        return Cache::remember($cacheKey, 1800, function () use ($mk, $projectId, $media, $startDate, $endDate) {
            try {
                $rawData = $mk->topHashtags($projectId, $media, $startDate, $endDate, 0, 23);

                $rawItems = [];

                if (isset($rawData['data']['hashtags']) && is_array($rawData['data']['hashtags'])) {
                    $rawItems = $rawData['data']['hashtags'];
                } elseif (isset($rawData['data']) && is_array($rawData['data'])) {
                    $rawItems = $rawData['data'];
                } elseif (is_array($rawData) && isset($rawData[0])) {
                    $rawItems = $rawData;
                }

                if (empty($rawItems)) {
                    return response()->json([
                        'success' => false,
                        'data'    => [],
                        'error'   => 'No hashtag data available',
                    ]);
                }

                $normalized = [];

                foreach ($rawItems as $item) {
                    if (!is_array($item)) continue;

                    $name      = $item['name'] ?? $item['hashtag'] ?? $item['tag'] ?? null;
                    $sizeValue = $item['size'] ?? $item['mention'] ?? $item['count'] ?? $item['y'] ?? 0;
                    $mention   = (int) $sizeValue;

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

                return response()->json([
                    'success' => true,
                    'data'    => $normalized,
                    'total'   => count($normalized),
                ]);

            } catch (\Exception $e) {
                Log::error('❌ Top Hashtags Exception', [
                    'message' => $e->getMessage(),
                ]);

                return response()->json([
                    'success' => false,
                    'data'    => [],
                    'error'   => $e->getMessage(),
                ], 500);
            }
        });
    }

    /**
     * ✅ OPTIMIZED: mentionCounts (Membaca langsung dari DB lokal ProjectDailySentiment)
     */
    public function mentionCounts(Request $request, MediaKernelsClient $mk)
    {
        $projectId = (int) $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date', now()->toDateString());

        if (!$projectId) {
            return response()->json(['success' => false, 'social' => 0, 'news' => 0, 'error' => 'Project ID required'], 400);
        }

        try {
            // 1. Ambil agregat langsung dari database lokal (< 2ms)
            $stats = ProjectDailySentiment::where('project_id', $projectId)
                ->whereBetween('date', [$startDate, $endDate])
                ->selectRaw('SUM(positive) as pos, SUM(neutral) as neu, SUM(negative) as neg, SUM(total) as tot')
                ->first();

            $totalMentions = (int) ($stats->tot ?? 0);

            // 2. Jika DB belum ada datanya, fallback ke API
            if ($totalMentions === 0) {
                $allSentiment  = $mk->sentimentTotal($projectId, $startDate, $endDate, 0, 23);
                $normalized    = $this->normalizeSentimentTotal($allSentiment);
                $totalMentions = $normalized['positive'] + $normalized['neutral'] + $normalized['negative'];
            }

            if ($totalMentions === 0) {
                return response()->json(['success' => true, 'social' => 0, 'news' => 0]);
            }

            // 3. Ambil rasio online news vs social
            $cacheKeyMedia = "sentiment_media_{$projectId}_{$startDate}_{$endDate}";
            $byMedia = Cache::get($cacheKeyMedia);
            $newsCount = 0;

            if ($byMedia && isset($byMedia['data'])) {
                foreach ($byMedia['data'] as $item) {
                    if (($item['media_key'] ?? '') === 'doc') {
                        $newsCount = (int) ($item['total'] ?? 0);
                        break;
                    }
                }
            }

            if ($newsCount === 0 || $newsCount > $totalMentions) {
                $newsCount = (int) round($totalMentions * 0.20);
            }

            return response()->json([
                'success' => true,
                'social'  => max(0, $totalMentions - $newsCount),
                'news'    => $newsCount,
                'total'   => $totalMentions,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Mention counts failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'social' => 0, 'news' => 0], 500);
        }
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

        return Cache::remember($cacheKey, 1800, function () use ($mk, $projectId, $startDate, $endDate) {
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

        return Cache::remember($cacheKey, 1800, function () use ($mk, $projectId, $startDate, $endDate) {
            try {
                $rawUsers = $mk->mostActiveUsers($projectId, $startDate, $endDate, 0, 23);

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
     * ✅ OPTIMIZED: sentimentTimeline (Membaca langsung dari DB lokal ProjectDailySentiment)
     */
    public function sentimentTimeline(Request $request, MediaKernelsClient $mk)
    {
        $projectId = (int) $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));

        if (!$projectId) {
            return response()->json(['success' => false, 'dates' => [], 'values' => []], 400);
        }

        try {
            $start = new \DateTime($startDate);
            $end   = new \DateTime($endDate);
            
            // 1. Ambil seluruh data dari database lokal
            $existing = ProjectDailySentiment::where('project_id', $projectId)
                ->whereBetween('date', [$startDate, $endDate])
                ->orderBy('date', 'asc')
                ->get()
                ->keyBy(fn($item) => $item->date->format('Y-m-d'));

            // 2. Generate tanggal lengkap
            $allDates = [];
            $current  = clone $start;
            while ($current <= $end) {
                $allDates[] = $current->format('Y-m-d');
                $current->modify('+1 day');
            }

            $missingDates = [];
            foreach ($allDates as $dStr) {
                if (!$existing->has($dStr)) {
                    $missingDates[] = $dStr;
                }
            }

            // 3. Jika ada tanggal kosong, auto-sync
            if (!empty($missingDates)) {
                try {
                    $token   = $mk->getToken();
                    $baseUrl = rtrim(config('services.mediakernels.base_url'), '/');
                    $urls    = [];

                    foreach ($missingDates as $dStr) {
                        $urls[$dStr] = $baseUrl . '/sentiment_total/?' . http_build_query([
                            'project_id' => $projectId,
                            'start_date' => $dStr,
                            'start_time' => 0,
                            'end_date'   => $dStr,
                            'end_time'   => 23,
                            'token'      => $token,
                        ]);
                    }

                    $responses = Http::pool(function ($pool) use ($urls) {
                        foreach ($urls as $dStr => $url) {
                            $pool->as($dStr)->timeout(30)->acceptJson()->get($url);
                        }
                    });

                    $upsertData = [];
                    foreach ($missingDates as $dStr) {
                        $res = $responses[$dStr] ?? null;
                        $pos = 0; $neu = 0; $neg = 0;

                        if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                            $norm = $this->normalizeSentimentTotal($res->json() ?? []);
                            $pos  = $norm['positive'];
                            $neu  = $norm['neutral'];
                            $neg  = $norm['negative'];
                        }

                        $upsertData[] = [
                            'project_id' => $projectId,
                            'date'       => $dStr,
                            'positive'   => $pos,
                            'neutral'    => $neu,
                            'negative'   => $neg,
                            'total'      => $pos + $neu + $neg,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    if (!empty($upsertData)) {
                        ProjectDailySentiment::upsert(
                            $upsertData,
                            ['project_id', 'date'],
                            ['positive', 'neutral', 'negative', 'total', 'updated_at']
                        );
                    }

                    $existing = ProjectDailySentiment::where('project_id', $projectId)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date', 'asc')
                        ->get()
                        ->keyBy(fn($item) => $item->date->format('Y-m-d'));

                } catch (\Throwable $e) {
                    Log::warning("DataOverview: timeline auto-sync error: " . $e->getMessage());
                }
            }

            // 4. Susun respon timeline
            $dates     = [];
            $datesEnd  = [];
            $values    = [];
            $posArr    = [];
            $neuArr    = [];
            $negArr    = [];

            foreach ($allDates as $dStr) {
                $row   = $existing->get($dStr);
                $pos   = $row ? $row->positive : 0;
                $neu   = $row ? $row->neutral  : 0;
                $neg   = $row ? $row->negative : 0;
                $total = $pos + $neu + $neg;

                $dates[]    = $dStr;
                $datesEnd[] = $dStr;
                $values[]   = $total;
                $posArr[]   = $pos;
                $neuArr[]   = $neu;
                $negArr[]   = $neg;
            }

            return response()->json([
                'success'   => true,
                'dates'     => $dates,
                'dates_end' => $datesEnd,
                'values'    => $values,
                'sentiment' => [
                    'positive' => $posArr,
                    'neutral'  => $neuArr,
                    'negative' => $negArr,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Timeline failed', [
                'error' => $e->getMessage(),
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

        return Cache::remember($cacheKey, 1800, function () use ($mk, $projectId, $media, $startDate, $endDate) {
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
}