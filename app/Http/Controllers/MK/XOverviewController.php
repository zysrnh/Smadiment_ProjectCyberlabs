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

    public function index(Request $request)
    {
        try {
            // Get projects list first
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];
            
            // Get project_id from query parameter or auto-select first project
            $projectId = $request->query('project_id');
            
            // If no project_id provided, auto-select first project
            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                
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
                return redirect()->route('mk.dashboard')
                    ->with('error', 'No projects available');
            }

            // Get date range from query parameters or use defaults (last 7 days)
            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            // Media type for X/Twitter
            $media = 'twitter';

            // Fetch all data in parallel (or sequentially if needed)
            $data = [
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projects' => $projects,
            ];

            return view('mk.x.overview', $data);

        } catch (\Exception $e) {
            Log::error('X Overview Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('mk.dashboard')
                ->with('error', 'Failed to load X Overview: ' . $e->getMessage());
        }
    }
}