<?php

namespace App\Http\Controllers;

use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;

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
     * 📊 DASHBOARD
     */
    public function dashboard(Request $request, MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        
        return view('mk.dashboard', [
            'projects' => $projects,
        ]);
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
        $chartData = ['labels' => [], 'values' => []];

        if ($projectId) {
            $rawData = $mk->categories(
                $projectId,
                'all',
                $params['startDate'],
                $params['endDate'],
                $params['startTime'],
                $params['endTime']
            );
            
            // Normalize categories
            $data = $rawData['data'] ?? $rawData;
            if (!empty($data) && is_array($data)) {
                $labels = [];
                $values = [];
                foreach ($data as $key => $item) {
                    if (is_array($item)) {
                        $labels[] = $item['name'] ?? $item['category'] ?? $key;
                        $values[] = (int) ($item['total'] ?? $item['count'] ?? $item['value'] ?? 0);
                    } else {
                        $labels[] = $key;
                        $values[] = (int) $item;
                    }
                }
                $chartData = ['labels' => $labels, 'values' => $values];
            }
        }

        return view('mk.categories', [
            'projects' => $projects,
            'projectId' => $projectId,
            'params' => $params,
            'rawData' => $rawData,
            'chartData' => $chartData,
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

        // Normalize reach data - PERBAIKAN DI SINI
        $data = $rawData['data'] ?? $rawData;
        if (!empty($data) && is_array($data)) {
            $labels = [];
            $values = [];
            
            foreach ($data as $key => $item) {  // 👈 Key = follower range
                if (is_array($item)) {
                    $labels[] = $key;  // 👈 Gunakan key sebagai label (0_3, 1001_10K, dll)
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

        // Normalize users data - PERBAIKAN DI SINI
        $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;  // 👈 Nested data.data
        if (!empty($data) && is_array($data)) {
            $rows = [];
            foreach ($data as $item) {
                if (is_array($item)) {
                    // Parse name to get username (format: "Name @username")
                    $fullName = $item['name'] ?? 'Unknown User';
                    $username = $fullName;
                    
                    // Extract username from "Name @username" format
                    if (preg_match('/@(\w+)/', $fullName, $matches)) {
                        $username = $matches[1]; // Get username without @
                    }
                    
                    $rows[] = [
                        'username' => $username,
                        'count' => (int) ($item['y'] ?? $item['post_count'] ?? $item['posts'] ?? $item['count'] ?? 0),  // 👈 Gunakan 'y'
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
}