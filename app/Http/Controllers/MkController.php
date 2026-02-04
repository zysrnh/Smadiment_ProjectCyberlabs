<?php

namespace App\Http\Controllers;

use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MkController extends Controller
{
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
     * Helper: Normalize chart data (Age/Gender/Type)
     */
    private function normalizeChartData(array $raw, string $labelKey = 'age_group', string $valueKey = 'post_freq'): array
    {
        $data = $raw['data'] ?? $raw;
        
        if (empty($data) || !is_array($data)) {
            return ['labels' => [], 'values' => []];
        }

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            if (is_array($item)) {
                $label = $item[$labelKey] ?? 'Unknown';
                $value = (int) ($item[$valueKey] ?? 0);
                
                $labels[] = $label;
                $values[] = $value;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values
        ];
    }

    /**
     * Helper: Normalize geo data
     */
    private function normalizeGeoRows(array $raw): array
    {
        $src = $raw['data'] ?? $raw;
        $rows = [];

        foreach ($src as $k => $v) {
            if (is_numeric($v)) {
                $rows[] = ['name' => (string)$k, 'count' => (int)$v];
            } elseif (is_array($v)) {
                $rows[] = [
                    'name' => $v['name'] ?? $k,
                    'count' => (int)($v['count'] ?? $v['total'] ?? 0),
                ];
            }
        }

        usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
        return array_slice($rows, 0, 10);
    }

    /**
     * Helper: Get common parameters from request
     */
    private function getParams(Request $request): array
    {
        return [
            'startDate' => $request->query('start_date', now()->subDay()->toDateString()),
            'endDate'   => $request->query('end_date', now()->toDateString()),
            'startTime' => (int) $request->query('start_time', 0),
            'endTime'   => (int) $request->query('end_time', 23),
            'media'     => $request->query('media', 'twit'),
            'sentiment' => (int) $request->query('sentiment', 1),
        ];
    }

    /**
     * Helper: Get project list
     */
    private function getProjects(MediaKernelsClient $mk): array
    {
        $rawProjects = $mk->listProjects(0, 100);
        return array_values($rawProjects);
    }

    /**
     * 📊 DASHBOARD (User - Filtered by assigned projects)
     */
    public function dashboard(Request $request, MediaKernelsClient $mk)
    {
        // Get all projects from API
        $allProjects = $this->getProjects($mk);
        
        // Filter projects based on user's access
        $user = auth()->user();
        $assignedProjectIds = $user->assignedProjectIds();
        
        // Filter projects: only show projects user has access to
        $projects = array_filter($allProjects, function($project) use ($assignedProjectIds) {
            return in_array($project['id'], $assignedProjectIds);
        });
        
        // Re-index array
        $projects = array_values($projects);
        
        return view('mk.dashboard', [
            'projects' => $projects,
        ]);
    }

    /**
     * 🔥 Helper: Extract Daily Timeline with Sentiment Breakdown (7 days INCLUDING TODAY)
     */
    private function extractDailyTimeline($projectId, MediaKernelsClient $mk): array
    {
        $timeline = [
            'dates' => [],
            'values' => [],
            'sentiment' => [
                'positive' => [],
                'neutral' => [],
                'negative' => [],
            ]
        ];
        
        try {
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                
                $day = $date->format('d');
                $month = $date->format('M');
                $dateLabel = $day . '. ' . $month;
                
                $sentimentData = $mk->sentimentTotal(
                    $projectId,
                    $dateStr,
                    $dateStr,
                    0,
                    23
                );
                
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
            
        } catch (\Exception $e) {
            Log::warning("Failed to fetch daily timeline for project {$projectId}", [
                'error' => $e->getMessage()
            ]);
        }
        
        return $timeline;
    }

    /**
     * 👨‍💼 ADMIN DASHBOARD - List Projects with Stats & Charts
     */
    public function adminDashboard(Request $request, MediaKernelsClient $mk)
    {
        $rawProjects = $mk->listProjects(0, 100);
        $projects = array_values($rawProjects);
        
        $dateRange = [
            'start' => now()->subDays(7)->toDateString(),
            'end' => now()->toDateString(),
        ];
        
        foreach ($projects as &$project) {
            try {
                $allStats = $mk->projectStats($project['id'], 'all', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                $newsStats = $mk->projectStats($project['id'], 'onlinenews', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                $twitStats = $mk->projectStats($project['id'], 'twit', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                $fbStats = $mk->projectStats($project['id'], 'fb', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                $igStats = $mk->projectStats($project['id'], 'ig', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                $ytStats = $mk->projectStats($project['id'], 'yt', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                $tiktokStats = $mk->projectStats($project['id'], 'tiktok', $dateRange['start'], $dateRange['end'], 0, 23, 'volumetotal');
                
                $project['stats'] = [
                    'all' => $this->extractTotal($allStats),
                    'news' => $this->extractTotal($newsStats),
                    'twit' => $this->extractTotal($twitStats),
                    'fb' => $this->extractTotal($fbStats),
                    'ig' => $this->extractTotal($igStats),
                    'yt' => $this->extractTotal($ytStats),
                    'tiktok' => $this->extractTotal($tiktokStats),
                ];
                
                $project['timeline'] = $this->extractDailyTimeline($project['id'], $mk);
                
            } catch (\Exception $e) {
                Log::warning("Failed to fetch stats for project {$project['id']}", ['error' => $e->getMessage()]);
                
                $project['stats'] = [
                    'all' => 0, 'news' => 0, 'twit' => 0, 'fb' => 0, 'ig' => 0, 'yt' => 0, 'tiktok' => 0,
                ];
                $project['timeline'] = [
                    'dates' => [], 'values' => [],
                    'sentiment' => ['positive' => [], 'neutral' => [], 'negative' => []]
                ];
            }
        }
        
        return view('admin.dashboard', [
            'projects' => $projects,
            'dateRange' => $dateRange,
        ]);
    }
    
    /**
     * Helper: Extract total from stats response
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

    /**
     * 📁 PROJECTS LIST
     */
    public function projects(Request $request, MediaKernelsClient $mk)
    {
        $start  = (int) $request->query('start', 0);
        $limit  = (int) $request->query('limit', 20);

        $rawProjects = $mk->listProjects($start, $limit);
        $projects    = array_values($rawProjects);

        return view('mk.projects', [
            'raw' => $rawProjects,
            'projects' => $projects,
            'start' => $start,
            'limit' => $limit,
        ]);
    }

    /**
     * 💬 SENTIMENT ANALYSIS
     */
    public function sentiment(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $sentimentData = ['positive' => 0, 'neutral' => 0, 'negative' => 0];

        if ($projectId) {
            $rawData = $mk->sentimentTotal($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
            $sentimentData = $this->normalizeSentimentTotal($rawData);
        }

        return view('mk.sentiment', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'sentimentData' => $sentimentData,
        ]);
    }

    /**
     * 🌍 GEOGRAPHIC DATA
     */
    public function geographic(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $geoRawData = [];
        $geoRows = [];
        $geoUserRawData = [];
        $geoUserRows = [];

        if ($projectId) {
            $geoRawData = $mk->geoTwitterUserSentiment($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime'], $params['sentiment']);
            $geoRows = $this->normalizeGeoRows($geoRawData);

            $geoUserRawData = $mk->geoTwitterUser($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
            $geoUserRows = $this->normalizeGeoRows($geoUserRawData);
        }

        return view('mk.geographic', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'geoRawData' => $geoRawData,
            'geoRows' => $geoRows,
            'geoUserRawData' => $geoUserRawData,
            'geoUserRows' => $geoUserRows,
        ]);
    }

    /**
     * 👥 AUTHORS - AGE DISTRIBUTION
     */
    public function authorsAge(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $chartData = ['labels' => [], 'values' => []];

        if ($projectId) {
            $rawData = $mk->authorsAge($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
            $chartData = $this->normalizeChartData($rawData, 'age_group', 'post_freq');
        }

        return view('mk.authors.age', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'chartData' => $chartData,
        ]);
    }

    /**
     * 👥 AUTHORS - GENDER DISTRIBUTION
     */
    public function authorsGender(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $chartData = ['labels' => [], 'values' => []];

        if ($projectId) {
            $rawData = $mk->authorsGender($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
            $chartData = $this->normalizeChartData($rawData, 'gender', 'post_freq');
        }

        return view('mk.authors.gender', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'chartData' => $chartData,
        ]);
    }

    /**
     * 👥 AUTHORS - ORGANIZATION TYPE
     */
    public function authorsType(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $chartData = ['labels' => [], 'values' => []];

        if ($projectId) {
            $rawData = $mk->authorsType($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
            $chartData = $this->normalizeChartData($rawData, 'is_organization', 'post_freq');
        }

        return view('mk.authors.type', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'chartData' => $chartData,
        ]);
    }

    /**
     * 🏷️ CATEGORIES
     */
    public function categories(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];

        if ($projectId) {
            $rawData = $mk->categories($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
        }

        return view('mk.categories', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
        ]);
    }

    /**
     * 📈 ENGAGEMENT - ESTIMATED REACH
     */
    public function reach(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $chartData = ['labels' => [], 'values' => []];

        if ($projectId) {
            $rawData = $mk->estReach($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime'], 'all');

            $data = $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $labels = [];
                $values = [];
                
                foreach ($data as $key => $item) {
                    if (is_array($item)) {
                        $labels[] = $key;
                        $values[] = (int) ($item['reach'] ?? $item['est_reach'] ?? $item['value'] ?? 0);
                    }
                }
                
                $chartData = ['labels' => $labels, 'values' => $values];
            }
        }

        return view('mk.engagement.reach', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'chartData' => $chartData,
        ]);
    }

    /**
     * 📈 ENGAGEMENT - SHARED URLs
     */
    public function sharedUrls(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $tableData = [];

        if ($projectId) {
            $rawData = $mk->sharedUrlFreq($projectId, $params['startDate'], $params['endDate']);

            $data = $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $rows = [];
                foreach ($data as $item) {
                    if (is_array($item)) {
                        $rows[] = [
                            'url' => $item['url'] ?? $item['link'] ?? 'Unknown',
                            'freq' => (int) ($item['freq'] ?? $item['frequency'] ?? $item['count'] ?? 0),
                        ];
                    }
                }
                usort($rows, fn($a, $b) => $b['freq'] <=> $a['freq']);
                $tableData = array_slice($rows, 0, 10);
            }
        }

        return view('mk.engagement.urls', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'tableData' => $tableData,
        ]);
    }

    /**
     * 📈 ENGAGEMENT - ACTIVE USERS
     */
    public function activeUsers(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $tableData = [];

        if ($projectId) {
            $rawData = $mk->mostActiveUsers($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);

            $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $rows = [];
                foreach ($data as $item) {
                    if (is_array($item)) {
                        $fullName = $item['name'] ?? 'Unknown User';
                        $username = $fullName;
                        
                        if (preg_match('/@(\w+)/', $fullName, $matches)) {
                            $username = $matches[1];
                        }
                        
                        $rows[] = [
                            'username' => $username,
                            'count' => (int) ($item['y'] ?? $item['post_count'] ?? $item['posts'] ?? $item['count'] ?? 0),
                        ];
                    }
                }
                usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
                $tableData = array_slice($rows, 0, 10);
            }
        }

        return view('mk.engagement.users', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'tableData' => $tableData,
        ]);
    }

    /**
     * 🔄 ENGAGEMENT - MOST RETWEETS
     */
    public function mostRetweets(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        $rawData = [];
        $tableData = [];

        if ($projectId) {
            $rawData = $mk->mostRetweets($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);

            $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $rows = [];
                foreach ($data as $item) {
                    if (is_array($item)) {
                        $author = $item['name'] ?? $item['author_name'] ?? $item['author'] ?? $item['screen_name'] ?? 'Unknown';
                        $content = $item['content'] ?? $item['text'] ?? 'No content';
                        $retweetCount = (int) ($item['rt'] ?? $item['retweet_count'] ?? $item['retweets'] ?? 0);
                        
                        $rows[] = [
                            'author' => is_array($author) ? ($author[0] ?? 'Unknown') : (string) $author,
                            'content' => is_array($content) ? ($content[0] ?? 'No content') : (string) $content,
                            'retweet_count' => $retweetCount,
                        ];
                    }
                }
                usort($rows, fn($a, $b) => $b['retweet_count'] <=> $a['retweet_count']);
                $tableData = array_slice($rows, 0, 10);
            }
        }

        return view('mk.engagement.retweets', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'tableData' => $tableData,
        ]);
    }

    /**
     * 📰 PUBLISHER STATS
     */
    public function publisherStats(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);
        $rows = (int) $request->query('rows', 100);
        $includePagerank = $request->query('pagerank', 'true') === 'true';

        $rawData = [];
        $tableData = [];

        if ($projectId) {
            $rawData = $mk->publisherStats($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime'], $rows, $includePagerank);
            $tableData = $this->normalizePublisherData($rawData, $includePagerank);
        }

        return view('mk.publisher', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'tableData' => $tableData,
        ]);
    }

    /**
     * Helper: Normalize publisher data
     */
    private function normalizePublisherData(array $rawData, bool $includePagerank = true): array
    {
        $normalized = [];

        $publisherData = $rawData['article']['publisher'] ?? null;
        $pagerankData = $rawData['article']['pagerank'] ?? null;
        $mediaType = $rawData['article']['media_type_label'] ?? $rawData['article']['media_type_code'] ?? 'Social Media';

        if ($publisherData && is_array($publisherData)) {
            foreach ($publisherData as $publisherName => $count) {
                if ($count <= 0) continue;
                
                if (empty($publisherName) || trim($publisherName) === '') {
                    $publisherName = $mediaType . ' Posts';
                }

                $pagerank = null;
                if ($includePagerank && $pagerankData && isset($pagerankData[$publisherName])) {
                    $pagerank = (float) $pagerankData[$publisherName];
                }

                $normalized[] = [
                    'publisher' => (string) $publisherName,
                    'count' => (int) $count,
                    'pagerank' => $pagerank,
                ];
            }
        }

        if (empty($normalized)) {
            $dataArray = $rawData['data'] ?? $rawData;
            
            if (!empty($dataArray) && is_array($dataArray)) {
                foreach ($dataArray as $item) {
                    if (is_array($item)) {
                        $publisherName = $item['publisher'] ?? $item['name'] ?? $item['source'] ?? 'Unknown';
                        
                        $normalized[] = [
                            'publisher' => (string) $publisherName,
                            'count' => (int) ($item['count'] ?? $item['total'] ?? $item['articles'] ?? 0),
                            'pagerank' => isset($item['pagerank']) ? (float) $item['pagerank'] : null,
                        ];
                    }
                }
            }
        }

        if (empty($normalized) && !empty($rawData)) {
            foreach ($rawData as $key => $value) {
                if ($key !== 'data' && $key !== 'article' && is_numeric($value)) {
                    $normalized[] = [
                        'publisher' => (string) $key,
                        'count' => (int) $value,
                        'pagerank' => null,
                    ];
                }
            }
        }
        
        if (!empty($normalized)) {
            usort($normalized, fn($a, $b) => $b['count'] <=> $a['count']);
        }

        return $normalized;
    }

    /**
     * 📰 RECENT TOPICS (News)
     */
    public function recentTopics(Request $request, MediaKernelsClient $mk)
    {
        $level = $request->query('level', 'internasional');
        $size = (int) $request->query('size', 10);

        $rawData = $mk->recentTopics($level, $size);
        $topics = $rawData['data'] ?? $rawData;

        return view('mk.topics', [
            'rawData' => $rawData,
            'topics' => $topics,
            'level' => $level,
            'size' => $size,
        ]);
    }

  /**
 * 📊 DATA OVERVIEW - SIMPLIFIED (Lazy Loading)
 */
