<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
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
        $allProjects = [];
        $offset      = 0;
        $limit       = 100;

        do {
            $projectsData = $this->client->listProjects($offset, $limit);
            $data         = $projectsData['data'] ?? [];
            $allProjects  = array_merge($allProjects, $data);
            $offset      += $limit;

            $total = $projectsData['total'] ?? $projectsData['meta']['total'] ?? null;
        } while (count($data) === $limit && ($total === null || $offset < $total));

        return $allProjects;
    }

    private function redirectWithDates(Request $request, string $routeName, string $projectId): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route($routeName, [
            'project_id' => $projectId,
            'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
        ]);
    }

    private function defaultViewData(Request $request): array
    {
        return [
            'projectId' => null,
            'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
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

            $result = $this->client->volumeTotal($projectId, 'tiktok', $startDate, $endDate);

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

            $result = $this->client->getSentiment($projectId, 'tiktok', $startDate, $endDate);

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

            $result = $this->client->mostActiveUsers($projectId, $startDate, $endDate);

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

            // ✅ FIX: limit dinamis, max 1000, default 100
            $limit = (int) $request->query('limit', 1000);
            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->tiktokTopStatus($projectId, $startDate, $endDate, 0, 23, $limit, $sub);

            Log::info('TikTok mostViewedPostsData raw', [
                'count'  => is_array($result) ? count($result) : 0,
                'limit'  => $limit,
                'sample' => is_array($result) ? array_slice($result, 0, 1) : [],
            ]);

            $posts = [];
            $items = is_array($result) ? $result : [];

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $rawName    = $item['name'] ?? '';
                $authorName = $item['author_scr_name'] ?? $item['author_id'] ?? '';

                if (!$authorName && $rawName) {
                    $colonPos   = strpos($rawName, ':');
                    $authorName = $colonPos !== false
                        ? trim(substr($rawName, 0, $colonPos))
                        : '';
                }

                if (!$authorName) $authorName = 'TikTok Creator';

                $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
                if (!$profilePic && $authorName && $authorName !== 'TikTok Creator') {
                    $initials   = urlencode($this->getInitials($authorName));
                    $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=EE1D52&color=fff&size=80&bold=true&format=png";
                }

                $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
                $views    = (int) ($item['view_cnt']     ?? $item['freq']     ?? $item['views'] ?? 0);
                $shares   = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);
                $postUrl  = $item['url']     ?? $item['link']  ?? null;
                $content  = $item['content'] ?? $item['caption'] ?? '';

                if (!$content && $rawName) {
                    $colonPos = strpos($rawName, ':');
                    $content  = $colonPos !== false
                        ? trim(substr($rawName, $colonPos + 1))
                        : $rawName;
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
                    'url'            => $postUrl,
                    'avatar_url'     => $profilePic,
                    'tcode'          => $item['tcode'] ?? 'tiktok',
                    'author'         => [
                        'name'     => $authorName,
                        'scr_name' => $item['author_scr_name'] ?? $authorName,
                        'image'    => $profilePic,
                    ],
                ];
            }

            // Sort
            if ($sub === 'postbylike') {
                usort($posts, fn($a, $b) => $b['likes'] - $a['likes']);
            } elseif ($sub === 'postbycomment') {
                usort($posts, fn($a, $b) => $b['comments'] - $a['comments']);
            } elseif ($sub === 'postbyview') {
                usort($posts, fn($a, $b) => $b['view_cnt'] - $a['view_cnt']);
            } else {
                usort($posts, fn($a, $b) => $b['engagement'] - $a['engagement']);
            }

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
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
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
            // ✅ FIX: limit dinamis untuk hashtag, max 1000, default 500
            $limit = (int) $request->query('limit', 1000);

            $posts = $this->client->tiktokTopStatus($projectId, $startDate, $endDate, 0, 23, $limit, 'postbylike');

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

            $hashtags      = [];
            $totalMentions = 0;

            foreach ($hashtagCount as $name => $size) {
                $hashtags[]     = ['name' => $name, 'hashtag' => $name, 'size' => $size];
                $totalMentions += $size;
            }

            Log::info('TikTok trendingTopicsData parsed', [
                'total_hashtags' => count($hashtags),
                'top5'           => array_slice($hashtags, 0, 5),
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
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('TikTok Word Cloud Page Error', ['error' => $e->getMessage()]);
            return view('mk.tiktok.tiktok-trending-word-cloud')->with(array_merge($this->defaultViewData($request), ['error' => $e->getMessage()]));
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