<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    protected $mkClient;

    public function __construct(MediaKernelsClient $mkClient)
    {
        $this->mkClient = $mkClient;
    }

    public function recentTopicsPage(Request $request)
    {
        try {
            $level = $request->query('level', 'internasional');
            $size = (int) $request->query('size', 10);

            Log::info('=== RECENT TOPICS PAGE (HYBRID) ===', [
                'level' => $level,
                'size' => $size,
            ]);

            if (!in_array($level, ['internasional', 'nasional', 'regional_apac'])) {
                $level = 'internasional';
            }

            $response = $this->mkClient->recentTopicsHybrid($level, $size);

            $issues = $response['daftar_isu'] ?? [];
            $apiVersion = $response['api_version'] ?? 'unknown';
            $status = $response['status'] ?? 'unknown';

            Log::info('=== FINAL DATA ===', [
                'api_version' => $apiVersion,
                'status' => $status,
                'issues_count' => count($issues),
                'first_issue' => count($issues) > 0 ? $issues[0]['judul'] : 'no issues',
            ]);

            return view('mk.news.recent-topics', [
                'level' => $level,
                'size' => $size,
                'issues' => $issues,
                'apiVersion' => $apiVersion,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            Log::error('Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('mk.news.recent-topics', [
                'level' => $request->query('level', 'internasional'),
                'size' => 10,
                'issues' => [],
                'apiVersion' => 'error',
                'status' => 'error',
                'error' => 'Failed to load topics',
            ]);
        }
    }

    public function recentTopicsApi(Request $request)
    {
        try {
            $level = $request->query('level', 'internasional');
            $size = (int) $request->query('size', 10);

            if (!in_array($level, ['internasional', 'nasional', 'regional_apac'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid level parameter',
                ], 400);
            }

            $response = $this->mkClient->recentTopicsHybrid($level, $size);

            return response()->json([
                'success' => $response['status'] === 'success',
                'level' => $level,
                'api_version' => $response['api_version'] ?? 'unknown',
                'data' => $response['daftar_isu'] ?? [],
            ]);

        } catch (\Exception $e) {
            Log::error('API Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch topics',
                'data' => [],
            ], 500);
        }
    }

    public function newsWordCloudPage(Request $request)
    {
        try {
            $projectId = $request->query('project_id', session('selected_project_id'));
            
            if (!$projectId) {
                Log::warning('News Word Cloud: No project selected');
                return redirect()->route('mk.dashboard')
                    ->with('error', 'Please select a project first');
            }

            session(['selected_project_id' => $projectId]);

            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(29)->format('Y-m-d'));

            Log::info('News Word Cloud Page Loaded', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            return view('mk.news.word-cloud', [
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('News Word Cloud Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('mk.dashboard')
                ->with('error', 'Failed to load News Word Cloud page');
        }
    }

    /**
     * API: Get News Word Cloud Data (ENHANCED VERSION)
     * Combines multiple data sources for richer word cloud
     */
    public function newsWordCloudData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $sentiment = $request->query('sentiment', '2');

            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Project ID is required',
                ], 400);
            }

            Log::info('🔍 News Word Cloud - Enhanced Data Fetch Started', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sentiment' => $sentiment,
            ]);

            $phrases = [];

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // SOURCE 1: WordCloud API (Primary)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            try {
                $wordCloudData = $this->mkClient->wordCloud(
                    $projectId,
                    $startDate,
                    0,
                    $endDate,
                    23,
                    $sentiment
                );

                $wcPhrases = $wordCloudData['data']['phrases'] ?? [];
                foreach ($wcPhrases as $word => $count) {
                    $phrases[$word] = ($phrases[$word] ?? 0) + (int)$count;
                }

                Log::info('✅ WordCloud API fetched', ['count' => count($wcPhrases)]);
            } catch (\Exception $e) {
                Log::warning('⚠️ WordCloud API failed', ['error' => $e->getMessage()]);
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // SOURCE 2: Top Hashtags (Add trending topics)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            try {
                $hashtagsData = $this->mkClient->topHashtags(
                    $projectId,
                    'all',
                    $startDate,
                    $endDate
                );

                $hashtags = $hashtagsData['data'] ?? [];
                foreach ($hashtags as $item) {
                    $tag = ltrim($item['hashtag'] ?? '', '#');
                    $count = $item['count'] ?? 1;
                    
                    if (strlen($tag) >= 3) {
                        $phrases[$tag] = ($phrases[$tag] ?? 0) + (int)($count * 1.5); // Boost hashtags
                    }
                }

                Log::info('✅ Hashtags fetched', ['count' => count($hashtags)]);
            } catch (\Exception $e) {
                Log::warning('⚠️ Hashtags API failed', ['error' => $e->getMessage()]);
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // SOURCE 3: Topic Map (Add topic clusters)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            try {
                $topicData = $this->mkClient->topicMap(
                    $projectId,
                    'all',
                    $startDate,
                    $endDate
                );

                foreach ($topicData as $topic) {
                    $name = $topic['name'] ?? '';
                    $weight = $topic['weight'] ?? 1;
                    
                    if ($name && strlen($name) >= 3) {
                        $phrases[$name] = ($phrases[$name] ?? 0) + (int)($weight * 2); // Boost topics
                    }
                }

                Log::info('✅ Topics fetched', ['count' => count($topicData)]);
            } catch (\Exception $e) {
                Log::warning('⚠️ Topics API failed', ['error' => $e->getMessage()]);
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // SOURCE 4: Mentions Text Mining (Fallback if still low)
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            if (count($phrases) < 30) {
                Log::info('📝 Extracting words from mentions (fallback)...');
                
                try {
                    $mentions = $this->mkClient->mentions(
                        $projectId,
                        $startDate,
                        $endDate,
                        0,
                        23,
                        true,
                        0,
                        200 // Fetch 200 mentions
                    );

                    $extractedPhrases = $this->extractPhrasesFromMentions($mentions, $sentiment);
                    
                    foreach ($extractedPhrases as $word => $count) {
                        $phrases[$word] = ($phrases[$word] ?? 0) + $count;
                    }

                    Log::info('✅ Mentions text-mined', ['extracted' => count($extractedPhrases)]);
                } catch (\Exception $e) {
                    Log::warning('⚠️ Mentions mining failed', ['error' => $e->getMessage()]);
                }
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // CLEANUP & FINAL PROCESSING
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            
            // Remove very short words and common terms
            $phrases = array_filter($phrases, function($word) {
                $word = strtolower($word);
                $banned = ['http', 'https', 'www', 'com', 'net', 'org', 'the', 'and', 'atau'];
                return strlen($word) >= 3 && !in_array($word, $banned);
            }, ARRAY_FILTER_USE_KEY);

            // Sort by frequency
            arsort($phrases);

            // Take top 150 words
            $phrases = array_slice($phrases, 0, 150, true);

            Log::info('🎉 Final word cloud data', [
                'total_phrases' => count($phrases),
                'top_5' => array_slice(array_keys($phrases), 0, 5),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => [
                        'phrases' => $phrases,
                    ],
                ],
                'meta' => [
                    'total_words' => count($phrases),
                    'sources_used' => ['wordcloud', 'hashtags', 'topics', 'mentions'],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ News Word Cloud API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch word cloud data',
            ], 500);
        }
    }

    /**
     * Extract phrases from mentions text content
     */
    private function extractPhrasesFromMentions($mentions, $sentiment): array
    {
        $wordFreq = [];
        
        // Comprehensive stopwords (Indonesian + English)
        $stopwords = [
            // Indonesian common words
            'yang', 'dan', 'di', 'dari', 'untuk', 'pada', 'dengan', 'ini', 'itu',
            'adalah', 'akan', 'ada', 'juga', 'atau', 'dalam', 'ke', 'tidak',
            'sudah', 'dapat', 'telah', 'oleh', 'sebagai', 'saat', 'lebih', 'bisa',
            'tersebut', 'bagi', 'antara', 'saja', 'melalui', 'hingga', 'nya', 'kami',
            'kita', 'anda', 'mereka', 'dia', 'saya', 'kamu', 'apa', 'siapa', 'dimana',
            'kapan', 'mengapa', 'bagaimana', 'hari', 'tahun', 'bulan', 'waktu',
            // English common words
            'the', 'a', 'an', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for',
            'of', 'with', 'by', 'from', 'as', 'is', 'was', 'are', 'were', 'be',
            'been', 'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will',
            'would', 'could', 'should', 'may', 'might', 'must', 'can', 'this',
            'that', 'these', 'those', 'i', 'you', 'he', 'she', 'it', 'we', 'they',
        ];

        foreach ($mentions as $mention) {
            // Filter by sentiment if needed
            if ($sentiment !== '2') {
                $mentionSentiment = $mention['sentiment'] ?? '1';
                if ($mentionSentiment !== $sentiment) {
                    continue;
                }
            }

            $content = $mention['content'] ?? '';
            
            // Remove URLs, mentions, hashtags
            $content = preg_replace('/(https?:\/\/[^\s]+)/', '', $content);
            $content = preg_replace('/@\w+/', '', $content);
            $content = preg_replace('/#(\w+)/', '$1', $content); // Keep hashtag text
            
            // Remove special characters but keep Indonesian characters
            $content = preg_replace('/[^\p{L}\s]/u', ' ', $content);
            
            // Split into words
            $words = preg_split('/\s+/', $content, -1, PREG_SPLIT_NO_EMPTY);
            
            foreach ($words as $word) {
                // Clean and capitalize first letter
                $word = trim($word);
                $wordLower = mb_strtolower($word, 'UTF-8');
                $word = mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
                
                // Filter: min length, not stopword, not number, not too long
                if (
                    mb_strlen($word, 'UTF-8') >= 3 &&
                    mb_strlen($word, 'UTF-8') <= 25 &&
                    !in_array($wordLower, $stopwords) &&
                    !is_numeric($word)
                ) {
                    $wordFreq[$word] = ($wordFreq[$word] ?? 0) + 1;
                }
            }
        }

        // Sort and return top 80
        arsort($wordFreq);
        return array_slice($wordFreq, 0, 80, true);
    }
    public function topPublisherPage(Request $request)
{
    try {
        $projectId = $request->query('project_id', session('selected_project_id'));
        
        if (!$projectId) {
            Log::warning('Top Publisher: No project selected');
            return redirect()->route('mk.dashboard')
                ->with('error', 'Please select a project first');
        }

        session(['selected_project_id' => $projectId]);

        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        Log::info('Top Publisher Page Loaded', [
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        return view('mk.news.top-publisher', [
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
        ]);

    } catch (\Exception $e) {
        Log::error('Top Publisher Page Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->route('mk.dashboard')
            ->with('error', 'Failed to load Top Publisher page');
    }
}

/**
 * API: Get Top Publisher Data
 */
public function topPublisherData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $newsType = $request->query('news_type', 'article');

        if (!$projectId) {
            return response()->json([
                'success' => false,
                'error'   => 'Project ID is required',
            ], 400);
        }

        Log::info('🔍 Top Publisher Data Fetch Started', [
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'news_type'  => $newsType,
        ]);

        $publishersData = $this->mkClient->topPublisher(
            $projectId,
            $startDate,
            $endDate,
            0,
            23,
            100,
            $newsType
        );

        // Transform data to array of objects
        $publishers = [];
        $rank = 1;
        foreach ($publishersData as $domain => $count) {
            $publishers[] = [
                'rank'   => $rank++,
                'domain' => $domain,
                'count'  => (int)$count,
            ];
        }

        Log::info('🎉 Top Publisher data retrieved', [
            'total_publishers' => count($publishers),
        ]);

        return response()->json([
            'success' => true,
            'data'    => $publishers,
            'meta'    => [
                'total_publishers' => count($publishers),
                'total_articles'   => array_sum(array_column($publishers, 'count')),
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('❌ Top Publisher API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error'   => 'Failed to fetch top publisher data',
        ], 500);
    }
}
public function newsTimelinePage(Request $request)
{
    try {
        $projectId = $request->query('project_id', session('selected_project_id'));
        
        if (!$projectId) {
            Log::warning('News Timeline: No project selected');
            return redirect()->route('mk.dashboard')
                ->with('error', 'Please select a project first');
        }

        session(['selected_project_id' => $projectId]);

        $endDate = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        Log::info('News Timeline Page Loaded', [
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return view('mk.news.timeline', [
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);

    } catch (\Exception $e) {
        Log::error('News Timeline Page Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return redirect()->route('mk.dashboard')
            ->with('error', 'Failed to load News Timeline page');
    }
}

/**
 * API: Get News Mentions Data
 */
public function newsMentionsData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        if (!$projectId) {
            return response()->json([
                'success' => false,
                'error' => 'Project ID is required',
            ], 400);
        }

        Log::info('🔍 News Mentions Data Fetch Started', [
            'project_id' => $projectId,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // Fetch mentions with filter for news media
        // Note: The mentions() method should filter by media_type='news' or media_type_id=5
        $mentions = $this->mkClient->mentions(
            $projectId,
            $startDate,
            $endDate,
            0,       // start_time
            23,      // end_time
            true,    // with_content
            0,       // start (offset)
            1000     // rows (get more data for timeline)
        );

        // Filter for news mentions only (media_type_id = 5 based on your JSON)
        $newsMentions = array_filter($mentions, function($mention) {
            return ($mention['media_type_id'] ?? '') === '5' || 
                   ($mention['media_type'] ?? '') === 'news' ||
                   ($mention['media_type'] ?? '') === 'article';
        });

        // Re-index array after filtering
        $newsMentions = array_values($newsMentions);

        Log::info('🎉 News Mentions data retrieved', [
            'total_mentions' => count($newsMentions),
        ]);

        return response()->json([
            'success' => true,
            'data' => $newsMentions,
            'meta' => [
                'total_mentions' => count($newsMentions),
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('❌ News Mentions API Error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error' => 'Failed to fetch news mentions data',
        ], 500);
    }
}















}