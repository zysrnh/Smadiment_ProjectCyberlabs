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
     * 🔥 API: Trending Topics (FIXED - using Twitter trending endpoint without platform filter)
     */
    public function trendingTopics(Request $request, MediaKernelsClient $mk)
    {
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $location = $request->query('location', 'Indonesia');
        $limit = (int) $request->query('limit', 50); // Get more for "View All"
        
        $cacheKey = "trending_topics_{$startDate}_{$endDate}_{$location}";
        
        return Cache::remember($cacheKey, 300, function() use ($mk, $startDate, $endDate, $location, $limit) {
            try {
                // Use Twitter trending topics endpoint (no platform filter)
                $result = $mk->twitterTrendingTopics(
                    $startDate,
                    $endDate,
                    0,  // start_time
                    23, // end_time
                    $location,
                    ''  // topics (empty for all)
                );
                
                Log::info('Trending Topics Raw Data from Twitter API', [
                    'count' => count($result),
                    'sample_keys' => array_slice(array_keys($result), 0, 3)
                ]);
                
                // Collect all unique topics across all time periods
                $allTopics = [];
                
                foreach ($result as $datetime => $period) {
                    if (!is_array($period) || !isset($period['data'])) continue;
                    
                    foreach ($period['data'] as $topic) {
                        $name = $topic['name'] ?? '';
                        $volume = (int) ($topic['tweet_volume_i'] ?? 0);
                        $url = $topic['url'] ?? '';
                        
                        if (!$name) continue;
                        
                        // Aggregate by topic name
                        if (!isset($allTopics[$name])) {
                            $allTopics[$name] = [
                                'name' => $name,
                                'title' => $name,
                                'topic' => $name,
                                'total_volume' => 0,
                                'appearances' => 0,
                                'url' => $url,
                                'urls' => [$url]
                            ];
                        }
                        
                        $allTopics[$name]['total_volume'] += $volume;
                        $allTopics[$name]['appearances']++;
                        
                        if ($url && !in_array($url, $allTopics[$name]['urls'])) {
                            $allTopics[$name]['urls'][] = $url;
                        }
                    }
                }
                
                // Convert to array and add required fields
                $normalized = [];
                foreach ($allTopics as $topic) {
                    $normalized[] = [
                        'title' => $topic['name'],
                        'name' => $topic['name'],
                        'topic' => $topic['name'],
                        'volume' => $topic['total_volume'],
                        'count' => $topic['total_volume'],
                        'total' => $topic['total_volume'],
                        'appearances' => $topic['appearances'],
                        'description' => '',
                        'reference' => $topic['url'],
                        'urls' => array_filter($topic['urls']),
                    ];
                }
                
                // Sort by volume descending
                usort($normalized, fn($a, $b) => $b['total'] - $a['total']);
                
                // Limit to top 50
                $normalized = array_slice($normalized, 0, $limit);
                
                Log::info('Trending Topics Normalized', [
                    'count' => count($normalized),
                    'sample' => array_slice($normalized, 0, 2)
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $normalized,
                    'total' => count($normalized)
                ]);
            } catch (\Exception $e) {
                Log::error('API: trending topics failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                return response()->json([
                    'success' => false,
                    'data' => [],
                    'error' => 'Failed to fetch trending topics'
                ], 500);
            }
        });
    }

    /**
     * 🔥 API: Top Hashtags (FIXED - better error handling)
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
                
                Log::info('Top Hashtags Raw Response', [
                    'has_data' => isset($rawData['data']),
                    'is_array' => is_array($rawData),
                    'structure' => array_keys($rawData),
                    'sample' => json_encode(array_slice($rawData, 0, 3))
                ]);
                
                // Handle different response structures
                $rawItems = [];
                
                if (isset($rawData['data']) && is_array($rawData['data'])) {
                    $rawItems = $rawData['data'];
                } elseif (is_array($rawData)) {
                    $rawItems = $rawData;
                }
                
                $normalized = [];
                
                foreach ($rawItems as $item) {
                    if (!is_array($item)) continue;
                    
                    $name = $item['name'] ?? $item['hashtag'] ?? $item['tag'] ?? null;
                    $mention = (int)($item['size'] ?? $item['mention'] ?? $item['count'] ?? $item['y'] ?? 0);
                    
                    // Skip invalid entries
                    if (empty($name) || $mention === 0) {
                        continue;
                    }
                    
                    $normalized[] = [
                        'hashtag' => $name,
                        'name' => $name,
                        'tag' => $name,
                        'mention' => $mention,
                        'count' => $mention,
                        'size' => $mention,
                    ];
                }
                
                // Sort by mention count
                usort($normalized, fn($a, $b) => $b['mention'] <=> $a['mention']);
                
                Log::info('Top Hashtags Normalized', [
                    'count' => count($normalized),
                    'top_3' => array_slice($normalized, 0, 3)
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => $normalized
                ]);
            } catch (\Exception $e) {
                Log::error('API: top hashtags failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
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
     * 🔥 API: Sentiment by Media
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
                
                Log::info('Sentiment Media Raw Data', [
                    'total' => $totalAll,
                    'bymedia' => $byMedia
                ]);
                
                $mediaData = [];
                
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
                    
                    if ($total === 0) continue;
                    
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
                
                usort($mediaData, fn($a, $b) => $b['total'] <=> $a['total']);
                
                return response()->json([
                    'success' => true,
                    'total_all' => $totalAll,
                    'data' => $mediaData
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: sentiment by media failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
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
     * 🔥 API: Active Users (FIXED - better data extraction)
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
                
                Log::info('Active Users Raw Response', [
                    'has_data' => isset($rawUsers['data']),
                    'structure' => array_keys($rawUsers),
                    'sample' => json_encode(array_slice($rawUsers, 0, 2))
                ]);
                
                // Try multiple paths to find the actual user data
                $userData = $rawUsers['data']['data'] ?? 
                           $rawUsers['data'] ?? 
                           $rawUsers['users'] ?? 
                           $rawUsers;
                
                // If still not an array, try to extract from response
                if (!is_array($userData) || empty($userData)) {
                    Log::warning('No valid user data found in response');
                    return response()->json([
                        'success' => false,
                        'data' => [],
                        'error' => 'No active user data available'
                    ]);
                }
                
                $rows = [];
                
                foreach ($userData as $item) {
                    if (!is_array($item)) continue;

                    // Extract username from various possible fields
                    $fullName = $item['name'] ?? $item['username'] ?? $item['author'] ?? 'Unknown User';
                    $username = $fullName;
                    
                    // Try to extract @username if present
                    if (preg_match('/@(\w+)/', $fullName, $matches)) {
                        $username = $matches[1];
                    } elseif (preg_match('/\((@\w+)\)/', $fullName, $matches)) {
                        $username = ltrim($matches[1], '@');
                    }
                    
                    // Extract count from various possible fields
                    $count = (int)($item['y'] ?? 
                                  $item['post_count'] ?? 
                                  $item['posts'] ?? 
                                  $item['count'] ?? 
                                  $item['tweets'] ?? 
                                  $item['mentions'] ?? 0);
                    
                    // Skip if no valid count
                    if ($count === 0) {
                        continue;
                    }

                    $rows[] = [
                        'username' => $username,
                        'count' => $count,
                        'full_name' => $fullName,
                    ];
                }
                
                // Sort by count descending
                usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
                
                Log::info('Active Users Normalized', [
                    'count' => count($rows),
                    'top_3' => array_slice($rows, 0, 3)
                ]);
                
                return response()->json([
                    'success' => true,
                    'data' => array_slice($rows, 0, 6)
                ]);
                
            } catch (\Exception $e) {
                Log::error('API: active users failed', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
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