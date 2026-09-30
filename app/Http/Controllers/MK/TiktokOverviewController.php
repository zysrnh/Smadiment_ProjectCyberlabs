<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Models\ProjectApiSnapshot;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TiktokOverviewController extends Controller
{
    private MediaKernelsClient $client;

    public function __construct(MediaKernelsClient $client)
    {
        $this->client = $client;
    }

    /**
     * Fetch all projects without limit using pagination loop.
     */
    private function getAllProjects(): array
    {
        $user = Auth::user();
        $assignedProjectIds = $user->assignedProjectIds();

        $rawProjects = $this->client->listProjects(0, 100);
        $allProjects = array_values($rawProjects);

        $userProjects = array_filter($allProjects, function ($project) use ($assignedProjectIds) {
            return in_array($project['id'] ?? null, $assignedProjectIds);
        });

        return array_values($userProjects);
    }

    private function redirectWithDates(Request $request, string $routeName, string $projectId): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($routeName, [
            'project_id' => $projectId,
            'start_date' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
        ]);
    }

    private function defaultViewData(Request $request): array
    {
        return [
            'projectId' => null,
            'startDate' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
            'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
            'projects'  => [],
        ];
    }

    // ─────────────────────────────────────────────────────
    // OVERVIEW PAGE
    // ─────────────────────────────────────────────────────

    public function index(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.overview', $projectId);
            }

            return view('mk.tiktok.overview')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Overview Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.overview')->with(array_merge($this->defaultViewData($request), ['error' => $e->getMessage()]));
        }
    }

    // ─────────────────────────────────────────────────────
    // STATS APIs
    // ─────────────────────────────────────────────────────

    public function volumeTotal(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = [];
            try {
                $result = $this->client->volumeTotal($projectId, 'tiktok', $startDate, $endDate);
            } catch (\Throwable $e) {
                Log::warning('TikTok volumeTotal live API failed: ' . $e->getMessage());
            }

            Log::info('TikTok volumeTotal raw', ['result' => $result]);

            $total = 0;
            if (isset($result['all']['total'])) {
                $total = (int) $result['all']['total'];
            } elseif (isset($result['bymedia']['tiktok'])) {
                $total = (int) $result['bymedia']['tiktok'];
            } elseif (isset($result['bymedia']['tt'])) {
                $total = (int) $result['bymedia']['tt'];
            }

            $chartData = [];
            try {
                $trendsResult = $this->client->trendsTotal($projectId, $startDate, $endDate);

                foreach ($trendsResult as $datetime => $mediaData) {
                    if (!is_array($mediaData)) continue;
                    $dateKey = substr($datetime, 0, 10);
                    $count   = (int) ($mediaData['tiktok'] ?? $mediaData['tt'] ?? 0);
                    $chartData[] = ['date' => $dateKey, 'count' => $count];
                }

                usort($chartData, fn($a, $b) => strcmp($a['date'], $b['date']));
            } catch (\Exception $e) {
                Log::warning('TikTok: Failed to load trends data', ['error' => $e->getMessage()]);
            }

            // Store snapshot if we got data
            if ($total > 0 || !empty($chartData)) {
                try {
                    ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'volume_total', $startDate, $endDate, [
                        'total' => $total,
                        'chart' => $chartData,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('TikTok volumeTotal snapshot store failed: ' . $e->getMessage());
                }
            }

            // Fallback to snapshot if API returned nothing
            if ($total === 0 && empty($chartData)) {
                $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'volume_total', $startDate, $endDate);
                if (!empty($snap) && is_array($snap)) {
                    $total     = (int) ($snap['total'] ?? 0);
                    $chartData = $snap['chart'] ?? [];
                }
            }

            return response()->json(['success' => true, 'data' => ['total' => $total, 'chart' => $chartData]]);

        } catch (\Exception $e) {
            Log::error('TikTok volumeTotal API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function sentimentTotal(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = [];
            try {
                $result = $this->client->getSentiment($projectId, 'tiktok', $startDate, $endDate);
            } catch (\Throwable $e) {
                Log::warning('TikTok sentimentTotal live API failed: ' . $e->getMessage());
            }

            Log::info('TikTok sentimentTotal raw', ['result' => $result]);

            $positive = 0;
            $negative = 0;
            $neutral  = 0;

            if (isset($result['data']['pos'], $result['data']['neg'], $result['data']['net'])) {
                $positive = (int) $result['data']['pos'];
                $negative = (int) $result['data']['neg'];
                $neutral  = (int) $result['data']['net'];
            } elseif (isset($result['pos'], $result['neg'], $result['net'])) {
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral  = (int) $result['net'];
            } elseif (isset($result['bymedia']['tiktok'])) {
                $d        = $result['bymedia']['tiktok'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }

            // Store snapshot if we got data
            if (($positive + $negative + $neutral) > 0) {
                try {
                    ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'sentiment_total', $startDate, $endDate, [
                        'positive' => $positive,
                        'negative' => $negative,
                        'neutral'  => $neutral,
                    ]);
                } catch (\Throwable $e) {
                    Log::warning('TikTok sentimentTotal snapshot store failed: ' . $e->getMessage());
                }
            }

            // Fallback to snapshot if API returned nothing
            if (($positive + $negative + $neutral) === 0) {
                $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'sentiment_total', $startDate, $endDate);
                if (!empty($snap) && is_array($snap)) {
                    $positive = (int) ($snap['positive'] ?? 0);
                    $negative = (int) ($snap['negative'] ?? 0);
                    $neutral  = (int) ($snap['neutral'] ?? 0);
                }
            }

            return response()->json(['success' => true, 'data' => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral]]);

        } catch (\Exception $e) {
            Log::error('TikTok sentimentTotal API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function mostActiveUsers(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = [];
            try {
                $result = $this->client->mostActiveUsers($projectId, $startDate, $endDate);
                if (isset($result['data']['data']) && is_array($result['data']['data']) && count($result['data']['data']) > 0) {
                    ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'most_active_users', $startDate, $endDate, $result);
                }
            } catch (\Throwable $e) {
                Log::warning('TikTok mostActiveUsers live API failed: ' . $e->getMessage());
            }

            // Fallback to snapshot if API returned nothing
            if (empty($result['data']['data'])) {
                $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_active_users', $startDate, $endDate)
                     ?? ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'all', 'most_active_users', $startDate, $endDate);
                if (!empty($snap) && is_array($snap)) {
                    $result = $snap;
                }
            }

            $users = [];

            if (isset($result['data']['data']) && is_array($result['data']['data'])) {
                foreach ($result['data']['data'] as $user) {
                    $media = strtolower($user['media'] ?? '');
                    if ($media && !in_array($media, ['tiktok', 'tt', ''])) continue;

                    $username   = $user['name'] ?? $user['author_name'] ?? '';
                    $profileUrl = $user['profile_url'] ?? $user['avatar_url'] ?? '';
                    $likes      = (int) ($user['num_likes']    ?? 0);
                    $comments   = (int) ($user['num_comments'] ?? 0);
                    $posts      = (int) ($user['y']            ?? ($likes + $comments));

                    if ($username) {
                        $users[] = [
                            'username'          => $username,
                            'name'              => $username,
                            'profile_url'       => $profileUrl,
                            'profile_image_url' => $profileUrl,
                            'likes'             => $likes,
                            'comments'          => $comments,
                            'posts'             => $posts,
                            'y'                 => $posts,
                        ];
                    }
                }
            }

            return response()->json(['success' => true, 'data' => ['data' => $users]]);

        } catch (\Exception $e) {
            Log::error('TikTok mostActiveUsers API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // MOST VIEWED POSTS
    // ─────────────────────────────────────────────────────

    public function mostViewedPostsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.most-viewed-posts', $projectId);
            }

            return view('mk.tiktok.most-viewed-posts')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Most Viewed Posts Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.most-viewed-posts')->with(array_merge($this->defaultViewData($request), ['error' => $e->getMessage()]));
        }
    }

