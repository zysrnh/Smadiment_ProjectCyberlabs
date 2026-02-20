<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InstagramOverviewController extends Controller
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
                    return redirect()->route('mk.instagram.overview', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            if (!$projectId) {
                return view('mk.instagram.overview', [
                    'projectId' => null,
                    'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                    'projects'  => [],
                ]);
            }

            return view('mk.instagram.overview')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Overview Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return view('mk.instagram.overview')->with([
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

            $result = $this->client->totalAuthors($projectId, 'instagram', $startDate, $endDate);

            Log::info('IG totalUsers raw', [
                'all'     => $result['all'] ?? null,
                'bymedia' => $result['bymedia'] ?? [],
            ]);

            $total = 0;
            if (isset($result['bymedia']['ig'])) {
                $total = (int) $result['bymedia']['ig'];
            } elseif (isset($result['bymedia']['instagram'])) {
                $total = (int) $result['bymedia']['instagram'];
            } elseif (isset($result['bymedia']['ig_post'])) {
                $total = (int) $result['bymedia']['ig_post'];
            } elseif (isset($result['all'])) {
                $total = (int) $result['all'];
            }

            return response()->json(['success' => true, 'data' => ['total' => $total]]);

        } catch (\Exception $e) {
            Log::error('Instagram totalUsers API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->totalAuthors($projectId, 'instagram', $startDate, $endDate);

            Log::info('IG totalAuthors raw', [
                'all'     => $result['all'] ?? null,
                'bymedia' => $result['bymedia'] ?? [],
            ]);

            $total = 0;
            if (isset($result['bymedia']['ig'])) {
                $total = (int) $result['bymedia']['ig'];
            } elseif (isset($result['bymedia']['instagram'])) {
                $total = (int) $result['bymedia']['instagram'];
            } elseif (isset($result['bymedia']['ig_post'])) {
                $total = (int) $result['bymedia']['ig_post'];
            } elseif (isset($result['all'])) {
                $total = (int) $result['all'];
            }

            return response()->json(['success' => true, 'data' => ['total' => $total]]);

        } catch (\Exception $e) {
            Log::error('Instagram totalAuthors API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->volumeTotal($projectId, 'instagram', $startDate, $endDate);

            Log::info('IG volumeTotal raw', [
                'all'     => $result['all'] ?? null,
                'bymedia' => $result['bymedia'] ?? [],
            ]);

            $total = 0;
            if (isset($result['all']['total'])) {
                $total = (int) $result['all']['total'];
            } elseif (isset($result['bymedia']['ig'])) {
                $total = (int) $result['bymedia']['ig'];
            } elseif (isset($result['bymedia']['instagram'])) {
                $total = (int) $result['bymedia']['instagram'];
            } elseif (isset($result['bymedia']['ig_post'])) {
                $total = (int) $result['bymedia']['ig_post'];
            }

            $chartData = [];
            try {
                $trendsResult = $this->client->trendsTotal($projectId, $startDate, $endDate);

                Log::info('IG trendsTotal raw', ['result' => $trendsResult]);

                foreach ($trendsResult as $datetime => $mediaData) {
                    if (!is_array($mediaData)) continue;

                    $dateKey = substr($datetime, 0, 10);

                    $count = (int) (
                        $mediaData['instagram'] ??
                        $mediaData['ig']        ??
                        $mediaData['ig_post']   ??
                        0
                    );

                    $chartData[] = ['date' => $dateKey, 'count' => $count];
                }

                usort($chartData, fn($a, $b) => strcmp($a['date'], $b['date']));

            } catch (\Exception $e) {
                Log::warning('Instagram: Failed to load trends data', ['error' => $e->getMessage()]);
            }

            return response()->json(['success' => true, 'data' => ['total' => $total, 'chart' => $chartData]]);

        } catch (\Exception $e) {
            Log::error('Instagram volumeTotal API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->getSentiment($projectId, 'instagram', $startDate, $endDate);

            Log::info('IG sentimentTotal raw', ['result' => $result]);

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
            } elseif (isset($result['bymedia']['ig'])) {
                $d        = $result['bymedia']['ig'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            } elseif (isset($result['bymedia']['instagram'])) {
                $d        = $result['bymedia']['instagram'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }

            return response()->json(['success' => true, 'data' => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral]]);

        } catch (\Exception $e) {
            Log::error('Instagram sentimentTotal API error', ['error' => $e->getMessage()]);
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

            Log::info('IG mostActiveUsers raw', [
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
                    if ($media && !in_array($media, ['ig', 'instagram', 'ig_post', ''])) continue;

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
            Log::error('Instagram mostActiveUsers API error', ['error' => $e->getMessage()]);
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
                    return redirect()->route('mk.instagram.most-viewed-posts', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.most-viewed-posts')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Most Viewed Posts Page Error', ['error' => $e->getMessage()]);

            return view('mk.instagram.most-viewed-posts')->with([
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
            $sub       = $request->query('sub', 'postbylike');

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, 100, $sub);

            Log::info('IG mostViewedPostsData raw result', [
                'type'   => gettype($result),
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 1, true) : $result,
            ]);

            $posts = [];
            $items = is_array($result) ? $result : [];

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                // ── Author fields ──────────────────────────────────────
                // API response: author_id = "beritasatu", author_scr_name = "BeritaSatu"
                // name field contains "Username: post content..." format
                $rawName    = $item['name'] ?? '';
                $authorId   = $item['author_id']      ?? $item['author_scr_name'] ?? '';
                $authorName = $item['author_scr_name'] ?? $item['author_id']      ?? '';

                // Fallback: kalau author_scr_name kosong, parse dari field 'name'
                // Format name: "BeritaSatu: Menteri Pendidikan..."
                if (!$authorName && $rawName) {
                    $colonPos   = strpos($rawName, ':');
                    $authorName = $colonPos !== false
                        ? trim(substr($rawName, 0, $colonPos))
                        : '';
                }

                // Final fallback
                if (!$authorName) $authorName = 'Instagram User';

                // ── Image: API returns empty string, tidak ada foto profil ──
                // Gunakan UI Avatars sebagai generated avatar berdasarkan nama
                $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? $item['image'] ?? '';
                if (!$profilePic && $authorName && $authorName !== 'Instagram User') {
                    $initials   = urlencode($this->getInitials($authorName));
                    $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=e6683c&color=fff&size=80&bold=true&format=png";
                }

                // ── Stats ──────────────────────────────────────────────
                $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
                $views    = (int) ($item['view_cnt']     ?? $item['freq']     ?? $item['views'] ?? 0);
                $postUrl  = $item['url']     ?? $item['link']    ?? null;
                $content  = $item['content'] ?? $item['caption'] ?? '';

                // ── Handle name field: "Username: content..." → ambil content saja ──
                if (!$content && $rawName) {
                    $colonPos = strpos($rawName, ':');
                    $content  = $colonPos !== false
                        ? trim(substr($rawName, $colonPos + 1))
                        : $rawName;
                }

                $posts[] = [
                    'id'             => $item['id']      ?? '',
                    'sub_id'         => $item['sub_id']  ?? $item['docid'] ?? $item['id'] ?? '',
                    'name'           => $authorName,
                    'content'        => $content,
                    'view_cnt'       => $views,
                    'likes'          => $likes,
                    'comments'       => $comments,
                    'engagement'     => $likes + $comments,
                    'sentiment_str'  => $item['sentiment_str']  ?? 'Neutral',
                    'sentiment_prec' => $item['sentiment_prec'] ?? 0,
                    'date_created'   => $item['date_created']   ?? '',
                    'url'            => $postUrl,
                    'avatar_url'     => $profilePic,
                    'tcode'          => $item['tcode'] ?? 'ig-post',
                    'author'         => [
                        'name'     => $authorName,
                        'scr_name' => $item['author_scr_name'] ?? $authorId ?? $authorName,
                        'image'    => $profilePic,
                    ],
                ];
            }

            // Sort by sub type
            if ($sub === 'postbylike') {
                usort($posts, fn($a, $b) => $b['likes'] - $a['likes']);
            } elseif ($sub === 'postbycomment') {
                usort($posts, fn($a, $b) => $b['comments'] - $a['comments']);
            } elseif ($sub === 'postbyview') {
                usort($posts, fn($a, $b) => $b['view_cnt'] - $a['view_cnt']);
            } else {
                usort($posts, fn($a, $b) => $b['engagement'] - $a['engagement']);
            }

            Log::info('IG mostViewedPostsData processed', ['total_posts' => count($posts)]);

            return response()->json(['success' => true, 'data' => $posts]);

        } catch (\Exception $e) {
            Log::error('IG mostViewedPostsData error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
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
    // TRENDING TOPICS (TOP HASHTAGS)
    // ─────────────────────────────────────────────────────

    public function trendingTopicsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.instagram.trending-topics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.instagram-trending-topics')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Trending Topics Page Error', ['error' => $e->getMessage()]);

            return view('mk.instagram.instagram-trending-topics')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
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

        // ── Ambil posts Instagram (pakai igTopStatus yang sudah terbukti jalan) ──
        // Ambil banyak post supaya hashtag representatif
        $posts = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, 500, 'postbylike');

        Log::info('IG trendingTopicsData via igTopStatus', [
            'type'  => gettype($posts),
            'count' => is_array($posts) ? count($posts) : 0,
        ]);

        // ── Parse hashtag dari field content setiap post ──
        $hashtagCount = [];

        if (is_array($posts)) {
            foreach ($posts as $post) {
                if (!is_array($post)) continue;

                // Cari konten di berbagai kemungkinan key
                $content = $post['content'] ?? $post['caption'] ?? $post['text'] ?? $post['name'] ?? '';

                if (empty($content)) continue;

                // Extract semua hashtag dengan regex
                preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $content, $matches);

                foreach ($matches[1] as $tag) {
                    $tag = strtolower(trim($tag));
                    if (strlen($tag) < 2) continue; // skip hashtag terlalu pendek
                    $hashtagCount[$tag] = ($hashtagCount[$tag] ?? 0) + 1;
                }
            }
        }

        // ── Sort descending by count ──
        arsort($hashtagCount);

        // ── Format output ──
        $hashtags      = [];
        $totalMentions = 0;

        foreach ($hashtagCount as $name => $size) {
            $hashtags[]     = [
                'name'    => $name,
                'hashtag' => $name,
                'size'    => $size,
            ];
            $totalMentions += $size;
        }

        Log::info('IG trendingTopicsData parsed from content', [
            'total_hashtags' => count($hashtags),
            'total_mentions' => $totalMentions,
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
        Log::error('IG trendingTopicsData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                    return redirect()->route('mk.instagram.authors.demographics', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.authors-demographics')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Authors Demographics Page Error', ['error' => $e->getMessage()]);

            return view('mk.instagram.authors-demographics')->with([
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

            $result = $this->client->authorsAge($projectId, 'instagram', $startDate, $endDate);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('IG authorsAge API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->authorsGender($projectId, 'instagram', $startDate, $endDate);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('IG authorsGender API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->authorsType($projectId, 'instagram', $startDate, $endDate);
            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('IG authorsType API error', ['error' => $e->getMessage()]);
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
                    return redirect()->route('mk.instagram.geographic', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.geographic')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Geographic Page Error', ['error' => $e->getMessage()]);

            return view('mk.instagram.geographic')->with([
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

            $result = $this->client->geoTwitterUser($projectId, 'ig', $startDate, $endDate);

            if (isset($result['data']) && is_array($result['data'])) {
                $result['data'] = array_values(array_filter($result['data'], function ($item) {
                    $media = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                    if (!$media) return true;
                    return str_contains($media, 'ig') || str_contains($media, 'instagram');
                }));
            }

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('IG geoUser API error', ['error' => $e->getMessage()]);
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

            $result = $this->client->geoTwitterUserSentiment($projectId, 'ig', $startDate, $endDate);

            if (isset($result['data']) && is_array($result['data'])) {
                $result['data'] = array_values(array_filter($result['data'], function ($item) {
                    $media = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                    if (!$media) return true;
                    return str_contains($media, 'ig') || str_contains($media, 'instagram');
                }));
            }

            return response()->json(['success' => true, 'data' => $result]);

        } catch (\Exception $e) {
            Log::error('IG geoSentiment API error', ['error' => $e->getMessage()]);
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

            $result    = $this->client->topAuthorLocation($projectId, 'ig', $startDate, $endDate);
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
            Log::error('IG topLocations API error', ['error' => $e->getMessage()]);
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
                    return redirect()->route('mk.instagram.trending-word-cloud', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.instagram-trending-word-cloud')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            return view('mk.instagram.instagram-trending-word-cloud')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
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
                    return redirect()->route('mk.instagram.ai-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.ai-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram AI Analysis Page Error', ['error' => $e->getMessage()]);

            return view('mk.instagram.ai-analysis')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }
}