<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class FacebookOverviewController extends Controller
{
    private MediaKernelsClient $client;

    public function __construct(MediaKernelsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Display Facebook Overview Page
     */
    public function index(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];
            
            $projectId = $request->query('project_id');
            
            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                
                if ($projectId) {
                    return redirect()->route('mk.facebook.overview', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date' => $request->query('end_date', now()->format('Y-m-d'))
                    ]);
                }
            }
            
            if (!$projectId) {
                $endDate = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                
                return view('mk.facebook.overview', [
                    'projectId' => null,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'projects' => [],
                ]);
            }

            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.overview')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate' => $endDate,
                'projects' => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Overview Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('mk.facebook.overview')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate' => now()->format('Y-m-d'),
                'projects' => [],
                'error' => 'Failed to load projects: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * API: Get Total Users for Facebook
     * Uses totalAuthors with 'facebook' media filter since totalUsers doesn't support media filtering
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

            // Use totalAuthors with facebook filter to get FB-specific count
            $result = $this->client->totalAuthors($projectId, 'facebook', $startDate, $endDate);

            $total = 0;
            if (isset($result['all'])) {
                $total = (int) $result['all'];
            } elseif (isset($result['bymedia']['fb'])) {
                $total = (int) $result['bymedia']['fb'];
            } elseif (isset($result['bymedia']['facebook'])) {
                $total = (int) $result['bymedia']['facebook'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook totalUsers API error', [
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
     * API: Get Total Authors for Facebook
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

            $result = $this->client->totalAuthors($projectId, 'facebook', $startDate, $endDate);

            $total = 0;
            if (isset($result['all'])) {
                $total = (int) $result['all'];
            } elseif (isset($result['bymedia']['fb'])) {
                $total = (int) $result['bymedia']['fb'];
            } elseif (isset($result['bymedia']['facebook'])) {
                $total = (int) $result['bymedia']['facebook'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook totalAuthors API error', [
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
     * API: Get Volume Total for Facebook
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

            $result = $this->client->volumeTotal($projectId, 'facebook', $startDate, $endDate);

            $total = 0;
            if (isset($result['all']['total'])) {
                $total = (int) $result['all']['total'];
            } elseif (isset($result['bymedia']['fb'])) {
                $total = (int) $result['bymedia']['fb'];
            } elseif (isset($result['bymedia']['facebook'])) {
                $total = (int) $result['bymedia']['facebook'];
            }

            // Get chart data from trends
            $chartData = [];
            try {
                $trendsResult = $this->client->trendsTotal($projectId, $startDate, $endDate);
                
                if (isset($trendsResult['data']) && is_array($trendsResult['data'])) {
                    foreach ($trendsResult['data'] as $trend) {
                        $keyword = strtolower($trend['keyword'] ?? '');
                        if ($keyword === 'fb' || $keyword === 'facebook') {
                            $chartData = $trend['data'] ?? [];
                            break;
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Failed to load trends data for chart', ['error' => $e->getMessage()]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total,
                    'chart' => $chartData
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook volumeTotal API error', [
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
     * API: Get Sentiment Total for Facebook
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

            // Get sentiment using getSentiment with facebook filter
            $result = $this->client->getSentiment($projectId, 'facebook', $startDate, $endDate);

            $positive = 0;
            $negative = 0;
            $neutral = 0;

            if (isset($result['pos']) && isset($result['neg']) && isset($result['net'])) {
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral = (int) $result['net'];
            } elseif (isset($result['bymedia']['fb'])) {
                $fbData = $result['bymedia']['fb'];
                $positive = isset($fbData['pos']) ? (int) $fbData['pos'] : 0;
                $negative = isset($fbData['neg']) ? (int) $fbData['neg'] : 0;
                $neutral = isset($fbData['net']) ? (int) $fbData['net'] : 0;
            } elseif (isset($result['bymedia']['facebook'])) {
                $fbData = $result['bymedia']['facebook'];
                $positive = isset($fbData['pos']) ? (int) $fbData['pos'] : 0;
                $negative = isset($fbData['neg']) ? (int) $fbData['neg'] : 0;
                $neutral = isset($fbData['net']) ? (int) $fbData['net'] : 0;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'positive' => $positive,
                    'negative' => $negative,
                    'neutral' => $neutral
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook sentimentTotal API error', [
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
     * API: Get Most Active Users for Facebook
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

            $users = [];
            
            if (isset($result['data']['data']) && is_array($result['data']['data'])) {
                foreach ($result['data']['data'] as $user) {
                    // Filter only Facebook users
                    $media = strtolower($user['media'] ?? '');
                    if ($media !== 'fb' && $media !== 'facebook') {
                        continue;
                    }
                    
                    // Extract username
                    $username = '';
                    if (isset($user['contentJson']['from']['name'])) {
                        $username = $user['contentJson']['from']['name'];
                    } elseif (isset($user['name'])) {
                        $username = $user['name'];
                    }
                    
                    // Extract profile picture
                    $profileUrl = '';
                    if (isset($user['profile_url'])) {
                        $profileUrl = $user['profile_url'];
                    } elseif (isset($user['contentJson']['from']['picture']['data']['url'])) {
                        $profileUrl = $user['contentJson']['from']['picture']['data']['url'];
                    }
                    
                    // Extract engagement metrics
                    $likes = isset($user['num_likes']) ? (int) $user['num_likes'] : 0;
                    $shares = isset($user['num_shares']) ? (int) $user['num_shares'] : 0;
                    $comments = isset($user['num_comments']) ? (int) $user['num_comments'] : 0;
                    $posts = isset($user['y']) ? (int) $user['y'] : ($likes + $shares + $comments);
                    
                    if ($username) {
                        $users[] = [
                            'username' => $username,
                            'name' => $username,
                            'profile_url' => $profileUrl,
                            'profile_image_url' => $profileUrl,
                            'likes' => $likes,
                            'shares' => $shares,
                            'comments' => $comments,
                            'posts' => $posts,
                            'y' => $posts,
                            'id' => $user['id'] ?? '',
                            'contentJson' => $user['contentJson'] ?? null,
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $users
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook mostActiveUsers API error', [
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