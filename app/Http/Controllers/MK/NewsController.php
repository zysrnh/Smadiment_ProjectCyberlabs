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
public function articlesPage(Request $request)
    {
        try {
            $projectId = $request->query('project_id', session('selected_project_id'));
            
            if (!$projectId) {
                Log::warning('Articles: No project selected');
                return redirect()->route('mk.dashboard')
                    ->with('error', 'Please select a project first');
            }

            session(['selected_project_id' => $projectId]);

            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            Log::info('📄 Articles Page Loaded', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            return view('mk.news.articles', [
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Articles Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('mk.dashboard')
                ->with('error', 'Failed to load Articles page');
        }
    }

    /**
     * API: Get Articles Data with Quotes (FINAL FIX WITH DEEP DEBUGGING)
     */
    public function articlesData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $media = $request->query('media', 'doc');
            $sentiment = $request->query('sentiment', 'all');

            if (!$projectId) {
                Log::warning('⚠️ Articles API: Missing project ID');
                return response()->json([
                    'success' => false,
                    'error' => 'Project ID is required',
                ], 400);
            }

            Log::info('🔍 Articles Data Fetch Started', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'media' => $media,
                'sentiment' => $sentiment,
            ]);

            // Fetch articles with quotes
            $articles = $this->mkClient->articles(
                $projectId,
                $media,
                $startDate,
                $endDate,
                0,       // start_time
                23,      // end_time
                0,       // start (offset)
                1000,    // rows
                true     // with_quotes - MAKE SURE THIS IS TRUE!
            );

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // DEEP QUOTES DEBUGGING
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $articlesWithQuotesField = 0;
            $rawQuotesCountTotal = 0;
            
            foreach ($articles as $art) {
                if (isset($art['quotes'])) {
                    $articlesWithQuotesField++;
                    if (is_array($art['quotes'])) {
                        $rawQuotesCountTotal += count($art['quotes']);
                    }
                }
            }

            Log::info('📊 Raw API Response Analysis', [
                'total_articles_fetched' => count($articles),
                'articles_with_quotes_field' => $articlesWithQuotesField,
                'raw_quotes_count_before_filtering' => $rawQuotesCountTotal,
                'percentage_with_quotes' => count($articles) > 0 ? round(($articlesWithQuotesField / count($articles)) * 100, 2) . '%' : '0%',
                'first_article_sample' => count($articles) > 0 ? [
                    'title' => substr($articles[0]['title'] ?? 'N/A', 0, 50) . '...',
                    'has_quotes_field' => isset($articles[0]['quotes']),
                    'quotes_type' => isset($articles[0]['quotes']) ? gettype($articles[0]['quotes']) : 'NOT_SET',
                    'quotes_count' => is_array($articles[0]['quotes'] ?? null) ? count($articles[0]['quotes']) : 0,
                    'first_quote_has_Kutipan' => (isset($articles[0]['quotes']) && is_array($articles[0]['quotes']) && count($articles[0]['quotes']) > 0) 
                        ? isset($articles[0]['quotes'][0]['Kutipan']) 
                        : false,
                    'first_quote_Kutipan_value' => (isset($articles[0]['quotes']) && is_array($articles[0]['quotes']) && count($articles[0]['quotes']) > 0 && isset($articles[0]['quotes'][0]['Kutipan'])) 
                        ? substr($articles[0]['quotes'][0]['Kutipan'], 0, 50) . '...'
                        : 'N/A',
                ] : 'No articles returned',
            ]);

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // PROCESS ARTICLES WITH DETAILED TRACKING
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $totalQuotesBeforeFilter = 0;
            $totalQuotesAfterFilter = 0;
            $articlesWithValidQuotes = 0;
            $quotesFilteredOut = 0;

            $articles = array_map(function($article) use (&$totalQuotesBeforeFilter, &$totalQuotesAfterFilter, &$articlesWithValidQuotes, &$quotesFilteredOut) {
                // Ensure all required fields exist
                $article['title'] = $article['title'] ?? 'Untitled';
                $article['publisher'] = $article['publisher'] ?? 'Unknown Publisher';
                $article['url'] = $article['url'] ?? '#';
                $article['date_created'] = $article['date_created'] ?? now()->toDateTimeString();
                $article['content'] = $article['content'] ?? '';
                $article['sentiment'] = $article['sentiment'] ?? 'Neutral';
                $article['sentiment_class'] = $article['sentiment_class'] ?? 'neutral';
                
                // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                // HANDLE QUOTES WITH DETAILED LOGGING
                // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
                $quotes = $article['quotes'] ?? [];
                $beforeFilterCount = is_array($quotes) ? count($quotes) : 0;
                $totalQuotesBeforeFilter += $beforeFilterCount;
                
                if (is_array($quotes) && count($quotes) > 0) {
                    // Filter valid quotes
                    $validQuotes = array_filter($quotes, function($quote) use (&$quotesFilteredOut) {
                        // Check if quote is array
                        if (!is_array($quote)) {
                            $quotesFilteredOut++;
                            return false;
                        }
                        
                        // Check if has Kutipan field
                        if (!isset($quote['Kutipan'])) {
                            $quotesFilteredOut++;
                            return false;
                        }
                        
                        // Check if Kutipan is not empty
                        $kutipan = trim($quote['Kutipan']);
                        if ($kutipan === '') {
                            $quotesFilteredOut++;
                            return false;
                        }
                        
                        return true;
                    });
                    
                    $quotes = array_values($validQuotes);
                } else {
                    $quotes = [];
                }
                
                $afterFilterCount = count($quotes);
                $totalQuotesAfterFilter += $afterFilterCount;
                
                if ($afterFilterCount > 0) {
                    $articlesWithValidQuotes++;
                }
                
                $article['quotes'] = $quotes;
                $article['total_quotes'] = $afterFilterCount;
                
                return $article;
            }, $articles);

            Log::info('💬 Quotes Processing Results', [
                'quotes_before_filtering' => $totalQuotesBeforeFilter,
                'quotes_after_filtering' => $totalQuotesAfterFilter,
                'quotes_filtered_out' => $quotesFilteredOut,
                'articles_with_valid_quotes' => $articlesWithValidQuotes,
                'filter_success_rate' => $totalQuotesBeforeFilter > 0 ? round(($totalQuotesAfterFilter / $totalQuotesBeforeFilter) * 100, 2) . '%' : 'N/A',
            ]);

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // SENTIMENT FILTERING
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            if ($sentiment !== 'all') {
                $beforeSentimentFilter = count($articles);
                
                $articles = array_filter($articles, function($article) use ($sentiment) {
                    $articleSentiment = $article['class_sentiment'] ?? $article['sentiment_class'] ?? '0';
                    
                    // Normalize sentiment values
                    $sentimentLower = strtolower($article['sentiment'] ?? '');
                    if ($sentimentLower === 'positive' || $sentimentLower === 'positif') {
                        $articleSentiment = '1';
                    } elseif ($sentimentLower === 'negative' || $sentimentLower === 'negatif') {
                        $articleSentiment = '-1';
                    } elseif ($sentimentLower === 'neutral' || $sentimentLower === 'netral') {
                        $articleSentiment = '0';
                    }
                    
                    return $articleSentiment == $sentiment;
                });
                
                $articles = array_values($articles);
                
                Log::info('🎯 Sentiment Filter Applied', [
                    'filter' => $sentiment,
                    'before' => $beforeSentimentFilter,
                    'after' => count($articles),
                    'removed' => $beforeSentimentFilter - count($articles),
                ]);
            }

            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            // FINAL TOTALS
            // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
            $totalQuotes = array_sum(array_column($articles, 'total_quotes'));
            $finalArticlesWithQuotes = count(array_filter($articles, function($art) {
                return ($art['total_quotes'] ?? 0) > 0;
            }));

            Log::info('✅ Articles Data Processing Complete', [
                'final_total_articles' => count($articles),
                'final_articles_with_quotes' => $finalArticlesWithQuotes,
                'final_total_quotes' => $totalQuotes,
                'avg_quotes_per_article' => count($articles) > 0 ? round($totalQuotes / count($articles), 2) : 0,
                'sentiment_filter_applied' => $sentiment,
                'sample_article_with_quotes' => $finalArticlesWithQuotes > 0 ? [
                    'title' => $articles[0]['title'] ?? 'N/A',
                    'quotes_count' => $articles[0]['total_quotes'] ?? 0,
                    'first_quote_preview' => (isset($articles[0]['quotes'][0]['Kutipan'])) 
                        ? substr($articles[0]['quotes'][0]['Kutipan'], 0, 60) . '...'
                        : 'No quotes',
                ] : 'No articles with quotes found',
            ]);

            return response()->json([
                'success' => true,
                'data' => $articles,
                'meta' => [
                    'total_articles' => count($articles),
                    'articles_with_quotes' => $finalArticlesWithQuotes,
                    'total_quotes' => $totalQuotes,
                    'avg_quotes_per_article' => count($articles) > 0 ? round($totalQuotes / count($articles), 2) : 0,
                    'sentiment_filter' => $sentiment,
                    'media_type' => $media,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Articles API Error', [
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile()),
                'trace' => $e->getTraceAsString(),
                'request_params' => [
                    'project_id' => $request->query('project_id'),
                    'start_date' => $request->query('start_date'),
                    'end_date' => $request->query('end_date'),
                    'media' => $request->query('media'),
                    'sentiment' => $request->query('sentiment'),
                ],
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch articles data',
                'message' => config('app.debug') ? $e->getMessage() : 'Internal server error',
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
            'end_date'   => $endDate,
        ]);

        return view('mk.news.timeline', [
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
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
 * API: Get News Mentions Data for Timeline
 */

public function newsMentionsData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $start     = (int) $request->query('start', 0);
            $rows      = (int) $request->query('rows', 1000);

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID is required'], 400);
            }

            Log::info('🔍 News Mentions Timeline Data Fetch', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date'   => $endDate,
                'start'      => $start,
                'rows'       => $rows,
            ]);

            $mentions = $this->mkClient->mentions(
                $projectId,
                $startDate,
                $endDate,
                0,      // start_time
                23,     // end_time
                true,   // flag
                $start, // offset — support pagination
                $rows   // rows per page
            );

            Log::info('✅ News Mentions fetched', ['total' => count($mentions), 'start' => $start]);

            return response()->json([
                'success' => true,
                'data'    => $mentions,
                'meta'    => ['total' => count($mentions), 'start' => $start, 'rows' => $rows],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ News Mentions API Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to fetch mentions data'], 500);
        }
    }

    // ════════════════════════════════════════════════════════════════
    // TikTok Top Status
    // ════════════════════════════════════════════════════════════════
    public function tiktokTopStatus(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $rows      = (int) $request->query('rows', 500);
            $start     = (int) $request->query('start', 0);
            $sub       = $request->query('sub', 'postbylike');

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID is required'], 400);
            }

            Log::info('🎵 TikTok Top Status Fetch', compact('projectId', 'startDate', 'endDate', 'rows', 'start', 'sub'));

            $data = [];

            // ── Try dedicated TikTok API ──────────────────────────
            try {
                $data = $this->mkClient->tiktokTopStatus(
                    $projectId, $startDate, $endDate, 0, 23, $rows, $sub
                );
                Log::info('✅ TikTok dedicated API returned', ['count' => count((array)$data)]);
            } catch (\Exception $e) {
                Log::warning('⚠️ TikTok dedicated API failed, falling back to mentions', ['error' => $e->getMessage()]);
            }

            // ── Fallback: filter mentions by TikTok media_type_id ─
            if (empty($data)) {
                Log::info('📋 TikTok: using mentions fallback');
                $mentions = $this->mkClient->mentions($projectId, $startDate, $endDate, 0, 23, true, $start, $rows);
                $data = array_values(array_filter($mentions, function ($item) {
                    $mt = strtolower((string) ($item['media_type_id'] ?? $item['media_type'] ?? $item['tcode'] ?? ''));
                    return $mt === '6' || str_contains($mt, 'tiktok');
                }));
                Log::info('📋 TikTok fallback result', ['count' => count($data)]);
            }

            $normalised = array_map(fn($item) => $this->normaliseTiktok($item), is_array($data) ? $data : []);

            return response()->json([
                'success' => true,
                'data'    => $normalised,
                'meta'    => ['total' => count($normalised), 'platform' => 'tiktok', 'sub' => $sub],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ TikTok Top Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to fetch TikTok data'], 500);
        }
    }

    private function normaliseTiktok(array $item): array
    {
        $handle = $item['author_scr_name'] ?? $item['author_id'] ?? '';
        return [
            '_platform'       => 'tiktok',
            'media_type_id'   => '6',
            'id'              => $item['id'] ?? $item['docid'] ?? '',
            'url'             => $item['url'] ?? '',
            'content'         => strip_tags($item['content'] ?? $item['name'] ?? ''),
            'author_name'     => $handle,
            'author_handle'   => $handle,
            'avatar_url'      => ($item['image'] ?? '') ?: '', // TikTok often empty
            'date_created'    => $item['date_created'] ?? '',
            'num_likes'       => (int) ($item['likes'] ?? $item['num_likes'] ?? $item['freq'] ?? 0),
            'num_comments'    => (int) ($item['comments'] ?? $item['num_comments'] ?? 0),
            'num_shares'      => (int) ($item['shares'] ?? 0),
            'num_views'       => (int) ($item['views'] ?? $item['num_views'] ?? 0),
            'num_followers'   => (int) ($item['num_followers'] ?? 0),
            'class_sentiment' => (string) ($item['sentiment'] ?? $item['class_sentiment'] ?? '0'),
            'mention_type'    => $item['mention_type'] ?? 'video',
            'hostname'        => 'tiktok.com',
        ];
    }

    // ════════════════════════════════════════════════════════════════
    // Instagram Top Status — FIXED
    // ════════════════════════════════════════════════════════════════
    public function igTopStatus(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $rows      = (int) $request->query('rows', 500);
            $start     = (int) $request->query('start', 0);
            $sub       = $request->query('sub', 'postbylike');

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID is required'], 400);
            }

            Log::info('📷 Instagram Top Status Fetch', compact('projectId', 'startDate', 'endDate', 'rows', 'start', 'sub'));

            $data = [];

            // ── Try dedicated IG API ───────────────────────────────
            try {
                $data = $this->mkClient->igTopStatus(
                    $projectId, $startDate, $endDate, 0, 23, $rows, $sub
                );
                Log::info('✅ IG dedicated API returned', ['count' => count((array)$data)]);
            } catch (\Exception $e) {
                Log::warning('⚠️ IG dedicated API failed, falling back to mentions', ['error' => $e->getMessage()]);
            }

            // ── Fallback: filter mentions by Instagram media_type_id ─
            // Raw IG data dari mentions TIDAK punya media_type_id, tapi punya:
            // - id starts with "in-"
            // - url contains "instagram.com"
            // - mention_type = "image"
            if (empty($data)) {
                Log::info('📋 Instagram: using mentions fallback');
                $mentions = $this->mkClient->mentions($projectId, $startDate, $endDate, 0, 23, true, $start, $rows);

                $data = array_values(array_filter($mentions, function ($item) {
                    $mt    = strtolower((string) ($item['media_type_id'] ?? $item['media_type'] ?? $item['tcode'] ?? ''));
                    $id    = (string) ($item['id'] ?? $item['docid'] ?? '');
                    $url   = (string) ($item['url'] ?? '');

                    // Detect Instagram by media_type OR by id prefix "in-" OR by URL
                    return $mt === '3'
                        || str_contains($mt, 'ig')
                        || str_contains($mt, 'instagram')
                        || str_starts_with($id, 'in-')
                        || str_contains($url, 'instagram.com');
                }));

                // Sort by likes desc
                usort($data, fn($a, $b) =>
                    (int)($b['num_likes'] ?? $b['likes'] ?? $b['freq'] ?? 0)
                    - (int)($a['num_likes'] ?? $a['likes'] ?? $a['freq'] ?? 0)
                );

                Log::info('📋 Instagram fallback result', ['count' => count($data)]);
            }

            $normalised = array_map(fn($item) => $this->normaliseInstagram($item), is_array($data) ? $data : []);

            Log::info('✅ Instagram Top Status final', ['total' => count($normalised)]);

            return response()->json([
                'success' => true,
                'data'    => $normalised,
                'meta'    => ['total' => count($normalised), 'platform' => 'instagram', 'sub' => $sub],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Instagram Top Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to fetch Instagram data'], 500);
        }
    }

    private function normaliseInstagram(array $item): array
    {
        // Raw IG dari mentions: author_id = "beritasatu", author_scr_name = "BeritaSatu"
        $handle    = $item['author_scr_name'] ?? $item['author_id'] ?? '';
        $authorId  = $item['author_id'] ?? $handle; // lowercase, e.g. "beritasatu"
        $rawImage  = $item['image'] ?? '';

        // Avatar: IG API sering kirim kosong — fallback ke unavatar
        // unavatar.io/instagram/{handle} cukup reliable untuk public accounts
        $avatarUrl = ($rawImage && str_starts_with($rawImage, 'http'))
            ? $rawImage
            : ($handle ? "https://unavatar.io/instagram/{$authorId}" : '');

        return [
            '_platform'       => 'ig',
            'media_type_id'   => '3',
            'id'              => $item['id'] ?? $item['docid'] ?? '',
            'url'             => $item['url'] ?? '',
            'content'         => strip_tags($item['content'] ?? $item['name'] ?? ''),
            'author_name'     => $handle, // IG biasanya display name = scr_name
            'author_handle'   => $authorId, // lowercase ID for unavatar
            'avatar_url'      => $avatarUrl,
            'date_created'    => $item['date_created'] ?? '',
            'num_likes'       => (int) ($item['num_likes'] ?? $item['likes'] ?? $item['freq'] ?? 0),
            'num_comments'    => (int) ($item['num_comments'] ?? $item['comments'] ?? 0),
            'num_shares'      => (int) ($item['shares'] ?? 0),
            'num_views'       => (int) ($item['views'] ?? $item['num_views'] ?? 0),
            'num_followers'   => (int) ($item['num_followers'] ?? 0),
            'interaction'     => (int) ($item['interaction'] ?? $item['interaction_with_post'] ?? 0),
            'class_sentiment' => (string) ($item['sentiment'] ?? $item['class_sentiment'] ?? '0'),
            'sentiment_str'   => $item['sentiment_str'] ?? '',
            'mention_type'    => $item['mention_type'] ?? 'image',
            'hostname'        => 'instagram.com',
        ];
    }

    // ════════════════════════════════════════════════════════════════
    // Facebook Top Status
    // ════════════════════════════════════════════════════════════════
    public function fbTopStatusApi(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $rows      = (int) $request->query('rows', 500);
            $start     = (int) $request->query('start', 0);
            $sub       = $request->query('sub', 'fblike');

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID is required'], 400);
            }

            Log::info('📘 Facebook Top Status Fetch', compact('projectId', 'startDate', 'endDate', 'rows', 'start', 'sub'));

            $data = [];

            try {
                $data = $this->mkClient->fbTopStatus(
                    $projectId, $startDate, $endDate, 0, 23, $rows, $sub
                );
                Log::info('✅ FB dedicated API returned', ['count' => count((array)$data)]);
            } catch (\Exception $e) {
                Log::warning('⚠️ FB dedicated API failed, falling back to mentions', ['error' => $e->getMessage()]);
            }

            if (empty($data)) {
                Log::info('📋 Facebook: using mentions fallback');
                $mentions = $this->mkClient->mentions($projectId, $startDate, $endDate, 0, 23, true, $start, $rows);
                $data = array_values(array_filter($mentions, function ($item) {
                    $mt  = strtolower((string) ($item['media_type_id'] ?? $item['media_type'] ?? $item['tcode'] ?? ''));
                    $id  = (string) ($item['id'] ?? '');
                    $url = (string) ($item['url'] ?? '');
                    return $mt === '2'
                        || str_contains($mt, 'fb')
                        || str_contains($mt, 'facebook')
                        || str_starts_with($id, 'fb-')
                        || str_contains($url, 'facebook.com');
                }));
                usort($data, fn($a, $b) =>
                    (int)($b['num_likes'] ?? $b['likes'] ?? 0)
                    - (int)($a['num_likes'] ?? $a['likes'] ?? 0)
                );
                Log::info('📋 Facebook fallback result', ['count' => count($data)]);
            }

            $normalised = array_map(fn($item) => $this->normaliseFacebook($item), is_array($data) ? $data : []);

            return response()->json([
                'success' => true,
                'data'    => $normalised,
                'meta'    => ['total' => count($normalised), 'platform' => 'facebook', 'sub' => $sub],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Facebook Top Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to fetch Facebook data'], 500);
        }
    }

    private function normaliseFacebook(array $item): array
    {
        $handle = $item['author_scr_name'] ?? $item['author_id'] ?? '';
        $name   = $item['author_name'] ?? $handle;
        return [
            '_platform'       => 'fb',
            'media_type_id'   => '2',
            'id'              => $item['id'] ?? $item['docid'] ?? '',
            'url'             => $item['url'] ?? '',
            'content'         => strip_tags($item['content'] ?? $item['name'] ?? ''),
            'author_name'     => $name,
            'author_handle'   => $handle,
            'avatar_url'      => ($item['image'] ?? ''),
            'date_created'    => $item['date_created'] ?? '',
            'num_likes'       => (int) ($item['likes'] ?? $item['num_likes'] ?? $item['freq'] ?? 0),
            'num_comments'    => (int) ($item['comments'] ?? $item['num_comments'] ?? 0),
            'num_shares'      => (int) ($item['shares'] ?? 0),
            'num_views'       => (int) ($item['views'] ?? $item['view_cnt'] ?? $item['num_views'] ?? 0),
            'num_followers'   => (int) ($item['num_followers'] ?? 0),
            'class_sentiment' => (string) ($item['sentiment'] ?? $item['sentiment_id'] ?? $item['class_sentiment'] ?? '0'),
            'mention_type'    => $item['mention_type'] ?? 'post',
            'hostname'        => 'facebook.com',
        ];
    }

    // ════════════════════════════════════════════════════════════════
    // YouTube Top Status
    // ════════════════════════════════════════════════════════════════
    public function ytbTopStatus(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $rows      = (int) $request->query('rows', 500);
            $start     = (int) $request->query('start', 0);

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID is required'], 400);
            }

            Log::info('▶️ YouTube Top Status Fetch', compact('projectId', 'startDate', 'endDate', 'rows', 'start'));

            $mentions = $this->mkClient->mentions($projectId, $startDate, $endDate, 0, 23, true, $start, $rows);

            $ytbItems = array_values(array_filter($mentions, function ($item) {
                $mt  = strtolower((string) ($item['media_type_id'] ?? $item['media_type'] ?? $item['tcode'] ?? ''));
                $id  = (string) ($item['id'] ?? $item['docid'] ?? '');
                $url = (string) ($item['url'] ?? '');
                return $mt === '4'
                    || str_contains($mt, 'ytb')
                    || str_contains($mt, 'youtube')
                    || str_starts_with($id, 'yt-')
                    || str_contains($url, 'youtube.com')
                    || str_contains($url, 'youtu.be');
            }));

            usort($ytbItems, fn($a, $b) =>
                (int)($b['num_likes'] ?? 0) - (int)($a['num_likes'] ?? 0)
            );

            $normalised = array_map(fn($item) => [
                '_platform'       => 'ytb',
                'media_type_id'   => '4',
                'id'              => $item['id'] ?? $item['docid'] ?? '',
                'url'             => $item['url'] ?? '',
                'content'         => strip_tags($item['content'] ?? ''),
                'author_name'     => $item['author_name'] ?? $item['author_scr_name'] ?? '',
                'author_handle'   => $item['author_scr_name'] ?? '',
                'avatar_url'      => '',
                'date_created'    => $item['date_created'] ?? '',
                'num_likes'       => (int) ($item['num_likes'] ?? 0),
                'num_comments'    => (int) ($item['num_comments'] ?? 0),
                'num_shares'      => (int) ($item['num_shares'] ?? 0),
                'num_views'       => (int) ($item['num_views'] ?? 0),
                'num_followers'   => 0,
                'class_sentiment' => (string) ($item['class_sentiment'] ?? '0'),
                'mention_type'    => $item['mention_type'] ?? 'video',
                'hostname'        => 'youtube.com',
            ], $ytbItems);

            Log::info('✅ YouTube Top Status fetched', ['total' => count($normalised)]);

            return response()->json([
                'success' => true,
                'data'    => $normalised,
                'meta'    => ['total' => count($normalised), 'platform' => 'youtube'],
            ]);

        } catch (\Exception $e) {
            Log::error('❌ YouTube Top Status Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => 'Failed to fetch YouTube data'], 500);
        }
    }

}






    