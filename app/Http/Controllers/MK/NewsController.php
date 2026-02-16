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
}