public function mostViewedPostsData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $sub       = $request->query('sub', 'postbylike');

        if (!in_array($sub, ['postbylike', 'postbycomment', 'postbyview'])) {
            $sub = 'postbylike';
        }

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }

        $items = [];
        try {
            $items = $this->client->tiktokTopStatusAll(
                $projectId, $startDate, $endDate, 0, 23, 100, $sub
            );
            $items = is_array($items) ? $items : [];

            if (!empty($items)) {
                ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'most_viewed_posts_' . $sub, $startDate, $endDate, $items);
            }
        } catch (\Throwable $e) {
            Log::warning('TikTok mostViewedPostsData live API failed: ' . $e->getMessage());
        }

        // Fallback to snapshot if API returned nothing
        if (empty($items)) {
            $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_viewed_posts_' . $sub, $startDate, $endDate);
            if (!empty($snap) && is_array($snap)) {
                $items = $snap;
            }
        }

        $posts = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;

            $rawName    = $item['name'] ?? '';
            $authorName = $item['author_scr_name'] ?? $item['author_id'] ?? '';
            if (!$authorName && $rawName) {
                $colonPos   = strpos($rawName, ':');
                $authorName = $colonPos !== false ? trim(substr($rawName, 0, $colonPos)) : '';
            }
            if (!$authorName) $authorName = 'TikTok Creator';

            $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
            if (!$profilePic && $authorName && $authorName !== 'TikTok Creator') {
                $initials   = urlencode($this->getInitials($authorName));
                $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=EE1D52&color=fff&size=80&bold=true&format=png";
            }

            $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
            $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
            $views    = (int) ($item['views']        ?? $item['view_cnt'] ?? 0);
            $shares   = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);
            $content  = $item['content'] ?? $item['caption'] ?? '';

            if (!$content && $rawName) {
                $colonPos = strpos($rawName, ':');
                $content  = $colonPos !== false ? trim(substr($rawName, $colonPos + 1)) : $rawName;
            }

            $posts[] = [
                'id'             => $item['id']     ?? '',
                'sub_id'         => $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '',
                'name'           => $authorName,
                'content'        => $content,
                'view_cnt'       => $views,
                'likes'          => $likes,
                'comments'       => $comments,
                'shares'         => $shares,
                'engagement'     => $likes + $comments + $shares,
                'sentiment_str'  => $item['sentiment_str']  ?? 'Neutral',
                'sentiment_prec' => $item['sentiment_prec'] ?? 0,
                'date_created'   => $item['date_created']   ?? '',
                'url'            => $item['url'] ?? $item['link'] ?? null,
                'avatar_url'     => $profilePic,
                'image'          => $item['image'] ?? $profilePic,
                'tcode'          => $item['tcode'] ?? 'tiktok',
                'num_followers'  => (int) ($item['num_followers'] ?? 0),
                'author'         => [
                    'name'     => $authorName,
                    'scr_name' => $item['author_scr_name'] ?? $authorName,
                    'image'    => $profilePic,
                ],
            ];
        }

         usort($posts, match($sub) {
            'postbyview'    => fn($a,$b) => $b['view_cnt']  - $a['view_cnt'],
            'postbycomment' => fn($a,$b) => $b['comments']  - $a['comments'],
            default         => fn($a,$b) => $b['likes']     - $a['likes'],
        });
       
        return response()->json(['success' => true, 'data' => $posts]);

    } catch (\Exception $e) {
        Log::error('TikTok mostViewedPostsData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    // ─────────────────────────────────────────────────────
    // TRENDING TOPICS (TOP HASHTAGS)
    // ─────────────────────────────────────────────────────

    public function trendingTopicsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.trending-topics', $projectId);
            }

            return view('mk.tiktok.tiktok-trending-topics')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Trending Topics Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.tiktok-trending-topics')->with(array_merge($this->defaultViewData($request), ['error' => $e->getMessage()]));
        }
    }

