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

            $result = $this->client->topHashtags($projectId, 'fb', $startDate, $endDate);

            Log::info('FB trendingTopicsData raw result', [
                'type'   => gettype($result),
                'keys'   => is_array($result) ? array_keys($result) : [],
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            $hashtags      = [];
            $totalMentions = 0;
            $rawItems      = [];

            if (isset($result['data']['hashtags']) && is_array($result['data']['hashtags'])) {
                $rawItems = $result['data']['hashtags'];
            } elseif (isset($result['data']) && is_array($result['data'])) {
                $rawItems = $result['data'];
            } elseif (is_array($result)) {
                $firstVal = reset($result);
                if (is_array($firstVal) && isset($firstVal['name'])) {
                    $rawItems = $result;
                } elseif (isset($result['fb']) && is_array($result['fb'])) {
                    $rawItems = $result['fb'];
                } else {
                    $rawItems = $result;
                }
            }

            foreach ($rawItems as $item) {
                if (!is_array($item)) continue;

                $name  = $item['name'] ?? $item['hashtag'] ?? '';
                $size  = (int) ($item['size'] ?? $item['count'] ?? $item['total'] ?? 0);
                $media = strtolower($item['media'] ?? $item['source'] ?? $item['platform'] ?? '');

                if ($media && !in_array($media, ['fb', 'facebook', ''])) continue;

                if ($name && $size > 0) {
                    $hashtags[]     = ['name' => ltrim($name, '#'), 'size' => $size, 'hashtag' => ltrim($name, '#')];
                    $totalMentions += $size;
                }
            }

            usort($hashtags, fn($a, $b) => $b['size'] - $a['size']);

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
            Log::error('FB trendingTopicsData error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // MOST VIEWED POSTS (Facebook)
    // ─────────────────────────────────────────────────────

    /**
     * Display Facebook Most Viewed Posts Page
     */
    public function mostViewedPostsPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects     = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.facebook.most-viewed-posts', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.most-viewed-posts')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Most Viewed Posts Page Error', ['error' => $e->getMessage()]);

            return view('mk.facebook.most-viewed-posts')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Facebook Most Viewed Posts Data
     * Uses /fb_top_status/ endpoint (correct Facebook-specific endpoint)
     */
    public function mostViewedPostsData(Request $request)
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

            // ✅ FIX: Use fb_top_status endpoint instead of twitter_most_status
            $result = $this->client->fbTopStatus($projectId, $startDate, $endDate);

            Log::info('FB mostViewedPostsData raw result', [
                'type'   => gettype($result),
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            $posts = [];

            // Handle both flat array and nested data structures
            $items = [];
            if (isset($result['data']) && is_array($result['data'])) {
                $items = $result['data'];
            } elseif (is_array($result) && !isset($result['success'])) {
                $items = $result;
            }

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                // Extra safety filter: only FB posts
                $media = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                if ($media && !str_contains($media, 'fb') && !str_contains($media, 'facebook')) {
                    continue;
                }

                // Try to get author name from multiple possible fields
                $authorName = $item['contentJson']['from']['name']
                    ?? $item['author_name']
                    ?? $item['author']['name']
                    ?? $item['name']
                    ?? 'Unknown';

                // Clean up name if it contains HTML bold tags (e.g. "<b>Name:</b> ...")
                if (str_contains($authorName, '<b>')) {
                    preg_match('/<b>(.*?)<\/b>/', $authorName, $matches);
                    $authorName = $matches[1] ?? $authorName;
                    $authorName = trim(str_replace(':', '', $authorName));
                }

                // Try to get profile picture from multiple sources
                $profilePic = $item['contentJson']['from']['picture']['data']['url']
                    ?? $item['profile_url']
                    ?? $item['avatar_url']
                    ?? $item['author']['image']
                    ?? '';

                $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $shares   = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);
                $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);

                // Engagement score: likes + comments + shares
                $engagement = $likes + $comments + $shares;

                // Some FB items have freq (comment count) or interaction fields
                $viewCount = (int) ($item['view_cnt']
                    ?? $item['freq']
                    ?? $engagement);

                // Get post URL
                $postUrl = $item['url'] ?? $item['link'] ?? null;

                // Get post ID for direct Facebook link
                $subId = $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '';

                // Clean content - strip HTML if present
                $content = $item['content'] ?? $item['name'] ?? '';
                if (str_contains($content, '<b>')) {
                    // Remove name prefix pattern like "<b>Name:</b> content"
                    $content = preg_replace('/<b>.*?<\/b>\s*/', '', $content);
                    $content = trim($content);
                }

                $posts[] = [
                    'id'             => $item['id']            ?? '',
                    'sub_id'         => $subId,
                    'name'           => $authorName,
                    'content'        => $content,
                    'view_cnt'       => $viewCount,
                    'likes'          => $likes,
                    'shares'         => $shares,
                    'comments'       => $comments,
                    'engagement'     => $engagement,
                    'sentiment_str'  => $item['sentiment_str']  ?? 'Neutral',
                    'sentiment_prec' => $item['sentiment_prec'] ?? 0,
                    'date_created'   => $item['date_created']   ?? '',
                    'url'            => $postUrl,
                    'avatar_url'     => $profilePic,
                    'tcode'          => $item['tcode']          ?? 'fb-post',
                    'author'         => [
                        'name'     => $authorName,
                        'scr_name' => $authorName,
                        'image'    => $profilePic,
                    ],
                ];
            }

            // Sort by engagement (likes + comments + shares) descending
            usort($posts, fn($a, $b) => $b['engagement'] - $a['engagement']);

            Log::info('FB mostViewedPostsData processed', [
                'total_posts' => count($posts),
            ]);

            return response()->json([
                'success' => true,
                'data'    => $posts,
            ]);

        } catch (\Exception $e) {
            Log::error('FB mostViewedPostsData error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
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

            return response()->json(['success' => true, 'data' => ['total' => $total]]);

        } catch (\Exception $e) {
            Log::error('Facebook totalUsers API error', ['error' => $e->getMessage()]);
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
                return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
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

            return response()->json(['success' => true, 'data' => ['total' => $total]]);

        } catch (\Exception $e) {
            Log::error('Facebook totalAuthors API error', ['error' => $e->getMessage()]);
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
                return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
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

            return response()->json(['success' => true, 'data' => ['total' => $total, 'chart' => $chartData]]);

        } catch (\Exception $e) {
            Log::error('Facebook volumeTotal API error', ['error' => $e->getMessage()]);
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
                return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
            }

            $result   = $this->client->getSentiment($projectId, 'facebook', $startDate, $endDate);
            $positive = 0;
            $negative = 0;
            $neutral  = 0;

            if (isset($result['pos'], $result['neg'], $result['net'])) {
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral  = (int) $result['net'];
            } elseif (isset($result['bymedia']['fb'])) {
                $d = $result['bymedia']['fb'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            } elseif (isset($result['bymedia']['facebook'])) {
                $d = $result['bymedia']['facebook'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }

            return response()->json(['success' => true, 'data' => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral]]);

        } catch (\Exception $e) {
            Log::error('Facebook sentimentTotal API error', ['error' => $e->getMessage()]);
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
                return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
            }

            $result = $this->client->mostActiveUsers($projectId, $startDate, $endDate);
            $users  = [];

            if (isset($result['data']['data']) && is_array($result['data']['data'])) {
                foreach ($result['data']['data'] as $user) {
                    $media = strtolower($user['media'] ?? '');
                    if ($media !== 'fb' && $media !== 'facebook') continue;

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

            return response()->json(['success' => true, 'data' => ['data' => $users]]);

        } catch (\Exception $e) {
            Log::error('Facebook mostActiveUsers API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
    public function topHashtagsPage(Request $request)
{
    try {
        $projectsData = $this->client->listProjects(0, 100);
        $projects     = $projectsData['data'] ?? [];

        $projectId = $request->query('project_id');

        if (!$projectId && count($projects) > 0) {
            $projectId = $projects[0]['id'] ?? null;

            if ($projectId) {
                return redirect()->route('mk.facebook.top-hashtags', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.facebook.top-hashtags')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('Facebook Top Hashtags Page Error', ['error' => $e->getMessage()]);

        return view('mk.facebook.top-hashtags')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
            'error'     => $e->getMessage(),
        ]);
    }
}
public function authorsDemographicsPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects     = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.facebook.authors.demographics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.authors-demographics')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Authors Demographics Page Error', ['error' => $e->getMessage()]);

            return view('mk.facebook.authors-demographics')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Facebook Authors Age Data
     */
    public function authorsAgeData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->authorsAge($projectId, 'facebook', $startDate, $endDate);

            Log::info('FB authorsAge API response', [
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('FB authorsAge API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get Facebook Authors Gender Data
     */
    public function authorsGenderData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->authorsGender($projectId, 'facebook', $startDate, $endDate);

            Log::info('FB authorsGender API response', [
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('FB authorsGender API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get Facebook Authors Type Data
     */
    public function authorsTypeData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->authorsType($projectId, 'facebook', $startDate, $endDate);

            Log::info('FB authorsType API response', [
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('FB authorsType API error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
public function geographicPage(Request $request)
    {
        try {
            $projectsData = $this->client->listProjects(0, 100);
            $projects     = $projectsData['data'] ?? [];

            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.facebook.geographic', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.geographic')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Geographic Page Error', ['error' => $e->getMessage()]);

            return view('mk.facebook.geographic')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get Facebook Geo User Data (filtered to FB only via 'fb' keyword)
     */
    public function geoUser(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            // Gunakan 'fb' — sama seperti pattern FB lainnya di controller ini
            $result = $this->client->geoUser($projectId, 'fb', $startDate, $endDate);

            Log::info('FB geoUser raw response', [
                'type'   => gettype($result),
                'keys'   => is_array($result) ? array_keys($result) : [],
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('FB geoUser API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get Facebook Geo Sentiment Data (filtered to FB only)
     */
    public function geoSentiment(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->geoSentiment($projectId, 'fb', $startDate, $endDate);

            Log::info('FB geoSentiment raw response', [
                'type'   => gettype($result),
                'keys'   => is_array($result) ? array_keys($result) : [],
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('FB geoSentiment API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Get Facebook Top Locations Data
     */
    public function topLocations(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result    = $this->client->topLocations($projectId, 'fb', $startDate, $endDate);
            $locations = [];

            // Normalize berbagai kemungkinan shape response
            $items = [];
            if (isset($result['data']) && is_array($result['data'])) {
                $items = $result['data'];
            } elseif (is_array($result)) {
                $items = $result;
            }

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $name  = $item['name'] ?? $item['location'] ?? $item['city'] ?? '';
                $count = (int) ($item['count'] ?? $item['total'] ?? $item['y'] ?? 0);

                if ($name && $count > 0) {
                    $locations[] = ['name' => $name, 'count' => $count];
                }
            }

            usort($locations, fn($a, $b) => $b['count'] - $a['count']);

            Log::info('FB topLocations processed', ['count' => count($locations)]);

            return response()->json(['success' => true, 'data' => $locations]);

        } catch (\Exception $e) {
            Log::error('FB topLocations API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }


















}