<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TopicMapController extends Controller
{
    private MediaKernelsClient $mkClient;

    public function __construct(MediaKernelsClient $mkClient)
    {
        $this->mkClient = $mkClient;
    }

    private function getAllProjects(): array
    {
        $user = Auth::user();
        $assignedProjectIds = $user->assignedProjectIds();

        $rawProjects = $this->mkClient->listProjects(0, 100);
        $allProjects = array_values($rawProjects);

        $userProjects = array_filter($allProjects, function ($project) use ($assignedProjectIds) {
            return in_array($project['id'] ?? null, $assignedProjectIds);
        });

        return array_values($userProjects);
    }

    /**
     * 🔥 Display Topic Map Page
     */
    public function index(Request $request)
    {
        try {
            $projects = $this->getAllProjects();
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
            $startDate = $request->get('start_date', now()->subDays(7)->format('Y-m-d'));
            $endDate   = $request->get('end_date',   now()->format('Y-m-d'));
            $startTime = (int) $request->get('start_time', 0);
            $endTime   = (int) $request->get('end_time',   23);

            if (!$projectId) {
                return response()->json(['error' => 'project_id required'], 400);
            }

            // Using wordCloud instead of topicMap as requested by user
            $resp = $this->mkClient->wordCloud(
                $projectId,
                $startDate,
                $startTime,
                $endDate,
                $endTime
            );

            $phrases = $resp['data']['phrases'] ?? [];
            
            // Transform phrases object { "Word": count } into array [ { name: "Word", count: count } ]
            $topics = [];
            foreach ($phrases as $phrase => $count) {
                $topics[] = [
                    'name'  => $phrase,
                    'count' => (int) $count,
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