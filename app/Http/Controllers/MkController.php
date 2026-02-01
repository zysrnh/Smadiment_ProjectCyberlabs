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
     * 📊 DATA OVERVIEW - Dashboard ringkasan
     * 🔥 WITH PAGINATION FOR MENTIONS
     */
   /**
 * 📊 DATA OVERVIEW - OPTIMIZED VERSION
 */
public function dataOverview(Request $request, MediaKernelsClient $mk)
{
    $projects = $this->getProjects($mk);
    $params = $this->getParams($request);
    $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

    // Default empty data
    $data = [
        'trendingTopics' => ['data' => []],
        'topHashtags' => ['data' => []],
        'mentionSocialMedia' => 0,
        'mentionOnlineNews' => 0,
        'activeUsers' => ['data' => []],
        'sentimentTimeline' => [
            'dates' => [], 'values' => [],
            'sentiment' => ['positive' => [], 'neutral' => [], 'negative' => []]
        ],
        'geoUsers' => ['locality' => ['rows' => []]]
    ];

    if (!$projectId) {
        return view('mk.data-overview', array_merge($data, [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'startDate' => $params['startDate'],
            'endDate' => $params['endDate'],
        ]));
    }

    // ── TRENDING TOPICS ──
    try {
        $rawTopics = $mk->recentTopics('internasional', 10);
        if (isset($rawTopics['data']) && is_array($rawTopics['data'])) {
            $data['trendingTopics'] = $rawTopics;
        } elseif (is_array($rawTopics) && !empty($rawTopics)) {
            $data['trendingTopics'] = ['data' => array_values($rawTopics)];
        }
    } catch (\Exception $e) {
        Log::warning('dataOverview: recentTopics failed', ['error' => $e->getMessage()]);
    }

    // ── TOP HASHTAGS ──
    try {
        $rawHashtags = $mk->topHashtags($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
        
        $rawItems = $rawHashtags['data'] ?? (is_array($rawHashtags) ? $rawHashtags : []);
        $normalized = [];
        foreach ($rawItems as $item) {
            if (!is_array($item)) continue;
            $normalized[] = [
                'hashtag' => $item['name'] ?? $item['hashtag'] ?? $item['tag'] ?? 'unknown',
                'mention' => (int)($item['size'] ?? $item['mention'] ?? $item['count'] ?? 0),
            ];
        }
        usort($normalized, fn($a, $b) => $b['mention'] <=> $a['mention']);
        
        $data['topHashtags'] = ['data' => $normalized];
    } catch (\Exception $e) {
        Log::warning('dataOverview: topHashtags failed', ['error' => $e->getMessage()]);
    }

    // ── 🔥 OPTIMIZED MENTIONS - USE PROJECT STATS INSTEAD ──
    try {
        Log::info('📊 Fetching mention counts using projectStats');
        
        // Get social media stats
        $socialStats = $mk->projectStats(
            $projectId,
            'all', // or 'twit,fb,ig,yt,tiktok'
            $params['startDate'],
            $params['endDate'],
            $params['startTime'],
            $params['endTime'],
            'volumetotal'
        );
        
        // Get news stats
        $newsStats = $mk->projectStats(
            $projectId,
            'onlinenews',
            $params['startDate'],
            $params['endDate'],
            $params['startTime'],
            $params['endTime'],
            'volumetotal'
        );
        
        $socialTotal = $this->extractTotal($socialStats);
        $newsTotal = $this->extractTotal($newsStats);
        
        // Social media = All - News
        $data['mentionSocialMedia'] = max(0, $socialTotal - $newsTotal);
        $data['mentionOnlineNews'] = $newsTotal;
        
        Log::info('📊 Mention counts (projectStats method)', [
            'all' => $socialTotal,
            'news' => $newsTotal,
            'social' => $data['mentionSocialMedia']
        ]);
        
    } catch (\Exception $e) {
        Log::error('dataOverview: projectStats failed', ['error' => $e->getMessage()]);
        
        // ── FALLBACK: Try limited mentions fetch ──
        try {
            Log::info('📊 Fallback: Using limited mentions fetch');
            
            $mentionCounts = $this->fetchMentionCountsOptimized($projectId, $params, $mk);
            $data['mentionSocialMedia'] = $mentionCounts['social'];
            $data['mentionOnlineNews'] = $mentionCounts['news'];
            
        } catch (\Exception $e2) {
            Log::error('dataOverview: fallback mentions also failed', ['error' => $e2->getMessage()]);
        }
    }

    // ── ACTIVE USERS ──
    try {
        $rawUsers = $mk->mostActiveUsers($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);

        $userData = $rawUsers['data']['data'] ?? $rawUsers['data'] ?? $rawUsers;
        if (!empty($userData) && is_array($userData)) {
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
            $data['activeUsers'] = ['data' => array_slice($rows, 0, 6)];
        }
    } catch (\Exception $e) {
        Log::warning('dataOverview: mostActiveUsers failed', ['error' => substr($e->getMessage(), 0, 200)]);
    }

    // ── SENTIMENT TIMELINE ──
    try {
        $data['sentimentTimeline'] = $this->extractDailyTimeline($projectId, $mk);
    } catch (\Exception $e) {
        Log::warning('dataOverview: sentiment timeline failed', ['error' => $e->getMessage()]);
    }

    // ── GEO USERS ──
    try {
        $rawGeo = $mk->geoTwitterUser($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
        $data['geoUsers'] = $rawGeo;
    } catch (\Exception $e) {
        Log::warning('dataOverview: geoTwitterUser failed', ['error' => $e->getMessage()]);
    }

    return view('mk.data-overview', array_merge($data, [
        'projects' => $projects,
        'projectId' => $projectId,
        'params' => $params,
        'startDate' => $params['startDate'],
        'endDate' => $params['endDate'],
    ]));
}

/**
 * 🔥 NEW: Optimized mention counting with smart sampling
 */
/**
 * 🔥 NEW: Optimized mention counting - FIXED for STRING media_type_id
 */
private function fetchMentionCountsOptimized($projectId, $params, MediaKernelsClient $mk): array
{
    // 🔥 IMPORTANT: API returns media_type_id as STRING, not integer!
    $socialMediaIds = ['1', '2', '5', '6', '7', '8']; // STRING array
    $newsMediaIds = ['4', '9', '10']; // STRING array
    
    $counts = ['social' => 0, 'news' => 0];
    
    try {
        $firstBatch = $mk->mentions(
            $projectId,
            $params['startDate'],
            $params['endDate'],
            $params['startTime'],
            $params['endTime'],
            false,
            0,
            1000
        );
        
        $totalMentions = (int)($firstBatch['total'] ?? $firstBatch['numFound'] ?? 0);
        $batchData = $firstBatch['data'] ?? [];
        
        Log::info('📊 Mention batch received', [
            'total' => $totalMentions,
            'batch_size' => count($batchData),
            'sample_item' => $batchData[0] ?? null
        ]);
        
        // If total is small enough (<= 5000), fetch all
        if ($totalMentions <= 5000) {
            $allMentions = $batchData;
            
            $batches = ceil($totalMentions / 1000);
            for ($i = 1; $i < $batches && $i < 5; $i++) {
                $batch = $mk->mentions(
                    $projectId,
                    $params['startDate'],
                    $params['endDate'],
                    $params['startTime'],
                    $params['endTime'],
                    false,
                    $i * 1000,
                    1000
                );
                
                $allMentions = array_merge($allMentions, $batch['data'] ?? []);
                usleep(50000);
            }
            
            // 🔥 Count from all data - FIXED: use STRING comparison
            foreach ($allMentions as $item) {
                if (!is_array($item)) continue;
                
                // Get as STRING (API returns string)
                $mediaTypeId = (string)($item['media_type_id'] ?? '');
                
                if (in_array($mediaTypeId, $socialMediaIds, true)) {
                    $counts['social']++;
                } elseif (in_array($mediaTypeId, $newsMediaIds, true)) {
                    $counts['news']++;
                }
            }
            
            Log::info('📊 Counted all mentions', [
                'total_items' => count($allMentions),
                'social' => $counts['social'],
                'news' => $counts['news']
            ]);
            
        } else {
            // For large datasets, use sampling
            Log::info('📊 Using sampling for large dataset');
            
            $sampleSize = min(count($batchData), 1000);
            $sample = array_slice($batchData, 0, $sampleSize);
            
            $sampleCounts = ['social' => 0, 'news' => 0];
            foreach ($sample as $item) {
                if (!is_array($item)) continue;
                
                $mediaTypeId = (string)($item['media_type_id'] ?? '');
                
                if (in_array($mediaTypeId, $socialMediaIds, true)) {
                    $sampleCounts['social']++;
                } elseif (in_array($mediaTypeId, $newsMediaIds, true)) {
                    $sampleCounts['news']++;
                }
            }
            
            $sampleTotal = $sampleCounts['social'] + $sampleCounts['news'];
            if ($sampleTotal > 0) {
                $socialRatio = $sampleCounts['social'] / $sampleTotal;
                $newsRatio = $sampleCounts['news'] / $sampleTotal;
                
                $counts['social'] = round($totalMentions * $socialRatio);
                $counts['news'] = round($totalMentions * $newsRatio);
            }
            
            Log::info('📊 Extrapolated counts', [
                'sample' => $sampleCounts,
                'extrapolated' => $counts
            ]);
        }
        
    } catch (\Exception $e) {
        Log::error('fetchMentionCountsOptimized failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
    }
    
    return $counts;
}
}