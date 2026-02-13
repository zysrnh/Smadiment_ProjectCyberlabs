<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\MediaKernelsClient;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 🔥 UPDATED: SHARE FILTERED PROJECTS WITH ALL MK LAYOUT PAGES
        // Now filters projects based on user assignment
        View::composer('mk.layouts.app', function ($view) {
            try {
                // 🔥 Check if user is authenticated
                if (!Auth::check()) {
                    Log::info('View Composer - User not authenticated, returning empty projects');
                    $view->with('projects', []);
                    return;
                }

                $user = Auth::user();
                
                // 🔥 Get user's assigned project IDs from database
                $assignedProjectIds = $user->assignedProjectIds();
                
                Log::info('View Composer - Loading projects for user', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'assigned_project_ids' => $assignedProjectIds
                ]);
                
                // Get MediaKernelsClient instance
                $client = app(MediaKernelsClient::class);
                
                // Fetch ALL projects from API
                $projectsData = $client->listProjects(0, 100);
                
                Log::info('View Composer - Raw projects response', [
                    'response_type' => gettype($projectsData),
                    'has_data' => isset($projectsData['data']),
                    'is_array' => is_array($projectsData)
                ]);
                
                // Handle multiple possible response structures
                $allProjects = [];
                
                if (isset($projectsData['data']) && is_array($projectsData['data'])) {
                    // Structure: ['data' => [...]]
                    $allProjects = $projectsData['data'];
                    
                } elseif (isset($projectsData['projects']) && is_array($projectsData['projects'])) {
                    // Structure: ['projects' => [...]]
                    $allProjects = $projectsData['projects'];
                    
                } elseif (is_array($projectsData) && !empty($projectsData)) {
                    // Check if it's a direct array of projects
                    $firstItem = reset($projectsData);
                    if (is_array($firstItem) && (isset($firstItem['id']) || isset($firstItem['project_id']))) {
                        // Direct array of projects
                        $allProjects = $projectsData;
                    }
                }
                
                Log::info('View Composer - Total projects from API', [
                    'total_count' => count($allProjects)
                ]);
                
                // 🔥 FILTER: Only keep projects user has access to
                $userProjects = array_filter($allProjects, function($project) use ($assignedProjectIds) {
                    $projectId = $project['id'] ?? $project['project_id'] ?? null;
                    
                    if ($projectId === null) {
                        return false;
                    }
                    
                    return in_array($projectId, $assignedProjectIds);
                });
                
                // Re-index array (important for blade foreach)
                $userProjects = array_values($userProjects);
                
                // Log final result
                Log::info('View Composer - ✅ FILTERED projects loaded', [
                    'total_projects_from_api' => count($allProjects),
                    'user_assigned_projects' => count($userProjects),
                    'project_ids' => array_column($userProjects, 'id'),
                    'project_names' => array_column($userProjects, 'name')
                ]);
                
                // Share FILTERED projects with view
                $view->with('projects', $userProjects);
                
            } catch (\Exception $e) {
                // Log error with full trace
                Log::error('View Composer - ❌ Failed to load projects', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ]);
                
                // Provide empty array so page still works
                $view->with('projects', []);
            }
        });
    }
}