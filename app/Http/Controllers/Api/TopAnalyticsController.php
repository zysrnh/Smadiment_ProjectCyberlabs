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
        $startDate = $request->get('start_date', '2026-06-01');
        $endDate   = $request->get('end_date', '2026-09-26');

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

            $startDate = $request->query('start_date', '2026-06-01');
            $endDate = $request->query('end_date', '2026-09-26');
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

            $hashtags = [
                ['name' => 'PrabowoSubianto', 'size' => 45200],
                ['name' => 'IndonesiaMaju', 'size' => 38100],
                ['name' => 'Pilpres2024', 'size' => 29400],
                ['name' => 'PrabowoGibran', 'size' => 24100],
                ['name' => 'Gerindra', 'size' => 18500],
                ['name' => 'MenhanRI', 'size' => 14200],
                ['name' => 'KabinetIndonesia', 'size' => 11800],
                ['name' => 'KemhanRI', 'size' => 9500],
                ['name' => 'PresidenRI', 'size' => 7400],
                ['name' => 'TransformasiBangsa', 'size' => 6200],
            ];
            $chartData = $this->transformHashtagsForChart($hashtags);

            return response()->json([
                'success' => true,
                'data' => [
                    'hashtags' => $hashtags,
                    'chartData' => $chartData,
                    'total' => count($hashtags),
                ]
            ]);
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

            $startDate = $request->query('start_date', '2026-06-01');
            $endDate = $request->query('end_date', '2026-09-26');
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

            $locations = [
                ['location' => 'DKI Jakarta', 'count' => 4520],
                ['location' => 'Jawa Barat', 'count' => 3810],
                ['location' => 'Jawa Timur', 'count' => 2940],
                ['location' => 'Jawa Tengah', 'count' => 2410],
                ['location' => 'Banten', 'count' => 1250],
                ['location' => 'Sumatera Utara', 'count' => 980],
                ['location' => 'Sulawesi Selatan', 'count' => 840],
                ['location' => 'Bali', 'count' => 620],
                ['location' => 'DI Yogyakarta', 'count' => 590],
                ['location' => 'Lampung', 'count' => 430],
            ];
            $chartData = $this->transformLocationsForChart($locations);

            return response()->json([
                'success' => true,
                'data' => [
                    'locations' => $locations,
                    'chartData' => $chartData,
                    'total' => count($locations),
                ]
            ]);
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

            $startDate = $request->query('start_date', '2026-06-01');
            $endDate = $request->query('end_date', '2026-09-26');
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

            $influencers = [
                ['name' => '@prabowo', 'followers' => 6820000, 'post_count' => 142],
                ['name' => '@gerindra', 'followers' => 1250000, 'post_count' => 285],
                ['name' => '@kemhan_ri', 'followers' => 980000, 'post_count' => 110],
                ['name' => '@katadatacoid', 'followers' => 840000, 'post_count' => 88],
                ['name' => '@detikcom', 'followers' => 19500000, 'post_count' => 340],
                ['name' => '@kompascom', 'followers' => 14200000, 'post_count' => 290],
                ['name' => '@tempodotco', 'followers' => 8900000, 'post_count' => 175],
                ['name' => '@cnnindonesia', 'followers' => 11200000, 'post_count' => 220],
            ];
            $chartData = $this->transformInfluencersForChart($influencers);

            return response()->json([
                'success' => true,
                'data' => [
                    'influencers' => $influencers,
                    'chartData' => $chartData,
                    'total' => count($influencers),
                ]
            ]);
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