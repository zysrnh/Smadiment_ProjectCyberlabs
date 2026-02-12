<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnalyticsOverviewController extends Controller
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
    // 🔥 MAIN PAGE
    // ========================================================================
    public function index(Request $request)
    {
        $projectId = $request->get('project_id');
        $projects = $this->getProjects();
        
        if (!$projectId && !empty($projects)) {
            $projectId = $projects[0]['id'] ?? null;
        }

        [$startDate, $endDate] = $this->getDateRange($request);
        $media = $request->get('media', 'all');

        return view('mk.analytics-overview', [
            'projects' => $projects,
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'media' => $media,
        ]);
    }

    // ========================================================================
    // 🔥 API: TOPIC MAP
    // ========================================================================
    public function getTopicMap(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Project ID is required'
                ], 400);
            }

            $media = $request->query('media', 'all');
            $startDate = $request->query('start_date', Carbon::now()->subDays(6)->format('Y-m-d'));
            $endDate = $request->query('end_date', Carbon::now()->format('Y-m-d'));
            $startTime = (int) $request->query('start_time', 0);
            $endTime = (int) $request->query('end_time', 23);

            $response = $this->client->topicMap(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );

            Log::info('Topic Map API response', [
                'project_id' => $projectId,
                'data_count' => count($response)
            ]);

            // Transform to array format for frontend
            $topics = [];
            foreach ($response as $topic => $info) {
                $topics[] = [
                    'name' => $topic,
                    'count' => $info['num_docs'] ?? 0
                ];
            }

            // Sort by count descending
            usort($topics, function($a, $b) {
                return $b['count'] - $a['count'];
            });

            return response()->json([
                'success' => true,
                'data' => $topics,
                'total' => count($topics)
            ]);

        } catch (\Exception $e) {
            Log::error('Get Topic Map API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch topic map data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================================================================
    // 🔥 API: TOP HASHTAGS
    // ========================================================================
    public function getHashtags(Request $request)
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

            // API returns array directly
            $hashtags = is_array($response) ? $response : [];

            return response()->json([
                'success' => true,
                'data' => $hashtags,
                'total' => count($hashtags)
            ]);

        } catch (\Exception $e) {
            Log::error('Get Hashtags API error', [
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
    // 🔥 API: TOP LOCATIONS - FIXED RESPONSE STRUCTURE
    // ========================================================================
    public function getLocations(Request $request)
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

            Log::info('Top Locations API raw response', [
                'project_id' => $projectId,
                'response_keys' => array_keys($response),
                'has_country' => isset($response['country']),
                'has_data' => isset($response['data'])
            ]);

            // 🔥 FIXED: Handle actual response structure
            // Response structure: { "country": { "total": X, "rows": [...] } }
            $locations = [];
            
            if (isset($response['country']['rows'])) {
                // Structure has country->rows
                $locations = $response['country']['rows'];
            } elseif (isset($response['data'])) {
                // Fallback: check if data exists
                $locations = $response['data'];
            } elseif (is_array($response)) {
                // Fallback: use response directly if it's an array
                $locations = $response;
            }

            Log::info('Top Locations processed data', [
                'locations_count' => count($locations),
                'sample' => array_slice($locations, 0, 3, true)
            ]);

            return response()->json([
                'success' => true,
                'data' => $locations,
                'total' => count($locations)
            ]);

        } catch (\Exception $e) {
            Log::error('Get Locations API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
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
    // 🔥 API: TOP INFLUENCERS - FIXED RESPONSE STRUCTURE
    // ========================================================================
    public function getInfluencers(Request $request)
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

            $response = $this->client->topInfluencers(
                $projectId,
                $startDate,
                $endDate
            );

            Log::info('Top Influencers API raw response', [
                'project_id' => $projectId,
                'response_keys' => is_array($response) ? array_keys($response) : 'not_array',
                'is_array' => is_array($response),
                'has_data' => isset($response['data']),
                'sample' => is_array($response) ? array_slice($response, 0, 2, true) : null
            ]);

            // 🔥 FIXED: Handle actual response structure
            // Response is array of objects directly: [{ author_id, total, name, info }, ...]
            $influencers = [];
            
            if (isset($response['data']) && is_array($response['data'])) {
                // If wrapped in data key
                $influencers = $response['data'];
            } elseif (is_array($response) && !empty($response)) {
                // Check if first element has expected structure
                $firstElement = reset($response);
                if (is_array($firstElement) && isset($firstElement['author_id'])) {
                    // Response is array of influencer objects directly
                    $influencers = $response;
                }
            }

            Log::info('Top Influencers processed data', [
                'influencers_count' => count($influencers),
                'sample' => array_slice($influencers, 0, 3, true)
            ]);

            return response()->json([
                'success' => true,
                'data' => $influencers,
                'total' => count($influencers)
            ]);

        } catch (\Exception $e) {
            Log::error('Get Influencers API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'project_id' => $request->query('project_id')
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch influencers data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}