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
        $limit       = 1000;

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

                foreach ($trendsResult as $datetime => $mediaData) {
                    if (!is_array($mediaData)) continue;

                    $dateKey = substr($datetime, 0, 10);
                    $count   = (int) (
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
    // MOST ENGAGEMENT PAGE + DATA  ← DIUBAH
    // ─────────────────────────────────────────────────────

    public function mostEngagementPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;

                if ($projectId) {
                    return redirect()->route('mk.instagram.most-engagement', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.most-engagement')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Most Engagement Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('mk.instagram.most-engagement')->with([
                'projectId' => null,
                'startDate' => now()->startOfMonth()->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    /**
     * API endpoint untuk Most Engagement data.
     *
     * Query params:
     *   - project_id
     *   - start_date
     *   - end_date
     *   - sub         : 'postbylike' | 'postbycomment'
     *   - rows        : jumlah data (default 1000 agar frontend bisa filter image/video)
     *
     * Frontend akan memfilter sendiri berdasarkan mention_type (image/video),
     * jadi kita cukup return semua data mentah dengan mention_type yang benar.
     */
    public function mostViewedPostsData(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');
            $sub       = $request->query('sub', 'postbylike');
            $rows      = (int) $request->query('rows', 1000);

            // Validasi sub yang diizinkan
            if (!in_array($sub, ['postbylike', 'postbycomment'])) {
                $sub = 'postbylike';
            }

            if (!$projectId || !$startDate || !$endDate) {
                return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
            }

            $result = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, $rows, $sub);

            Log::info('IG mostViewedPostsData raw result', [
                'sub'    => $sub,
                'type'   => gettype($result),
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            $posts = [];
            $items = is_array($result) ? $result : [];

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                // ── Author fields ──────────────────────────────────────
                $rawName    = $item['name'] ?? '';
                $authorId   = $item['author_id']      ?? $item['author_scr_name'] ?? '';
                $authorName = $item['author_scr_name'] ?? $item['author_id']      ?? '';

                // Fallback: parse dari field 'name' format "Username: content..."
                if (!$authorName && $rawName) {
                    $colonPos   = strpos($rawName, ':');
                    $authorName = $colonPos !== false
                        ? trim(substr($rawName, 0, $colonPos))
                        : '';
                }

                if (!$authorName) $authorName = 'Instagram User';

                // ── mention_type detection ────────────────────────────
                // Priority 1: field mention_type dari API kalau eksplisit 'video'
                // Priority 2: deteksi dari URL Instagram (paling reliable)
                //   - Reels  → instagram.com/reel/xxx
                //   - IGTV   → instagram.com/tv/xxx
                // Priority 3: deteksi dari tcode / id
                // Default  : 'image'
                $mentionType = strtolower(trim($item['mention_type'] ?? ''));

                if ($mentionType !== 'video') {
                    $postUrl = $item['url']   ?? $item['link'] ?? '';
                    $tcode   = strtolower($item['tcode'] ?? '');
                    $itemId  = strtolower($item['id']    ?? '');

                    $isVideo = (
                        str_contains($postUrl, '/reel/') ||
                        str_contains($postUrl, '/tv/')   ||
                        str_contains($tcode,   'video')  ||
                        str_contains($tcode,   'reel')   ||
                        str_contains($itemId,  'reel')
                    );

                    $mentionType = $isVideo ? 'video' : 'image';
                }

                // ── Avatar ────────────────────────────────────────────
                $profilePic = $item['profile_url'] ?? $item['avatar_url'] ?? '';
                if (!$profilePic && $authorName && $authorName !== 'Instagram User') {
                    $initials   = urlencode($this->getInitials($authorName));
                    $profilePic = "https://ui-avatars.com/api/?name={$initials}&background=e6683c&color=fff&size=80&bold=true&format=png";
                }

                // ── Stats ──────────────────────────────────────────────
                $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
                $postUrl  = $item['url']     ?? $item['link']    ?? null;
                $content  = $item['content'] ?? $item['caption'] ?? '';

                // Fallback content dari name field
                if (!$content && $rawName) {
                    $colonPos = strpos($rawName, ':');
                    $content  = $colonPos !== false
                        ? trim(substr($rawName, $colonPos + 1))
                        : $rawName;
                }

                $posts[] = [
                    'id'             => $item['id']             ?? '',
                    'sub_id'         => $item['sub_id']         ?? $item['docid'] ?? $item['id'] ?? '',
                    'author_id'      => $authorId,
                    'author_scr_name'=> $item['author_scr_name'] ?? $authorId ?? '',
                    'name'           => $authorName,
                    'content'        => $content,
                    'mention_type'   => $mentionType,           // ← KEY: 'image' or 'video'
                    'num_likes'      => $likes,
                    'likes'          => $likes,
                    'num_comments'   => $comments,
                    'comments'       => $comments,
                    'engagement'     => $likes + $comments,
                    'sentiment_str'  => $item['sentiment_str']  ?? 'Neutral',
                    'sentiment_prec' => $item['sentiment_prec'] ?? 0,
                    'date_created'   => $item['date_created']   ?? '',
                    'url'            => $postUrl,
                    'avatar_url'     => $profilePic,
                    'image'          => $item['image']          ?? '',
                    'tcode'          => $item['tcode']          ?? 'ig-post',
                    'author'         => [
                        'name'     => $authorName,
                        'scr_name' => $item['author_scr_name'] ?? $authorId ?? $authorName,
                        'image'    => $profilePic,
                    ],
                ];
            }

            // Sort sesuai sub di sisi server juga (sebagai fallback)
            if ($sub === 'postbylike') {
                usort($posts, fn($a, $b) => $b['likes'] - $a['likes']);
            } elseif ($sub === 'postbycomment') {
                usort($posts, fn($a, $b) => $b['comments'] - $a['comments']);
            }

            // Log breakdown image vs video untuk debugging
            $imageCount = count(array_filter($posts, fn($p) => $p['mention_type'] === 'image'));
            $videoCount = count(array_filter($posts, fn($p) => $p['mention_type'] === 'video'));

            Log::info('IG mostViewedPostsData processed', [
                'sub'          => $sub,
                'total_posts'  => count($posts),
                'image_count'  => $imageCount,
                'video_count'  => $videoCount,
            ]);

            return response()->json(['success' => true, 'data' => $posts]);

        } catch (\Exception $e) {
            Log::error('IG mostViewedPostsData error', [
                'error'      => $e->getMessage(),
                'project_id' => $request->query('project_id'),
                'sub'        => $request->query('sub'),
            ]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // MOST VIEWED POSTS PAGE (legacy — kept for other routes)
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

            $posts = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, 500, 'postbylike');

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

    // ─────────────────────────────────────────────────────
    // HELPERS
    // ─────────────────────────────────────────────────────

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
    public function emotionAnalysisPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) {
                    return redirect()->route('mk.instagram.emotion-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.instagram.emotion-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Instagram Emotion Analysis Page Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return view('mk.instagram.emotion-analysis')->with([
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

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID required'], 400);
            }

            // Fetch posts (by likes) + hashtags dari post content + sentiment + volume
            $postsRaw     = $this->client->igTopStatus($projectId, $startDate, $endDate, 0, 23, 50, 'postbylike');
            $sentimentRaw = $this->client->getSentiment($projectId, 'instagram', $startDate, $endDate);
            $volumeRaw    = $this->client->volumeTotal($projectId, 'instagram', $startDate, $endDate);

            // ── Parse sentiment ──
            $positive = 0; $negative = 0; $neutral = 0;
            if (isset($sentimentRaw['data']['pos'])) {
                $positive = (int) $sentimentRaw['data']['pos'];
                $negative = (int) ($sentimentRaw['data']['neg'] ?? 0);
                $neutral  = (int) ($sentimentRaw['data']['net'] ?? 0);
            } elseif (isset($sentimentRaw['pos'])) {
                $positive = (int) $sentimentRaw['pos'];
                $negative = (int) ($sentimentRaw['neg'] ?? 0);
                $neutral  = (int) ($sentimentRaw['net'] ?? 0);
            } elseif (isset($sentimentRaw['bymedia']['ig'])) {
                $d = $sentimentRaw['bymedia']['ig'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            } elseif (isset($sentimentRaw['bymedia']['instagram'])) {
                $d = $sentimentRaw['bymedia']['instagram'];
                $positive = (int) ($d['pos'] ?? 0);
                $negative = (int) ($d['neg'] ?? 0);
                $neutral  = (int) ($d['net'] ?? 0);
            }

            // ── Parse volume ──
            $volume = 0;
            if (isset($volumeRaw['all']['total'])) {
                $volume = (int) $volumeRaw['all']['total'];
            } elseif (isset($volumeRaw['bymedia']['ig'])) {
                $volume = (int) $volumeRaw['bymedia']['ig'];
            } elseif (isset($volumeRaw['bymedia']['instagram'])) {
                $volume = (int) $volumeRaw['bymedia']['instagram'];
            } elseif (isset($volumeRaw['bymedia']['ig_post'])) {
                $volume = (int) $volumeRaw['bymedia']['ig_post'];
            }

            // ── Parse posts & extract hashtags dari content ──
            $posts        = [];
            $hashtagCount = [];
            $items        = is_array($postsRaw) ? $postsRaw : ($postsRaw['data'] ?? []);

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                // Author name
                $rawName    = $item['name'] ?? '';
                $authorName = $item['author_scr_name'] ?? $item['author_id'] ?? '';
                if (!$authorName && $rawName) {
                    $colonPos   = strpos($rawName, ':');
                    $authorName = $colonPos !== false ? trim(substr($rawName, 0, $colonPos)) : $rawName;
                }
                if (!$authorName) $authorName = 'Instagram User';

                // Content / caption
                $content = $item['content'] ?? $item['caption'] ?? '';
                if (!$content && $rawName) {
                    $colonPos = strpos($rawName, ':');
                    $content  = $colonPos !== false ? trim(substr($rawName, $colonPos + 1)) : $rawName;
                }

                // Extract hashtags from caption
                if ($content) {
                    preg_match_all('/#([a-zA-Z0-9_\x{00C0}-\x{024F}\x{0400}-\x{04FF}]+)/u', $content, $matches);
                    foreach ($matches[1] as $tag) {
                        $tag = strtolower(trim($tag));
                        if (strlen($tag) >= 2) {
                            $hashtagCount[$tag] = ($hashtagCount[$tag] ?? 0) + 1;
                        }
                    }
                }

                $likes    = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $comments = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);

                $posts[] = [
                    'name'          => $authorName,
                    'content'       => substr(strip_tags($content), 0, 150),
                    'likes'         => $likes,
                    'comments'      => $comments,
                    'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',
                    'date_created'  => substr($item['date_created'] ?? '', 0, 10),
                    'mention_type'  => $item['mention_type'] ?? 'image',
                ];
            }

            arsort($hashtagCount);
            $hashtags = [];
            foreach ($hashtagCount as $name => $size) {
                $hashtags[] = ['name' => $name, 'size' => $size];
            }

            // ── Build dataset string untuk AI ──
            $total = $positive + $negative + $neutral ?: 1;
            $lines = [];
            $lines[] = "=== DATA INSTAGRAM PROJECT {$projectId} ===";
            $lines[] = "Periode: {$startDate} s/d {$endDate}";
            $lines[] = "Total Volume: {$volume} posts";
            $lines[] = "Sentimen: Positif " . round($positive / $total * 100) . "% ({$positive}) | Negatif " . round($negative / $total * 100) . "% ({$negative}) | Netral " . round($neutral / $total * 100) . "% ({$neutral})";

            if (!empty($hashtags)) {
                $lines[] = "\n--- TOP HASHTAGS INSTAGRAM (" . count($hashtags) . ") ---";
                foreach (array_slice($hashtags, 0, 25) as $i => $h) {
                    $lines[] = ($i + 1) . ". #{$h['name']} ({$h['size']} mentions)";
                }
            }

            if (!empty($posts)) {
                $lines[] = "\n--- TOP POSTS BY LIKES (" . count($posts) . " posts) ---";
                foreach (array_slice($posts, 0, 30) as $i => $post) {
                    $type = $post['mention_type'] === 'video' ? 'Reel/Video' : 'Image/Carousel';
                    $lines[] = "[" . ($i + 1) . "] @{$post['name']} | {$post['date_created']} | {$post['sentiment_str']} | {$type}";
                    $lines[] = "   Likes: {$post['likes']} | Comments: {$post['comments']}";
                    if ($post['content']) $lines[] = "   \"{$post['content']}\"";
                }
            }

            $lines[] = "=== AKHIR DATASET ===";

            return response()->json([
                'success' => true,
                'data'    => [
                    'dataset' => implode("\n", $lines),
                    'summary' => [
                        'total_posts'    => count($posts),
                        'total_hashtags' => count($hashtags),
                        'sentiment'      => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral],
                        'volume'         => $volume,
                    ],
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('IG aiAnalysisData error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    // ─────────────────────────────────────────────────────
    // AI PROXY (Gemini — sama persis dengan FB)
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
            $maxTokens = (int) $request->input('max_tokens', 8192);

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
                'gemini-2.5-pro',
                'gemini-2.0-flash',
                'gemini-2.0-flash-lite',
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
                        Log::info("✅ Gemini OK (Instagram)", ['model' => $model]);
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
            Log::error('IG AI Proxy Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

}