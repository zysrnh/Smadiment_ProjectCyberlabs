<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;
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
        // 🔥 SHARE PROJECTS WITH ALL MK LAYOUT PAGES
        // Layout path: resources/views/mk/layouts/app.blade.php
        View::composer('mk.layouts.app', function ($view) {
            try {
                // Get MediaKernelsClient instance
                $client = app(MediaKernelsClient::class);
                
                // Fetch projects with detailed logging
                Log::info('View Composer - Attempting to load projects');
                
                $projectsData = $client->listProjects(0, 100);
                
                Log::info('View Composer - Raw projects response', [
                    'response' => $projectsData,
                    'type' => gettype($projectsData),
                    'keys' => is_array($projectsData) ? array_keys($projectsData) : 'not_array'
                ]);
                
                // Handle multiple possible response structures
                $projects = [];
                
                if (isset($projectsData['data']) && is_array($projectsData['data'])) {
                    // Structure: ['data' => [...]]
                    $projects = $projectsData['data'];
                    Log::info('View Composer - Found projects in data key', ['count' => count($projects)]);
                    
                } elseif (isset($projectsData['projects']) && is_array($projectsData['projects'])) {
                    // Structure: ['projects' => [...]]
                    $projects = $projectsData['projects'];
                    Log::info('View Composer - Found projects in projects key', ['count' => count($projects)]);
                    
                } elseif (is_array($projectsData) && !empty($projectsData)) {
                    // Check if it's a direct array of projects
                    $firstItem = reset($projectsData);
                    if (is_array($firstItem) && (isset($firstItem['id']) || isset($firstItem['project_id']))) {
                        // Direct array of projects
                        $projects = $projectsData;
                        Log::info('View Composer - Found direct array of projects', ['count' => count($projects)]);
                    }
                }
                
                // Log final result
                Log::info('View Composer - Projects loaded for layout', [
                    'total_projects' => count($projects),
                    'first_project' => $projects[0] ?? null,
                    'sample_projects' => array_slice($projects, 0, 3)
                ]);
                
                // Share with view
                $view->with('projects', $projects);
                
            } catch (\Exception $e) {
                // Log error with full trace
                Log::error('View Composer - Failed to load projects', [
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