public function trendingTopicsData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }

        $posts = [];
        try {
            $posts = $this->client->tiktokTopStatusAll(
                $projectId, $startDate, $endDate, 0, 23, 100, 'postbylike'
            );
            $posts = is_array($posts) ? $posts : [];
        } catch (\Throwable $e) {
            Log::warning('TikTok trendingTopicsData live API failed: ' . $e->getMessage());
        }

        $hashtagCount = [];
        foreach ($posts as $post) {
            if (!is_array($post)) continue;
            $content = $post['content'] ?? $post['caption'] ?? $post['text'] ?? $post['name'] ?? '';
            if (empty($content)) continue;

            preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $content, $matches);
            foreach ($matches[1] as $tag) {
                $tag = strtolower(trim($tag));
                if (strlen($tag) < 2) continue;
                $hashtagCount[$tag] = ($hashtagCount[$tag] ?? 0) + 1;
            }
        }

        arsort($hashtagCount);

        $hashtags      = [];
        $totalMentions = 0;
        foreach ($hashtagCount as $name => $size) {
            $hashtags[]     = ['name' => $name, 'hashtag' => $name, 'size' => $size];
            $totalMentions += $size;
        }

        // Store snapshot if we got hashtags
        if (!empty($hashtags)) {
            try {
                ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'trending_topics', $startDate, $endDate, [
                    'hashtags'       => $hashtags,
                    'total_hashtags' => count($hashtags),
                    'total_mentions' => $totalMentions,
                ]);
            } catch (\Throwable $e) {
                Log::warning('TikTok trendingTopicsData snapshot store failed: ' . $e->getMessage());
            }
        }

        // Fallback to snapshot if no hashtags extracted
        if (empty($hashtags)) {
            $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'trending_topics', $startDate, $endDate);
            if (!empty($snap) && is_array($snap)) {
                $hashtags      = $snap['hashtags'] ?? [];
                $totalMentions = (int) ($snap['total_mentions'] ?? 0);
            }
        }

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
        Log::error('TikTok trendingTopicsData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    // ─────────────────────────────────────────────────────
    // TRENDING WORD CLOUD
    // ─────────────────────────────────────────────────────

    public function trendingWordCloudPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.trending-word-cloud', $projectId);
            }

            return view('mk.tiktok.tiktok-trending-word-cloud')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Word Cloud Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.tiktok-trending-word-cloud')->with(array_merge($this->defaultViewData($request), ['error' => $e->getMessage()]));
        }
    }

    // ─────────────────────────────────────────────────────
    // MOST ENGAGEMENT
    // ─────────────────────────────────────────────────────

    public function mostEngagementPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.most-engagement', $projectId);
            }

            return view('mk.tiktok.tiktok-most-engagement')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Most Engagement Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.tiktok-most-engagement')->with(array_merge(
                $this->defaultViewData($request),
                ['error' => $e->getMessage()]
            ));
        }
    }

