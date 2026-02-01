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
            // 🔥 FIXED: Generate last 6 days + TODAY (total 7 days)
            // Loop dari 6 hari yang lalu sampai hari ini
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $dateStr = $date->format('Y-m-d');
                
                // 🔥 Format tanggal lebih jelas: "02. Feb" 
                $day = $date->format('d');
                $month = $date->format('M');
                $dateLabel = $day . '. ' . $month;
                
                Log::info("Fetching sentiment for date", [
                    'date' => $dateStr,
                    'label' => $dateLabel,
                    'is_today' => $date->isToday()
                ]);
                
                // Fetch sentiment data for this specific date
                $sentimentData = $mk->sentimentTotal(
                    $projectId,
                    $dateStr,
                    $dateStr, // same date for single day
                    0,
                    23
                );
                
                // Normalize sentiment response
                $normalized = $this->normalizeSentimentTotal($sentimentData);
                
                $pos = $normalized['positive'];
                $neu = $normalized['neutral'];
                $neg = $normalized['negative'];
                $total = $pos + $neu + $neg;
                
                // Add to timeline
                $timeline['dates'][] = $dateLabel;
                $timeline['values'][] = $total;
                $timeline['sentiment']['positive'][] = $pos;
                $timeline['sentiment']['neutral'][] = $neu;
                $timeline['sentiment']['negative'][] = $neg;
                
                Log::info("Sentiment data for {$dateLabel}", [
                    'total' => $total,
                    'pos' => $pos,
                    'neu' => $neu,
                    'neg' => $neg
                ]);
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
        
        // Enrich each project with stats data
        $dateRange = [
            'start' => now()->subDays(7)->toDateString(),
            'end' => now()->toDateString(),
        ];
        
        foreach ($projects as &$project) {
            try {
                // Get project stats for each media type
                $allStats = $mk->projectStats(
                    $project['id'],
                    'all',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                $newsStats = $mk->projectStats(
                    $project['id'],
                    'onlinenews',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                $twitStats = $mk->projectStats(
                    $project['id'],
                    'twit',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                $fbStats = $mk->projectStats(
                    $project['id'],
                    'fb',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                $igStats = $mk->projectStats(
                    $project['id'],
                    'ig',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                $ytStats = $mk->projectStats(
                    $project['id'],
                    'yt',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                $tiktokStats = $mk->projectStats(
                    $project['id'],
                    'tiktok',
                    $dateRange['start'],
                    $dateRange['end'],
                    0,
                    23,
                    'volumetotal'
                );
                
                // Extract totals
                $project['stats'] = [
                    'all' => $this->extractTotal($allStats),
                    'news' => $this->extractTotal($newsStats),
                    'twit' => $this->extractTotal($twitStats),
                    'fb' => $this->extractTotal($fbStats),
                    'ig' => $this->extractTotal($igStats),
                    'yt' => $this->extractTotal($ytStats),
                    'tiktok' => $this->extractTotal($tiktokStats),
                ];
                
                // 🔥 Extract DAILY timeline with sentiment breakdown (7 days)
                $project['timeline'] = $this->extractDailyTimeline($project['id'], $mk);
                
            } catch (\Exception $e) {
                Log::warning("Failed to fetch stats for project {$project['id']}", [
                    'error' => $e->getMessage()
                ]);
                
                // Fallback to zeros
                $project['stats'] = [
                    'all' => 0,
                    'news' => 0,
                    'twit' => 0,
                    'fb' => 0,
                    'ig' => 0,
                    'yt' => 0,
                    'tiktok' => 0,
                ];
                $project['timeline'] = [
                    'dates' => [],
                    'values' => [],
                    'sentiment' => [
                        'positive' => [],
                        'neutral' => [],
                        'negative' => [],
                    ]
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
        
        // Sum from timeline if available
        if (isset($stats['data']) && is_array($stats['data'])) {
            return array_sum(array_map(fn($v) => (int)$v, $stats['data']));
        }
        
        return 0;
    }
    
    /**
     * Helper: Extract timeline data for chart
     */
    private function extractTimeline(array $stats): array
    {
        $timeline = [
            'dates' => [],
            'values' => []
        ];
        
        if (isset($stats['data']) && is_array($stats['data'])) {
            foreach ($stats['data'] as $date => $value) {
                if (is_numeric($value)) {
                    $timeline['dates'][] = date('d M', strtotime($date));
                    $timeline['values'][] = (int) $value;
                }
            }
        }
        
        return $timeline;
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
            $rawData = $mk->sentimentTotal(
                $projectId,
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
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
            // Geographic by sentiment
            $geoRawData = $mk->geoTwitterUserSentiment(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime'],
                $params['sentiment']
            );
            $geoRows = $this->normalizeGeoRows($geoRawData);

            // Geographic all users
            $geoUserRawData = $mk->geoTwitterUser(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
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
            $rawData = $mk->authorsAge(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
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
            $rawData = $mk->authorsGender(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
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
            $rawData = $mk->authorsType(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
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
            $rawData = $mk->categories(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
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
            $rawData = $mk->estReach(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime'],
                'all'
            );

            // Normalize reach data
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
            $rawData = $mk->sharedUrlFreq(
                $projectId,
                $params['startDate'],
                $params['endDate']
            );

            // Normalize URL data
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
            $rawData = $mk->mostActiveUsers(
                $projectId,
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );

            // Normalize users data
            $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $rows = [];
                foreach ($data as $item) {
                    if (is_array($item)) {
                        // Parse name to get username
                        $fullName = $item['name'] ?? 'Unknown User';
                        $username = $fullName;
                        
                        // Extract username from "Name @username" format
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
            $rawData = $mk->mostRetweets(
                $projectId,
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );

            // Normalize retweets data
            $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $rows = [];
                foreach ($data as $item) {
                    if (is_array($item)) {
                        // API uses 'name' field for author (based on actual response)
                        $author = $item['name'] ?? $item['author_name'] ?? $item['author'] ?? $item['screen_name'] ?? 'Unknown';
                        
                        // API uses 'content' field for tweet text
                        $content = $item['content'] ?? $item['text'] ?? 'No content';
                        
                        // API uses 'rt' field for retweet count
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
     * 📰 PUBLISHER STATS - IMPROVED WITH BETTER NORMALIZATION
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
            $rawData = $mk->publisherStats(
                $projectId,
                $params['media'],
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime'],
                $rows,
                $includePagerank
            );

            // 🔥 IMPROVED NORMALIZATION - Handle multiple response structures
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
     * 🔥 NEW HELPER: Normalize publisher data with multiple fallback strategies
     */
    private function normalizePublisherData(array $rawData, bool $includePagerank = true): array
    {
        $normalized = [];

        // Strategy 1: Check for nested article.publisher structure
        $publisherData = $rawData['article']['publisher'] ?? null;
        $pagerankData = $rawData['article']['pagerank'] ?? null;
        
        // Get media type for fallback naming
        $mediaType = $rawData['article']['media_type_label'] ?? 
                     $rawData['article']['media_type_code'] ?? 
                     'Social Media';

        if ($publisherData && is_array($publisherData)) {
            // Handle associative array: {"Publisher Name": count}
            foreach ($publisherData as $publisherName => $count) {
                // Skip if count is 0 or negative
                if ($count <= 0) {
                    continue;
                }
                
                // Better handling for empty publisher names
                if (empty($publisherName) || trim($publisherName) === '') {
                    // Use media type as fallback instead of "Unknown Publisher"
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

        // Strategy 2: Check for direct data array
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

        // Strategy 3: Check for publishers as top-level key-value pairs
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
        
        // Sort by count descending
        if (!empty($normalized)) {
            usort($normalized, fn($a, $b) => $b['count'] <=> $a['count']);
        }

        // Log the normalization result for debugging
        Log::info('Publisher data normalized', [
            'input_keys' => array_keys($rawData),
            'output_count' => count($normalized),
            'sample' => array_slice($normalized, 0, 3)
        ]);

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
     * 📊 DATA OVERVIEW - Dashboard ringkasan dengan Sentiment Timeline
     */
    public function dataOverview(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        $params = $this->getParams($request);
        $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

        // Initialize empty data
        $trendingTopics = ['data' => []];
        $topHashtags = ['data' => []];
        $mentionSocialMedia = 0;
        $mentionOnlineNews = 0;
        $activeUsers = ['data' => []];
        $sentimentTimeline = [
            'dates' => [],
            'values' => [],
            'sentiment' => [
                'positive' => [],
                'neutral' => [],
                'negative' => [],
            ]
        ];
        $geoUsers = ['data' => []];

        if ($projectId) {
            // ── TRENDING TOPICS (News - public endpoint) ──
            try {
                $rawTopics = $mk->recentTopics('internasional', 10);
                
                if (isset($rawTopics['data']) && is_array($rawTopics['data'])) {
                    $trendingTopics = $rawTopics;
                } elseif (is_array($rawTopics) && !empty($rawTopics)) {
                    $trendingTopics = ['data' => array_values($rawTopics)];
                }
            } catch (\Exception $e) {
                Log::warning('dataOverview: recentTopics failed', ['error' => $e->getMessage()]);
            }

    // ── TOP HASHTAGS ──
try {
    $rawHashtags = $mk->topHashtags(
        $projectId,
        $params['media'],
        $params['startDate'],
        $params['endDate'],
        $params['startTime'],
        $params['endTime']
    );
    
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
    
    // 🔥 Ambil semua (atau top 20 jika mau dibatasi)
    $topHashtags = ['data' => $normalized]; // atau array_slice($normalized, 0, 20)
} catch (\Exception $e) {
    Log::warning('dataOverview: topHashtags failed', ['error' => $e->getMessage()]);
}

            // ── MENTIONS (untuk hitung Social Media vs Online News) ──
            try {
                $rawMentions = $mk->mentions(
                    $projectId,
                    $params['startDate'],
                    $params['endDate'],
                    $params['startTime'],
                    $params['endTime'],
                    false,
                    0,
                    100
                );

                $mentionsData = [];
                if (isset($rawMentions['data']) && is_array($rawMentions['data'])) {
                    $mentionsData = $rawMentions['data'];
                } elseif (is_array($rawMentions) && !empty($rawMentions)) {
                    $mentionsData = array_values($rawMentions);
                }

                foreach ($mentionsData as $item) {
                    if (!is_array($item)) continue;
                    
                    $mediaType = strtolower($item['media_type'] ?? $item['type'] ?? '');
                    
                    if (in_array($mediaType, ['twitter', 'x', 'facebook', 'fb', 'instagram', 'ig', 'youtube', 'yt', 'tiktok'])) {
                        $mentionSocialMedia++;
                    } elseif (in_array($mediaType, ['news', 'online_news', 'onlinenews'])) {
                        $mentionOnlineNews++;
                    }
                }
            } catch (\Exception $e) {
                Log::warning('dataOverview: mentions failed', ['error' => $e->getMessage()]);
            }

            // ── ACTIVE USERS (Most Engaged) ──
            try {
                $rawUsers = $mk->mostActiveUsers(
                    $projectId,
                    $params['startDate'],
                    $params['endDate'],
                    $params['startTime'],
                    $params['endTime']
                );

                $data = $rawUsers['data']['data'] ?? $rawUsers['data'] ?? $rawUsers;
                if (!empty($data) && is_array($data)) {
                    $rows = [];
                    foreach ($data as $item) {
                        if (!is_array($item)) continue;

                        $fullName = $item['name'] ?? 'Unknown User';
                        $username = $fullName;
                        if (preg_match('/@(\w+)/', $fullName, $matches)) {
                            $username = $matches[1];
                        }

                        $rows[] = [
                            'username' => $username,
                            'count'    => (int)($item['y'] ?? $item['post_count'] ?? $item['posts'] ?? $item['count'] ?? 0),
                        ];
                    }
                    usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
                    $activeUsers = ['data' => array_slice($rows, 0, 6)];
                }
            } catch (\Exception $e) {
                Log::warning('dataOverview: mostActiveUsers timeout/error', [
                    'error' => substr($e->getMessage(), 0, 200)
                ]);
            }

            // ── SENTIMENT TIMELINE - 🔥 Gunakan extractDailyTimeline yang sama seperti Admin ──
            try {
                $sentimentTimeline = $this->extractDailyTimeline($projectId, $mk);
                
                Log::info('dataOverview: sentiment timeline extracted', [
                    'dates_count' => count($sentimentTimeline['dates']),
                    'sample_date' => $sentimentTimeline['dates'][0] ?? 'none',
                ]);
            } catch (\Exception $e) {
                Log::warning('dataOverview: sentiment timeline failed', ['error' => $e->getMessage()]);
            }

            // ── GEO USERS (Buzzer Map) ──
            try {
                $rawGeo = $mk->geoTwitterUser(
                    $projectId,
                    $params['media'],
                    $params['startDate'],
                    $params['endDate'],
                    $params['startTime'],
                    $params['endTime']
                );

                $geoUsers = $rawGeo;
                
                Log::info('dataOverview: geoTwitterUser response', [
                    'has_country' => isset($rawGeo['country']),
                    'has_locality' => isset($rawGeo['locality']),
                    'locality_count' => isset($rawGeo['locality']['rows']) ? count($rawGeo['locality']['rows']) : 0
                ]);
                
            } catch (\Exception $e) {
                Log::warning('dataOverview: geoTwitterUser failed', ['error' => $e->getMessage()]);
                $geoUsers = ['locality' => ['rows' => []]];
            }
        }

        return view('mk.data-overview', [
            'projects'           => $projects,
            'projectId'          => $projectId,
            'params'             => $params,
            'startDate'          => $params['startDate'],
            'endDate'            => $params['endDate'],
            'trendingTopics'     => $trendingTopics,
            'topHashtags'        => $topHashtags,
            'mentionSocialMedia' => $mentionSocialMedia,
            'mentionOnlineNews'  => $mentionOnlineNews,
            'activeUsers'        => $activeUsers,
            'sentimentTimeline'  => $sentimentTimeline,
            'geoUsers'           => $geoUsers,
        ]);
    }
}