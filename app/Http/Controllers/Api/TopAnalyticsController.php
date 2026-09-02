<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TopAnalyticsController extends Controller
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
    // 🔥 1. TOP HASHTAGS PAGE
    // ========================================================================
    public function hashtags(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        [$startDate, $endDate] = $this->getDateRange($request);
        $media = $request->get('media', 'all');

        return view('mk.top-analytics.hashtags', [
            'projects' => $projects,
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'media' => $media,
        ]);
    }

    /**
     * 🔥 API: Get Top Hashtags Data (Lazy Loading)
     * API returns array directly: [{"name": "hashtag", "size": "count"}, ...]
     */
    public function getHashtagsData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required'
                ], 400);
            }

            $startDate = $request->query('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
            $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
            $media = $request->query('media', 'all');

            $response = $this->client->topHashtags(
                $projectId,
                $media,
                $startDate,
                $endDate
            );

            Log::info('Top Hashtags API response', [
                'project_id' => $projectId,
                'is_array' => is_array($response),
                'data_count' => count($response)
            ]);

            // API returns array directly, not wrapped in 'data' key
            $hashtags = is_array($response) ? $response : [];
            $chartData = $this->transformHashtagsForChart($hashtags);

            return response()->json([
                'success' => true,
                'data' => [
                    'hashtags' => $hashtags,
                    'chartData' => $chartData,
                    'total' => count($hashtags),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Hashtags Data API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch hashtags data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================================================================
    // 🔥 2. TOP LOCATIONS PAGE
    // ========================================================================
    public function locations(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        [$startDate, $endDate] = $this->getDateRange($request);
        $media = $request->get('media', 'all');

        return view('mk.top-analytics.locations', [
            'projects' => $projects,
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'media' => $media,
        ]);
    }

    /**
     * 🔥 API: Get Top Locations Data (Lazy Loading)
     */
    public function getLocationsData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required'
                ], 400);
            }

            $startDate = $request->query('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
            $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
            $media = $request->query('media', 'all');

            $response = $this->client->topAuthorLocation(
                $projectId,
                $media,
                $startDate,
                $endDate
            );

            Log::info('Top Locations API response', [
                'project_id' => $projectId,
                'data_count' => count($response['data'] ?? [])
            ]);

            // Transform data for frontend
            $locations = $response['data'] ?? [];
            $chartData = $this->transformLocationsForChart($locations);

            return response()->json([
                'success' => true,
                'data' => [
                    'locations' => $locations,
                    'chartData' => $chartData,
                    'total' => count($locations),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Locations Data API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch locations data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================================================================
    // 🔥 3. TOP INFLUENCERS PAGE
    // ========================================================================
    public function influencers(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        [$startDate, $endDate] = $this->getDateRange($request);

        return view('mk.top-analytics.influencers', [
            'projects' => $projects,
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    /**
     * 🔥 API: Get Top Influencers Data (Lazy Loading)
     */
    public function getInfluencersData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required'
                ], 400);
            }

            $startDate = $request->query('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
            $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
            $media = $request->query('media', '');

            $response = $this->client->topInfluencers(
                $projectId,
                $startDate,
                $endDate,
                0,
                23,
                '',
                200
            );

            // API returns {data: [...]} or [...]
            $rawData = $response['data'] ?? $response ?? [];
            if (!is_array($rawData)) $rawData = [];

            // Filter & Sanitize
            $influencers = [];
            foreach ($rawData as $item) {
                if (!is_array($item)) continue;

                $name = $item['name'] ?? $item['author'] ?? '';
                $info = $item['info'] ?? [];
                $screenName = $info['screen_name'] ?? ltrim($name, '@');

                // Skip YouTube Channel IDs (UC...)
                if (preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $screenName)) {
                    continue;
                }

                // Clean display name if it's a raw ID
                if (preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $name)) {
                    $item['name'] = $screenName ? ('@' . $screenName) : 'Unknown';
                }

                $influencers[] = $item;
            }

            $chartData = $this->transformInfluencersForChart($influencers);

            return response()->json([
                'success' => true,
                'data' => [
                    'influencers' => array_values($influencers),
                    'chartData' => $chartData,
                    'total' => count($influencers),
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Get Influencers Data API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch influencers data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================================================================
    // 🔥 HELPER METHODS - Transform Data for Charts
    // ========================================================================

    /**
     * Transform hashtags data for chart visualization
     * API Response format: [{"name": "hashtag", "size": "count"}, ...]
     */
    private function transformHashtagsForChart(array $hashtags): array
    {
        if (empty($hashtags)) {
            return ['labels' => [], 'values' => []];
        }

        // Take top 10 hashtags
        $top10 = array_slice($hashtags, 0, 10);
        
        $labels = [];
        $values = [];

        foreach ($top10 as $item) {
            // API uses 'name' for hashtag and 'size' for count
            $labels[] = '#' . ($item['name'] ?? 'Unknown');
            $values[] = (int)($item['size'] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Transform locations data for chart visualization
     */
    private function transformLocationsForChart(array $locations): array
    {
        if (empty($locations)) {
            return ['labels' => [], 'values' => []];
        }

        // Take top 10 locations
        $top10 = array_slice($locations, 0, 10);
        
        $labels = [];
        $values = [];

        foreach ($top10 as $item) {
            $labels[] = $item['location'] ?? $item['city'] ?? $item['name'] ?? 'Unknown';
            $values[] = (int)($item['count'] ?? $item['frequency'] ?? $item['total'] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Transform influencers data for chart visualization
     */
    private function transformInfluencersForChart(array $influencers): array
    {
        if (empty($influencers)) {
            return ['labels' => [], 'values' => []];
        }

        // Take top 10 influencers
        $top10 = array_slice($influencers, 0, 10);
        
        $labels = [];
        $values = [];

        foreach ($top10 as $item) {
            $labels[] = $item['name'] ?? $item['username'] ?? $item['author'] ?? 'Unknown';
            $values[] = (int)($item['followers'] ?? $item['reach'] ?? $item['influence_score'] ?? 0);
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}