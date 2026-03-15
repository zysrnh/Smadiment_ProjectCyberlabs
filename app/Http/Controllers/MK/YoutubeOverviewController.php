<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class YoutubeOverviewController extends Controller
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

                if ($projectId) {
                    return redirect()->route('mk.youtube.overview', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            if (!$projectId) {
                return view('mk.youtube.overview', [
                    'projectId' => null,
                    'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                    'projects'  => [],
                ]);
            }

            return view('mk.youtube.overview')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube Overview Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return view('mk.youtube.overview')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => 'Failed to load projects: ' . $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
    // STATS APIs (Overview)
    // ─────────────────────────────────────────────────────

    public function totalUsers(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->totalAuthors($projectId, 'youtube', $startDate, $endDate);

            Log::info('YT totalUsers raw', [
                'all'     => $result['all'] ?? null,
                'bymedia' => $result['bymedia'] ?? [],
            ]);

            $total = 0;
            if (isset($result['bymedia']['youtube'])) {
                $total = (int) $result['bymedia']['youtube'];
            } elseif (isset($result['bymedia']['ytb'])) {
                $total = (int) $result['bymedia']['ytb'];
            } elseif (isset($result['bymedia']['yt'])) {
                $total = (int) $result['bymedia']['yt'];
            } elseif (isset($result['all'])) {
                $total = (int) $result['all'];
            }

            return response()->json(['success' => true, 'data' => ['total' => $total]]);

        } catch (\Exception $e) {
            Log::error('YouTube totalUsers API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function totalAuthors(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->totalAuthors($projectId, 'youtube', $startDate, $endDate);

            Log::info('YT totalAuthors raw', [
                'all'     => $result['all'] ?? null,
                'bymedia' => $result['bymedia'] ?? [],
            ]);

            $total = 0;
            if (isset($result['bymedia']['youtube'])) {
                $total = (int) $result['bymedia']['youtube'];
            } elseif (isset($result['bymedia']['ytb'])) {
                $total = (int) $result['bymedia']['ytb'];
            } elseif (isset($result['bymedia']['yt'])) {
                $total = (int) $result['bymedia']['yt'];
            } elseif (isset($result['all'])) {
                $total = (int) $result['all'];
            }

            return response()->json(['success' => true, 'data' => ['total' => $total]]);

        } catch (\Exception $e) {
            Log::error('YouTube totalAuthors API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function volumeTotal(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->volumeTotal($projectId, 'youtube', $startDate, $endDate);

            Log::info('YT volumeTotal raw', [
                'all'     => $result['all'] ?? null,
                'bymedia' => $result['bymedia'] ?? [],
            ]);

            $total = 0;
            if (isset($result['all']['total'])) {
                $total = (int) $result['all']['total'];
            } elseif (isset($result['bymedia']['youtube'])) {
                $total = (int) $result['bymedia']['youtube'];
            } elseif (isset($result['bymedia']['ytb'])) {
                $total = (int) $result['bymedia']['ytb'];
            } elseif (isset($result['bymedia']['yt'])) {
                $total = (int) $result['bymedia']['yt'];
            }

            $chartData = [];
            try {
                $trendsResult = $this->client->trendsTotal($projectId, $startDate, $endDate);

                Log::info('YT trendsTotal raw', ['result' => $trendsResult]);

                foreach ($trendsResult as $datetime => $mediaData) {
                    if (!is_array($mediaData)) continue;

                    $dateKey = substr($datetime, 0, 10);

                    $count = (int) (
                        $mediaData['youtube'] ??
                        $mediaData['ytb']     ??
                        $mediaData['yt']      ??
                        0
                    );

                    $chartData[] = ['date' => $dateKey, 'count' => $count];
                }

                usort($chartData, fn($a, $b) => strcmp($a['date'], $b['date']));

            } catch (\Exception $e) {
                Log::warning('YouTube: Failed to load trends data', ['error' => $e->getMessage()]);
            }

            return response()->json(['success' => true, 'data' => ['total' => $total, 'chart' => $chartData]]);

        } catch (\Exception $e) {
            Log::error('YouTube volumeTotal API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->getSentiment($projectId, 'youtube', $startDate, $endDate);

            Log::info('YT sentimentTotal raw', ['result' => $result]);

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
            } elseif (isset($result['bymedia']['youtube'])) {
                $d        = $result['bymedia']['youtube'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            } elseif (isset($result['bymedia']['ytb'])) {
                $d        = $result['bymedia']['ytb'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }

            return response()->json(['success' => true, 'data' => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral]]);

        } catch (\Exception $e) {
            Log::error('YouTube sentimentTotal API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->mostActiveUsers($projectId, $startDate, $endDate);

            Log::info('YT mostActiveUsers raw', [
                'type'   => gettype($result),
                'keys'   => is_array($result) ? array_keys($result) : [],
                'sample' => is_array($result['data']['data'] ?? null)
                    ? array_slice($result['data']['data'], 0, 2)
                    : ($result[0] ?? null),
            ]);

            $users = [];

            if (isset($result['data']['data']) && is_array($result['data']['data'])) {
                foreach ($result['data']['data'] as $user) {
                    $media = strtolower($user['media'] ?? '');
                    if ($media && !in_array($media, ['youtube', 'ytb', 'yt', ''])) continue;

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
                            'id'                => $user['id'] ?? '',
                        ];
                    }
                }
            }

            return response()->json(['success' => true, 'data' => ['data' => $users]]);

        } catch (\Exception $e) {
            Log::error('YouTube mostActiveUsers API error', ['error' => $e->getMessage()]);
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

                if ($projectId) {
                    return redirect()->route('mk.youtube.most-viewed-posts', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.youtube.most-viewed-posts')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube Most Viewed Posts Page Error', ['error' => $e->getMessage()]);

            return view('mk.youtube.most-viewed-posts')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

  public function mostViewedPostsData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $sub       = $request->query('sub', 'postbyview');
        $rows      = (int) $request->query('rows', 100);

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }

        $result = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, $rows, $sub);

        Log::info('YT mostViewedPostsData raw result', [
            'type'   => gettype($result),
            'count'  => is_array($result) ? count($result) : 0,
            'sample' => is_array($result) ? array_slice($result, 0, 1, true) : $result,
        ]);

        $posts = [];
        $items = is_array($result) ? $result : [];

        foreach ($items as $item) {
            if (!is_array($item)) continue;

            // Nama asli channel — prioritaskan author_name dari API
            $authorName = $item['author_name'] ?? '';
            if (!$authorName) $authorName = $item['author_scr_name'] ?? '';
            if (!$authorName || preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $authorName)) {
                $authorName = 'YouTube Channel';
            }

            $authorId   = $item['author_id'] ?? $item['author_scr_name'] ?? '';
            $videoTitle = $item['title'] ?? $item['name'] ?? '';
            $content    = $item['content'] ?? $item['caption'] ?? $videoTitle;

            $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
            if (!$profilePic && $authorName !== 'YouTube Channel') {
                $initials   = urlencode($this->getInitials($authorName));
                $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=FF0000&color=fff&size=80&bold=true&format=png";
            }

            $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
            $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
$views = (int) ($item['num_views'] ?? $item['view_cnt'] ?? $item['views'] ?? 0);
            $postUrl  = $item['url'] ?? $item['link'] ?? null;

            $posts[] = [
    'id'              => $item['id']     ?? '',
    'sub_id'          => $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '',
    'docid'           => $item['docid']  ?? $item['sub_id'] ?? '',   // ← tambah
    'video_id'        => $item['docid']  ?? $item['sub_id'] ?? '',   // ← tambah untuk ytVideoId()
    'author_name'     => $authorName,
    'author_id'       => $authorId,
    'author_scr_name' => $item['author_scr_name'] ?? '',
    'name'            => $authorName,
    'title'           => $videoTitle,
    'content'         => $content,
    'num_views'       => $views,          // ← tambah (JS pakai num_views juga)
    'view_cnt'        => $views,
    'num_likes'       => $likes,          // ← tambah
    'likes'           => $likes,
    'num_comments'    => $comments,       // ← tambah
    'comments'        => $comments,
    'engagement'      => $likes + $comments,
    'sentiment_str'   => $item['sentiment_str']  ?? 'Neutral',
    'sentiment_prec'  => $item['sentiment_prec'] ?? 0,
    'date_created'    => $item['date_created']   ?? '',
    'url'             => $postUrl,
    'avatar_url'      => $profilePic,
    'tcode'           => $item['tcode'] ?? 'youtube',
    'author'          => [
        'name'     => $authorName,
        'scr_name' => $item['author_scr_name'] ?? $authorId,
        'image'    => $profilePic,
    ],
];
    
        }

        if ($sub === 'postbylike') {
            usort($posts, fn($a, $b) => $b['likes'] - $a['likes']);
        } elseif ($sub === 'postbycomment') {
            usort($posts, fn($a, $b) => $b['comments'] - $a['comments']);
        } elseif ($sub === 'postbyview') {
            usort($posts, fn($a, $b) => $b['view_cnt'] - $a['view_cnt']);
        } else {
            usort($posts, fn($a, $b) => $b['engagement'] - $a['engagement']);
        }

        Log::info('YT mostViewedPostsData processed', ['total_posts' => count($posts)]);

        return response()->json(['success' => true, 'data' => $posts]);

    } catch (\Exception $e) {
        Log::error('YT mostViewedPostsData error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    public function mostEngagementData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $sub       = $request->query('sub', 'postbyview');
            $rows      = (int) ($request->query('rows', 100));

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $apiSub = match ($sub) {
                'postbyview'    => 'postbyview',
                'postbylike'    => 'postbylike',
                'postbycomment' => 'postbycomment',
                default         => 'postbyview',
            };

            $result = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, $rows, $apiSub);

            Log::info('YT mostEngagementData raw', [
                'sub'   => $sub,
                'rows'  => $rows,
                'count' => is_array($result) ? count($result) : 0,
            ]);

            $posts = [];
            $items = is_array($result) ? $result : [];

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $authorName = $item['author_name'] ?? '';
                if (!$authorName) $authorName = $item['author_scr_name'] ?? '';
                if (!$authorName || preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $authorName)) {
                    $authorName = 'YouTube Channel';
                }

                $authorId   = $item['author_id'] ?? $item['author_scr_name'] ?? '';
                $videoTitle = $item['title'] ?? $item['name'] ?? '';
                $content    = $item['content'] ?? $item['caption'] ?? $videoTitle;

                $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
                if (!$profilePic && $authorName !== 'YouTube Channel') {
                    $initials   = urlencode($this->getInitials($authorName));
                    $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=FF0000&color=fff&size=80&bold=true&format=png";
                }

                $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
                $views    = (int) ($item['num_views']    ?? $item['view_cnt'] ?? $item['views'] ?? 0);
                $postUrl  = $item['url'] ?? $item['link'] ?? null;

                $posts[] = [
                    'id'              => $item['id']     ?? '',
                    'sub_id'          => $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '',
                    'docid'           => $item['docid']  ?? $item['sub_id'] ?? '',
                    'video_id'        => $item['docid']  ?? $item['sub_id'] ?? '',
                    'author_name'     => $authorName,
                    'author_id'       => $authorId,
                    'author_scr_name' => $item['author_scr_name'] ?? '',
                    'name'            => $authorName,
                    'title'           => $videoTitle,
                    'content'         => $content,
                    'num_views'       => $views,
                    'view_cnt'        => $views,
                    'num_likes'       => $likes,
                    'likes'           => $likes,
                    'num_comments'    => $comments,
                    'comments'        => $comments,
                    'engagement'      => $likes + $comments,
                    'sentiment_str'   => $item['sentiment_str']  ?? 'Neutral',
                    'sentiment_prec'  => $item['sentiment_prec'] ?? 0,
                    'date_created'    => $item['date_created']   ?? '',
                    'url'             => $postUrl,
                    'avatar_url'      => $profilePic,
                    'tcode'           => $item['tcode'] ?? 'youtube',
                    'author'          => [
                        'name'     => $authorName,
                        'scr_name' => $item['author_scr_name'] ?? $authorId,
                        'image'    => $profilePic,
                    ],
                ];
            }

            usort($posts, match ($sub) {
                'postbyview'    => fn($a, $b) => $b['view_cnt']  - $a['view_cnt'],
                'postbylike'    => fn($a, $b) => $b['likes']     - $a['likes'],
                'postbycomment' => fn($a, $b) => $b['comments']  - $a['comments'],
                default         => fn($a, $b) => $b['view_cnt']  - $a['view_cnt'],
            });

            Log::info('YT mostEngagementData processed', ['total_posts' => count($posts)]);

            return response()->json(['success' => true, 'data' => $posts]);

        } catch (\Exception $e) {
            Log::error('YT mostEngagementData error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Generate 1-2 letter initials from a name.
     */
    private function getInitials(string $name): string
    {
        $name  = trim($name);
        $parts = preg_split('/[\s_\-]+/', $name);
        if (count($parts) === 1) {
            return strtoupper(substr($parts[0], 0, 2));
        }
        return strtoupper(substr($parts[0], 0, 1) . substr(end($parts), 0, 1));
    }

    // ─────────────────────────────────────────────────────
    // TRENDING TOPICS (TOP HASHTAGS / KEYWORDS)
    // ─────────────────────────────────────────────────────

    public function trendingTopicsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.youtube.trending-topics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
            $endDate   = $request->query('end_date', now()->format('Y-m-d'));

            // Pre-load trending topics data server-side
            $hashtagsJson = '{"success":false}';
            if ($projectId) {
                try {
                    $posts = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, 500, 'postbyview');
                    $hashtagCount = [];
                    if (is_array($posts)) {
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
                    }
                    arsort($hashtagCount);
                    $hashtags = [];
                    $totalMentions = 0;
                    foreach ($hashtagCount as $name => $size) {
                        $hashtags[] = ['name' => $name, 'hashtag' => $name, 'size' => $size];
                        $totalMentions += $size;
                    }
                    $hashtagsJson = json_encode([
                        'success' => true,
                        'data' => [
                            'hashtags' => $hashtags,
                            'total_hashtags' => count($hashtags),
                            'total_mentions' => $totalMentions,
                            'top_hashtag' => $hashtags[0] ?? null,
                        ],
                    ]);
                } catch (\Exception $e) {
                    Log::warning('YT trending topics pre-load failed', ['error' => $e->getMessage()]);
                }
            }

            return view('mk.youtube.youtube-trending-topics')->with([
                'projectId'    => $projectId,
                'startDate'    => $startDate,
                'endDate'      => $endDate,
                'projects'     => $projects,
                'hashtagsJson' => $hashtagsJson,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube Trending Topics Page Error', ['error' => $e->getMessage()]);

            return view('mk.youtube.youtube-trending-topics')->with([
                'projectId'    => null,
                'startDate'    => now()->subDays(6)->format('Y-m-d'),
                'endDate'      => now()->format('Y-m-d'),
                'projects'     => [],
                'hashtagsJson' => '{"success":false}',
                'error'        => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
    // AUTHORS DEMOGRAPHICS
    // ─────────────────────────────────────────────────────

    public function authorsDemographicsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.youtube.authors.demographics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.youtube.authors-demographics')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube Authors Demographics Page Error', ['error' => $e->getMessage()]);

            return view('mk.youtube.authors-demographics')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function authorsAgeData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->authorsAge($projectId, 'youtube', $startDate, $endDate);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('YT authorsAge API error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function authorsGenderData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->authorsGender($projectId, 'youtube', $startDate, $endDate);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('YT authorsGender API error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function authorsTypeData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->authorsType($projectId, 'youtube', $startDate, $endDate);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('YT authorsType API error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // GEOGRAPHIC
    // ─────────────────────────────────────────────────────

    public function geographicPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.youtube.geographic', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.youtube.geographic')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube Geographic Page Error', ['error' => $e->getMessage()]);

            return view('mk.youtube.geographic')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    public function geoUser(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->geoTwitterUser($projectId, 'youtube', $startDate, $endDate);

            if (isset($result['data']) && is_array($result['data'])) {
                $result['data'] = array_values(array_filter($result['data'], function ($item) {
                    $media = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                    if (!$media) return true;
                    return str_contains($media, 'youtube') || str_contains($media, 'ytb') || str_contains($media, 'yt');
                }));
            }

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('YT geoUser API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function geoSentiment(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->geoTwitterUserSentiment($projectId, 'youtube', $startDate, $endDate);

            if (isset($result['data']) && is_array($result['data'])) {
                $result['data'] = array_values(array_filter($result['data'], function ($item) {
                    $media = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                    if (!$media) return true;
                    return str_contains($media, 'youtube') || str_contains($media, 'ytb') || str_contains($media, 'yt');
                }));
            }

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('YT geoSentiment API error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    public function topLocations(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result    = $this->client->topAuthorLocation($projectId, 'youtube', $startDate, $endDate);
            $locations = [];

            $items = $result['data'] ?? (is_array($result) ? $result : []);

            foreach ($items as $item) {
                if (!is_array($item)) continue;
                $name  = $item['name'] ?? $item['location'] ?? $item['city'] ?? '';
                $count = (int) ($item['count'] ?? $item['total'] ?? $item['y'] ?? 0);
                if ($name && $count > 0) {
                    $locations[] = ['name' => $name, 'count' => $count];
                }
            }

            usort($locations, fn($a, $b) => $b['count'] - $a['count']);

            return response()->json(['success' => true, 'data' => $locations]);

        } catch (\Exception $e) {
            Log::error('YT topLocations API error', ['error' => $e->getMessage()]);
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
 
            if ($projectId) {
                return redirect()->route('mk.youtube.trending-word-cloud', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }
 
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
 
        // Pre-load data server-side
        $hashtagsJson = '[]';
 
        if ($projectId) {
            try {
                $posts = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, 500, 'postbyview');
 
                $hashtagCount = [];
                $keywordCount = [];
 
                if (is_array($posts)) {
                    foreach ($posts as $post) {
                        if (!is_array($post)) continue;
 
                        $content = ($post['content'] ?? '') . ' ' . ($post['title'] ?? '') . ' ' . ($post['name'] ?? '');
 
                        if (empty(trim($content))) continue;
 
                        // ── Extract #hashtags ──
                        preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $content, $hashMatches);
                        foreach ($hashMatches[1] as $tag) {
                            $tag = strtolower(trim($tag));
                            if (strlen($tag) < 2) continue;
                            $hashtagCount[$tag] = ($hashtagCount[$tag] ?? 0) + 1;
                        }
 
                        // ── Extract keywords from title (fallback) ──
                        $titleText = strtolower($post['title'] ?? $post['name'] ?? '');
                        $titleText = preg_replace('/https?:\/\/\S+/', '', $titleText);
                        $titleText = preg_replace('/[^a-z0-9\x{00C0}-\x{024F}\x{0400}-\x{04FF}\s]/u', ' ', $titleText);
                        $words = preg_split('/\s+/', trim($titleText), -1, PREG_SPLIT_NO_EMPTY);
 
                        $stopwords = ['the','a','an','and','or','but','in','on','at','to','for','of','with','is','are','was','were','be','been','this','that','i','you','he','she','we','they','it','my','your','his','her','our','dari','dan','ke','di','yang','dengan','untuk','ini','itu','ada','tidak','bisa','akan','juga','sudah','pada','atau','dalam','oleh','karena','kita','anda','kami','mereka','ya','jadi','tapi','kalau','aja','video','youtube','channel','watch','subscribe','like','comment','new','how','what','why','when','where','all','get','let','amp','http','https','www','com','co','id'];
 
                        foreach ($words as $word) {
                            if (strlen($word) < 3) continue;
                            if (preg_match('/^\d+$/', $word)) continue;
                            if (in_array($word, $stopwords)) continue;
                            $keywordCount[$word] = ($keywordCount[$word] ?? 0) + 1;
                        }
                    }
                }
 
                // Prefer hashtags; fall back to keywords if not enough
                if (count($hashtagCount) >= 5) {
                    arsort($hashtagCount);
                    $topics = [];
                    foreach ($hashtagCount as $name => $size) {
                        $topics[] = ['name' => '#' . $name, 'hashtag' => $name, 'size' => $size];
                    }
                } else {
                    // Use keywords, merge with any hashtags found
                    arsort($keywordCount);
                    arsort($hashtagCount);
 
                    $topics = [];
                    foreach ($hashtagCount as $name => $size) {
                        $topics[] = ['name' => '#' . $name, 'hashtag' => $name, 'size' => $size * 3]; // boost hashtags
                    }
                    foreach ($keywordCount as $name => $size) {
                        if ($size >= 2) { // only words appearing 2+ times
                            $topics[] = ['name' => $name, 'hashtag' => $name, 'size' => $size];
                        }
                    }
 
                    // Sort merged list by size
                    usort($topics, fn($a, $b) => $b['size'] - $a['size']);
                }
 
                $hashtagsJson = json_encode($topics);
 
                Log::info('YT word cloud pre-load', [
                    'hashtag_count' => count($hashtagCount),
                    'keyword_count' => count($keywordCount),
                    'topics_total'  => count($topics),
                ]);
 
            } catch (\Exception $e) {
                Log::warning('YT word cloud pre-load failed', ['error' => $e->getMessage()]);
            }
        }
 
        return view('mk.youtube.youtube-trending-word-cloud')->with([
            'projectId'    => $projectId,
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'projects'     => $projects,
            'hashtagsJson' => $hashtagsJson,
        ]);
 
    } catch (\Exception $e) {
        return view('mk.youtube.youtube-trending-word-cloud')->with([
            'projectId'    => null,
            'startDate'    => now()->subDays(6)->format('Y-m-d'),
            'endDate'      => now()->format('Y-m-d'),
            'projects'     => [],
            'hashtagsJson' => '[]',
            'error'        => $e->getMessage(),
        ]);
    }
}
 
// ─────────────────────────────────────────────────────
// TRENDING TOPICS DATA API
// ─────────────────────────────────────────────────────
 
public function trendingTopicsData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
 
        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }
 
        $posts = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, 500, 'postbyview');
 
        Log::info('YT trendingTopicsData via ytbTopStatus', [
            'type'  => gettype($posts),
            'count' => is_array($posts) ? count($posts) : 0,
        ]);
 
        $hashtagCount = [];
        $keywordCount = [];
 
        $stopwords = ['the','a','an','and','or','but','in','on','at','to','for','of','with','is','are','was','were','be','been','this','that','i','you','he','she','we','they','it','my','your','his','her','our','dari','dan','ke','di','yang','dengan','untuk','ini','itu','ada','tidak','bisa','akan','juga','sudah','pada','atau','dalam','oleh','karena','kita','anda','kami','mereka','ya','jadi','tapi','kalau','aja','video','youtube','channel','watch','subscribe','like','comment','new','how','what','why','when','where','all','get','let','amp','http','https','www','com','co','id'];
 
        if (is_array($posts)) {
            foreach ($posts as $post) {
                if (!is_array($post)) continue;
 
                $content = ($post['content'] ?? '') . ' ' . ($post['title'] ?? '') . ' ' . ($post['name'] ?? '');
 
                if (empty(trim($content))) continue;
 
                // Extract hashtags
                preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $content, $hashMatches);
                foreach ($hashMatches[1] as $tag) {
                    $tag = strtolower(trim($tag));
                    if (strlen($tag) < 2) continue;
                    $hashtagCount[$tag] = ($hashtagCount[$tag] ?? 0) + 1;
                }
 
                // Extract keywords from title as fallback
                $titleText = strtolower($post['title'] ?? $post['name'] ?? '');
                $titleText = preg_replace('/https?:\/\/\S+/', '', $titleText);
                $titleText = preg_replace('/[^a-z0-9\x{00C0}-\x{024F}\x{0400}-\x{04FF}\s]/u', ' ', $titleText);
                $words = preg_split('/\s+/', trim($titleText), -1, PREG_SPLIT_NO_EMPTY);
 
                foreach ($words as $word) {
                    if (strlen($word) < 3) continue;
                    if (preg_match('/^\d+$/', $word)) continue;
                    if (in_array($word, $stopwords)) continue;
                    $keywordCount[$word] = ($keywordCount[$word] ?? 0) + 1;
                }
            }
        }
 
        $hashtags      = [];
        $totalMentions = 0;
 
        if (count($hashtagCount) >= 5) {
            arsort($hashtagCount);
            foreach ($hashtagCount as $name => $size) {
                $hashtags[]     = ['name' => '#'.$name, 'hashtag' => $name, 'size' => $size];
                $totalMentions += $size;
            }
        } else {
            // Merge hashtags + keywords
            arsort($hashtagCount);
            arsort($keywordCount);
 
            foreach ($hashtagCount as $name => $size) {
                $boosted        = $size * 3;
                $hashtags[]     = ['name' => '#'.$name, 'hashtag' => $name, 'size' => $boosted];
                $totalMentions += $boosted;
            }
            foreach ($keywordCount as $name => $size) {
                if ($size >= 2) {
                    $hashtags[]     = ['name' => $name, 'hashtag' => $name, 'size' => $size];
                    $totalMentions += $size;
                }
            }
            usort($hashtags, fn($a, $b) => $b['size'] - $a['size']);
        }
 
        Log::info('YT trendingTopicsData parsed', [
            'hashtag_count' => count($hashtagCount),
            'keyword_count' => count($keywordCount),
            'topics_total'  => count($hashtags),
            'top5'          => array_slice($hashtags, 0, 5),
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
        Log::error('YT trendingTopicsData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
 

    // ─────────────────────────────────────────────────────
    // AI ANALYSIS
    // ─────────────────────────────────────────────────────

    public function aiAnalysisPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.youtube.ai-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.youtube.ai-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube AI Analysis Page Error', ['error' => $e->getMessage()]);

            return view('mk.youtube.ai-analysis')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }
    public function mostEngagementPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.youtube.most-engagement', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.youtube.youtube-most-engagement')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('YouTube Most Engagement Page Error', ['error' => $e->getMessage()]);

            return view('mk.youtube.youtube-most-engagement')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }
    public function emotionAnalysisPage(Request $request)
{
    try {
        $projects  = $this->getAllProjects();
        $projectId = $request->query('project_id');

        if (!$projectId && count($projects) > 0) {
            $projectId = $projects[0]['id'] ?? null;

            if ($projectId) {
                return redirect()->route('mk.youtube.emotion-analysis', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        return view('mk.youtube.youtube-emotion-analysis')->with([
            'projectId' => $projectId,
            'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
            'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('YouTube Emotion Analysis Page Error', ['error' => $e->getMessage()]);

        return view('mk.youtube.youtube-emotion-analysis')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
            'error'     => $e->getMessage(),
        ]);
    }
}

public function aiAnalysisData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }

        // ── Fetch top videos by view ──
        $rawPosts = [];
        try {
            $rawPosts = $this->client->ytbTopStatus($projectId, $startDate, $endDate, 0, 23, 500, 'postbyview') ?? [];
        } catch (\Exception $e) {
            Log::warning('YT aiAnalysisData: ytbTopStatus failed', ['error' => $e->getMessage()]);
        }

        // ── Fetch sentiment ──
        $sentimentData = [];
        try {
            $sentimentData = $this->client->getSentiment($projectId, 'youtube', $startDate, $endDate) ?? [];
        } catch (\Exception $e) {
            Log::warning('YT aiAnalysisData: getSentiment failed', ['error' => $e->getMessage()]);
        }

        // ── Fetch volume ──
        $volumeData = [];
        try {
            $volumeData = $this->client->volumeTotal($projectId, 'youtube', $startDate, $endDate) ?? [];
        } catch (\Exception $e) {
            Log::warning('YT aiAnalysisData: volumeTotal failed', ['error' => $e->getMessage()]);
        }

        // ── Parse sentiment ──
        $positive = 0; $negative = 0; $neutral = 0;

        if (isset($sentimentData['data']['pos'])) {
            $positive = (int) $sentimentData['data']['pos'];
            $negative = (int) ($sentimentData['data']['neg'] ?? 0);
            $neutral  = (int) ($sentimentData['data']['net'] ?? 0);
        } elseif (isset($sentimentData['pos'])) {
            $positive = (int) $sentimentData['pos'];
            $negative = (int) ($sentimentData['neg'] ?? 0);
            $neutral  = (int) ($sentimentData['net'] ?? 0);
        } elseif (isset($sentimentData['bymedia']['youtube'])) {
            $d = $sentimentData['bymedia']['youtube'];
            $positive = (int) ($d['pos'] ?? 0);
            $negative = (int) ($d['neg'] ?? 0);
            $neutral  = (int) ($d['net'] ?? 0);
        } elseif (isset($sentimentData['bymedia']['ytb'])) {
            $d = $sentimentData['bymedia']['ytb'];
            $positive = (int) ($d['pos'] ?? 0);
            $negative = (int) ($d['neg'] ?? 0);
            $neutral  = (int) ($d['net'] ?? 0);
        }

        // ── Parse volume ──
        $totalVolume = 0;
        if (isset($volumeData['all']['total'])) {
            $totalVolume = (int) $volumeData['all']['total'];
        } elseif (isset($volumeData['bymedia']['youtube'])) {
            $totalVolume = (int) $volumeData['bymedia']['youtube'];
        } elseif (isset($volumeData['bymedia']['ytb'])) {
            $totalVolume = (int) $volumeData['bymedia']['ytb'];
        } elseif (isset($volumeData['bymedia']['yt'])) {
            $totalVolume = (int) $volumeData['bymedia']['yt'];
        }

        // ── Extract hashtags from video content/titles ──
        $hashtagCount = [];
        $items = is_array($rawPosts) ? $rawPosts : [];

        foreach ($items as $post) {
            if (!is_array($post)) continue;
            $text = ($post['content'] ?? '') . ' ' . ($post['title'] ?? '') . ' ' . ($post['name'] ?? '');
            preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $text, $matches);
            foreach ($matches[1] as $tag) {
                $tag = strtolower(trim($tag));
                if (strlen($tag) < 2) continue;
                $hashtagCount[$tag] = ($hashtagCount[$tag] ?? 0) + 1;
            }
        }
        arsort($hashtagCount);

        // ── Build dataset string ──
        $lines = [];
        $tot   = $positive + $negative + $neutral ?: 1;

        $lines[] = "=== DATA YOUTUBE PROJECT {$projectId} ===";
        $lines[] = "Periode: {$startDate} s/d {$endDate}";
        $lines[] = "Total Volume: {$totalVolume} video/komentar";
        $lines[] = "Sentimen: Positif " . round($positive / $tot * 100) . "% ({$positive}) | Negatif " . round($negative / $tot * 100) . "% ({$negative}) | Netral " . round($neutral / $tot * 100) . "% ({$neutral})";
        $lines[] = '';

        // Top hashtags
        if (!empty($hashtagCount)) {
            $topHashtags = array_slice($hashtagCount, 0, 20, true);
            $lines[] = '--- TOP HASHTAGS/KEYWORDS YOUTUBE (' . count($topHashtags) . ') ---';
            $i = 1;
            foreach ($topHashtags as $tag => $count) {
                $lines[] = "{$i}. #{$tag} ({$count} mentions)";
                $i++;
            }
            $lines[] = '';
        }

        // Top videos
        if (!empty($items)) {
            $negPosts = array_filter($items, fn($p) => stripos($p['sentiment_str'] ?? '', 'neg') !== false);
            $posPosts = array_filter($items, fn($p) => stripos($p['sentiment_str'] ?? '', 'pos') !== false);
            $neuPosts = array_filter($items, fn($p) =>
                stripos($p['sentiment_str'] ?? '', 'neg') === false &&
                stripos($p['sentiment_str'] ?? '', 'pos') === false
            );

            $sample = array_merge(
                array_slice(array_values($negPosts), 0, 10),
                array_slice(array_values($posPosts), 0, 8),
                array_slice(array_values($neuPosts), 0, 5)
            );

            $lines[] = '--- TOP YOUTUBE VIDEOS (' . count($sample) . ' dari ' . count($items) . ') ---';
            foreach ($sample as $idx => $post) {
                $date      = substr($post['date_created'] ?? '', 0, 10);
                $channel   = $post['author_name'] ?? $post['name'] ?? 'Unknown Channel';
                // Sanitize channel ID-only names
                if (!$channel || preg_match('/^UC[A-Za-z0-9_-]{20,}$/', $channel)) {
                    $channel = 'YouTube Channel';
                }
                $title     = $post['title'] ?? '';
                $content   = substr(trim(($post['content'] ?? '') ?: $title), 0, 180);
                $content   = str_replace("\n", ' ', $content);
                $views     = $post['num_views']    ?? $post['view_cnt'] ?? 0;
                $likes     = $post['num_likes']    ?? $post['likes']    ?? 0;
                $comments  = $post['num_comments'] ?? $post['comments'] ?? 0;
                $sentiment = $post['sentiment_str'] ?? 'Neutral';
                $n         = $idx + 1;

                $lines[] = "[V{$n}] {$channel} | {$date} | {$sentiment}";
                $lines[] = "   Views: {$views} | Likes: {$likes} | Comments: {$comments}";
                if ($title)   $lines[] = "   Judul: \"{$title}\"";
                if ($content) $lines[] = "   \"{$content}\"";
            }
        }

        $lines[] = '=== AKHIR DATASET ===';
        $dataset = implode("\n", $lines);

        return response()->json([
            'success' => true,
            'data'    => [
                'dataset' => $dataset,
                'summary' => [
                    'total_videos'  => count($items),
                    'total_hashtags'=> count($hashtagCount),
                    'sentiment'     => [
                        'positive' => $positive,
                        'negative' => $negative,
                        'neutral'  => $neutral,
                    ],
                    'volume' => $totalVolume,
                ],
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('YT aiAnalysisData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

public function aiAnalysisProxy(Request $request)
{
    try {
        $apiKey = env('GEMINI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'GEMINI_API_KEY not configured'], 500);
        }

        $messages  = $request->input('messages', []);
        $system    = $request->input('system', '');
        $maxTokens = (int) $request->input('max_tokens', 8192);

        // Gemini model fallback chain
        $models = [
            'gemini-2.5-flash',
            'gemini-2.5-pro',
            'gemini-2.0-flash',
            'gemini-2.0-flash-lite',
            'gemini-flash-latest',
        ];

        $lastError = null;

        foreach ($models as $model) {
            try {
                $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                // Convert messages to Gemini format
                $contents = [];

                if ($system) {
                    $contents[] = [
                        'role'  => 'user',
                        'parts' => [['text' => $system]],
                    ];
                    $contents[] = [
                        'role'  => 'model',
                        'parts' => [['text' => 'Understood. I will act as SMADIMENT AI Analyst and follow all instructions provided.']],
                    ];
                }

                foreach ($messages as $msg) {
                    $role = $msg['role'] === 'assistant' ? 'model' : 'user';
                    $contents[] = [
                        'role'  => $role,
                        'parts' => [['text' => $msg['content'] ?? '']],
                    ];
                }

                $payload = [
                    'contents'         => $contents,
                    'generationConfig' => [
                        'maxOutputTokens' => $maxTokens,
                        'temperature'     => 0.7,
                    ],
                ];

                $ch = curl_init($url);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST           => true,
                    CURLOPT_POSTFIELDS     => json_encode($payload),
                    CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
                    CURLOPT_TIMEOUT        => 120,
                ]);

                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode !== 200) {
                    $lastError = "Model {$model} returned HTTP {$httpCode}";
                    Log::warning("YT aiProxy: {$lastError}");
                    continue;
                }

                $decoded = json_decode($response, true);
                $text    = $decoded['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if (!$text) {
                    $lastError = "Model {$model} returned empty text";
                    Log::warning("YT aiProxy: {$lastError}");
                    continue;
                }

                Log::info("YT aiProxy: success with model {$model}");

                return response()->json([
                    'content' => [['type' => 'text', 'text' => $text]],
                    'model'   => $model,
                ]);

            } catch (\Exception $e) {
                $lastError = $e->getMessage();
                Log::warning("YT aiProxy: model {$model} exception — {$lastError}");
                continue;
            }
        }

        return response()->json(['error' => 'All Gemini models failed. Last error: ' . $lastError], 500);

    } catch (\Exception $e) {
        Log::error('YT aiAnalysisProxy error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
}