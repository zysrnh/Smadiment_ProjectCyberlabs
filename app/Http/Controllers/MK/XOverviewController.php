<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class XOverviewController extends Controller
{
    private MediaKernelsClient $client;

    public function __construct(MediaKernelsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Display X Overview Page
     */
    public function index(Request $request)
    {
        try {
            // Get projects list first
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];
            
            Log::info('X Overview - Projects loaded', [
                'total_projects' => count($projects),
                'projects' => $projects
            ]);
            
            // Get project_id from query parameter or auto-select first project
            $projectId = $request->query('project_id');
            
            // If no project_id provided, auto-select first project
            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                
                Log::info('X Overview - Auto-selecting first project', [
                    'project_id' => $projectId
                ]);
                
                // Redirect with project_id to maintain clean URL
                if ($projectId) {
                    return redirect()->route('mk.x.overview', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date' => $request->query('end_date', now()->format('Y-m-d'))
                    ]);
                }
            }
            
            // If still no project available
            if (!$projectId) {
                Log::warning('X Overview - No projects available');
                
                // Still render the page but with empty project
                $endDate = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                
                return view('mk.x.overview', [
                    'projectId' => null,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'projects' => [],
                ]);
            }

            // Get date range from query parameters or use defaults (last 7 days)
            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            Log::info('X Overview - Loading page', [
                'project_id' => $projectId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'total_projects' => count($projects)
            ]);

            // Prepare view data - MAKE SURE ALL VARIABLES ARE PASSED
            return view('mk.x.overview')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projects' => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('X Overview Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Return to page with error message instead of redirecting
            return view('mk.x.overview')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'projects' => [],
                'error' => 'Failed to load projects: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get Total Users for X
     */
    public function totalUsers(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->totalUsers($projectId, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('X totalUsers API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id'),
                'start_date' => $request->query('start_date'),
                'end_date' => $request->query('end_date')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get Total Authors for X
     */
    public function totalAuthors(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->totalAuthors($projectId, 'twitter', $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('X totalAuthors API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get Volume Total for X
     */
    public function volumeTotal(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->volumeTotal($projectId, 'twitter', $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('X volumeTotal API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get Sentiment Total for X
     */
    public function sentimentTotal(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->sentimentTotal($projectId, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('X sentimentTotal API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get Top Hashtags for X
     */
    public function topHashtags(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->topHashtags($projectId, 'twitter', $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('X topHashtags API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get Most Active Users for X
     */
    public function mostActiveUsers(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->mostActiveUsers($projectId, $startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => $result
            ]);

        } catch (\Exception $e) {
            Log::error('X mostActiveUsers API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}