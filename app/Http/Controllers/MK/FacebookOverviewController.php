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
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            if (!$projectId) {
                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

                return view('mk.facebook.overview', [
                    'projectId' => null,
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'projects'  => [],
                ]);
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.overview')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Overview Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('mk.facebook.overview')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => 'Failed to load projects: ' . $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
    // TRENDING TOPICS
    // ─────────────────────────────────────────────────────

    /**
     * Display Facebook Trending Topics Page
     */
    public function trendingTopicsPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects     = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.facebook.trending-topics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.facebook-trending-topics')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Trending Topics Page Error', ['error' => $e->getMessage()]);

            return view('mk.facebook.facebook-trending-topics')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Facebook Trending Topics Data
     * Uses topHashtags endpoint filtered to 'fb' media
     */
    public function trendingTopicsData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date',
                ], 400);
            }

            // Fetch hashtags with 'fb' media filter
            $result = $this->client->topHashtags($projectId, 'fb', $startDate, $endDate);

            Log::info('FB trendingTopicsData raw result', [
                'type'   => gettype($result),
                'keys'   => is_array($result) ? array_keys($result) : [],
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            $hashtags      = [];
            $totalMentions = 0;

            // Normalise response — handles multiple wrapping formats from MediaKernels
            $rawItems = [];

            if (isset($result['data']['hashtags']) && is_array($result['data']['hashtags'])) {
                $rawItems = $result['data']['hashtags'];
            } elseif (isset($result['data']) && is_array($result['data'])) {
                $rawItems = $result['data'];
            } elseif (is_array($result)) {
                $firstVal = reset($result);
                if (is_array($firstVal) && isset($firstVal['name'])) {
                    // Flat array of hashtag objects
                    $rawItems = $result;
                } elseif (isset($result['fb']) && is_array($result['fb'])) {
                    // Keyed by media: {'fb': [...], 'twit': [...]}
                    $rawItems = $result['fb'];
                } else {
                    $rawItems = $result;
                }
            }

            foreach ($rawItems as $item) {
                if (!is_array($item)) continue;

                $name = $item['name'] ?? $item['hashtag'] ?? '';
                $size = (int) ($item['size'] ?? $item['count'] ?? $item['total'] ?? 0);

                // Extra safety: skip explicitly non-Facebook items
                $media = strtolower($item['media'] ?? $item['source'] ?? $item['platform'] ?? '');
                if ($media && !in_array($media, ['fb', 'facebook', ''])) {
                    continue;
                }

                if ($name && $size > 0) {
                    $hashtags[] = [
                        'name'    => ltrim($name, '#'),
                        'size'    => $size,
                        'hashtag' => ltrim($name, '#'),
                    ];
                    $totalMentions += $size;
                }
            }

            // Sort descending by mention count
            usort($hashtags, fn($a, $b) => $b['size'] - $a['size']);

            Log::info('FB trendingTopicsData processed', [
                'raw_items'      => count($rawItems),
                'after_filter'   => count($hashtags),
                'total_mentions' => $totalMentions,
            ]);

            return response()->json([
                'success' => true,
                'data'    => [
                    'hashtags'       => $hashtags,
                    'total_hashtags' => count($hashtags),
                    'total_mentions' => $totalMentions,
                    'top_hashtag'    => $hashtags[0] ?? null,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('FB trendingTopicsData error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json([
                'success' => false,
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // STATS APIs (Overview)
    // ─────────────────────────────────────────────────────

    /**
     * API: Get Total Users for Facebook
     */
    public function totalUsers(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date',
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
                'data'    => ['total' => $total],
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook totalUsers API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date',
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
                'data'    => ['total' => $total],
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook totalAuthors API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date',
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

            // Chart data from trends
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
                'data'    => ['total' => $total, 'chart' => $chartData],
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook volumeTotal API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date',
                ], 400);
            }

            $result = $this->client->getSentiment($projectId, 'facebook', $startDate, $endDate);

            $positive = 0;
            $negative = 0;
            $neutral  = 0;

            if (isset($result['pos'], $result['neg'], $result['net'])) {
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral  = (int) $result['net'];
            } elseif (isset($result['bymedia']['fb'])) {
                $d        = $result['bymedia']['fb'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            } elseif (isset($result['bymedia']['facebook'])) {
                $d        = $result['bymedia']['facebook'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }

            return response()->json([
                'success' => true,
                'data'    => [
                    'positive' => $positive,
                    'negative' => $negative,
                    'neutral'  => $neutral,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook sentimentTotal API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'error'   => 'Missing required parameters: project_id, start_date, end_date',
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

                    $username   = $user['contentJson']['from']['name'] ?? $user['name'] ?? '';
                    $profileUrl = $user['profile_url'] ?? $user['contentJson']['from']['picture']['data']['url'] ?? '';
                    $likes      = (int) ($user['num_likes']    ?? 0);
                    $shares     = (int) ($user['num_shares']   ?? 0);
                    $comments   = (int) ($user['num_comments'] ?? 0);
                    $posts      = (int) ($user['y']            ?? ($likes + $shares + $comments));

                    if ($username) {
                        $users[] = [
                            'username'          => $username,
                            'name'              => $username,
                            'profile_url'       => $profileUrl,
                            'profile_image_url' => $profileUrl,
                            'likes'             => $likes,
                            'shares'            => $shares,
                            'comments'          => $comments,
                            'posts'             => $posts,
                            'y'                 => $posts,
                            'id'                => $user['id'] ?? '',
                        ];
                    }
                }
            }

            return response()->json([
                'success' => true,
                'data'    => ['data' => $users],
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook mostActiveUsers API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}