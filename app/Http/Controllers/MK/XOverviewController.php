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
     * Response format: {"status": "success", "data": {"total_author": "119021"}, "numrows": 1}
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
            
            Log::info('totalUsers raw API response', ['result' => $result]);

            // ✅ FIX: Extract total from nested data structure
            $total = 0;
            if (isset($result['data']['total_author'])) {
                $total = (int) $result['data']['total_author'];
            } elseif (isset($result['data']['total'])) {
                $total = (int) $result['data']['total'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total
                ]
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
     * Response format: {"all": 43276, "bymedia": {"fb": "282", "twit": "42662", ...}}
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
            
            Log::info('totalAuthors raw API response', ['result' => $result]);

            // ✅ FIX: Extract total from "all" field or "bymedia.twit"
            $total = 0;
            if (isset($result['all'])) {
                $total = (int) $result['all'];
            } elseif (isset($result['bymedia']['twit'])) {
                $total = (int) $result['bymedia']['twit'];
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'total' => $total
                ]
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
     * Response format: {"all": {"total": "3553"}, "bymedia": {"twit": "2639", ...}, "byplatforms": {...}}
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
            
            Log::info('volumeTotal raw API response', ['result' => $result]);

            // ✅ FIX: Extract total and chart data
            $total = 0;
            if (isset($result['all']['total'])) {
                $total = (int) $result['all']['total'];
            } elseif (isset($result['bymedia']['twit'])) {
                $total = (int) $result['bymedia']['twit'];
            }

            // Get chart data from trends_total API
            $chartData = [];
            try {
                $trendsResult = $this->client->trendsTotal($projectId, $startDate, $endDate);
                
                if (isset($trendsResult['data']) && is_array($trendsResult['data'])) {
                    // Find twitter data
                    foreach ($trendsResult['data'] as $trend) {
                        if (isset($trend['keyword']) && strtolower($trend['keyword']) === 'twit') {
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
     * Response format: {"neg": "1716", "pos": "5185", "net": "1017"}
     * OR: {"all": 311, "bymedia": {"twit": {"neg": 49, "pos": 83, "net": 17}}}
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
            
            Log::info('sentimentTotal raw API response', ['result' => $result]);

            $positive = 0;
            $negative = 0;
            $neutral = 0;

            // ✅ FIX: Handle both response formats
            if (isset($result['pos']) && isset($result['neg']) && isset($result['net'])) {
                // Format 1: Direct sentiment counts
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral = (int) $result['net'];
            } elseif (isset($result['bymedia']['twit'])) {
                // Format 2: By media breakdown
                $twitData = $result['bymedia']['twit'];
                $positive = isset($twitData['pos']) ? (int) $twitData['pos'] : 0;
                $negative = isset($twitData['neg']) ? (int) $twitData['neg'] : 0;
                $neutral = isset($twitData['net']) ? (int) $twitData['net'] : 0;
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
     * API: Get Most Active Users for X
     * Response format: {"status": "success", "data": {"data": [{"name": "...", "mentions": "77", ...}]}}
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
            
            Log::info('mostActiveUsers raw API response', ['result' => $result]);

            // ✅ FIX: Extract users from nested data structure with ALL fields
            $users = [];
            
            if (isset($result['data']['data']) && is_array($result['data']['data'])) {
                foreach ($result['data']['data'] as $user) {
                    // Extract username from name field or screen_name from contentJson
                    $username = '';
                    if (isset($user['contentJson']['screen_name'])) {
                        $username = $user['contentJson']['screen_name'];
                    } elseif (isset($user['name'])) {
                        // Extract username from "Name @username" format
                        if (preg_match('/@(\w+)/', $user['name'], $matches)) {
                            $username = $matches[1];
                        }
                    }
                    
                    // Extract profile URL
                    $profileUrl = '';
                    if (isset($user['profile_url'])) {
                        $profileUrl = $user['profile_url'];
                    } elseif (isset($user['contentJson']['profile_image_url_https'])) {
                        // Convert _normal to larger size for better quality
                        $profileUrl = str_replace('_normal', '_bigger', $user['contentJson']['profile_image_url_https']);
                    } elseif (isset($user['contentJson']['profile_image_url'])) {
                        $profileUrl = str_replace('_normal', '_bigger', $user['contentJson']['profile_image_url']);
                    }
                    
                    // Get follower count
                    $followers = 0;
                    if (isset($user['followers'])) {
                        $followers = (int) $user['followers'];
                    } elseif (isset($user['contentJson']['followers_count'])) {
                        $followers = (int) $user['contentJson']['followers_count'];
                    }
                    
                    // Get following count
                    $following = 0;
                    if (isset($user['contentJson']['friends_count'])) {
                        $following = (int) $user['contentJson']['friends_count'];
                    }
                    
                    // Get account name (real name, not username)
                    $accountName = '';
                    if (isset($user['contentJson']['name'])) {
                        $accountName = $user['contentJson']['name'];
                    } elseif (isset($user['name'])) {
                        // Remove @username from "Name @username" format
                        $accountName = preg_replace('/@\w+/', '', $user['name']);
                        $accountName = trim($accountName);
                    }
                    
                    // Calculate total posts (mentions + replies + retweets)
                    $mentions = isset($user['mentions']) ? (int) $user['mentions'] : 0;
                    $replies = isset($user['replies']) ? (int) $user['replies'] : 0;
                    $retweets = isset($user['retweets']) ? (int) $user['retweets'] : 0;
                    $posts = isset($user['y']) ? (int) $user['y'] : ($mentions + $replies + $retweets);
                    
                    if ($username) {
                        $users[] = [
                            'username' => $username,
                            'name' => $accountName ?: $username,
                            'profile_url' => $profileUrl,
                            'profile_image_url' => $profileUrl, // Alias for compatibility
                            'followers' => $followers,
                            'following' => $following,
                            'mentions' => $mentions,
                            'replies' => $replies,
                            'retweets' => $retweets,
                            'posts' => $posts,
                            'y' => $posts, // Alias for compatibility
                            'id' => $user['id'] ?? '',
                            'contentJson' => $user['contentJson'] ?? null, // Include full contentJson for detail view
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

    /**
     * API: Get User Mentions for X
     * Get all mentions/tweets from a specific user
     */
    public function userMentions(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate = $request->query('end_date');
            $username = $request->query('username');

            if (!$projectId || !$startDate || !$endDate || !$username) {
                return response()->json([
                    'success' => false,
                    'error' => 'Missing required parameters: project_id, start_date, end_date, username'
                ], 400);
            }

            // Call the Media Kernels API to get user mentions
            $result = $this->client->getUserMentions($projectId, $startDate, $endDate, $username);
            
            Log::info('userMentions raw API response', [
                'username' => $username,
                'result_count' => count($result)
            ]);

            $mentions = [];
            
            // Extract mentions from the response
            // Response is array of mention objects directly
            if (is_array($result)) {
                foreach ($result as $mention) {
                    // Parse contentJson if it exists
                    $contentJson = null;
                    if (isset($mention['contentJson']) && is_string($mention['contentJson'])) {
                        $contentJson = json_decode($mention['contentJson'], true);
                    }
                    
                    // Get sentiment from class_sentiment_code
                    $sentimentCode = $mention['class_sentiment_code'] ?? 'neutral';
                    $sentiment = $sentimentCode === 'pos' ? 'positive' : 
                                ($sentimentCode === 'neg' ? 'negative' : 'neutral');
                    
                    $mentions[] = [
                        'text' => $mention['content'] ?? '',
                        'created_at' => $mention['date_created'] ?? '',
                        'sentiment' => $sentiment,
                        'author' => $mention['author_scr_name'] ?? $username,
                        'id' => $mention['id'] ?? '',
                        'mention_type' => $mention['mention_type'] ?? '',
                        'num_likes' => $mention['num_likes'] ?? 0,
                        'num_shares' => $mention['num_shares'] ?? 0,
                        'num_comments' => $mention['num_comments'] ?? 0,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'username' => $username,
                    'mentions' => $mentions,
                    'total' => count($mentions)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('X userMentions API error', [
                'error' => $e->getMessage(),
                'username' => $request->query('username')
            ]);
            
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}