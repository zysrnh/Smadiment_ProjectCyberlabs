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
            $projectId = $request->query('project_id');
            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) {
                    return redirect()->route('mk.topic-map', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }
            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
            return view('mk.topic-map', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ]);
        } catch (\Exception $e) {
            Log::error('Topic Map page error', ['error' => $e->getMessage()]);
            return view('mk.topic-map', [
                'projects'  => [],
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
            ]);
        }
    }

    /**
     * 🔥 API Endpoint: Get Topic Map Data
     */
    public function getTopicMap(Request $request)
    {
        try {
            $projectId = $request->get('project_id');
            $media     = $request->get('media', 'all');
            $startDate = $request->get('start_date', now()->subDays(7)->format('Y-m-d')); // ✅ ->get() biar baca inject middleware
            $endDate   = $request->get('end_date',   now()->format('Y-m-d'));              // ✅ ->get() biar baca inject middleware
            $startTime = (int) $request->get('start_time', 0);
            $endTime   = (int) $request->get('end_time',   23);

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
                    'name'  => $topic,
                    'count' => $info['num_docs'] ?? 0,
                ];
            }

            // Sort by count descending
            usort($topics, fn($a, $b) => $b['count'] - $a['count']);

            return response()->json([
                'success' => true,
                'data'    => $topics,
                'total'   => count($topics),
            ]);

        } catch (\Exception $e) {
            Log::error('getTopicMap API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}