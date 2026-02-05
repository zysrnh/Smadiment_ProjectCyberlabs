<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TopicMapController extends Controller
{
    private MediaKernelsClient $mkClient;

    public function __construct(MediaKernelsClient $mkClient)
    {
        $this->mkClient = $mkClient;
    }

    /**
     * 🔥 Display Topic Map Page
     */
    public function index(Request $request)
    {
        try {
            $projects = $this->mkClient->listProjects(0, 100)['data'] ?? [];
            
            return view('mk.topic-map', [
                'projects' => $projects,
            ]);
        } catch (\Exception $e) {
            Log::error('Topic Map page error', ['error' => $e->getMessage()]);
            return view('mk.topic-map', [
                'projects' => [],
            ]);
        }
    }

    /**
     * 🔥 API Endpoint: Get Topic Map Data
     */
    public function getTopicMap(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $media = $request->query('media', 'all');
            $startDate = $request->query('start_date', now()->subDays(7)->format('Y-m-d'));
            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startTime = (int) $request->query('start_time', 0);
            $endTime = (int) $request->query('end_time', 23);

            if (!$projectId) {
                return response()->json(['error' => 'project_id required'], 400);
            }

            $data = $this->mkClient->topicMap(
                $projectId,
                $media,
                $startDate,
                $endDate,
                $startTime,
                $endTime
            );

            // Transform to array format for frontend
            $topics = [];
            foreach ($data as $topic => $info) {
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
            Log::error('getTopicMap API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}