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

    /**
     * Display Facebook Overview Page
     */
    public function index(Request $request)
    {
        try {
            $projects = $this->getAllProjects();

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

    public function trendingTopicsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
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

            $result = $this->client->fbTopStatus($projectId, $startDate, $endDate);

            Log::info('FB mostViewedPostsData raw result', [
                'type'   => gettype($result),
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
            ]);

            $posts = [];
            $items = [];

            if (isset($result['data']) && is_array($result['data'])) {
                $items = $result['data'];
            } elseif (is_array($result) && !isset($result['success'])) {
                $items = $result;
            }

            foreach ($items as $item) {
                if (!is_array($item)) continue;

                $media = strtolower($item['media'] ?? $item['source'] ?? $item['tcode'] ?? '');
                if ($media && !str_contains($media, 'fb') && !str_contains($media, 'facebook')) {
                    continue;
                }

                $authorName = $item['contentJson']['from']['name']
                    ?? $item['author_name']
                    ?? $item['author']['name']
                    ?? $item['name']
                    ?? 'Unknown';

                if (str_contains($authorName, '<b>')) {
                    preg_match('/<b>(.*?)<\/b>/', $authorName, $matches);
                    $authorName = $matches[1] ?? $authorName;
                    $authorName = trim(str_replace(':', '', $authorName));
                }

                $profilePic = $item['contentJson']['from']['picture']['data']['url']
                    ?? $item['profile_url']
                    ?? $item['avatar_url']
                    ?? $item['author']['image']
                    ?? '';

                $likes      = (int) ($item['num_likes']    ?? $item['likes']    ?? 0);
                $shares     = (int) ($item['num_shares']   ?? $item['shares']   ?? 0);
                $comments   = (int) ($item['num_comments'] ?? $item['comments'] ?? 0);
                $engagement = $likes + $comments + $shares;
                $viewCount  = (int) ($item['view_cnt'] ?? $item['freq'] ?? $engagement);
                $postUrl    = $item['url'] ?? $item['link'] ?? null;
                $subId      = $item['sub_id'] ?? $item['docid'] ?? $item['id'] ?? '';

                $content = $item['content'] ?? $item['name'] ?? '';
                if (str_contains($content, '<b>')) {
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

            usort($posts, fn($a, $b) => $b['engagement'] - $a['engagement']);

            Log::info('FB mostViewedPostsData processed', ['total_posts' => count($posts)]);

            return response()->json(['success' => true, 'data' => $posts]);

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

            return response()->json(['success' => true, 'data' => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral]]);

        } catch (\Exception $e) {
            Log::error('Facebook sentimentTotal API error', ['error' => $e->getMessage()]);
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

    // ─────────────────────────────────────────────────────
    // TOP HASHTAGS
    // ─────────────────────────────────────────────────────

    public function topHashtagsPage(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
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
            Log::error('FB authorsAge API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
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

            $result = $this->client->authorsGender($projectId, 'facebook', $startDate, $endDate);

            Log::info('FB authorsGender API response', [
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('FB authorsGender API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
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

            $result = $this->client->authorsType($projectId, 'facebook', $startDate, $endDate);

            Log::info('FB authorsType API response', [
                'count'  => is_array($result) ? count($result) : 0,
                'sample' => is_array($result) ? array_slice($result, 0, 2, true) : [],
            ]);

            return response()->json($result);

        } catch (\Exception $e) {
            Log::error('FB authorsType API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
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
     * API: Get Facebook Geo User Data
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

            $result = $this->client->geoUserFacebook($projectId, $startDate, $endDate);

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
     * API: Get Facebook Geo Sentiment Data
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

            $result = $this->client->geoSentimentFacebook($projectId, $startDate, $endDate);

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

            $result    = $this->client->topLocationsFacebook($projectId, $startDate, $endDate);
            $locations = [];

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
                    return redirect()->route('mk.facebook.trending-word-cloud', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.facebook.facebook-trending-word-cloud')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            return view('mk.facebook.facebook-trending-word-cloud')->with([
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
                    return redirect()->route('mk.facebook.ai-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.ai-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook AI Analysis Page Error', [
                'error' => $e->getMessage(),
            ]);

            return view('mk.facebook.ai-analysis')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }

    // ─────────────────────────────────────────────────────
// AI ANALYSIS DATA + PROXY
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

        // ✅ Panggil client langsung, bukan lewat HTTP pool
        $postsRaw    = $this->client->fbTopStatus($projectId, $startDate, $endDate, 0, 23, 50, 'fblike');
        $hashtagsRaw = $this->client->topHashtags($projectId, 'fb', $startDate, $endDate);
        $sentimentRaw = $this->client->getSentiment($projectId, 'facebook', $startDate, $endDate);
        $volumeRaw   = $this->client->volumeTotal($projectId, 'facebook', $startDate, $endDate);

        // ── Parse sentiment (sama persis seperti sentimentTotal()) ──
        $positive = 0; $negative = 0; $neutral = 0;
        if (isset($sentimentRaw['pos'], $sentimentRaw['neg'], $sentimentRaw['net'])) {
            $positive = (int) $sentimentRaw['pos'];
            $negative = (int) $sentimentRaw['neg'];
            $neutral  = (int) $sentimentRaw['net'];
        } elseif (isset($sentimentRaw['bymedia']['fb'])) {
            $d = $sentimentRaw['bymedia']['fb'];
            $positive = (int) ($d['pos'] ?? 0);
            $negative = (int) ($d['neg'] ?? 0);
            $neutral  = (int) ($d['net'] ?? 0);
        } elseif (isset($sentimentRaw['bymedia']['facebook'])) {
            $d = $sentimentRaw['bymedia']['facebook'];
            $positive = (int) ($d['pos'] ?? 0);
            $negative = (int) ($d['neg'] ?? 0);
            $neutral  = (int) ($d['net'] ?? 0);
        }

        // ── Parse volume ──
        $volume = 0;
        if (isset($volumeRaw['all']['total'])) {
            $volume = (int) $volumeRaw['all']['total'];
        } elseif (isset($volumeRaw['bymedia']['fb'])) {
            $volume = (int) $volumeRaw['bymedia']['fb'];
        } elseif (isset($volumeRaw['bymedia']['facebook'])) {
            $volume = (int) $volumeRaw['bymedia']['facebook'];
        }

        // ── Parse hashtags ──
        $hashtags = [];
        $rawItems = $hashtagsRaw['data']['hashtags'] ?? $hashtagsRaw['data'] ?? $hashtagsRaw['fb'] ?? $hashtagsRaw ?? [];
        foreach ($rawItems as $item) {
            if (!is_array($item)) continue;
            $name = $item['name'] ?? $item['hashtag'] ?? '';
            $size = (int) ($item['size'] ?? $item['count'] ?? 0);
            if ($name && $size > 0) {
                $hashtags[] = ['name' => ltrim($name, '#'), 'size' => $size];
            }
        }
        usort($hashtags, fn($a, $b) => $b['size'] - $a['size']);

        // ── Parse posts ──
        $posts = [];
        $items = is_array($postsRaw) ? $postsRaw : ($postsRaw['data'] ?? []);
        foreach ($items as $item) {
            if (!is_array($item)) continue;
            $authorName = $item['contentJson']['from']['name'] ?? $item['author_name'] ?? $item['name'] ?? 'Unknown';
            if (str_contains($authorName, '<b>')) {
                preg_match('/<b>(.*?)<\/b>/', $authorName, $matches);
                $authorName = trim(str_replace(':', '', $matches[1] ?? $authorName));
            }
            $content = $item['content'] ?? $item['name'] ?? '';
            if (str_contains($content, '<b>')) {
                $content = trim(preg_replace('/<b>.*?<\/b>\s*/', '', $content));
            }
            $posts[] = [
                'name'          => $authorName,
                'content'       => substr(strip_tags($content), 0, 150),
                'likes'         => (int) ($item['num_likes']    ?? $item['likes']    ?? 0),
                'shares'        => (int) ($item['num_shares']   ?? $item['shares']   ?? 0),
                'comments'      => (int) ($item['num_comments'] ?? $item['comments'] ?? 0),
                'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',
                'date_created'  => substr($item['date_created'] ?? '', 0, 10),
            ];
        }

        // ── Build dataset string untuk AI ──
        $total = $positive + $negative + $neutral ?: 1;
        $lines = [];
        $lines[] = "=== DATA FACEBOOK PROJECT {$projectId} ===";
        $lines[] = "Periode: {$startDate} s/d {$endDate}";
        $lines[] = "Total Volume: {$volume} posts";
        $lines[] = "Sentimen: Positif " . round($positive/$total*100) . "% ({$positive}) | Negatif " . round($negative/$total*100) . "% ({$negative}) | Netral " . round($neutral/$total*100) . "% ({$neutral})";

        if (!empty($hashtags)) {
            $lines[] = "\n--- TOP HASHTAGS ---";
            foreach (array_slice($hashtags, 0, 20) as $i => $h) {
                $lines[] = ($i+1) . ". #{$h['name']} ({$h['size']} mentions)";
            }
        }

        if (!empty($posts)) {
            $lines[] = "\n--- TOP POSTS BY ENGAGEMENT (" . count($posts) . " posts) ---";
            foreach (array_slice($posts, 0, 30) as $i => $post) {
                $lines[] = "[" . ($i+1) . "] \"{$post['content']}\" | {$post['name']} | {$post['date_created']} | Likes:{$post['likes']} Shares:{$post['shares']} Comments:{$post['comments']} | {$post['sentiment_str']}";
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
        Log::error('FB aiAnalysisData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

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
        Log::error('FB AI Proxy Error', ['error' => $e->getMessage()]);
        return response()->json(['error' => $e->getMessage()], 500);
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
                return redirect()->route('mk.facebook.most-engagement', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.facebook.most-engagement')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('Facebook Most Engagement Page Error', ['error' => $e->getMessage()]);
        return view('mk.facebook.most-engagement')->with([
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
                    return redirect()->route('mk.facebook.emotion-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

            return view('mk.facebook.facebook-emotion-analysis')->with([
                'projectId' => $projectId,
                'startDate' => $startDate,
                'endDate'   => $endDate,
                'projects'  => $projects,
            ]);

        } catch (\Exception $e) {
            Log::error('Facebook Emotion Analysis Page Error', ['error' => $e->getMessage()]);
            return view('mk.facebook.facebook-emotion-analysis')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
                'error'     => $e->getMessage(),
            ]);
        }
    }
    public function mostEngagementData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date');
        $endDate   = $request->query('end_date');
        $sub       = $request->query('sub', 'fblike'); // fblike | fbshare | fbcomment
        $rows      = (int) $request->query('rows', 100);

        if (!$projectId || !$startDate || !$endDate) {
            return response()->json(['success' => false, 'error' => 'Missing required parameters'], 400);
        }

        $result = $this->client->fbTopStatus($projectId, $startDate, $endDate, 0, 23, $rows, $sub);

        $posts = [];
        $items = is_array($result) ? $result : ($result['data'] ?? []);

        foreach ($items as $item) {
            if (!is_array($item)) continue;

            $authorName = $item['contentJson']['from']['name']
                ?? $item['author_name']
                ?? $item['name']
                ?? 'Unknown';

            // Bersihkan HTML dari nama
            if (str_contains($authorName, '<b>')) {
                preg_match('/<b>(.*?)<\/b>/', $authorName, $matches);
                $authorName = trim(str_replace(':', '', $matches[1] ?? $authorName));
            }

            $profilePic = $item['contentJson']['from']['picture']['data']['url']
                ?? $item['profile_url']
                ?? $item['avatar_url']
                ?? '';

            $content = $item['content'] ?? $item['name'] ?? '';
            if (str_contains($content, '<b>')) {
                $content = trim(preg_replace('/<b>.*?<\/b>\s*/', '', $content));
            }

            $posts[] = [
                'id'            => $item['id']           ?? '',
                'sub_id'        => $item['sub_id']       ?? $item['docid'] ?? '',
                'name'          => $authorName,
                'content'       => $content,
                'likes'         => (int) ($item['num_likes']    ?? $item['likes']    ?? $item['freq'] ?? 0),
                'shares'        => (int) ($item['num_shares']   ?? $item['shares']   ?? 0),
                'comments'      => (int) ($item['num_comments'] ?? $item['comments'] ?? 0),
                'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',
                'date_created'  => $item['date_created']  ?? '',
                'url'           => $item['url']           ?? $item['link'] ?? null,
                'avatar_url'    => $profilePic,
                'tcode'         => $item['tcode']         ?? 'fb-post',
            ];
        }

        // JANGAN sort ulang — API sudah sort by sub yang diminta
        return response()->json(['success' => true, 'data' => $posts]);

    } catch (\Exception $e) {
        Log::error('FB mostEngagementData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}
}
//jjkllokij