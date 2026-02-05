<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DataSourceController extends Controller
{
    private MediaKernelsClient $client;

    public function __construct(MediaKernelsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Get projects list for dropdown
     */
    private function getProjects(): array
    {
        try {
            $response = $this->client->listProjects(0, 100);
            return $response['data'] ?? [];
        } catch (\Exception $e) {
            Log::error('Failed to fetch projects', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Get date range from request or use defaults
     */
    private function getDateRange(Request $request): array
    {
        $endDate = $request->get('end_date', Carbon::now()->format('Y-m-d'));
        $startDate = $request->get('start_date', Carbon::parse($endDate)->subDays(6)->format('Y-m-d'));

        return [$startDate, $endDate];
    }

    // ========================================================================
    // 🔥 1. TOTAL USERS - AGGREGATE FORMAT
    // ========================================================================
    public function users(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        if (!$projectId) {
            return view('mk.data-source.users', [
                'projects' => $projects,
                'error' => 'Please select a project to view data.',
                'usersData' => [],
                'chartData' => [],
                'totalUsers' => 0,
            ]);
        }

        [$startDate, $endDate] = $this->getDateRange($request);

        try {
            $response = $this->client->totalUsers(
                projectId: (string)$projectId,
                startDate: $startDate,
                endDate: $endDate
            );

            Log::info('totalUsers raw response', ['response' => $response]);

            // API returns: {"status": "success", "data": {"total_author": "119021"}}
            $totalUsers = 0;
            
            if (isset($response['data']['total_author'])) {
                $totalUsers = (int) $response['data']['total_author'];
            } elseif (isset($response['data']['total_users'])) {
                $totalUsers = (int) $response['data']['total_users'];
            }
            
            $usersData = [
                [
                    'date' => "$startDate to $endDate",
                    'count' => $totalUsers,
                ]
            ];
            
            $chartData = [
                'labels' => ['Total Period'],
                'values' => [$totalUsers],
            ];

            return view('mk.data-source.users', [
                'projects' => $projects,
                'usersData' => $usersData,
                'chartData' => $chartData,
                'totalUsers' => $totalUsers,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projectId' => $projectId,
            ]);

        } catch (\Exception $e) {
            Log::error('totalUsers error', ['error' => $e->getMessage()]);
            
            return view('mk.data-source.users', [
                'projects' => $projects,
                'error' => 'Failed to fetch users data: ' . $e->getMessage(),
                'usersData' => [],
                'chartData' => [],
                'totalUsers' => 0,
            ]);
        }
    }

    // ========================================================================
    // 🔥 2. TOTAL AUTHORS - AGGREGATE FORMAT
    // ========================================================================
    public function authors(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        if (!$projectId) {
            return view('mk.data-source.authors', [
                'projects' => $projects,
                'error' => 'Please select a project to view data.',
                'authorsData' => [],
                'chartData' => [],
                'byMedia' => [],
            ]);
        }

        [$startDate, $endDate] = $this->getDateRange($request);
        $media = $request->get('media', 'all');

        try {
            $response = $this->client->totalAuthors(
                projectId: (string)$projectId,
                media: $media,
                startDate: $startDate,
                endDate: $endDate
            );

            Log::info('totalAuthors raw response', ['response' => $response]);

            // API returns: {"all": 43276, "bymedia": {"fb": "282", ...}}
            $totalAll = $response['all'] ?? 0;
            $byMedia = $response['bymedia'] ?? [];
            
            $authorsData = [
                [
                    'date' => "$startDate to $endDate",
                    'count' => $totalAll,
                ]
            ];
            
            $chartLabels = [];
            $chartValues = [];
            
            if (!empty($byMedia)) {
                foreach ($byMedia as $mediaName => $count) {
                    $chartLabels[] = strtoupper($mediaName);
                    $chartValues[] = (int)$count;
                }
            } else {
                $chartLabels = ['Total'];
                $chartValues = [$totalAll];
            }
            
            $chartData = [
                'labels' => $chartLabels,
                'values' => $chartValues,
            ];

            return view('mk.data-source.authors', [
                'projects' => $projects,
                'authorsData' => $authorsData,
                'chartData' => $chartData,
                'byMedia' => $byMedia,
                'totalAll' => $totalAll,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projectId' => $projectId,
                'media' => $media,
            ]);

        } catch (\Exception $e) {
            Log::error('totalAuthors error', ['error' => $e->getMessage()]);
            
            return view('mk.data-source.authors', [
                'projects' => $projects,
                'error' => 'Failed to fetch authors data: ' . $e->getMessage(),
                'authorsData' => [],
                'chartData' => [],
                'byMedia' => [],
            ]);
        }
    }

    // ========================================================================
    // 🔥 3. VOLUME TOTAL - FLEXIBLE FORMAT (Array OR Aggregate)
    // ========================================================================
    public function volume(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        if (!$projectId) {
            return view('mk.data-source.volume', [
                'projects' => $projects,
                'error' => 'Please select a project to view data.',
                'volumeData' => [],
                'chartData' => [],
                'totalVolume' => 0,
            ]);
        }

        [$startDate, $endDate] = $this->getDateRange($request);
        $media = $request->get('media', 'all');

        try {
            $response = $this->client->volumeTotal(
                projectId: (string)$projectId,
                media: $media,
                startDate: $startDate,
                endDate: $endDate,
                isCache: true
            );

            Log::info('volumeTotal raw response', ['response' => $response]);

            // 🔥 Handle 3 possible response formats:
            
            // Format 1: {"data": [{"date": "2024-12-08", "volume": 123}, ...]} - Per Date Array
            // Format 2: {"all": 12345, "bymedia": {...}} - Aggregate like authors
            // Format 3: {"data": {"total": 12345}} - Single aggregate value
            
            $volumeData = [];
            $chartData = [];
            $totalVolume = 0;
            $byMedia = [];
            
            if (isset($response['data']) && is_array($response['data'])) {
                // Check if it's array of dates or single aggregate
                if (isset($response['data'][0]) && isset($response['data'][0]['date'])) {
                    // Format 1: Array per date
                    $volumeData = $response['data'];
                    $chartData = $this->transformVolumeDataForChart($volumeData);
                    $totalVolume = array_sum(array_column($volumeData, 'volume')) ?: array_sum(array_column($volumeData, 'count'));
                } elseif (isset($response['data']['total'])) {
                    // Format 3: Single aggregate
                    $totalVolume = (int) $response['data']['total'];
                    $volumeData = [['date' => "$startDate to $endDate", 'volume' => $totalVolume]];
                    $chartData = ['labels' => ['Total Period'], 'values' => [$totalVolume]];
                } else {
                    // Unknown data format, try to parse
                    $volumeData = $response['data'];
                    $chartData = $this->transformVolumeDataForChart($volumeData);
                }
            } elseif (isset($response['all'])) {
                // Format 2: Aggregate like authors
                $totalVolume = (int) $response['all'];
                $byMedia = $response['bymedia'] ?? [];
                
                $volumeData = [['date' => "$startDate to $endDate", 'volume' => $totalVolume]];
                
                // Chart breakdown by media if available
                if (!empty($byMedia)) {
                    $chartLabels = [];
                    $chartValues = [];
                    foreach ($byMedia as $mediaName => $count) {
                        $chartLabels[] = strtoupper($mediaName);
                        $chartValues[] = (int)$count;
                    }
                    $chartData = ['labels' => $chartLabels, 'values' => $chartValues];
                } else {
                    $chartData = ['labels' => ['Total Period'], 'values' => [$totalVolume]];
                }
            } else {
                $volumeData = [];
                $chartData = ['labels' => [], 'values' => []];
            }

            return view('mk.data-source.volume', [
                'projects' => $projects,
                'volumeData' => $volumeData,
                'chartData' => $chartData,
                'totalVolume' => $totalVolume,
                'byMedia' => $byMedia,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projectId' => $projectId,
                'media' => $media,
            ]);

        } catch (\Exception $e) {
            Log::error('volumeTotal error', ['error' => $e->getMessage()]);
            
            return view('mk.data-source.volume', [
                'projects' => $projects,
                'error' => 'Failed to fetch volume data: ' . $e->getMessage(),
                'volumeData' => [],
                'chartData' => [],
                'totalVolume' => 0,
            ]);
        }
    }

    private function transformVolumeDataForChart(array $data): array
    {
        if (empty($data)) {
            return ['labels' => [], 'values' => []];
        }

        $labels = [];
        $values = [];

        foreach ($data as $item) {
            if (isset($item['date'])) {
                $labels[] = $item['date'];
                $values[] = $item['volume'] ?? $item['count'] ?? 0;
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    // ========================================================================
    // 🔥 4. TRENDS TOTAL - ARRAY FORMAT (FIXED)
    // ========================================================================
    public function trends(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        if (!$projectId) {
            return view('mk.data-source.trends', [
                'projects' => $projects,
                'error' => 'Please select a project to view data.',
                'trendsData' => [],
                'chartData' => [],
            ]);
        }

        [$startDate, $endDate] = $this->getDateRange($request);

        try {
            $response = $this->client->trendsTotal(
                projectId: (string)$projectId,
                startDate: $startDate,
                endDate: $endDate
            );

            Log::info('trendsTotal controller response', [
                'has_data' => isset($response['data']),
                'data_count' => count($response['data'] ?? [])
            ]);

            $trendsData = $response['data'] ?? [];
            $chartData = $this->transformTrendsDataForChart($trendsData);

            return view('mk.data-source.trends', [
                'projects' => $projects,
                'trendsData' => $trendsData,
                'chartData' => $chartData,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projectId' => $projectId,
            ]);

        } catch (\Exception $e) {
            Log::error('trendsTotal error', ['error' => $e->getMessage()]);
            
            return view('mk.data-source.trends', [
                'projects' => $projects,
                'error' => 'Failed to fetch trends data: ' . $e->getMessage(),
                'trendsData' => [],
                'chartData' => [],
            ]);
        }
    }

    private function transformTrendsDataForChart(array $data): array
    {
        if (empty($data)) {
            return ['labels' => [], 'datasets' => []];
        }

        $labels = [];
        $datasets = [];

        // Extract all unique dates
        foreach ($data as $trend) {
            if (isset($trend['data']) && is_array($trend['data'])) {
                foreach ($trend['data'] as $point) {
                    $date = $point['date'] ?? 'Unknown';
                    if (!in_array($date, $labels)) {
                        $labels[] = $date;
                    }
                }
            }
        }

        sort($labels);

        // Build datasets per keyword
        foreach ($data as $trend) {
            $keyword = $trend['keyword'] ?? $trend['topic'] ?? 'Unknown';
            $values = [];

            foreach ($labels as $label) {
                $found = false;
                if (isset($trend['data']) && is_array($trend['data'])) {
                    foreach ($trend['data'] as $point) {
                        if (($point['date'] ?? '') === $label) {
                            $values[] = $point['count'] ?? 0;
                            $found = true;
                            break;
                        }
                    }
                }
                if (!$found) {
                    $values[] = 0;
                }
            }

            $datasets[] = [
                'label' => $keyword,
                'data' => $values,
            ];
        }

        return [
            'labels' => $labels,
            'datasets' => $datasets,
        ];
    }
}