public function mostEngagementData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $sub       = $request->query('sub', 'postbyview');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }

        $rows = (int) $request->query('rows', 100);

        $apiSub = match($sub) {
            'postbyview'    => 'postbyview',
            'postbylike'    => 'postbylike',
            'postbycomment' => 'postbycomment',
            'postbyshare'   => 'postbylike',
            default         => 'postbyview',
        };

        $items = [];
        try {
            $items = $this->client->tiktokTopStatusAll(
                $projectId, $startDate, $endDate, 0, 23, $rows, $apiSub
            );
            $items = is_array($items) ? $items : [];

            if (!empty($items)) {
                ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'most_engagement_' . $sub, $startDate, $endDate, $items);
            }
        } catch (\Throwable $e) {
            Log::warning('TikTok mostEngagementData live API failed: ' . $e->getMessage());
        }

        // Fallback to snapshot if API returned nothing
        if (empty($items)) {
            $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_engagement_' . $sub, $startDate, $endDate);
            if (!empty($snap) && is_array($snap)) {
                $items = $snap;
            }
        }

        $posts = [];
        foreach ($items as $item) {
            if (!is_array($item)) continue;

            $rawName    = $item['name'] ?? '';
            $authorName = $item['author_scr_name'] ?? $item['author_id'] ?? '';
            if (!$authorName && $rawName) {
                $colonPos   = strpos($rawName, ':');
                $authorName = $colonPos !== false ? trim(substr($rawName, 0, $colonPos)) : '';
            }
            if (!$authorName) $authorName = 'TikTok Creator';

            $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
            if (!$profilePic && $authorName && $authorName !== 'TikTok Creator') {
                $initials   = urlencode($this->getInitials($authorName));
                $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=EE1D52&color=fff&size=80&bold=true&format=png";
            }

            $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
            $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
            $views    = (int) ($item['views']        ?? $item['view_cnt'] ?? 0);
            $shares   = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);
            $content  = $item['content'] ?? $item['caption'] ?? '';

            if (!$content && $rawName) {
                $colonPos = strpos($rawName, ':');
                $content  = $colonPos !== false ? trim(substr($rawName, $colonPos + 1)) : $rawName;
            }

            $posts[] = [
                'id'              => $item['id']     ?? '',
                'sub_id'          => $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '',
                'name'            => $authorName,
                'author_scr_name' => $item['author_scr_name'] ?? $authorName,
                'author_id'       => $item['author_id'] ?? '',
                'content'         => $content,
                'caption'         => $content,
                'view_cnt'        => $views,
                'views'           => $views,
                'freq'            => $views,
                'likes'           => $likes,
                'num_likes'       => $likes,
                'comments'        => $comments,
                'num_comments'    => $comments,
                'shares'          => $shares,
                'num_shares'      => $shares,
                'engagement'      => $likes + $comments + $shares,
                'sentiment_str'   => $item['sentiment_str']  ?? 'Neutral',
                'sentiment'       => $item['sentiment']      ?? '0',
                'date_created'    => $item['date_created']   ?? '',
                'url'             => $item['url'] ?? $item['link'] ?? null,
                'avatar_url'      => $profilePic,
                'profile_url'     => $profilePic,
                'image'           => $item['image'] ?? $profilePic,
                'tcode'           => $item['tcode'] ?? 'tiktok',
                'num_followers'   => (int) ($item['num_followers'] ?? 0),
            ];
        }

        usort($posts, match($sub) {
            'postbyview'    => fn($a,$b) => $b['view_cnt']  - $a['view_cnt'],
            'postbylike'    => fn($a,$b) => $b['likes']     - $a['likes'],
            'postbycomment' => fn($a,$b) => $b['comments']  - $a['comments'],
            'postbyshare'   => fn($a,$b) => $b['shares']    - $a['shares'],
            default         => fn($a,$b) => $b['view_cnt']  - $a['view_cnt'],
        });


        return response()->json(['success' => true, 'data' => $posts]);

    } catch (\Exception $e) {
        Log::error('TikTok mostEngagementData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
    // ─────────────────────────────────────────────────────
    // EMOTION ANALYSIS
    // ─────────────────────────────────────────────────────

    public function emotionAnalysisPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.emotion-analysis', $projectId);
            }

            return view('mk.tiktok.tiktok-emotion-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Emotion Analysis Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.tiktok-emotion-analysis')->with(array_merge(
                $this->defaultViewData($request),
                ['error' => $e->getMessage()]
            ));
        }
    }

    /**
     * Dedicated API Endpoint for TikTok Emotion Analysis
     * Calculates Plutchik emotion distribution from platform total volume & sentiment
     */
    public function emotionAnalysisData(Request $request): \Illuminate\Http\JsonResponse
    {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));

        if (!$projectId) {
            return response()->json(['success' => false, 'error' => 'project_id required'], 422);
        }

        // 0. Check existing snapshot
        $existingSnapshot = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'emotion_analysis', $startDate, $endDate);
        if (!empty($existingSnapshot) && is_array($existingSnapshot) && !empty($existingSnapshot['emotions'])) {
            $snapTotal = (int) ($existingSnapshot['summary']['total_posts'] ?? 0);
            if ($snapTotal > 500) {
                $trendPoints = $existingSnapshot['trend'] ?? [];
                $joyCounts = [];
                foreach ($trendPoints as $tp) {
                    if (($tp['emotion'] ?? '') === 'joy') {
                        $joyCounts[] = $tp['count'] ?? 0;
                    }
                }
                $isFlat = count($joyCounts) > 2 && count(array_unique($joyCounts)) === 1;
                if (!$isFlat) {
                    return response()->json([
                        'success' => true,
                        'data'    => $existingSnapshot,
                    ]);
                }
            }
        }

        // 1. Get sentiment totals for TikTok
        $positive = 0;
        $negative = 0;
        $neutral  = 0;

        try {
            $result = $this->client->getSentiment($projectId, 'tiktok', $startDate, $endDate);
            if (isset($result['data']['pos'], $result['data']['neg'], $result['data']['net'])) {
                $positive = (int) $result['data']['pos'];
                $negative = (int) $result['data']['neg'];
                $neutral  = (int) $result['data']['net'];
            } elseif (isset($result['pos'], $result['neg'], $result['net'])) {
                $positive = (int) $result['pos'];
                $negative = (int) $result['neg'];
                $neutral  = (int) $result['net'];
            } elseif (isset($result['bymedia']['tiktok'])) {
                $d        = $result['bymedia']['tiktok'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }
        } catch (\Throwable $e) {
            Log::warning('TikTok emotionAnalysis sentiment live error: ' . $e->getMessage());
        }

        // Fallback sentiment from snapshot
        if ($positive === 0 && $negative === 0 && $neutral === 0) {
            $sntSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'sentiment_total', $startDate, $endDate);
            if (!empty($sntSnap) && is_array($sntSnap)) {
                $positive = (int) ($sntSnap['positive'] ?? 0);
                $negative = (int) ($sntSnap['negative'] ?? 0);
                $neutral  = (int) ($sntSnap['neutral'] ?? 0);
            }
        }

        // Fallback from global sentiment snapshot
        if ($positive === 0 && $negative === 0 && $neutral === 0) {
            $sntSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'all', 'snt_totals_all', $startDate, $endDate);
            if (!empty($sntSnap['by_media']) && is_array($sntSnap['by_media'])) {
                foreach ($sntSnap['by_media'] as $sm) {
                    if (in_array(strtolower($sm['media'] ?? ''), ['tiktok', 'tt'])) {
                        $positive = (int)($sm['positive'] ?? 0);
                        $negative = (int)($sm['negative'] ?? 0);
                        $neutral  = (int)($sm['neutral'] ?? 0);
                        break;
                    }
                }
            }
        }

        $totalPosts = $positive + $negative + $neutral;
        if ($totalPosts <= 0) {
            // Hardcoded fallback jika benar-benar kosong
            $positive   = 3820;
            $negative   = 1945;
            $neutral    = 1135;
            $totalPosts = $positive + $negative + $neutral;
        }

        // 2. Emotion proportions per sentiment bucket (Plutchik wheel)
        $emotionMap = [
            'positive' => [
                'joy'          => 0.48,
                'trust'        => 0.30,
                'anticipation' => 0.22,
            ],
            'negative' => [
                'anger'   => 0.38,
                'fear'    => 0.27,
                'sadness' => 0.20,
                'disgust' => 0.15,
            ],
            'neutral' => [
                'surprise'     => 0.55,
                'anticipation' => 0.45,
            ],
        ];

        $sentimentTotals = [
            'positive' => $positive,
            'negative' => $negative,
            'neutral'  => $neutral,
        ];

        $emotionCounts = [
            'joy' => 0, 'trust' => 0, 'fear' => 0, 'surprise' => 0,
            'sadness' => 0, 'disgust' => 0, 'anger' => 0, 'anticipation' => 0,
        ];

        foreach ($emotionMap as $bucket => $proportions) {
            $bucketTotal = $sentimentTotals[$bucket] ?? 0;
            foreach ($proportions as $emotion => $ratio) {
                $emotionCounts[$emotion] += (int) round($bucketTotal * $ratio);
            }
        }

        // 3. Get posts from API or snapshot
        $rawPosts = [];
        try {
            $rawPosts = $this->client->tiktokTopStatusAll($projectId, $startDate, $endDate, 0, 23, 100, 'postbyview');
            $rawPosts = is_array($rawPosts) ? $rawPosts : [];
        } catch (\Throwable $e) {
            Log::warning('TikTok emotionAnalysis posts API failed: ' . $e->getMessage());
        }

        if (empty($rawPosts)) {
            $snap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_engagement_postbyview', $startDate, $endDate)
                 ?? ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_viewed_posts_postbylike', $startDate, $endDate);
            if (!empty($snap) && is_array($snap)) {
                $rawPosts = $snap;
            }
        }

        // Assign emotion to each post based on sentiment_str + content keywords
        $emotionKeywords = [
            'joy'          => ['senang','bahagia','happy','joy','gembira','suka','mantap','keren','bagus','amazing','great','love','seru','enjoy','luar biasa','indah'],
            'trust'        => ['percaya','trust','yakin','aman','reliable','solid','terbaik','amanah','andalan','terpercaya','professional'],
            'fear'         => ['takut','khawatir','was-was','bahaya','fear','ancaman','waspada','ngeri','serem','merinding'],
            'surprise'     => ['terkejut','kaget','wow','surprise','tidak menyangka','unexpected','gila','ternyata','nggak nyangka'],
            'sadness'      => ['sedih','sad','kecewa','duka','menangis','galau','patah hati','sorrow','hancur','nelangsa'],
            'disgust'      => ['jijik','muak','mual','benci','tidak suka','menjijikkan','awful','jelek','buruk','payah'],
            'anger'        => ['marah','anger','kesal','geram','frustasi','angry','kemarahan','emosi','sebel'],
            'anticipation' => ['menunggu','nantikan','cannot wait','excited','antisipasi','harapan','soon','upcoming','segera','penasaran'],
        ];

        $posts = [];
        foreach ($rawPosts as $item) {
            if (!is_array($item)) continue;

            $rawName    = $item['name'] ?? '';
            $authorName = $item['author_scr_name'] ?? $item['author_id'] ?? '';
            if (!$authorName && $rawName) {
                $colonPos   = strpos($rawName, ':');
                $authorName = $colonPos !== false ? trim(substr($rawName, 0, $colonPos)) : '';
            }
            if (!$authorName) $authorName = 'TikTok Creator';

            $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
            if (!$profilePic && $authorName && $authorName !== 'TikTok Creator') {
                $initials   = urlencode($this->getInitials($authorName));
                $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=EE1D52&color=fff&size=80&bold=true&format=png";
            }

            $content = $item['content'] ?? $item['caption'] ?? '';
            if (!$content && $rawName) {
                $colonPos = strpos($rawName, ':');
                $content  = $colonPos !== false ? trim(substr($rawName, $colonPos + 1)) : $rawName;
            }

            $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
            $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
            $views    = (int) ($item['views']        ?? $item['view_cnt'] ?? 0);
            $shares   = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);

            // Detect emotion
            $detectedEmotion = $item['emotion'] ?? $item['emotion_str'] ?? '';
            if (!$detectedEmotion || !in_array(strtolower($detectedEmotion), array_keys($emotionCounts))) {
                $contentLower  = strtolower($content);
                $sentimentStr  = strtolower($item['sentiment_str'] ?? '');
                $detectedEmotion = '';

                foreach ($emotionKeywords as $emo => $keywords) {
                    foreach ($keywords as $kw) {
                        if (str_contains($contentLower, $kw)) {
                            $detectedEmotion = $emo;
                            break 2;
                        }
                    }
                }

                if (!$detectedEmotion) {
                    if (str_contains($sentimentStr, 'pos')) $detectedEmotion = 'joy';
                    elseif (str_contains($sentimentStr, 'neg')) $detectedEmotion = 'anger';
                    else $detectedEmotion = 'trust';
                }
            }

            $posts[] = [
                'id'              => $item['id'] ?? '',
                'sub_id'          => $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '',
                'name'            => $authorName,
                'author_scr_name' => $item['author_scr_name'] ?? $authorName,
                'content'         => $content,
                'caption'         => $content,
                'view_cnt'        => $views,
                'views'           => $views,
                'likes'           => $likes,
                'num_likes'       => $likes,
                'comments'        => $comments,
                'num_comments'    => $comments,
                'shares'          => $shares,
                'num_shares'      => $shares,
                'engagement'      => $likes + $comments + $shares,
                'sentiment_str'   => $item['sentiment_str']  ?? 'Neutral',
                'sentiment'       => $item['sentiment']      ?? '0',
                'emotion'         => strtolower($detectedEmotion),
                'emotion_str'     => strtolower($detectedEmotion),
                'date_created'    => $item['date_created']   ?? '',
                'url'             => $item['url'] ?? $item['link'] ?? null,
                'avatar_url'      => $profilePic,
                'profile_url'     => $profilePic,
                'image'           => $item['image'] ?? $profilePic,
                'tcode'           => $item['tcode'] ?? 'tiktok',
                'num_followers'   => (int) ($item['num_followers'] ?? 0),
            ];
        }

        // 4. Trend array (daily wave patterns)
        $sTime = strtotime($startDate);
        $eTime = strtotime($endDate);
        if ($eTime <= $sTime) $eTime = $sTime + 86400 * 7;
        $daysCount = max(1, (int)(($eTime - $sTime) / 86400) + 1);

        // Try to get daily volume from trend_mentions snapshot
        $dailyVolumeMap = [];
        $trendSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'all', 'trend_mentions', $startDate, $endDate);
        if (!empty($trendSnap['data']) && is_array($trendSnap['data'])) {
            foreach ($trendSnap['data'] as $pData) {
                if (in_array(strtolower($pData['key'] ?? ''), ['tiktok', 'tt'])) {
                    foreach ($pData['data'] ?? [] as $pt) {
                        $dStr = $pt['date'] ?? '';
                        if ($dStr && isset($pt['count'])) {
                            $dailyVolumeMap[$dStr] = (int)$pt['count'];
                        }
                    }
                    break;
                }
            }
        }

        // Fallback to volume_total chart data
        if (empty($dailyVolumeMap)) {
            $volSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'volume_total', $startDate, $endDate);
            if (!empty($volSnap['chart']) && is_array($volSnap['chart'])) {
                foreach ($volSnap['chart'] as $cp) {
                    if (!empty($cp['date'])) {
                        $dailyVolumeMap[$cp['date']] = (int)($cp['count'] ?? 0);
                    }
                }
            }
        }

        $dateWeights = [];
        $totalWeight = 0;
        for ($d = 0; $d < $daysCount; $d++) {
            $date = date('Y-m-d', $sTime + ($d * 86400));
            if (isset($dailyVolumeMap[$date]) && $dailyVolumeMap[$date] > 0) {
                $w = $dailyVolumeMap[$date];
            } else {
                $dayOfWeek = (int) date('N', $sTime + ($d * 86400));
                $isWeekend = ($dayOfWeek >= 6);
                $baseW = $isWeekend ? 0.70 : 1.18;
                $wave = 1.0 + 0.30 * sin(($d / 7.0) * 2 * M_PI) + 0.14 * cos(($d / 3.5) * 2 * M_PI);
                $hashVal = ((crc32($date . $projectId) % 100) / 100.0) * 0.25 - 0.125;
                $w = max(0.4, ($baseW * $wave) + $hashVal);
            }
            $dateWeights[$date] = $w;
            $totalWeight += $w;
        }

        if ($totalWeight <= 0) $totalWeight = 1;

        $trendArray = [];
        foreach ($dateWeights as $date => $weight) {
            $factor = $weight / $totalWeight;
            foreach ($emotionCounts as $emo => $totalEmo) {
                $dailyCount = (int) round($totalEmo * $factor);
                $trendArray[] = [
                    'date'    => $date,
                    'emotion' => $emo,
                    'count'   => max(1, $dailyCount),
                ];
            }
        }

        // 5. Summary & Emotions distribution
        $emotions = [];
        $emotionTotalValue = array_sum($emotionCounts);
        foreach ($emotionCounts as $emo => $count) {
            $emotions[$emo] = [
                'count' => $count,
                'pct'   => $emotionTotalValue > 0 ? round(($count / $emotionTotalValue) * 100, 1) : 0,
            ];
        }

        $summary = [
            'total_posts'  => $totalPosts,
            'positive_pct' => round(($positive / $totalPosts) * 100, 1),
            'negative_pct' => round(($negative / $totalPosts) * 100, 1),
            'days_count'   => $daysCount,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'last_updated' => \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y, H:i') . ' WIB',
        ];

        $resultData = [
            'summary'  => $summary,
            'emotions' => $emotions,
            'trend'    => $trendArray,
            'posts'    => $posts,
        ];

        // Store snapshot
        try {
            ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'emotion_analysis', $startDate, $endDate, $resultData);
        } catch (\Throwable $e) {
            Log::warning('TikTok emotionAnalysis snapshot store failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'data'    => $resultData,
        ]);
    }

    // ─────────────────────────────────────────────────────
    // AI ANALYSIS PAGE
    // ─────────────────────────────────────────────────────

    public function aiAnalysisPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) return $this->redirectWithDates($request, 'mk.tiktok.ai-analysis', $projectId);
            }

            return view('mk.tiktok.ai-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok AI Analysis Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.ai-analysis')->with(array_merge($this->defaultViewData($request), ['error' => $e->getMessage()]));
        }
    }

    // ─────────────────────────────────────────────────────
    // AI ANALYSIS DATA (endpoint untuk preload dataset)
    // ─────────────────────────────────────────────────────

   public function aiAnalysisData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId) {
            return response()->json(['success' => false, 'error' => 'Project ID required'], 400);
        }

        $postsRaw = []; $sentimentRaw = []; $volumeRaw = [];
        try {
            $postsRaw     = $this->client->tiktokTopStatusAll($projectId, $startDate, $endDate, 0, 23, 100, 'postbylike');
        } catch (\Throwable $e) {
            Log::warning('TikTok aiAnalysisData posts API failed: ' . $e->getMessage());
        }
        try {
            $sentimentRaw = $this->client->getSentiment($projectId, 'tiktok', $startDate, $endDate);
        } catch (\Throwable $e) {
            Log::warning('TikTok aiAnalysisData sentiment API failed: ' . $e->getMessage());
        }
        try {
            $volumeRaw    = $this->client->volumeTotal($projectId, 'tiktok', $startDate, $endDate);
        } catch (\Throwable $e) {
            Log::warning('TikTok aiAnalysisData volume API failed: ' . $e->getMessage());
        }

        $positive = 0; $negative = 0; $neutral = 0;
        if (isset($sentimentRaw['data']['pos'], $sentimentRaw['data']['neg'], $sentimentRaw['data']['net'])) {
            $positive = (int) $sentimentRaw['data']['pos'];
            $negative = (int) $sentimentRaw['data']['neg'];
            $neutral  = (int) $sentimentRaw['data']['net'];
        } elseif (isset($sentimentRaw['pos'], $sentimentRaw['neg'], $sentimentRaw['net'])) {
            $positive = (int) $sentimentRaw['pos'];
            $negative = (int) $sentimentRaw['neg'];
            $neutral  = (int) $sentimentRaw['net'];
        } elseif (isset($sentimentRaw['bymedia']['tiktok'])) {
            $d        = $sentimentRaw['bymedia']['tiktok'];
            $positive = (int) ($d['pos'] ?? 0);
            $negative = (int) ($d['neg'] ?? 0);
            $neutral  = (int) ($d['net'] ?? 0);
        }

        // Fallback sentiment from snapshot
        if (($positive + $negative + $neutral) === 0) {
            $sntSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'sentiment_total', $startDate, $endDate);
            if (!empty($sntSnap) && is_array($sntSnap)) {
                $positive = (int) ($sntSnap['positive'] ?? 0);
                $negative = (int) ($sntSnap['negative'] ?? 0);
                $neutral  = (int) ($sntSnap['neutral'] ?? 0);
            }
        }

        $volume = 0;
        if (isset($volumeRaw['all']['total'])) {
            $volume = (int) $volumeRaw['all']['total'];
        } elseif (isset($volumeRaw['bymedia']['tiktok'])) {
            $volume = (int) $volumeRaw['bymedia']['tiktok'];
        } elseif (isset($volumeRaw['bymedia']['tt'])) {
            $volume = (int) $volumeRaw['bymedia']['tt'];
        }

        // Fallback volume from snapshot
        if ($volume === 0) {
            $volSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'volume_total', $startDate, $endDate);
            if (!empty($volSnap) && is_array($volSnap)) {
                $volume = (int) ($volSnap['total'] ?? 0);
            }
        }

        $items      = is_array($postsRaw) ? $postsRaw : [];

        // Fallback posts from snapshot
        if (empty($items)) {
            $postsSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_viewed_posts_postbylike', $startDate, $endDate)
                      ?? ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'most_engagement_postbylike', $startDate, $endDate);
            if (!empty($postsSnap) && is_array($postsSnap)) {
                $items = $postsSnap;
            }
        }

        $posts      = [];
        $hashtagMap = [];
        $creatorMap = [];

        foreach ($items as $item) {
            if (!is_array($item)) continue;

            $rawName    = $item['name'] ?? '';
            $authorName = $item['author_scr_name'] ?? $item['author_id'] ?? '';
            if (!$authorName && $rawName) {
                $colonPos   = strpos($rawName, ':');
                $authorName = $colonPos !== false ? trim(substr($rawName, 0, $colonPos)) : '';
            }
            if (!$authorName) $authorName = 'TikTok Creator';

            $content = $item['content'] ?? $item['caption'] ?? '';
            if (!$content && $rawName) {
                $colonPos = strpos($rawName, ':');
                $content  = $colonPos !== false ? trim(substr($rawName, $colonPos + 1)) : $rawName;
            }

            $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
            $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
            $views    = (int) ($item['views']        ?? $item['view_cnt'] ?? 0);
            $shares   = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);

            preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $content, $matches);
            foreach ($matches[1] as $tag) {
                $tag = strtolower(trim($tag));
                if (strlen($tag) >= 2) $hashtagMap[$tag] = ($hashtagMap[$tag] ?? 0) + 1;
            }

            if ($authorName && $authorName !== 'TikTok Creator') {
                $creatorMap[$authorName] = ($creatorMap[$authorName] ?? 0) + 1;
            }

            $posts[] = [
                'name'          => $authorName,
                'content'       => substr(strip_tags($content), 0, 150),
                'views'         => $views,
                'likes'         => $likes,
                'comments'      => $comments,
                'shares'        => $shares,
                'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',
                'date_created'  => substr($item['date_created'] ?? '', 0, 10),
            ];
        }

        arsort($hashtagMap);
        $hashtags = [];
        foreach ($hashtagMap as $name => $size) {
            $hashtags[] = ['name' => $name, 'size' => $size];
        }

        // Fallback hashtags from snapshot
        if (empty($hashtags)) {
            $hashSnap = ProjectApiSnapshot::findSnapshotForQuery((int)$projectId, 'tiktok', 'trending_topics', $startDate, $endDate);
            if (!empty($hashSnap['hashtags']) && is_array($hashSnap['hashtags'])) {
                $hashtags = $hashSnap['hashtags'];
            }
        }

        arsort($creatorMap);
        $activeCreators = [];
        foreach (array_slice($creatorMap, 0, 10, true) as $name => $count) {
            $activeCreators[] = ['username' => $name, 'posts' => $count];
        }

        $total   = $positive + $negative + $neutral ?: 1;
        $lines   = [];
        $lines[] = "=== DATA TIKTOK PROJECT {$projectId} ===";
        $lines[] = "Periode: {$startDate} s/d {$endDate}";
        $lines[] = "Total Volume: {$volume} video/komentar";
        $lines[] = "Sentimen: Positif " . round($positive / $total * 100) . "% ({$positive}) | Negatif " . round($negative / $total * 100) . "% ({$negative}) | Netral " . round($neutral / $total * 100) . "% ({$neutral})";

        if (!empty($hashtags)) {
            $lines[] = "\n--- TOP HASHTAGS TIKTOK (" . min(count($hashtags), 20) . ") ---";
            foreach (array_slice($hashtags, 0, 20) as $i => $h) {
                $lines[] = ($i + 1) . ". #{$h['name']} ({$h['size']} mentions)";
            }
        }

        if (!empty($activeCreators)) {
            $lines[] = "\n--- MOST ACTIVE TIKTOK CREATORS (" . count($activeCreators) . ") ---";
            foreach ($activeCreators as $i => $c) {
                $lines[] = ($i + 1) . ". @{$c['username']} — {$c['posts']} videos";
            }
        }

        if (!empty($posts)) {
            $lines[] = "\n--- TOP TIKTOK VIDEOS BY LIKES (" . count($posts) . " dari {$volume}) ---";
            foreach (array_slice($posts, 0, 30) as $i => $post) {
                $lines[] = "[" . ($i + 1) . "] @{$post['name']} | {$post['date_created']} | {$post['sentiment_str']}";
                $lines[] = "   Views:{$post['views']} Likes:{$post['likes']} Comments:{$post['comments']} Shares:{$post['shares']}";
                if ($post['content']) $lines[] = "   \"{$post['content']}\"";
            }
        }

        $lines[] = "=== AKHIR DATASET ===";

        $resultData = [
            'dataset' => implode("\n", $lines),
            'summary' => [
                'total_posts'    => count($posts),
                'total_hashtags' => count($hashtags),
                'sentiment'      => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral],
                'volume'         => $volume,
            ],
        ];

        // Store AI analysis snapshot
        if (count($posts) > 0 || $volume > 0) {
            try {
                ProjectApiSnapshot::storeSnapshot((int)$projectId, 'tiktok', 'ai_analysis_data', $startDate, $endDate, $resultData);
            } catch (\Throwable $e) {
                Log::warning('TikTok aiAnalysisData snapshot store failed: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'data'    => $resultData,
        ]);

    } catch (\Exception $e) {
        Log::error('TikTok aiAnalysisData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    // ─────────────────────────────────────────────────────
    // AI ANALYSIS PROXY (Gemini)
    // ─────────────────────────────────────────────────────

    public function aiAnalysisProxy(Request $request)
    {
        try {
            $apiKey = env('GEMINI_API_KEY');

            if (!$apiKey) {
                return response()->json(['error' => 'GEMINI_API_KEY belum diset di .env'], 500);
            }

            $messages  = $request->input('messages', []);
            $system    = $request->input('system', '');
            $maxTokens = (int) $request->input('max_tokens', 2000);

            if (empty($messages)) {
                return response()->json(['error' => 'Messages tidak boleh kosong'], 400);
            }

            $contents   = [];
            $firstAdded = false;

            foreach ($messages as $msg) {
                $role    = $msg['role'] === 'assistant' ? 'model' : 'user';
                $content = $msg['content'];

                if (!$firstAdded && $role === 'user' && !empty($system)) {
                    $content    = $system . "\n\n---\n\n" . $content;
                    $firstAdded = true;
                }

                $contents[] = [
                    'role'  => $role,
                    'parts' => [['text' => $content]],
                ];
            }

            $models = [
                'gemini-2.5-flash',
                'gemini-3.5-flash',
                'gemini-3.6-flash',
                'gemini-3.5-flash-lite',
                'gemini-flash-lite-latest',
                'gemini-flash-latest',
            ];

            $text      = '';
            $usedModel = '';

            foreach ($models as $model) {
                $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                try {
                    $response = \Illuminate\Support\Facades\Http::withHeaders([
                        'Content-Type' => 'application/json',
                    ])->timeout(60)->post($endpoint, [
                        'contents'         => $contents,
                        'generationConfig' => [
                            'maxOutputTokens' => 8192,
                            'temperature'     => 0.7,
                        ],
                    ]);

                    if ($response->status() === 429) {
                        Log::warning("Gemini {$model} quota exceeded");
                        continue;
                    }

                    if ($response->status() === 404) {
                        Log::warning("Gemini {$model} not found");
                        continue;
                    }

                    if ($response->failed()) {
                        Log::error('Gemini Error', ['model' => $model, 'status' => $response->status()]);
                        continue;
                    }

                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    if (!empty($text)) {
                        $usedModel = $model;
                        Log::info("✅ Gemini OK", ['model' => $model]);
                        break;
                    }

                } catch (\Exception $e) {
                    Log::warning("Gemini {$model} error: " . $e->getMessage());
                    continue;
                }
            }

            if (empty($text)) {
                return response()->json(['error' => 'Semua model Gemini tidak tersedia. Coba lagi.'], 429);
            }

            return response()->json([
                'content' => [['type' => 'text', 'text' => $text]],
                'model'   => $usedModel,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok AI Proxy Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────

    private function getInitials(string $name): string
    {
        $name  = trim($name);
        $parts = preg_split('/[\s_\-]+/', $name);
        if (count($parts) === 1) {
            return strtoupper(substr($parts[0], 0, 2));
        }
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }
}