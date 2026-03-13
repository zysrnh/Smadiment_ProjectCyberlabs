<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CompareProjectController extends Controller
{
    private MediaKernelsClient $client;

    public function __construct(MediaKernelsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Display Compare Projects Page
     */
    public function index(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = isset($projectsData['data']) && is_array($projectsData['data'])
                ? $projectsData['data']
                : array_values($projectsData);

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(29)->format('Y-m-d'));

            $selectedIds = $request->query('project_ids', '');
            if (is_string($selectedIds)) {
                $selectedIds = array_filter(explode(',', $selectedIds));
            }

            return view('mk.compare.index', [
                'projects'    => $projects,
                'selectedIds' => $selectedIds,
                'projectId'   => $selectedIds[0] ?? ($projects[0]['id'] ?? null),
                'startDate'   => $startDate,
                'endDate'     => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('Compare Projects Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('mk.compare.index', [
                'projects'    => [],
                'selectedIds' => [],
                'projectId'   => null,
                'startDate'   => now()->subDays(29)->format('Y-m-d'),
                'endDate'     => now()->format('Y-m-d'),
                'error'       => 'Failed to load projects: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get list of all projects (for the selector)
     */
    public function projectsList(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            // API returns flat array (no 'data' wrapper), like MkController::getProjects()
            $projects = isset($projectsData['data']) && is_array($projectsData['data'])
                ? $projectsData['data']
                : array_values($projectsData);

            // Debug: log raw structure dari API biar tau field apa yang ada
            if (!empty($projects)) {
                Log::info('CompareProject - Raw project sample', [
                    'keys'   => array_keys($projects[0]),
                    'sample' => array_slice($projects[0], 0, 15, true),
                ]);
            }

            $normalized = array_map(function ($p) {
                // Coba semua kemungkinan field nama project
                $title = $p['project_name']
                    ?? $p['name']
                    ?? $p['title']
                    ?? $p['label']
                    ?? $p['keyword']
                    ?? $p['project_title']
                    ?? $p['display_name']
                    ?? $p['project_label']
                    ?? ('Project #' . ($p['id'] ?? '?'));

                $groupName = $p['project_group_name']
                    ?? $p['group_name']
                    ?? $p['group']
                    ?? $p['client']
                    ?? '';

                return [
                    'id'           => $p['id'] ?? '',
                    'title'        => $title,
                    'project_type' => $p['project_type'] ?? 'keyword',
                    'keywords'     => $p['keywords'] ?? '',
                    'media_types'  => $p['media_types'] ?? '',
                    'group_name'   => $groupName,
                ];
            }, $projects);

            Log::info('CompareProject - Normalized projects', [
                'total'  => count($normalized),
                'sample' => array_slice($normalized, 0, 3),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $normalized,
                'total'   => count($normalized),
            ]);

        } catch (\Exception $e) {
            Log::error('Projects List API Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * API: Compare projects — volume total
     */
    public function compareVolumeTotal(Request $request)
    {
        try {
            $projectIds = $this->parseProjectIds($request);
            $startDate  = $request->query('start_date');
            $endDate    = $request->query('end_date');

            if (empty($projectIds)) {
                return response()->json(['success' => false, 'error' => 'project_ids is required'], 400);
            }

            $result = $this->client->compareProjects(
                $projectIds,
                $startDate,
                $endDate,
                'volumetotal'
            );

            return response()->json([
                'success' => true,
                'type'    => 'volumetotal',
                'data'    => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Compare Volume Total Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Compare projects — sentiment
     */
    public function compareSentiment(Request $request)
    {
        try {
            $projectIds = $this->parseProjectIds($request);
            $startDate  = $request->query('start_date');
            $endDate    = $request->query('end_date');

            if (empty($projectIds)) {
                return response()->json(['success' => false, 'error' => 'project_ids is required'], 400);
            }

            $result = $this->client->compareProjects(
                $projectIds,
                $startDate,
                $endDate,
                'sentimenttotal'
            );

            return response()->json([
                'success' => true,
                'type'    => 'sentimenttotal',
                'data'    => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Compare Sentiment Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Compare projects — authors total
     */
    public function compareAuthors(Request $request)
    {
        try {
            $projectIds = $this->parseProjectIds($request);
            $startDate  = $request->query('start_date');
            $endDate    = $request->query('end_date');

            if (empty($projectIds)) {
                return response()->json(['success' => false, 'error' => 'project_ids is required'], 400);
            }

            $result = $this->client->compareProjects(
                $projectIds,
                $startDate,
                $endDate,
                'authorstotal'
            );

            return response()->json([
                'success' => true,
                'type'    => 'authorstotal',
                'data'    => $result,
            ]);

        } catch (\Exception $e) {
            Log::error('Compare Authors Error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Compare all metrics at once (batched)
     */
    public function compareAll(Request $request)
    {
        try {
            $projectIds = $this->parseProjectIds($request);
            $startDate  = $request->query('start_date');
            $endDate    = $request->query('end_date');

            if (empty($projectIds)) {
                return response()->json(['success' => false, 'error' => 'project_ids is required'], 400);
            }

            if (count($projectIds) < 2) {
                return response()->json(['success' => false, 'error' => 'Minimum 2 project_ids required'], 400);
            }

            Log::info('CompareAll - Starting', [
                'project_ids' => $projectIds,
                'start_date'  => $startDate,
                'end_date'    => $endDate,
            ]);

            $types  = ['volumetotal', 'sentimenttotal', 'authorstotal'];
            $result = [];

            foreach ($types as $type) {
                try {
                    $raw = $this->client->compareProjects(
                        $projectIds,
                        $startDate,
                        $endDate,
                        $type
                    );

                    Log::info('CompareAll raw results', [
    'volumetotal_raw'   => $result['volumetotal'] ?? [],
    'sentimenttotal_raw'=> $result['sentimenttotal'] ?? [],
    'authorstotal_raw'  => $result['authorstotal'] ?? [],
]);

                    Log::info("CompareAll - {$type} raw response", [
                        'keys'   => is_array($raw) ? array_keys($raw) : gettype($raw),
                        'sample' => is_array($raw) ? array_slice($raw, 0, 2, true) : $raw,
                    ]);

                    $result[$type] = $raw;

                } catch (\Exception $e) {
                    Log::warning("CompareAll - {$type} failed", ['error' => $e->getMessage()]);
                    $result[$type] = [];
                }
            }

            // Fetch project details for titles
            $projectsData = $this->client->listProjects(0, 100);
            $allProjects = isset($projectsData['data']) && is_array($projectsData['data'])
                ? $projectsData['data']
                : array_values($projectsData);

            $projectDetails = [];
            foreach ($allProjects as $p) {
                $pid = (string) ($p['id'] ?? '');
                if (!in_array($pid, array_map('strval', $projectIds))) continue;

                $title = $p['project_name']
                    ?? $p['name']
                    ?? $p['title']
                    ?? $p['label']
                    ?? $p['keyword']
                    ?? $p['project_title']
                    ?? $p['display_name']
                    ?? $p['project_label']
                    ?? ('Project #' . $pid);

                $projectDetails[$pid] = [
                    'id'           => $pid,
                    'title'        => $title,
                    'project_type' => $p['project_type'] ?? 'keyword',
                    'keywords'     => $p['keywords'] ?? '',
                    'media_types'  => $p['media_types'] ?? '',
                    'group_name'   => $p['project_group_name'] ?? $p['group_name'] ?? $p['group'] ?? '',
                ];
            }

            Log::info('CompareAll - Done', [
                'project_ids'    => $projectIds,
                'project_details'=> array_keys($projectDetails),
                'types_returned' => array_keys($result),
            ]);

            return response()->json([
                'success'         => true,
                'project_ids'     => $projectIds,
                'project_details' => $projectDetails,
                'data'            => $result,
                'start_date'      => $startDate,
                'end_date'        => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('Compare All Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────────────
    // Helper
    // ─────────────────────────────────────────────────────────────

    private function parseProjectIds(Request $request): array
    {
        $raw = $request->query('project_ids', '');

        if (is_array($raw)) {
            return array_values(array_filter(array_map('intval', $raw)));
        }

        return array_values(array_filter(
            array_map('intval', explode(',', (string) $raw))
        ));
    }
}