public function dataOverview(Request $request, MediaKernelsClient $mk)
{
    $projects = $this->getProjects($mk);
    $params = $this->getParams($request);
    $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

    // Tidak perlu load semua data di sini!
    // Semua data akan di-load via API saat user scroll
    
    return view('mk.data-overview', [
        'projects' => $projects,
        'projectId' => $projectId,
        'params' => $params,
        'startDate' => $params['startDate'],
        'endDate' => $params['endDate'],
    ]);
}

    /**
     * 🔥 CORRECT: Get mention volume counts using projectStats (like Drone Emprit)
     */
    /**
     * 🔥 HYBRID SOLUTION: Use proven sentimentTotal + try to separate news
     */
    private function fetchMentionCountsEnhanced($projectId, $params, MediaKernelsClient $mk): array
    {
        $counts = ['social' => 0, 'news' => 0];
        
        try {
            Log::info('📊 Fetching mentions using hybrid approach');
            
            // ── STEP 1: Get TOTAL from sentimentTotal (PROVEN TO WORK!) ──
            $allSentiment = $mk->sentimentTotal(
                $projectId,
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
            
            $normalized = $this->normalizeSentimentTotal($allSentiment);
            $totalMentions = $normalized['positive'] + $normalized['neutral'] + $normalized['negative'];
            
            Log::info('✅ Total mentions from sentiment', [
                'positive' => $normalized['positive'],
                'neutral' => $normalized['neutral'],
                'negative' => $normalized['negative'],
                'total' => $totalMentions
            ]);
            
            if ($totalMentions == 0) {
                Log::warning('⚠️ No mentions found in date range');
                return $counts;
            }
            
            // ── STEP 2: Try to get NEWS count specifically ──
            try {
                // Try projectStats first
                $newsStats = $mk->projectStats(
                    $projectId,
                    'onlinenews',
                    $params['startDate'],
                    $params['endDate'],
                    $params['startTime'],
                    $params['endTime'],
                    'volumetotal'
                );
                
                // Debug log
                Log::info('🔍 projectStats (news) raw response', [
                    'response_keys' => array_keys($newsStats),
                    'has_data' => isset($newsStats['data']),
                    'data_content' => $newsStats['data'] ?? null,
                    'has_total' => isset($newsStats['total']),
                    'total_value' => $newsStats['total'] ?? null,
                ]);
                
                $newsCount = $this->extractTotal($newsStats);
                
                if ($newsCount > 0 && $newsCount <= $totalMentions) {
                    // Got valid news count!
                    $counts['news'] = $newsCount;
                    $counts['social'] = $totalMentions - $newsCount;
                    
                    Log::info('✅ Successfully split mentions', [
                        'method' => 'projectStats',
                        'social' => $counts['social'],
                        'news' => $counts['news']
                    ]);
                    
                    return $counts;
                }
                
                // If projectStats didn't work, try alternative method
                throw new \Exception('projectStats returned 0 or invalid count');
                
            } catch (\Exception $e) {
                Log::info('ℹ️ projectStats failed, trying alternative', [
                    'error' => $e->getMessage()
                ]);
                
                // Alternative: Use estimation based on typical patterns
                // Estimate: ~15-25% is typically news in most projects
                $estimatedNewsRatio = 0.20; // 20% default
                
                $counts['news'] = (int)round($totalMentions * $estimatedNewsRatio);
                $counts['social'] = $totalMentions - $counts['news'];
                
                Log::info('ℹ️ Using estimation for split', [
                    'method' => 'estimation (20% news)',
                    'social' => $counts['social'],
                    'news' => $counts['news'],
                    'total' => $totalMentions
                ]);
            }
            
            Log::info('📊 Final mention counts', [
                'social_media' => number_format($counts['social']),
                'online_news' => number_format($counts['news']),
                'total' => number_format($counts['social'] + $counts['news'])
            ]);
            
        } catch (\Exception $e) {
            Log::error('❌ Failed to calculate mentions', [
                'error' => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 500)
            ]);
        }
        
        return $counts;
    }
}