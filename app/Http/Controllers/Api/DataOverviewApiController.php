<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class DataOverviewApiController extends Controller
{
    /**
     * 🔥 API: Trending Topics (with cache)
     */
    public function trendingTopics(Request $request, MediaKernelsClient $mk)
    {
        $limit = (int) $request->query('limit', 10);
        $level = $request->query('level', 'internasional');
        
        $cacheKey = "trending_topics_{$level}_{$limit}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $level, $limit) {
            try {
                $rawData = $mk->recentTopics($level, $limit);
                $topics = $rawData['data'] ?? $rawData;
                
                return response()->json([
                    'success' => true,
                    'data' => array_values($topics)
                ]);
            } catch (\Exception $e) {
                Log::error('API: trending topics failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'error' => 'Failed to fetch trending topics'
                ], 500);
            }
        });
    }

    /**
     * 🔥 API: Top Hashtags (with cache)
     */
    public function topHashtags(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDay()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $media = $request->query('media', 'twit');
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Project ID required'
            ], 400);
        }
        
        $cacheKey = "hashtags_{$projectId}_{$startDate}_{$endDate}_{$media}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $projectId, $media, $startDate, $endDate) {
            try {
                $rawData = $mk->topHashtags($projectId, $media, $startDate, $endDate, 0, 23);
                
                $rawItems = $rawData['data'] ?? (is_array($rawData) ? $rawData : []);
                $normalized = [];
                
                foreach ($rawItems as $item) {
                    if (!is_array($item)) continue;
                    $normalized[] = [
                        'hashtag' => $item['name'] ?? $item['hashtag'] ?? $item['tag'] ?? 'unknown',
                        'mention' => (int)($item['size'] ?? $item['mention'] ?? $item['count'] ?? 0),
                    ];
                }
                
                usort($normalized, fn($a, $b) => $b['mention'] <=> $a['mention']);
                
                return response()->json([
                    'success' => true,
                    'data' => $normalized
                ]);
            } catch (\Exception $e) {
                Log::error('API: top hashtags failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'error' => 'Failed to fetch hashtags'
                ], 500);
            }
        });
    }

    /**
     * 🔥 API: Mention Counts (Social Media + News)
     */
    public function mentionCounts(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDay()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'social' => 0,
                'news' => 0,
                'error' => 'Project ID required'
            ], 400);
        }
        
        $cacheKey = "mentions_{$projectId}_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $projectId, $startDate, $endDate) {
            try {
                $allSentiment = $mk->sentimentTotal($projectId, $startDate, $endDate, 0, 23);
                $normalized = $this->normalizeSentimentTotal($allSentiment);
                $totalMentions = $normalized['positive'] + $normalized['neutral'] + $normalized['negative'];
                
                if ($totalMentions == 0) {
                    return response()->json([
                        'success' => true,
                        'social' => 0,
                        'news' => 0
                    ]);
                }
                
                try {
                    $newsStats = $mk->projectStats($projectId, 'onlinenews', $startDate, $endDate, 0, 23, 'volumetotal');
                    $newsCount = $this->extractTotal($newsStats);
                    
                    if ($newsCount > 0 && $newsCount <= $totalMentions) {
                        return response()->json([
                            'success' => true,
                            'social' => $totalMentions - $newsCount,
                            'news' => $newsCount
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::info('Mention counts: using estimation', ['error' => $e->getMessage()]);
                }
                
                $newsCount = (int)round($totalMentions * 0.20);
                
                return response()->json([
                    'success' => true,
                    'social' => $totalMentions - $newsCount,
                    'news' => $newsCount,
                    'estimated' => true
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: mention counts failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'social' => 0,
                    'news' => 0,
                    'error' => 'Failed to fetch mention counts'
                ], 500);
            }
        });
    }

    /**
     * 🔥 NEW API: Sentiment by Media
     */
    public function sentimentByMedia(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDay()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Project ID required'
            ], 400);
        }
        
        $cacheKey = "sentiment_media_{$projectId}_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $projectId, $startDate, $endDate) {
            try {
                $rawData = $mk->sentimentMedia($projectId, $startDate, $endDate, 0, 23);
                
                $totalAll = (int)($rawData['all'] ?? 0);
                $byMedia = $rawData['bymedia'] ?? [];
                
                // Transform data ke format yang lebih user-friendly
                $mediaData = [];
                
                // Map media types to friendly names
                $mediaNames = [
                    'doc' => 'Online News',
                    'twit' => 'X (Twitter)',
                    'fb' => 'Facebook',
                    'ig' => 'Instagram',
                    'yt' => 'YouTube',
                    'tiktok' => 'TikTok'
                ];
                
                foreach ($byMedia as $mediaKey => $sentiments) {
                    $mediaName = $mediaNames[$mediaKey] ?? ucfirst($mediaKey);
                    
                    $pos = (int)($sentiments['pos'] ?? 0);
                    $neg = (int)($sentiments['neg'] ?? 0);
                    $net = (int)($sentiments['net'] ?? ($pos - $neg));
                    
                    $total = $pos + $neg;
                    
                    $mediaData[] = [
                        'media' => $mediaName,
                        'media_key' => $mediaKey,
                        'positive' => $pos,
                        'negative' => $neg,
                        'net_sentiment' => $net,
                        'total' => $total,
                        'positive_percentage' => $total > 0 ? round(($pos / $total) * 100, 1) : 0,
                        'negative_percentage' => $total > 0 ? round(($neg / $total) * 100, 1) : 0,
                    ];
                }
                
                // Sort by total mentions (descending)
                usort($mediaData, fn($a, $b) => $b['total'] <=> $a['total']);
                
                return response()->json([
                    'success' => true,
                    'total_all' => $totalAll,
                    'data' => $mediaData
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: sentiment by media failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'total_all' => 0,
                    'data' => [],
                    'error' => 'Failed to fetch sentiment by media'
                ], 500);
            }
        });
    }

    /**
     * 🔥 API: Active Users (with cache)
     */
    public function activeUsers(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDay()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Project ID required'
            ], 400);
        }
        
        $cacheKey = "active_users_{$projectId}_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $projectId, $startDate, $endDate) {
            try {
                $rawUsers = $mk->mostActiveUsers($projectId, $startDate, $endDate, 0, 23);
                
                $userData = $rawUsers['data']['data'] ?? $rawUsers['data'] ?? $rawUsers;
                $rows = [];
                
                foreach ($userData as $item) {
                    if (!is_array($item)) continue;

                    $fullName = $item['name'] ?? 'Unknown User';
                    $username = $fullName;
                    
                    if (preg_match('/@(\w+)/', $fullName, $matches)) {
                        $username = $matches[1];
                    }

                    $rows[] = [
                        'username' => $username,
                        'count' => (int)($item['y'] ?? $item['post_count'] ?? $item['posts'] ?? $item['count'] ?? 0),
                    ];
                }
                
                usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
                
                return response()->json([
                    'success' => true,
                    'data' => array_slice($rows, 0, 6)
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: active users failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'error' => 'Failed to fetch active users'
                ], 500);
            }
        });
    }

    /**
     * 🔥 API: Sentiment Timeline (7 days with breakdown)
     */
    public function sentimentTimeline(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDays(6)->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'dates' => [],
                'values' => [],
                'sentiment' => ['positive' => [], 'neutral' => [], 'negative' => []],
                'error' => 'Project ID required'
            ], 400);
        }
        
        $cacheKey = "sentiment_timeline_{$projectId}_{$startDate}_{$endDate}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $projectId) {
            try {
                $timeline = [
                    'dates' => [],
                    'values' => [],
                    'sentiment' => [
                        'positive' => [],
                        'neutral' => [],
                        'negative' => [],
                    ]
                ];
                
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i);
                    $dateStr = $date->format('Y-m-d');
                    $dateLabel = $date->format('d') . '. ' . $date->format('M');
                    
                    $sentimentData = $mk->sentimentTotal($projectId, $dateStr, $dateStr, 0, 23);
                    $normalized = $this->normalizeSentimentTotal($sentimentData);
                    
                    $pos = $normalized['positive'];
                    $neu = $normalized['neutral'];
                    $neg = $normalized['negative'];
                    $total = $pos + $neu + $neg;
                    
                    $timeline['dates'][] = $dateLabel;
                    $timeline['values'][] = $total;
                    $timeline['sentiment']['positive'][] = $pos;
                    $timeline['sentiment']['neutral'][] = $neu;
                    $timeline['sentiment']['negative'][] = $neg;
                }
                
                return response()->json([
                    'success' => true,
                    'dates' => $timeline['dates'],
                    'values' => $timeline['values'],
                    'sentiment' => $timeline['sentiment']
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: sentiment timeline failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'dates' => [],
                    'values' => [],
                    'sentiment' => ['positive' => [], 'neutral' => [], 'negative' => []],
                    'error' => 'Failed to fetch sentiment timeline'
                ], 500);
            }
        });
    }

    /**
     * 🔥 API: Geo Users (for map)
     */
    public function geoUsers(Request $request, MediaKernelsClient $mk)
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDay()->toDateString());
        $endDate = $request->query('end_date', now()->toDateString());
        $media = $request->query('media', 'twit');
        
        if (!$projectId) {
            return response()->json([
                'success' => false,
                'data' => [],
                'error' => 'Project ID required'
            ], 400);
        }
        
        $cacheKey = "geo_users_{$projectId}_{$startDate}_{$endDate}_{$media}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $projectId, $media, $startDate, $endDate) {
            try {
                $rawGeo = $mk->geoTwitterUser($projectId, $media, $startDate, $endDate, 0, 23);
                
                $rows = $rawGeo['locality']['rows'] ?? 
                        $rawGeo['administrative_area_level_1']['rows'] ?? [];
                
                return response()->json([
                    'success' => true,
                    'data' => $rows
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: geo users failed', ['error' => $e->getMessage()]);
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'error' => 'Failed to fetch geo data'
                ], 500);
            }
        });
    }

    /**
     * Helper: Normalize sentiment data
     */
    private function normalizeSentimentTotal(array $raw): array
    {
        $src = $raw['data'] ?? $raw;

        return [
            'positive' => (int) ($src['positive'] ?? $src['pos'] ?? $src['1'] ?? 0),
            'neutral'  => (int) ($src['neutral']  ?? $src['neu'] ?? $src['0'] ?? 0),
            'negative' => (int) ($src['negative'] ?? $src['neg'] ?? $src['-1'] ?? 0),
        ];
    }

    /**
     * Helper: Extract total from stats
     */
    private function extractTotal(array $stats): int
    {
        if (isset($stats['data']['total'])) {
            return (int) $stats['data']['total'];
        }
        
        if (isset($stats['total'])) {
            return (int) $stats['total'];
        }
        
        if (isset($stats['data']) && is_array($stats['data'])) {
            return array_sum(array_map(fn($v) => is_numeric($v) ? (int)$v : 0, $stats['data']));
        }
        
        return 0;
    }
}