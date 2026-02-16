<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
{
    protected $mkClient;

    public function __construct(MediaKernelsClient $mkClient)
    {
        $this->mkClient = $mkClient;
    }

    /**
     * Recent Topics Page - Hybrid v2/v1 approach
     * Tries v2 first, falls back to v1 if needed
     */
    public function recentTopicsPage(Request $request)
    {
        try {
            $level = $request->query('level', 'internasional');
            $size = (int) $request->query('size', 10);

            Log::info('=== RECENT TOPICS PAGE (HYBRID) ===', [
                'level' => $level,
                'size' => $size,
            ]);

            // Validate level
            if (!in_array($level, ['internasional', 'nasional', 'regional_apac'])) {
                $level = 'internasional';
            }

            // Use hybrid method (v2 with v1 fallback)
            $response = $this->mkClient->recentTopicsHybrid($level, $size);

            $issues = $response['daftar_isu'] ?? [];
            $apiVersion = $response['api_version'] ?? 'unknown';
            $status = $response['status'] ?? 'unknown';

            Log::info('=== FINAL DATA ===', [
                'api_version' => $apiVersion,
                'status' => $status,
                'issues_count' => count($issues),
                'first_issue' => count($issues) > 0 ? $issues[0]['judul'] : 'no issues',
            ]);

            return view('mk.news.recent-topics', [
                'level' => $level,
                'size' => $size,
                'issues' => $issues,
                'apiVersion' => $apiVersion,
                'status' => $status,
            ]);

        } catch (\Exception $e) {
            Log::error('Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('mk.news.recent-topics', [
                'level' => $request->query('level', 'internasional'),
                'size' => 10,
                'issues' => [],
                'apiVersion' => 'error',
                'status' => 'error',
                'error' => 'Failed to load topics',
            ]);
        }
    }

    /**
     * API Endpoint - Hybrid approach
     */
    public function recentTopicsApi(Request $request)
    {
        try {
            $level = $request->query('level', 'internasional');
            $size = (int) $request->query('size', 10);

            if (!in_array($level, ['internasional', 'nasional', 'regional_apac'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid level parameter',
                ], 400);
            }

            $response = $this->mkClient->recentTopicsHybrid($level, $size);

            return response()->json([
                'success' => $response['status'] === 'success',
                'level' => $level,
                'api_version' => $response['api_version'] ?? 'unknown',
                'data' => $response['daftar_isu'] ?? [],
            ]);

        } catch (\Exception $e) {
            Log::error('API Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch topics',
                'data' => [],
            ], 500);
        }
    }
    public function newsWordCloudPage(Request $request)
    {
        try {
            // Get selected project
            $projectId = $request->query('project_id', session('selected_project_id'));
            
            if (!$projectId) {
                Log::warning('News Word Cloud: No project selected');
                return redirect()->route('mk.dashboard')
                    ->with('error', 'Please select a project first');
            }

            // Store in session
            session(['selected_project_id' => $projectId]);

            // Get date range (default: last 7 days)
            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            Log::info('News Word Cloud Page Loaded', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            return view('mk.news.word-cloud', [
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate' => $endDate,
            ]);

        } catch (\Exception $e) {
            Log::error('News Word Cloud Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->route('mk.dashboard')
                ->with('error', 'Failed to load News Word Cloud page');
        }
    }

    /**
     * API: Get News Word Cloud Data
     */
    public function newsWordCloudData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $sentiment = $request->query('sentiment', '2'); // 2 = all

            if (!$projectId) {
                return response()->json([
                    'success' => false,
                    'error' => 'Project ID is required',
                ], 400);
            }

            Log::info('News Word Cloud API Request', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'sentiment' => $sentiment,
            ]);

            // Call MediaKernels API
            $data = $this->mkClient->wordCloud(
                $projectId,
                $startDate,
                0, // start_time
                $endDate,
                23, // end_time
                $sentiment
            );

            Log::info('News Word Cloud Data Retrieved', [
                'has_data' => isset($data['data']),
                'has_phrases' => isset($data['data']['phrases']),
                'phrases_count' => isset($data['data']['phrases']) ? count($data['data']['phrases']) : 0,
            ]);

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            Log::error('News Word Cloud API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Failed to fetch word cloud data',
            ], 500);
        }
    }

}