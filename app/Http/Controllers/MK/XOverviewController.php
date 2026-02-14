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
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];
            
            $projectId = $request->query('project_id');
            
            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                
                if ($projectId) {
                    return redirect()->route('mk.x.overview', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date' => $request->query('end_date', now()->format('Y-m-d'))
                    ]);
                }
            }
            
            if (!$projectId) {
                $endDate = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                
                return view('mk.x.overview', [
                    'projectId' => null,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'projects' => [],
                ]);
            }

            $endDate = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

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
                'project_id' => $request->query('project_id')
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

            $total = 0;
            if (isset($result['all']['total'])) {
                $total = (int) $result['all']['total'];
            } elseif (isset($result['bymedia']['twit'])) {
                $total = (int) $result['bymedia']['twit'];
            }

            $chartData = [];
            try {
                $trendsResult = $this->client->trendsTotal($projectId, $startDate, $endDate);
                
                if (isset($trendsResult['data']) && is_array($trendsResult['data'])) {
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

            $positive = 0;
            $negative = 0;
            $neutral = 0;

            if (isset($result['pos']) && isset($result['neg']) && isset($result['net'])) {
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral = (int) $result['net'];
            } elseif (isset($result['bymedia']['twit'])) {
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
                    $username = '';
                    if (isset($user['contentJson']['screen_name'])) {
                        $username = $user['contentJson']['screen_name'];
                    } elseif (isset($user['name'])) {
                        if (preg_match('/@(\w+)/', $user['name'], $matches)) {
                            $username = $matches[1];
                        }
                    }
                    
                    $profileUrl = '';
                    if (isset($user['profile_url'])) {
                        $profileUrl = $user['profile_url'];
                    } elseif (isset($user['contentJson']['profile_image_url_https'])) {
                        $profileUrl = str_replace('_normal', '_bigger', $user['contentJson']['profile_image_url_https']);
                    } elseif (isset($user['contentJson']['profile_image_url'])) {
                        $profileUrl = str_replace('_normal', '_bigger', $user['contentJson']['profile_image_url']);
                    }
                    
                    $followers = 0;
                    if (isset($user['followers'])) {
                        $followers = (int) $user['followers'];
                    } elseif (isset($user['contentJson']['followers_count'])) {
                        $followers = (int) $user['contentJson']['followers_count'];
                    }
                    
                    $following = 0;
                    if (isset($user['contentJson']['friends_count'])) {
                        $following = (int) $user['contentJson']['friends_count'];
                    }
                    
                    $accountName = '';
                    if (isset($user['contentJson']['name'])) {
                        $accountName = $user['contentJson']['name'];
                    } elseif (isset($user['name'])) {
                        $accountName = preg_replace('/@\w+/', '', $user['name']);
                        $accountName = trim($accountName);
                    }
                    
                    $mentions = isset($user['mentions']) ? (int) $user['mentions'] : 0;
                    $replies = isset($user['replies']) ? (int) $user['replies'] : 0;
                    $retweets = isset($user['retweets']) ? (int) $user['retweets'] : 0;
                    $posts = isset($user['y']) ? (int) $user['y'] : ($mentions + $replies + $retweets);
                    
                    if ($username) {
                        $users[] = [
                            'username' => $username,
                            'name' => $accountName ?: $username,
                            'profile_url' => $profileUrl,
                            'profile_image_url' => $profileUrl,
                            'followers' => $followers,
                            'following' => $following,
                            'mentions' => $mentions,
                            'replies' => $replies,
                            'retweets' => $retweets,
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
     * Display Most Retweets Page
     */
    public function mostRetweetsPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.x.most-retweets', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.x.most-retweets')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Most Retweets Page Error', [
                'error' => $e->getMessage()
            ]);

            return view('mk.x.most-retweets')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Most Retweets for X
     */
    public function mostRetweets(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->mostRetweets($projectId, $startDate, $endDate);

            $tweets = [];

            if (is_array($result)) {
                foreach ($result as $item) {
                    $avatar = $item['avatar_url'] ?? $item['author']['image'] ?? '';
                    $avatar = str_replace('_normal.', '.', $avatar);

                    $tweets[] = [
                        'id'             => $item['id']             ?? '',
                        'sub_id'         => $item['sub_id']         ?? '',
                        'name'           => $item['name']           ?? '',
                        'content'        => $item['content']        ?? '',
                        'freq'           => (int) ($item['freq']    ?? $item['sentiment_freq'] ?? 0),
                        'sentiment_str'  => $item['sentiment_str']  ?? 'Neutral',
                        'sentiment_freq' => $item['sentiment_freq'] ?? 0,
                        'date_created'   => $item['date_created']   ?? '',
                        'avatar_url'     => $avatar,
                        'author'         => [
                            'name'     => $item['author']['name']     ?? $item['name'] ?? '',
                            'scr_name' => $item['author']['scr_name'] ?? $item['name'] ?? '',
                            'image'    => $item['author']['image']    ?? $avatar,
                        ],
                    ];
                }

                usort($tweets, fn($a, $b) => $b['freq'] - $a['freq']);
            }

            return response()->json([
                'success' => true,
                'data'    => $tweets,
            ]);

        } catch (\Exception $e) {
            Log::error('X mostRetweets API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get User Mentions for X
     */
    public function userMentions(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $username  = $request->query('username');

            if (!$projectId || !$startDate || !$endDate || !$username) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date, username'
                ], 400);
            }

            $result = $this->client->getUserMentions($projectId, $startDate, $endDate, $username);

            $mentions = [];
            
            if (is_array($result)) {
                foreach ($result as $mention) {
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

    /**
     * Display Top Hashtags Page
     */
    public function topHashtagsPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.x.top-hashtags', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.x.top-hashtags')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Top Hashtags Page Error', [
                'error' => $e->getMessage()
            ]);

            return view('mk.x.top-hashtags')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Top Hashtags Data
     */
    public function topHashtagsData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date'
                ], 400);
            }

            $result = $this->client->topHashtags($projectId, 'twitter', $startDate, $endDate);

            $hashtags = [];
            $totalMentions = 0;
            $totalHashtags = 0;

            if (is_array($result)) {
                $data = $result;
                if (isset($result['data']) && is_array($result['data'])) {
                    $data = $result['data'];
                }
                
                foreach ($data as $item) {
                    if (!is_array($item)) continue;
                    
                    $name = $item['name'] ?? '';
                    $size = (int) ($item['size'] ?? 0);
                    
                    if ($name && $size > 0) {
                        $hashtags[] = [
                            'name' => $name,
                            'size' => $size,
                            'hashtag' => '#' . ltrim($name, '#')
                        ];
                        $totalMentions += $size;
                        $totalHashtags++;
                    }
                }

                usort($hashtags, fn($a, $b) => $b['size'] - $a['size']);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'hashtags' => $hashtags,
                    'total_hashtags' => $totalHashtags,
                    'total_mentions' => $totalMentions,
                    'top_hashtag' => $hashtags[0] ?? null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('topHashtagsData API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // 🔥 AUTHORS DEMOGRAPHICS METHODS
    // ==========================================

    /**
     * Display Authors Age Page
     */
    public function authorsAgePage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.x.authors.age', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.x.authors-age')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Authors Age Page Error', [
                'error' => $e->getMessage()
            ]);

            return view('mk.x.authors-age')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Authors Age Data
     */
    public function authorsAgeData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'error' => 'Missing required parameters'
                ], 400);
            }

            // authorsAge already returns direct array from MediaKernels API
            $result = $this->client->authorsAge($projectId, 'twitter', $startDate, $endDate);

            Log::info('authorsAge API response', [
                'count' => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            // Return direct array, not wrapped
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('authorsAge API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display Authors Gender Page
     */
    public function authorsGenderPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.x.authors.gender', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.x.authors-gender')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Authors Gender Page Error', [
                'error' => $e->getMessage()
            ]);

            return view('mk.x.authors-gender')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Authors Gender Data
     */
    public function authorsGenderData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'error' => 'Missing required parameters'
                ], 400);
            }

            // authorsGender already returns direct array from MediaKernels API
            $result = $this->client->authorsGender($projectId, 'twitter', $startDate, $endDate);

            Log::info('authorsGender API response', [
                'count' => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            // Return direct array, not wrapped
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('authorsGender API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display Authors Type Page
     */
    public function authorsTypePage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.x.authors.type', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.x.authors-type')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Authors Type Page Error', [
                'error' => $e->getMessage()
            ]);

            return view('mk.x.authors-type')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Authors Type Data
     */
    public function authorsTypeData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'error' => 'Missing required parameters'
                ], 400);
            }

            // authorsType already returns direct array from MediaKernels API
            $result = $this->client->authorsType($projectId, 'twitter', $startDate, $endDate);

            Log::info('authorsType API response', [
                'count' => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            // Return direct array, not wrapped
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('authorsType API error', [
                'error' => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display Authors Demographics Page (Single Page with All Demographics)
     */
    public function authorsDemographicsPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.x.authors.demographics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.x.authors-demographics')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Authors Demographics Page Error', [
                'error' => $e->getMessage()
            ]);

            return view('mk.x.authors-demographics')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }
    // 🔥 ADD THESE METHODS TO XOverviewController.php

/**
 * Display X Geographic Page
 */
public function geographicPage(Request $request)
{
    try {
        $projectsData = $this->client->listProjects(0, 100);
        $projects = $projectsData['data'] ?? [];

        $projectId = $request->query('project_id');

        if (!$projectId && count($projects) > 0) {
            $projectId = $projects[0]['id'] ?? null;

            if ($projectId) {
                return redirect()->route('mk.x.geographic', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.x.geographic')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('X Geographic Page Error', [
            'error' => $e->getMessage()
        ]);

        return view('mk.x.geographic')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
            'error'     => $e->getMessage(),
        ]);
    }
}

/**
 * API: Get Geo Twitter User Data
 */
public function geoUser(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'error'   => 'Missing required parameters: project_id, start_date, end_date'
            ], 400);
        }

        $result = $this->client->geoTwitterUser($projectId, 'twitter', $startDate, $endDate);

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);

    } catch (\Exception $e) {
        Log::error('geoUser API error', [
            'error' => $e->getMessage(),
            'project_id' => $request->query('project_id'),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}

/**
 * API: Get Geo Twitter User Sentiment Data
 */
public function geoSentiment(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'error'   => 'Missing required parameters: project_id, start_date, end_date'
            ], 400);
        }

        $result = $this->client->geoTwitterUserSentiment(
            $projectId, 
            'twitter', 
            $startDate, 
            $endDate,
            0, // start_time
            23, // end_time
            1 // sentiment (1 = all sentiments)
        );

        return response()->json([
            'success' => true,
            'data'    => $result,
        ]);

    } catch (\Exception $e) {
        Log::error('geoSentiment API error', [
            'error' => $e->getMessage(),
            'project_id' => $request->query('project_id'),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}

/**
 * API: Get Top Author Locations Data
 */
public function topLocations(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'error'   => 'Missing required parameters: project_id, start_date, end_date'
            ], 400);
        }

        $result = $this->client->topAuthorLocation($projectId, 'twitter', $startDate, $endDate);

        // Transform data for table
        $locations = [];
        if (is_array($result)) {
            foreach ($result as $location) {
                $locations[] = [
                    'name' => $location['name'] ?? $location['location'] ?? 'Unknown',
                    'count' => $location['count'] ?? $location['total'] ?? 0,
                ];
            }
            
            // Sort by count descending
            usort($locations, fn($a, $b) => $b['count'] - $a['count']);
        }

        return response()->json([
            'success' => true,
            'data'    => $locations,
        ]);

    } catch (\Exception $e) {
        Log::error('topLocations API error', [
            'error' => $e->getMessage(),
            'project_id' => $request->query('project_id'),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}
// 🔥 ADD THESE METHODS TO XOverviewController.php (after geographicPage method)

/**
 * Display Most Status Page
 */
public function mostStatusPage(Request $request)
{
    try {
        $projectsData = $this->client->listProjects(0, 100);
        $projects = $projectsData['data'] ?? [];

        $projectId = $request->query('project_id');

        if (!$projectId && count($projects) > 0) {
            $projectId = $projects[0]['id'] ?? null;

            if ($projectId) {
                return redirect()->route('mk.x.most-status', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.x.most-status')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('Most Status Page Error', [
            'error' => $e->getMessage()
        ]);

        return view('mk.x.most-status')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
            'error'     => $e->getMessage(),
        ]);
    }
}

/**
 * API: Get Most Status (Most Viewed Posts)
 */
public function mostStatus(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'error'   => 'Missing required parameters: project_id, start_date, end_date'
            ], 400);
        }

        $result = $this->client->mostStatus($projectId, 'all', $startDate, $endDate);

        $posts = [];

        if (is_array($result)) {
            foreach ($result as $item) {
                $avatar = $item['avatar_url'] ?? $item['author']['image'] ?? '';
                $avatar = str_replace('_normal.', '.', $avatar);

                $posts[] = [
                    'id'             => $item['id']             ?? '',
                    'sub_id'         => $item['sub_id']         ?? '',
                    'name'           => $item['name']           ?? $item['author']['scr_name'] ?? '',
                    'content'        => $item['content']        ?? '',
                    'view_cnt'       => (int) ($item['view_cnt'] ?? $item['freq'] ?? 0),
                    'rt'             => (int) ($item['rt']      ?? 0),
                    'sentiment_str'  => $item['sentiment_str']  ?? 'Neutral',
                    'sentiment_freq' => $item['sentiment_freq'] ?? 0,
                    'sentiment_prec' => $item['sentiment_prec'] ?? 0,
                    'date_created'   => $item['date_created']   ?? '',
                    'avatar_url'     => $avatar,
                    'author'         => [
                        'name'     => $item['author']['name']     ?? $item['name'] ?? '',
                        'scr_name' => $item['author']['scr_name'] ?? $item['name'] ?? '',
                        'image'    => $item['author']['image']    ?? $avatar,
                        'flw_cnt'  => $item['author']['flw_cnt']  ?? 0,
                    ],
                ];
            }

            // Sort by view count descending
            usort($posts, fn($a, $b) => $b['view_cnt'] - $a['view_cnt']);
        }

        return response()->json([
            'success' => true,
            'data'    => $posts,
        ]);

    } catch (\Exception $e) {
        Log::error('X mostStatus API error', [
            'error'      => $e->getMessage(),
            'project_id' => $request->query('project_id'),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}
public function postWithLocationPage(Request $request)
{
    try {
        $projectsData = $this->client->listProjects(0, 100);
        $projects = $projectsData['data'] ?? [];

        $projectId = $request->query('project_id');

        if (!$projectId && count($projects) > 0) {
            $projectId = $projects[0]['id'] ?? null;

            if ($projectId) {
                return redirect()->route('mk.x.post-with-location', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.x.post-with-location')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('Post with Location Page Error', [
            'error' => $e->getMessage()
        ]);

        return view('mk.x.post-with-location')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
            'error'     => $e->getMessage(),
        ]);
    }
}

/**
 * API: Get Post with Location Data
 */
/**
 * API: Get Post with Location Data
 */
public function postWithLocation(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'error'   => 'Missing required parameters: project_id, start_date, end_date'
            ], 400);
        }

        // Call the API
        $result = $this->client->postWithLocation(
            $projectId, 
            $startDate, 
            $endDate,
            0,   // start_time
            23,  // end_time
            0,   // start (pagination)
            1000 // rows (increased limit)
        );

        Log::info('postWithLocation API response', [
            'count' => is_array($result) ? count($result) : 0,
            'sample' => is_array($result) && count($result) > 0 ? $result[0] : null
        ]);

        // Filter posts that have location data
        $posts = [];
        
        if (is_array($result)) {
            foreach ($result as $item) {
                // Only include posts with location
                if (empty($item['author_location']) && empty($item['cat_loc'])) {
                    continue;
                }
                
                // Parse author JSON
                $author = [];
                if (isset($item['author'])) {
                    $author = is_string($item['author']) 
                        ? json_decode($item['author'], true) 
                        : $item['author'];
                }
                
                // Parse contentJson
                $contentJson = [];
                if (isset($item['contentJson'])) {
                    $contentJson = is_string($item['contentJson']) 
                        ? json_decode($item['contentJson'], true) 
                        : $item['contentJson'];
                }
                
                $posts[] = [
                    'docid'                  => $item['id'] ?? '',
                    'author_id'              => $author['id'] ?? '',
                    'author_scr_name'        => $item['name'] ?? $author['scr_name'] ?? '',
                    'date_created'           => $item['date_created'] ?? '',
                    'location'               => $item['author_location'] ?? $item['cat_loc'] ?? '',
                    'coordinates'            => $item['cat_coord'] ?? '',
                    'content'                => $item['content'] ?? '',
                    'user_mention1'          => null, // Not available in this endpoint
                    'user_mention2'          => null,
                    'user_mention3'          => null,
                    'class_sentiment'        => $item['class_sentiment'] ?? '0',
                    'class_sentiment_label'  => $item['class_sentiment'] ?? 'neutral',
                ];
            }
        }

        Log::info('postWithLocation filtered posts', [
            'total_received' => is_array($result) ? count($result) : 0,
            'with_location' => count($posts)
        ]);

        return response()->json([
            'success' => true,
            'data'    => $posts,
            'total'   => count($posts),
        ]);

    } catch (\Exception $e) {
        Log::error('postWithLocation API error', [
            'error'      => $e->getMessage(),
            'trace'      => $e->getTraceAsString(),
            'project_id' => $request->query('project_id'),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}
public function trendingTopicsPage(Request $request)
{
    try {
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $location  = $request->query('location', 'Indonesia');

        return view('mk.x.trending-topics')->with([
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'location'  => $location,
        ]);

    } catch (\Exception $e) {
        Log::error('X Trending Topics Page Error', [
            'error' => $e->getMessage()
        ]);

        return view('mk.x.trending-topics')->with([
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'location'  => 'Indonesia',
            'error'     => $e->getMessage(),
        ]);
    }
}

public function trendingTopicsData(Request $request)
{
    try {
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $location  = $request->query('location', 'Indonesia');

        if (!$startDate || !$endDate) {
            return response()->json([
                'success' => false,
                'error'   => 'Missing required parameters: start_date, end_date'
            ], 400);
        }

        $result = $this->client->twitterTrendingTopics(
            $startDate,
            $endDate,
            0,  // start_time
            23, // end_time
            $location,
            ''  // topics (empty for all)
        );

        // Transform data - FILTER ONLY TWITTER/X DATA
        $trending = [];
        $allTopics = [];
        
        // 🔥 SENTIMENT KEYWORDS FOR CLASSIFICATION
        $positiveKeywords = [
            'win', 'winner', 'won', 'best', 'good', 'great', 'love', 'happy', 'success', 
            'amazing', 'excellent', 'perfect', 'beautiful', 'wonderful', 'fantastic',
            'celebrate', 'celebration', 'victory', 'achievement', 'congratulations'
        ];
        
        $negativeKeywords = [
            'bad', 'worst', 'hate', 'sad', 'fail', 'failed', 'lose', 'lost', 'angry',
            'terrible', 'awful', 'poor', 'wrong', 'crisis', 'disaster', 'tragic',
            'death', 'died', 'scandal', 'controversial', 'protest', 'boycott'
        ];
        
        foreach ($result as $datetime => $period) {
            if (!is_array($period) || !isset($period['data'])) continue;
            
            $date = date('Y-m-d', strtotime($datetime));
            $timeAgo = $period['str_datetime_ago'] ?? '';
            
            // 🔥 FILTER: Only process Twitter/X topics
            foreach ($period['data'] as $topic) {
                $name = $topic['name'] ?? '';
                $volume = (int) ($topic['tweet_volume_i'] ?? 0);
                $rank = (int) ($topic['rank_i'] ?? 0);
                $url = $topic['url'] ?? '';
                
                // Skip if not a valid topic
                if (!$name) continue;
                
                // 🔥 Check if this is a Twitter/X topic
                $source = strtolower($topic['source'] ?? '');
                $isTwitter = (
                    stripos($url, 'twitter.com') !== false || 
                    stripos($url, 'x.com') !== false ||
                    $source === 'twitter' ||
                    $source === 'x' ||
                    $source === 'twit'
                );
                
                // Skip non-Twitter topics
                if (!$isTwitter && $url && $url !== '#') {
                    if (stripos($url, 'facebook.com') !== false ||
                        stripos($url, 'youtube.com') !== false ||
                        stripos($url, 'instagram.com') !== false ||
                        stripos($url, 'tiktok.com') !== false) {
                        continue;
                    }
                }
                
                // 🔥 DETECT SENTIMENT
                $sentiment = 'neutral';
                $lowerName = strtolower($name);
                
                // Check positive keywords
                foreach ($positiveKeywords as $keyword) {
                    if (stripos($lowerName, $keyword) !== false) {
                        $sentiment = 'positive';
                        break;
                    }
                }
                
                // Check negative keywords (only if not already positive)
                if ($sentiment === 'neutral') {
                    foreach ($negativeKeywords as $keyword) {
                        if (stripos($lowerName, $keyword) !== false) {
                            $sentiment = 'negative';
                            break;
                        }
                    }
                }
                
                // Collect all unique Twitter topics
                if (!isset($allTopics[$name])) {
                    $allTopics[$name] = [
                        'name' => $name,
                        'total_volume' => 0,
                        'appearances' => 0,
                        'avg_rank' => 0,
                        'url' => $url,
                        'sentiment' => $sentiment,  // 🔥 ADD SENTIMENT
                        'history' => []
                    ];
                } else {
                    // Update sentiment if we found a more specific one
                    if ($allTopics[$name]['sentiment'] === 'neutral' && $sentiment !== 'neutral') {
                        $allTopics[$name]['sentiment'] = $sentiment;
                    }
                }
                
                $allTopics[$name]['total_volume'] += $volume;
                $allTopics[$name]['appearances']++;
                $allTopics[$name]['avg_rank'] += $rank;
                $allTopics[$name]['history'][] = [
                    'date' => $date,
                    'datetime' => $datetime,
                    'rank' => $rank,
                    'volume' => $volume,
                    'time_ago' => $timeAgo,
                    'sentiment' => $sentiment
                ];
            }
            
            // Store by date - only Twitter topics
            if (!isset($trending[$date])) {
                $trending[$date] = [
                    'date' => $date,
                    'datetime' => $datetime,
                    'time_ago' => $timeAgo,
                    'topics' => []
                ];
            }
            
            // Filter period data to only include Twitter topics
            $twitterTopics = array_filter($period['data'], function($topic) {
                $url = $topic['url'] ?? '';
                $source = strtolower($topic['source'] ?? '');
                
                $isTwitter = (
                    stripos($url, 'twitter.com') !== false || 
                    stripos($url, 'x.com') !== false ||
                    $source === 'twitter' ||
                    $source === 'x' ||
                    $source === 'twit'
                );
                
                // Exclude other social media
                if ($url && $url !== '#') {
                    if (stripos($url, 'facebook.com') !== false ||
                        stripos($url, 'youtube.com') !== false ||
                        stripos($url, 'instagram.com') !== false ||
                        stripos($url, 'tiktok.com') !== false) {
                        return false;
                    }
                }
                
                return $isTwitter || (!$url || $url === '#');
            });
            
            $trending[$date]['topics'] = array_values($twitterTopics);
        }
        
        // Calculate averages and sort
        foreach ($allTopics as &$topic) {
            if ($topic['appearances'] > 0) {
                $topic['avg_rank'] = round($topic['avg_rank'] / $topic['appearances'], 1);
            }
        }
        
        // Sort by total volume
        usort($allTopics, fn($a, $b) => $b['total_volume'] - $a['total_volume']);
        
        // Sort trending by date
        krsort($trending);

        // 🔥 COUNT SENTIMENTS
        $sentimentCounts = [
            'positive' => 0,
            'neutral' => 0,
            'negative' => 0
        ];
        
        foreach ($allTopics as $topic) {
            $sentimentCounts[$topic['sentiment']]++;
        }

        Log::info('trendingTopicsData - Twitter/X with Sentiment', [
            'total_periods' => count($trending),
            'total_unique_topics' => count($allTopics),
            'sentiment_breakdown' => $sentimentCounts,
            'sample_topic' => $allTopics[0] ?? null
        ]);

        // ✅ SEND ALL TWITTER/X TOPICS WITH SENTIMENT DATA
        return response()->json([
            'success' => true,
            'data' => [
                'trending' => array_values($trending),
                'top_topics' => $allTopics,
                'total_periods' => count($trending),
                'total_unique_topics' => count($allTopics),
                'sentiment_counts' => $sentimentCounts,  // 🔥 ADD SENTIMENT STATS
            ]
        ]);

    } catch (\Exception $e) {
        Log::error('trendingTopicsData API error', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ]);

        return response()->json([
            'success' => false,
            'error'   => $e->getMessage()
        ], 500);
    }
}
public function trendingWordCloudPage(Request $request)
{
    try {
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $location  = $request->query('location', 'Indonesia');

        return view('mk.x.trending-word-cloud')->with([
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'location'  => $location,
        ]);

    } catch (\Exception $e) {
        Log::error('X Trending Word Cloud Page Error', [
            'error' => $e->getMessage()
        ]);

        return view('mk.x.trending-word-cloud')->with([
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'location'  => 'Indonesia',
            'error'     => $e->getMessage(),
        ]);
    }
}

}