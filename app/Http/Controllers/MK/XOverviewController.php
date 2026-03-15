<?php

    namespace App\Http\Controllers\MK;

    use App\Http\Controllers\Controller;
    use App\Services\MediaKernelsClient;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Log;
    use Carbon\Carbon;

    class XOverviewController extends Controller
    {
        private MediaKernelsClient $client;

        public function __construct(MediaKernelsClient $client)
        {
            $this->client = $client;
        }

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

        /**
         * Display X Overview Page
         */
        public function index(Request $request)
        {
            try {
                $projects = $this->getAllProjects();

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
                if (isset($result['bymedia']['twit'])) {
                    $total = (int) $result['bymedia']['twit'];
                } elseif (isset($result['data']['total_author'])) {
                    $total = (int) $result['data']['total_author'];
                } elseif (isset($result['data']['total'])) {
                    $total = (int) $result['data']['total'];
                }

                return response()->json([
                    'success' => true,
                    'data' => ['total' => $total]
                ]);

            } catch (\Exception $e) {
                Log::error('X totalUsers API error', [
                    'error' => $e->getMessage(),
                    'project_id' => $request->query('project_id')
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                    'data' => ['total' => $total]
                ]);

            } catch (\Exception $e) {
                Log::error('X totalAuthors API error', [
                    'error' => $e->getMessage(),
                    'project_id' => $request->query('project_id')
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                    'data' => ['total' => $total, 'chart' => $chartData]
                ]);

            } catch (\Exception $e) {
                Log::error('X volumeTotal API error', [
                    'error' => $e->getMessage(),
                    'project_id' => $request->query('project_id')
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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

                $positive = 0; $negative = 0; $neutral = 0;

                if (isset($result['pos']) && isset($result['neg']) && isset($result['net'])) {
                    $positive = (int) $result['pos'];
                    $negative = (int) $result['neg'];
                    $neutral  = (int) $result['net'];
                } elseif (isset($result['bymedia']['twit'])) {
                    $twitData = $result['bymedia']['twit'];
                    $positive = isset($twitData['pos']) ? (int) $twitData['pos'] : 0;
                    $negative = isset($twitData['neg']) ? (int) $twitData['neg'] : 0;
                    $neutral  = isset($twitData['net']) ? (int) $twitData['net'] : 0;
                }

                return response()->json([
                    'success' => true,
                    'data' => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral]
                ]);

            } catch (\Exception $e) {
                Log::error('X sentimentTotal API error', [
                    'error' => $e->getMessage(),
                    'project_id' => $request->query('project_id')
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                    return response()->json(['success' => false, 'error' => 'Missing params'], 400);
                }

                $result = $this->client->mostActiveUsers($projectId, $startDate, $endDate);

                Log::info('RAW API mostActiveUsers response:', [
                    'status'       => 'received',
                    'has_data'     => isset($result['data']),
                    'data_structure' => is_array($result['data']) ? array_keys($result['data']) : 'not_array',
                    'sample_user_0' => isset($result['data']['data'][0]) ? [
                        'name'     => $result['data']['data'][0]['name'] ?? null,
                        'y'        => $result['data']['data'][0]['y'] ?? null,
                        'mentions' => $result['data']['data'][0]['mentions'] ?? null,
                        'replies'  => $result['data']['data'][0]['replies'] ?? null,
                        'retweets' => $result['data']['data'][0]['retweets'] ?? null,
                    ] : 'no_sample',
                ]);

                $users = [];

                if (isset($result['data']['data']) && is_array($result['data']['data'])) {
                    foreach ($result['data']['data'] as $user) {
                        $username = $user['contentJson']['screen_name'] ?? '';
                        if (!$username) {
                            preg_match('/@(\w+)/', $user['name'] ?? '', $m);
                            $username = $m[1] ?? '';
                        }

                        $mentions   = (int)$user['mentions'];
                        $replies    = (int)$user['replies'];
                        $retweets   = (int)$user['retweets'];
                        $engagement = (int)($user['y'] ?? 0);
                        if ($engagement === 0) {
                            $engagement = $mentions + $replies + $retweets;
                        }

                        $accountName = $user['contentJson']['name'] ?? '';
                        if (!$accountName) {
                            $accountName = trim(preg_replace('/@\w+/', '', $user['name'] ?? ''));
                        }

                        $profileUrl = $user['profile_url'] ?? $user['contentJson']['profile_image_url_https'] ?? '';
                        $profileUrl = str_replace('_normal', '_bigger', $profileUrl);

                        $followers = (int)($user['followers'] ?? $user['contentJson']['followers_count'] ?? 0);
                        $following  = (int)($user['contentJson']['friends_count'] ?? 0);

                        if ($username) {
                            $users[] = [
                                'username'           => $username,
                                'name'               => $accountName ?: $username,
                                'profile_url'        => $profileUrl,
                                'profile_image_url'  => $profileUrl,
                                'followers'          => $followers,
                                'following'          => $following,
                                'mentions'           => $mentions,
                                'replies'            => $replies,
                                'retweets'           => $retweets,
                                'posts'              => $engagement,
                                'y'                  => $engagement,
                                'engagement'         => $engagement,
                                'id'                 => $user['id'] ?? '',
                                'contentJson'        => $user['contentJson'] ?? null,
                            ];
                        }
                    }

                    usort($users, fn($a, $b) => $b['engagement'] - $a['engagement']);
                }

                Log::info('Most Active Users - Final Processed Data', [
                    'total_users' => count($users),
                    'top_5' => array_map(fn($u) => [
                        'name'       => $u['name'],
                        'username'   => $u['username'],
                        'engagement' => $u['engagement'],
                        'mentions'   => $u['mentions'],
                        'replies'    => $u['replies'],
                        'retweets'   => $u['retweets'],
                    ], array_slice($users, 0, 5))
                ]);

                return response()->json(['success' => true, 'data' => ['data' => $users]]);

            } catch (\Exception $e) {
                Log::error('mostActiveUsers error', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                Log::error('Most Retweets Page Error', ['error' => $e->getMessage()]);
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

                Log::info('mostRetweets raw sample', [
                    'sample' => array_slice(is_array($result) ? $result : [], 0, 3),
                    'fields' => (is_array($result) && count($result) > 0) ? array_keys($result[0]) : []
                ]);

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
                            'freq'           => (int) ($item['freq']    ?? $item['rt'] ?? 0),
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

                return response()->json(['success' => true, 'data' => $tweets]);

            } catch (\Exception $e) {
                Log::error('X mostRetweets API error', [
                    'error'      => $e->getMessage(),
                    'project_id' => $request->query('project_id'),
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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

                Log::info('userMentions raw result sample', [
                    'username' => $username,
                    'count'    => count($result),
                    'fields'   => count($result) > 0 ? array_keys($result[0]) : [],
                ]);

                $mentions = [];
                if (is_array($result)) {
                    foreach ($result as $mention) {
                        $sentimentCode = $mention['class_sentiment_code'] ?? $mention['class_sentiment'] ?? 'neutral';
                        $sentiment = match(strtolower((string)$sentimentCode)) {
                            'pos', '1', 'positive'  => 'positive',
                            'neg', '-1', 'negative' => 'negative',
                            default                 => 'neutral',
                        };

                        $likes    = (int)($mention['num_likes']    ?? $mention['likes']    ?? $mention['fav_count']   ?? 0);
                        $retweets = (int)($mention['num_shares']   ?? $mention['retweets'] ?? $mention['rt_count']    ?? $mention['rt'] ?? 0);
                        $replies  = (int)($mention['num_comments'] ?? $mention['replies']  ?? $mention['reply_count'] ?? 0);

                        $mentions[] = [
                            'id'           => $mention['id']             ?? $mention['docid'] ?? uniqid(),
                            'text'         => $mention['content']        ?? $mention['text']  ?? $mention['full_text'] ?? '',
                            'created_at'   => $mention['date_created']   ?? $mention['timestamp'] ?? now()->toISOString(),
                            'sentiment'    => $sentiment,
                            'author'       => $mention['author_scr_name'] ?? $mention['author_name'] ?? $username,
                            'author_name'  => $mention['author_name']     ?? $mention['name']        ?? $username,
                            'location'     => $mention['author_location'] ?? $mention['location']    ?? '',
                            'mention_type' => $mention['mention_type']    ?? $mention['type']        ?? 'tweet',
                            'num_likes'    => $likes,
                            'num_shares'   => $retweets,
                            'num_comments' => $replies,
                            'url'          => $mention['url'] ?? $mention['link'] ?? $mention['tweet_url'] ?? '#',
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'data'    => ['username' => $username, 'mentions' => $mentions, 'total' => count($mentions)]
                ]);

            } catch (\Exception $e) {
                Log::error('X userMentions API error', [
                    'error'    => $e->getMessage(),
                    'username' => $request->query('username')
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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
                Log::error('Top Hashtags Page Error', ['error' => $e->getMessage()]);
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

                $result = $this->client->topHashtags($projectId, 'twit', $startDate, $endDate);

                Log::info('topHashtagsData raw result', [
                    'type'   => gettype($result),
                    'keys'   => is_array($result) ? array_keys($result) : [],
                    'sample' => is_array($result) ? array_slice($result, 0, 2, true) : $result,
                ]);

                $hashtags = []; $totalMentions = 0; $rawItems = [];

                if (isset($result['data']['hashtags']) && is_array($result['data']['hashtags'])) {
                    $rawItems = $result['data']['hashtags'];
                } elseif (isset($result['data']) && is_array($result['data'])) {
                    $rawItems = $result['data'];
                } elseif (is_array($result)) {
                    $firstVal = reset($result);
                    if (is_array($firstVal) && isset($firstVal['name'])) {
                        $rawItems = $result;
                    } elseif (isset($result['twit']) && is_array($result['twit'])) {
                        $rawItems = $result['twit'];
                    } else {
                        $rawItems = $result;
                    }
                }

                foreach ($rawItems as $item) {
                    if (!is_array($item)) continue;
                    $name  = $item['name'] ?? $item['hashtag'] ?? '';
                    $size  = (int) ($item['size'] ?? $item['count'] ?? $item['total'] ?? 0);
                    $media = strtolower($item['media'] ?? $item['source'] ?? $item['platform'] ?? '');
                    if ($media && !in_array($media, ['twit', 'twitter', 'x', ''])) continue;
                    if ($name && $size > 0) {
                        $hashtags[]     = ['name' => ltrim($name, '#'), 'size' => $size, 'hashtag' => '#' . ltrim($name, '#')];
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
                Log::error('topHashtagsData API error', [
                    'error'      => $e->getMessage(),
                    'project_id' => $request->query('project_id'),
                ]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // ==========================================
        // AUTHORS DEMOGRAPHICS
        // ==========================================

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
                return view('mk.x.authors-age')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Authors Age Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.authors-age')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function authorsAgeData(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['error' => 'Missing required parameters'], 400);
                $result = $this->client->authorsAge($projectId, 'twitter', $startDate, $endDate);
                return response()->json($result);
            } catch (\Exception $e) {
                Log::error('authorsAge API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

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
                return view('mk.x.authors-gender')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Authors Gender Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.authors-gender')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function authorsGenderData(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['error' => 'Missing required parameters'], 400);
                $result = $this->client->authorsGender($projectId, 'twitter', $startDate, $endDate);
                return response()->json($result);
            } catch (\Exception $e) {
                Log::error('authorsGender API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

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
                return view('mk.x.authors-type')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Authors Type Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.authors-type')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function authorsTypeData(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['error' => 'Missing required parameters'], 400);
                $result = $this->client->authorsType($projectId, 'twitter', $startDate, $endDate);
                return response()->json($result);
            } catch (\Exception $e) {
                Log::error('authorsType API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['error' => $e->getMessage()], 500);
            }
        }

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
                return view('mk.x.authors-demographics')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Authors Demographics Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.authors-demographics')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        // ==========================================
        // GEOGRAPHIC
        // ==========================================

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
                return view('mk.x.geographic')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('X Geographic Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.geographic')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function geoUser(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
                $result = $this->client->geoTwitterUser($projectId, 'twitter', $startDate, $endDate);
                return response()->json(['success' => true, 'data' => $result]);
            } catch (\Exception $e) {
                Log::error('geoUser API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        public function geoSentiment(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
                $result = $this->client->geoTwitterUserSentiment($projectId, 'twitter', $startDate, $endDate, 0, 23, 1);
                return response()->json(['success' => true, 'data' => $result]);
            } catch (\Exception $e) {
                Log::error('geoSentiment API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        public function topLocations(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);
                $result    = $this->client->topAuthorLocation($projectId, 'twitter', $startDate, $endDate);
                $locations = [];
                if (is_array($result)) {
                    foreach ($result as $location) {
                        $locations[] = [
                            'name'  => $location['name']  ?? $location['location'] ?? 'Unknown',
                            'count' => $location['count'] ?? $location['total']    ?? 0,
                        ];
                    }
                    usort($locations, fn($a, $b) => $b['count'] - $a['count']);
                }
                return response()->json(['success' => true, 'data' => $locations]);
            } catch (\Exception $e) {
                Log::error('topLocations API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // ==========================================
        // MOST STATUS
        // ==========================================

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
                return view('mk.x.most-status')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Most Status Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.most-status')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function mostStatus(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);

$result = $this->client->mostStatus($projectId, 'all', $startDate, $endDate);

Log::info('mostStatus raw result', [
    'type'        => gettype($result),
    'keys'        => is_array($result) ? array_keys($result) : 'not_array',
    'sample_key0' => is_array($result) ? ($result[0] ?? 'NO_KEY_0') : 'not_array',
    'sample'      => is_array($result) ? array_slice($result, 0, 2, true) : $result,
]);                $posts  = [];

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
                    usort($posts, fn($a, $b) => $b['view_cnt'] - $a['view_cnt']);
                }

                return response()->json(['success' => true, 'data' => $posts]);

            } catch (\Exception $e) {
                Log::error('X mostStatus API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // ==========================================
        // POST WITH LOCATION
        // ==========================================

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
                return view('mk.x.post-with-location')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Post with Location Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.post-with-location')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function postWithLocation(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);

                $result = $this->client->postWithLocation($projectId, $startDate, $endDate, 0, 23, 0, 1000);

                Log::info('postWithLocation API response', [
                    'count'  => is_array($result) ? count($result) : 0,
                    'sample' => is_array($result) && count($result) > 0 ? $result[0] : null
                ]);

                $posts = [];
                if (is_array($result)) {
                    foreach ($result as $item) {
                        if (empty($item['author_location']) && empty($item['cat_loc'])) continue;
                        $author      = isset($item['author']) ? (is_string($item['author']) ? json_decode($item['author'], true) : $item['author']) : [];
                        $posts[] = [
                            'docid'                 => $item['id']             ?? '',
                            'author_id'             => $author['id']           ?? '',
                            'author_scr_name'       => $item['name']           ?? $author['scr_name'] ?? '',
                            'date_created'          => $item['date_created']   ?? '',
                            'location'              => $item['author_location'] ?? $item['cat_loc']   ?? '',
                            'coordinates'           => $item['cat_coord']       ?? '',
                            'content'               => $item['content']         ?? '',
                            'user_mention1'         => null,
                            'user_mention2'         => null,
                            'user_mention3'         => null,
                            'class_sentiment'       => $item['class_sentiment'] ?? '0',
                            'class_sentiment_label' => $item['class_sentiment'] ?? 'neutral',
                        ];
                    }
                }

                return response()->json(['success' => true, 'data' => $posts, 'total' => count($posts)]);

            } catch (\Exception $e) {
                Log::error('postWithLocation API error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString(), 'project_id' => $request->query('project_id')]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // ==========================================
        // TRENDING TOPICS
        // ==========================================

        public function trendingTopicsPage(Request $request)
        {
            try {
                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                $location  = $request->query('location', 'Indonesia');
                return view('mk.x.trending-topics')->with(['startDate' => $startDate, 'endDate' => $endDate, 'location' => $location]);
            } catch (\Exception $e) {
                Log::error('X Trending Topics Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.trending-topics')->with(['startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'location' => 'Indonesia', 'error' => $e->getMessage()]);
            }
        }

        public function trendingTopicsData(Request $request)
        {
            try {
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                $location  = $request->query('location', 'Indonesia');
                if (!$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: start_date, end_date'], 400);

                $result = $this->client->twitterTrendingTopics($startDate, $endDate, 0, 23, $location, '');

                $trending = []; $allTopics = [];
                $positiveKeywords = ['win','winner','won','best','good','great','love','happy','success','amazing','excellent','perfect','beautiful','wonderful','fantastic','celebrate','celebration','victory','achievement','congratulations'];
                $negativeKeywords = ['bad','worst','hate','sad','fail','failed','lose','lost','angry','terrible','awful','poor','wrong','crisis','disaster','tragic','death','died','scandal','controversial','protest','boycott'];

                foreach ($result as $datetime => $period) {
                    if (!is_array($period) || !isset($period['data'])) continue;
                    $date    = date('Y-m-d', strtotime($datetime));
                    $timeAgo = $period['str_datetime_ago'] ?? '';

                    foreach ($period['data'] as $topic) {
                        $name   = $topic['name']          ?? '';
                        $volume = (int) ($topic['tweet_volume_i'] ?? 0);
                        $rank   = (int) ($topic['rank_i']         ?? 0);
                        $url    = $topic['url']           ?? '';
                        if (!$name) continue;

                        $source    = strtolower($topic['source'] ?? '');
                        $isTwitter = stripos($url, 'twitter.com') !== false || stripos($url, 'x.com') !== false || in_array($source, ['twitter','x','twit']);
                        if (!$isTwitter && $url && $url !== '#') {
                            if (stripos($url, 'facebook.com') !== false || stripos($url, 'youtube.com') !== false || stripos($url, 'instagram.com') !== false || stripos($url, 'tiktok.com') !== false) continue;
                        }

                        $sentiment  = 'neutral';
                        $lowerName  = strtolower($name);
                        foreach ($positiveKeywords as $kw) { if (stripos($lowerName, $kw) !== false) { $sentiment = 'positive'; break; } }
                        if ($sentiment === 'neutral') foreach ($negativeKeywords as $kw) { if (stripos($lowerName, $kw) !== false) { $sentiment = 'negative'; break; } }

                        if (!isset($allTopics[$name])) {
                            $allTopics[$name] = ['name' => $name, 'total_volume' => 0, 'appearances' => 0, 'avg_rank' => 0, 'url' => $url, 'sentiment' => $sentiment, 'history' => []];
                        } elseif ($allTopics[$name]['sentiment'] === 'neutral' && $sentiment !== 'neutral') {
                            $allTopics[$name]['sentiment'] = $sentiment;
                        }
                        $allTopics[$name]['total_volume'] += $volume;
                        $allTopics[$name]['appearances']++;
                        $allTopics[$name]['avg_rank']     += $rank;
                        $allTopics[$name]['history'][]     = ['date' => $date, 'datetime' => $datetime, 'rank' => $rank, 'volume' => $volume, 'time_ago' => $timeAgo, 'sentiment' => $sentiment];
                    }

                    if (!isset($trending[$date])) $trending[$date] = ['date' => $date, 'datetime' => $datetime, 'time_ago' => $timeAgo, 'topics' => []];

                    $twitterTopics = array_values(array_filter($period['data'], function ($topic) {
                        $url = $topic['url'] ?? ''; $source = strtolower($topic['source'] ?? '');
                        $isTwitter = stripos($url,'twitter.com')!==false || stripos($url,'x.com')!==false || in_array($source,['twitter','x','twit']);
                        if ($url && $url !== '#') { if (stripos($url,'facebook.com')!==false || stripos($url,'youtube.com')!==false || stripos($url,'instagram.com')!==false || stripos($url,'tiktok.com')!==false) return false; }
                        return $isTwitter || (!$url || $url === '#');
                    }));
                    $trending[$date]['topics'] = $twitterTopics;
                }

                foreach ($allTopics as &$topic) {
                    if ($topic['appearances'] > 0) $topic['avg_rank'] = round($topic['avg_rank'] / $topic['appearances'], 1);
                }
                usort($allTopics, fn($a, $b) => $b['total_volume'] - $a['total_volume']);
                krsort($trending);

                $sentimentCounts = ['positive' => 0, 'neutral' => 0, 'negative' => 0];
                foreach ($allTopics as $topic) $sentimentCounts[$topic['sentiment']]++;

                return response()->json([
                    'success' => true,
                    'data'    => ['trending' => array_values($trending), 'top_topics' => $allTopics, 'total_periods' => count($trending), 'total_unique_topics' => count($allTopics), 'sentiment_counts' => $sentimentCounts],
                ]);

            } catch (\Exception $e) {
                Log::error('trendingTopicsData API error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        public function trendingWordCloudPage(Request $request)
        {
            try {
                $projects  = $this->getAllProjects();
                $projectId = $request->query('project_id');
                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                $location  = $request->query('location', 'Indonesia');

                if (!$projectId && count($projects) > 0) {
                    $projectId = $projects[0]['id'] ?? null;
                }

                return view('mk.x.trending-word-cloud')->with([
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'location'  => $location,
                    'projects'  => $projects,
                    'projectId' => $projectId,
                ]);
            } catch (\Exception $e) {
                Log::error('X Trending Word Cloud Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.trending-word-cloud')->with([
                    'startDate' => now()->subDays(6)->format('Y-m-d'),
                    'endDate'   => now()->format('Y-m-d'),
                    'location'  => 'Indonesia',
                    'projects'  => [],
                    'projectId' => null,
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        // ==========================================
        // SHARED URLS
        // ==========================================

        public function sharedUrlsPage(Request $request)
        {
            try {
                $projectsData = $this->client->listProjects(0, 100);
                $projects     = $projectsData['data'] ?? [];
                $projectId    = $request->query('project_id');
                if (!$projectId && count($projects) > 0) {
                    $projectId = $projects[0]['id'] ?? null;
                    if ($projectId) {
                        return redirect()->route('mk.x.shared-urls', [
                            'project_id' => $projectId,
                            'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                        ]);
                    }
                }
                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                return view('mk.x.shared-urls')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Shared URLs Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.shared-urls')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        public function sharedUrls(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                if (!$projectId || !$startDate || !$endDate) return response()->json(['success' => false, 'error' => 'Missing required parameters: project_id, start_date, end_date'], 400);

                $result = $this->client->sharedUrlFreq($projectId, $startDate, $endDate);
                $urls   = [];
                if (isset($result['data']) && is_array($result['data'])) {
                    foreach ($result['data'] as $item) {
                        $url      = $item['url']      ?? '';
                        $freq     = (int) ($item['freq'] ?? 0);
                        $hostname = $item['hostname']  ?? '';
                        if (!$hostname && $url) { try { $hostname = parse_url($url, PHP_URL_HOST) ?: ''; } catch (\Exception $e) { $hostname = ''; } }
                        if (!$url) continue;
                        $urls[] = ['url' => $url, 'freq' => $freq, 'hostname' => $hostname];
                    }
                    usort($urls, fn($a, $b) => $b['freq'] - $a['freq']);
                }

                Log::info('sharedUrls controller', ['project_id' => $projectId, 'total_items' => count($urls), 'sample' => array_slice($urls, 0, 3)]);
                return response()->json(['success' => true, 'data' => $urls, 'total' => count($urls)]);

            } catch (\Exception $e) {
                Log::error('sharedUrls API error', ['error' => $e->getMessage(), 'project_id' => $request->query('project_id')]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // ==========================================
        // MOST ACTIVE USERS PAGE
        // ==========================================

        public function mostActiveUsersPage(Request $request)
        {
            try {
                $projects = $this->getAllProjects();
                $projectId = $request->query('project_id');
                if (!$projectId && count($projects) > 0) {
                    $projectId = $projects[0]['id'] ?? null;
                    if ($projectId) {
                        return redirect()->route('mk.x.most-active-users', [
                            'project_id' => $projectId,
                            'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                        ]);
                    }
                }
                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                return view('mk.x.most-active-users')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('Most Active Users Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.most-active-users')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        // ==========================================
        // AI ANALYSIS
        // ==========================================

        public function aiAnalysisPage(Request $request)
        {
            try {
                $projects  = $this->getAllProjects();
                $projectId = $request->query('project_id');
                if (!$projectId && count($projects) > 0) {
                    $projectId = $projects[0]['id'] ?? null;
                    if ($projectId) {
                        return redirect()->route('mk.x.ai-analysis', [
                            'project_id' => $projectId,
                            'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                        ]);
                    }
                }
                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
                return view('mk.x.ai-analysis')->with(['projectId' => $projectId, 'startDate' => $startDate, 'endDate' => $endDate, 'projects' => $projects]);
            } catch (\Exception $e) {
                Log::error('X AI Analysis Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.ai-analysis')->with(['projectId' => null, 'startDate' => now()->subDays(6)->format('Y-m-d'), 'endDate' => now()->format('Y-m-d'), 'projects' => [], 'error' => $e->getMessage()]);
            }
        }

        // ==========================================
        // USER DETAILED MENTIONS
        // ==========================================

        public function userDetailedMentions(Request $request)
        {
            try {
                $projectId = $request->query('project_id');
                $username  = $request->query('username');
                $startDate = $request->query('start_date');
                $endDate   = $request->query('end_date');
                $apiStart  = (int) $request->query('api_start', 0);
                $perBatch  = 50;

                if (!$projectId || !$username) return response()->json(['success' => false, 'error' => 'Missing params'], 400);

                $result       = $this->client->getUserPosts($projectId, $startDate, $endDate, $username, $perBatch, 300, 5, $apiStart);
                $rawPosts     = $result['posts']         ?? [];
                $hasMore      = $result['has_more']       ?? false;
                $nextApiStart = $result['next_api_start'] ?? 0;
                $totalScanned = $result['total_scanned']  ?? 0;

                $formatted = []; $sentimentCounts = ['positive' => 0, 'neutral' => 0, 'negative' => 0]; $typeCounts = ['tweet' => 0, 'reply' => 0, 'retweet' => 0, 'mention' => 0];

                foreach ($rawPosts as $post) {
                    $sentimentCode = $post['class_sentiment_code'] ?? $post['class_sentiment'] ?? 'neutral';
                    $sentiment = match(strtolower((string)$sentimentCode)) { 'pos','1','positive' => 'positive', 'neg','-1','negative' => 'negative', default => 'neutral' };
                    $sentimentCounts[$sentiment]++;

                    $tcode = strtolower($post['tcode'] ?? $post['mention_type'] ?? '');
                    $mentionType = 'tweet';
                    if (str_contains($tcode,'rep') || str_contains($tcode,'reply')) $mentionType = 'reply';
                    elseif (str_contains($tcode,'rt')  || str_contains($tcode,'retweet')) $mentionType = 'retweet';
                    elseif (str_contains($tcode,'men') || str_contains($tcode,'mention')) $mentionType = 'mention';
                    $typeCounts[$mentionType]++;

                    $likes    = (int)($post['num_likes']    ?? $post['fav_count']   ?? $post['fav']      ?? 0);
                    $retweets = (int)($post['num_shares']   ?? $post['rt_count']    ?? $post['rt']       ?? $post['num_retweeted'] ?? 0);
                    $replies  = (int)($post['num_comments'] ?? $post['reply_count'] ?? $post['replies']  ?? 0);

                    $formatted[] = [
                        'id'           => $post['id']              ?? $post['docid']      ?? uniqid(),
                        'text'         => $post['content']         ?? $post['text']       ?? '',
                        'timestamp'    => $post['date_created']    ?? $post['created_at'] ?? '',
                        'sentiment'    => $sentiment,
                        'likes'        => $likes,
                        'retweets'     => $retweets,
                        'replies'      => $replies,
                        'url'          => $post['url']             ?? $post['link']       ?? '#',
                        'mention_type' => $mentionType,
                        'tcode'        => $tcode,
                        'author'       => $post['author_scr_name'] ?? $post['name']       ?? $username,
                        'author_name'  => $post['author_name']     ?? $username,
                        'location'     => $post['author_location'] ?? $post['location']   ?? '',
                    ];
                }

                usort($formatted, function ($a, $b) {
                    $engA = $a['likes'] + $a['retweets'] + $a['replies'];
                    $engB = $b['likes'] + $b['retweets'] + $b['replies'];
                    return $engA === $engB ? strtotime($b['timestamp']) - strtotime($a['timestamp']) : $engB - $engA;
                });

                $total        = array_sum($sentimentCounts);
                $sentimentPct = [
                    'positive' => $total > 0 ? round(($sentimentCounts['positive'] / $total) * 100, 1) : 0,
                    'neutral'  => $total > 0 ? round(($sentimentCounts['neutral']  / $total) * 100, 1) : 0,
                    'negative' => $total > 0 ? round(($sentimentCounts['negative'] / $total) * 100, 1) : 0,
                ];

                $start = Carbon::parse($startDate); $end = Carbon::parse($endDate);
                $labels = []; $values = []; $dateMap = [];
                foreach ($formatted as $m) { $dk = substr($m['timestamp'], 0, 10); $dateMap[$dk] = ($dateMap[$dk] ?? 0) + 1; }
                for ($d = clone $start; $d <= $end; $d->addDay()) { $dk = $d->format('Y-m-d'); $labels[] = $d->format('M d'); $values[] = $dateMap[$dk] ?? 0; }

                return response()->json([
                    'success' => true,
                    'data'    => [
                        'username'       => $username,
                        'mentions'       => $formatted,
                        'total'          => count($formatted),
                        'has_more'       => $hasMore,
                        'next_api_start' => $nextApiStart,
                        'total_scanned'  => $totalScanned,
                        'timeline'       => ['labels' => $labels, 'values' => $values],
                        'sentiment'      => ['counts' => $sentimentCounts, 'percentages' => $sentimentPct, 'total' => $total],
                        'type_breakdown' => $typeCounts,
                        'interactions'   => ['total' => count($formatted), 'avg_per_day' => count($values) > 0 ? round(count($formatted) / count($values), 1) : 0],
                    ],
                ]);

            } catch (\Exception $e) {
                Log::error('userDetailedMentions error', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
            }
        }

        // ==========================================
        // TOP INFLUENCERS  ← UPDATED
        // ==========================================

        /**
         * Display Top Influencers Page
         */
        public function topInfluencersPage(Request $request)
        {
            try {
                $projects = $this->getAllProjects();
                $projectId = $request->query('project_id');

                if (!$projectId && count($projects) > 0) {
                    $projectId = $projects[0]['id'] ?? null;
                    if ($projectId) {
                        return redirect()->route('mk.x.top-influencers', [
                            'project_id' => $projectId,
                            'start_date' => $request->query('start_date', now()->startOfMonth()->format('Y-m-d')),
                            'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                        ]);
                    }
                }

                $endDate   = $request->query('end_date', now()->format('Y-m-d'));
                $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));

                return view('mk.x.top-influencers')->with([
                    'projectId' => $projectId,
                    'startDate' => $startDate,
                    'endDate'   => $endDate,
                    'projects'  => $projects,
                ]);

            } catch (\Exception $e) {
                Log::error('Top Influencers Page Error', ['error' => $e->getMessage()]);
                return view('mk.x.top-influencers')->with([
                    'projectId' => null,
                    'startDate' => now()->startOfMonth()->format('Y-m-d'),
                    'endDate'   => now()->format('Y-m-d'),
                    'projects'  => [],
                    'error'     => $e->getMessage(),
                ]);
            }
        }

        /**
         * API: Get Top Influencers Data
         * Endpoint: GET /top_influencers/
         * Response structure: [{ author_id, total, name, info: { screen_name, followers_count, ... } }]
         */
        public function topInfluencersData(Request $request): \Illuminate\Http\JsonResponse
        {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
            $endDate   = $request->query('end_date', now()->format('Y-m-d'));
            $startTime = (int) $request->query('start_time', 0);
            $endTime   = (int) $request->query('end_time', 23);
            $sub       = $request->query('sub', 'rt'); // 'rt' = By Collected Mentions | 'rt_all' = By Total Retweets

            if (!$projectId) {
                return response()->json(['error' => 'project_id required'], 422);
            }

            try {
                $data = $this->client->topInfluencers(
                    (string) $projectId,
                    $startDate,
                    $endDate,
                    $startTime,
                    $endTime
                );

                Log::info('topInfluencersData raw', [
                    'project_id' => $projectId,
                    'sub'        => $sub,
                    'count'      => count($data),
                    'sample'     => count($data) > 0 ? array_slice($data[0], 0, 5) : [],
                ]);

                $influencers = [];

                foreach ($data as $item) {
                    if (!is_array($item)) continue;

                    // Data user ada di dalam 'info'
                    $info = $item['info'] ?? [];

                    // Screen name
                    $screenName = $info['screen_name'] ?? ltrim($item['name'] ?? '', '@');
                    if (!$screenName) continue;

                    // Display name
                    $displayName = $info['name'] ?? $item['name'] ?? ('@' . $screenName);

                    // Counts — total = RT + Reply Count dari API
                    $total    = (int) ($item['total']    ?? 0);
                    $retweets = (int) ($item['retweets'] ?? $item['rt']  ?? 0);
                    $replies  = (int) ($item['replies']  ?? $item['rep'] ?? 0);

                    // Fallback jika retweets/replies tidak tersedia
                    if ($retweets === 0 && $replies === 0 && $total > 0) {
                        $retweets = $total;
                    }

                    // Profile data dari info
                    $followers    = (int) ($info['followers_count']  ?? 0);
                    $following    = (int) ($info['friends_count']    ?? 0);
                    $statuses     = (int) ($info['statuses_count']   ?? 0);
                    $favs         = (int) ($info['favourites_count'] ?? 0);
                    $listed       = (int) ($info['listed_count']     ?? 0);
                    $profileImage = $info['profile_image_url_https'] ?? $info['profile_image_url'] ?? '';
                    $verifiedType = $info['verified_type'] ?? '';
                    $verified     = !empty($info['verified']) || $verifiedType === 'blue';

                    $influencers[] = [
                        'author_id'        => $item['author_id'] ?? '',
                        'total'            => $total,
                        'retweets'         => $retweets,
                        'replies'          => $replies,
                        'name'             => $displayName,
                        'screen_name'      => $screenName,
                        'followers_count'  => $followers,
                        'friends_count'    => $following,
                        'statuses_count'   => $statuses,
                        'favourites_count' => $favs,
                        'listed_count'     => $listed,
                        'verified'         => $verified,
                        'verified_type'    => $verifiedType,
                        'description'      => $info['description']      ?? '',
                        'location'         => $info['location']         ?? '',
                        'profile_image'    => $profileImage,
                        'profile_banner'   => $info['profile_banner_url'] ?? '',
                        'created_at'       => $info['created_at']       ?? '',
                        'profile_url'      => 'https://twitter.com/' . $screenName,
                    ];
                }

                // Sort berdasarkan tab
                if ($sub === 'rt_all') {
                    usort($influencers, fn($a, $b) => $b['retweets'] - $a['retweets']);
                } else {
                    usort($influencers, fn($a, $b) => $b['total'] - $a['total']);
                }

                return response()->json([
                    'status' => 'success',
                    'total'  => count($influencers),
                    'sub'    => $sub,
                    'data'   => $influencers,
                ]);

            } catch (\Exception $e) {
                Log::error('topInfluencersData error', [
                    'error'      => $e->getMessage(),
                    'project_id' => $projectId,
                    'sub'        => $sub,
                ]);
                return response()->json(['status' => 'error', 'data' => []], 500);
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
                return redirect()->route('mk.x.emotion-analysis', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.x.emotion-analysis')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('Emotion Analysis Page Error', ['error' => $e->getMessage()]);
        return view('mk.x.emotion-analysis')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
            'error'     => $e->getMessage(),
        ]);
    }
}
 
public function emotionAnalysisData(Request $request): \Illuminate\Http\JsonResponse
{
    $projectId = $request->query('project_id');
    $startDate = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->query('end_date', now()->format('Y-m-d'));

    if (!$projectId) {
        return response()->json(['success' => false, 'error' => 'project_id required'], 422);
    }

    // ── Proporsi emotion per sentiment bucket ────────────────────────────
    // Angka ini adalah "bobot" — akan dikalikan jumlah post per hari
    $emotionMap = [
        'positive' => [
            'joy'          => 0.50,
            'trust'        => 0.30,
            'anticipation' => 0.20,
        ],
        'negative' => [
            'anger'   => 0.40,
            'fear'    => 0.25,
            'sadness' => 0.20,
            'disgust' => 0.15,
        ],
        'neutral' => [
            'surprise'     => 0.60,
            'anticipation' => 0.40,
        ],
    ];

    try {
        // ─── 1. Fetch mentions (semua platform, filter Twitter di bawah) ──
        $rawMentions = $this->client->mentions(
            $projectId,
            $startDate,
            $endDate,
            0,      // sentiment_id: 0 = all
            23,     // end_hour
            true,   // include_content
            0,      // offset
            5000    // rows — ambil banyak supaya representatif
        );

        // Normalize response format (sama seperti extractArray di NewsController)
        if (!is_array($rawMentions)) {
            $mentions = [];
        } elseif (empty($rawMentions) || isset($rawMentions[0])) {
            $mentions = $rawMentions;
        } elseif (isset($rawMentions['data']) && is_array($rawMentions['data'])) {
            $mentions = $rawMentions['data'];
        } else {
            $mentions = $rawMentions;
        }

        Log::info('emotionAnalysis: raw mentions fetched', [
            'project_id' => $projectId,
            'total_raw'  => count($mentions),
        ]);

        // ─── 2. Filter Twitter only ───────────────────────────────────────
        $twitterMentions = array_values(array_filter($mentions, function ($item) {
            $tcode = strtolower((string) ($item['tcode']      ?? ''));
            $mt    = strtolower((string) ($item['media_type'] ?? ''));
            $mtid  = (string) ($item['media_type_id']         ?? '');
            $id    = (string) ($item['id']    ?? $item['docid'] ?? '');
            $url   = (string) ($item['url']   ?? '');

            return str_starts_with($tcode, 'tw-')
                || str_contains($tcode, 'twitter')
                || $mt   === 'tw'
                || $mt   === 'twitter'
                || $mtid === '1'
                || str_starts_with($id, 'tw-')
                || str_contains($url, 'twitter.com')
                || str_contains($url, 'x.com');
        }));

        Log::info('emotionAnalysis: twitter mentions filtered', [
            'twitter_count' => count($twitterMentions),
        ]);

        // ─── 3. Aggregate per tanggal per sentiment ───────────────────────
        // trendBySentiment['2026-02-01']['positive'] = 123
        $trendBySentiment = [];
        $sentimentTotals  = ['positive' => 0, 'negative' => 0, 'neutral' => 0];
        $processedTweets  = [];

        foreach ($twitterMentions as $item) {
            // Normalize sentiment string
            $rawSentiment = strtolower(
                $item['sentiment_str']
                ?? $item['sentiment']
                ?? ''
            );

            // Fallback ke class_sentiment / sentiment_id jika sentiment_str kosong
            if (empty($rawSentiment)) {
                $classVal = (string) ($item['class_sentiment'] ?? $item['sentiment_id'] ?? '0');
                if ($classVal === '1' || $classVal === 'positive' || $classVal === 'positif') {
                    $rawSentiment = 'positive';
                } elseif ($classVal === '-1' || $classVal === 'negative' || $classVal === 'negatif') {
                    $rawSentiment = 'negative';
                } else {
                    $rawSentiment = 'neutral';
                }
            }

            // Normalize alias
            if (str_contains($rawSentiment, 'pos')) {
                $bucket = 'positive';
            } elseif (str_contains($rawSentiment, 'neg')) {
                $bucket = 'negative';
            } else {
                $bucket = 'neutral';
            }

            $sentimentTotals[$bucket]++;

            // Tanggal untuk trend
            $dateKey = substr($item['date_created'] ?? '', 0, 10);
            if ($dateKey && preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateKey)) {
                if (!isset($trendBySentiment[$dateKey])) {
                    $trendBySentiment[$dateKey] = ['positive' => 0, 'negative' => 0, 'neutral' => 0];
                }
                $trendBySentiment[$dateKey][$bucket]++;
            }

            // Collect tweets untuk tabel (max 500)
            if (count($processedTweets) < 500) {
                $processedTweets[] = [
                    'text'        => strip_tags($item['content'] ?? ''),
                    'emotion'     => $this->_distributedEmotion($bucket),
                    'sentiment'   => $bucket,
                    'author'      => $item['author_scr_name'] ?? $item['author_id'] ?? '',
                    'author_name' => $item['author_name']     ?? $item['author_scr_name'] ?? '',
                    'timestamp'   => $item['date_created']    ?? '',
                    'likes'       => (int) ($item['num_likes']    ?? $item['freq'] ?? 0),
                    'retweets'    => (int) ($item['num_shares']   ?? $item['rt']   ?? 0),
                    'replies'     => (int) ($item['num_comments'] ?? 0),
                    'url'         => $item['url'] ?? '#',
                ];
            }
        }

        $totalPosts = array_sum($sentimentTotals);

        // ─── 4. Distribusi ke 8 emosi berdasarkan proporsi ───────────────
        $emotionCounts = [
            'joy'          => 0,
            'trust'        => 0,
            'fear'         => 0,
            'surprise'     => 0,
            'sadness'      => 0,
            'disgust'      => 0,
            'anger'        => 0,
            'anticipation' => 0,
        ];

        foreach ($emotionMap as $bucket => $proportions) {
            $bucketTotal = $sentimentTotals[$bucket];
            foreach ($proportions as $emotion => $ratio) {
                $emotionCounts[$emotion] += (int) round($bucketTotal * $ratio);
            }
        }

        // ─── 5. Build trend array (per tanggal per emosi) ─────────────────
        ksort($trendBySentiment);
        $trendArray = [];

        foreach ($trendBySentiment as $date => $buckets) {
            foreach ($emotionMap as $bucket => $proportions) {
                $bucketCount = $buckets[$bucket] ?? 0;
                foreach ($proportions as $emotion => $ratio) {
                    $count = (int) round($bucketCount * $ratio);
                    if ($count > 0) {
                        $trendArray[] = [
                            'date'    => $date,
                            'emotion' => $emotion,
                            'count'   => $count,
                        ];
                    }
                }
            }
        }

        // ─── 6. Sort tweets by engagement ─────────────────────────────────
        usort($processedTweets, function ($a, $b) {
            return ($b['likes'] + $b['retweets']) - ($a['likes'] + $a['retweets']);
        });

        // ─── 7. Emotions summary ──────────────────────────────────────────
        $emotionTotal = array_sum($emotionCounts);
        $emotions     = [];
        foreach ($emotionCounts as $emo => $count) {
            $emotions[$emo] = [
                'count' => $count,
                'pct'   => $emotionTotal > 0 ? round(($count / $emotionTotal) * 100, 1) : 0,
            ];
        }

        // ─── 8. Summary stats ─────────────────────────────────────────────
        $positiveTotal = $sentimentTotals['positive'];
        $negativeTotal = $sentimentTotals['negative'];

        $summary = [
            'total_posts'  => $totalPosts,
            'positive_pct' => $totalPosts > 0 ? round(($positiveTotal / $totalPosts) * 100, 1) : 0,
            'negative_pct' => $totalPosts > 0 ? round(($negativeTotal / $totalPosts) * 100, 1) : 0,
            'days_count'   => max(1, \Carbon\Carbon::parse($startDate)->diffInDays(\Carbon\Carbon::parse($endDate)) + 1),
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'last_updated' => \Carbon\Carbon::now('Asia/Jakarta')->format('d M Y, H:i') . ' WIB',
        ];

        Log::info('emotionAnalysis: done', [
            'project_id'      => $projectId,
            'total_posts'     => $totalPosts,
            'sentiment_dist'  => $sentimentTotals,
            'emotion_dist'    => array_map(fn($v) => $v['count'], $emotions),
        ]);

        return response()->json([
            'success' => true,
            'data'    => [
                'summary'  => $summary,
                'emotions' => $emotions,
                'trend'    => $trendArray,
                'tweets'   => $processedTweets,
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('emotionAnalysis error', [
            'error'      => $e->getMessage(),
            'project_id' => $projectId,
        ]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

private function _bucketToMainEmotion(string $bucket): string
{
    return match ($bucket) {
        'positive' => 'joy',
        'negative' => 'anger',
        default    => 'surprise',
    };
}

/**
 * Distribute emotion proportionally within a sentiment bucket.
 * Uses a counter per bucket so tweets are spread across sub-emotions
 * deterministically (round-robin weighted).
 */
private array $_emotionCounters = [];

private function _distributedEmotion(string $bucket): string
{
    $map = [
        'positive' => ['joy' => 50, 'trust' => 30, 'anticipation' => 20],
        'negative' => ['anger' => 40, 'fear' => 25, 'sadness' => 20, 'disgust' => 15],
        'neutral'  => ['surprise' => 60, 'anticipation' => 40],
    ];

    $proportions = $map[$bucket] ?? $map['neutral'];

    if (!isset($this->_emotionCounters[$bucket])) {
        $this->_emotionCounters[$bucket] = 0;
    }

    $idx   = $this->_emotionCounters[$bucket]++;
    $total = array_sum($proportions);
    $pos   = $idx % $total;

    $cumulative = 0;
    foreach ($proportions as $emotion => $weight) {
        $cumulative += $weight;
        if ($pos < $cumulative) {
            return $emotion;
        }
    }

    return array_key_first($proportions);
}
public function mostEngagementPage(Request $request)
{
    try {
        $projects  = $this->getAllProjects();
        $projectId = $request->query('project_id');

        if (!$projectId && count($projects) > 0) {
            $projectId = $projects[0]['id'] ?? null;
            if ($projectId) {
                return redirect()->route('mk.x.most-engagement', [
                    'project_id' => $projectId,
                    'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                    'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                ]);
            }
        }

        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));

        return view('mk.x.most-engagement')->with([
            'projectId' => $projectId,
            'startDate' => $startDate,
            'endDate'   => $endDate,
            'projects'  => $projects,
        ]);

    } catch (\Exception $e) {
        Log::error('mostEngagementPage error', ['error' => $e->getMessage()]);
        return view('mk.x.most-engagement')->with([
            'projectId' => null,
            'startDate' => now()->subDays(6)->format('Y-m-d'),
            'endDate'   => now()->format('Y-m-d'),
            'projects'  => [],
        ]);
    }
}

// ─────────────────────────────────────────────────────────────────────
// METHOD 2: API data endpoint — robust, tries multiple param combos
// Route: GET /mk/api/x/most-engagement-data
// ─────────────────────────────────────────────────────────────────────
public function mostEngagementData(Request $request)
{
    try {
        $projectId = $request->query('project_id');
        $startDate = $request->query('start_date', now()->subDays(6)->format('Y-m-d'));
        $endDate   = $request->query('end_date', now()->format('Y-m-d'));
        $rows      = (int) $request->query('rows', 100);

        if (!$projectId) {
            return response()->json(['success' => false, 'error' => 'project_id required'], 400);
        }

        $allPosts = [];
        $seenIds  = [];

        // Coba semua kombinasi media × sub sampai dapat data
        $mediaOptions = ['twitter', 'twit', 'all'];
        $subOptions   = ['postbyview', 'postbyrt', 'postbyfav', 'postbyreply'];

        foreach ($mediaOptions as $media) {
            $gotData = false;
            foreach ($subOptions as $sub) {
                try {
                    $result = $this->client->mostStatus(
                        $projectId, $media, $startDate, $endDate,
                        0, 23, $rows, $sub
                    );

                    Log::info("mostEngagementData", [
                        'media' => $media, 'sub' => $sub,
                        'count' => is_array($result) ? count($result) : 0
                    ]);

                    if (!empty($result) && is_array($result)) {
                        foreach ($result as $item) {
                            // Buat unique ID dari post
                            $uid = $item['sub_id']
                                ?? $item['id']
                                ?? md5(($item['content'] ?? '') . ($item['name'] ?? ''));

                            if (!isset($seenIds[$uid])) {
                                $seenIds[$uid] = count($allPosts);
                                $allPosts[]    = $item;
                            } else {
                                // Merge metric dari sub type lain agar tidak hilang
                                $idx = $seenIds[$uid];
                                $metricKeys = [
                                    'view_cnt', 'views', 'freq',
                                    'rt', 'retweets', 'rt_count',
                                    'fav_count', 'likes', 'fav',
                                    'reply_cnt', 'replies', 'reply_count',
                                ];
                                foreach ($metricKeys as $mk) {
                                    if (isset($item[$mk]) && (int) $item[$mk] > (int) ($allPosts[$idx][$mk] ?? 0)) {
                                        $allPosts[$idx][$mk] = $item[$mk];
                                    }
                                }
                            }
                        }
                        $gotData = true;
                    }
                } catch (\Exception $e) {
                    Log::warning("mostEngagementData failed", [
                        'media' => $media, 'sub' => $sub,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            // Kalau sudah dapat data dari media ini, tidak perlu coba media lain
            if ($gotData && count($allPosts) >= 10) {
                break;
            }
        }

        // Fallback: pakai mostRetweets kalau masih kosong
        if (empty($allPosts)) {
            try {
                $rtResult = $this->client->mostRetweets($projectId, $startDate, $endDate);
                Log::info("mostEngagementData fallback mostRetweets", ['count' => count($rtResult ?? [])]);
                if (!empty($rtResult) && is_array($rtResult)) {
                    $allPosts = $rtResult;
                }
            } catch (\Exception $e) {
                Log::warning("mostEngagementData fallback failed", ['error' => $e->getMessage()]);
            }
        }

        // Normalize semua field supaya konsisten di frontend
        $posts = array_map(function ($item) {
            $authorImg = $item['avatar_url']
                ?? ($item['author']['image'] ?? '');

            // Hapus _normal. di URL avatar supaya dapat foto full size
            $authorImg = str_replace('_normal.', '.', $authorImg ?? '');

            return [
                'id'            => $item['id']       ?? '',
                'sub_id'        => $item['sub_id']   ?? '',
                'content'       => $item['content']  ?? '',
                'date_created'  => $item['date_created'] ?? '',
                'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',

                // Engagement metrics — fallback chain
                'view_cnt'  => (int) ($item['view_cnt']  ?? $item['views']    ?? $item['freq'] ?? 0),
                'rt'        => (int) ($item['rt']        ?? $item['retweets'] ?? $item['rt_count'] ?? 0),
                'fav_count' => (int) ($item['fav_count'] ?? $item['likes']    ?? $item['fav'] ?? 0),
                'reply_cnt' => (int) ($item['reply_cnt'] ?? $item['replies']  ?? $item['reply_count'] ?? 0),

                // Author info
                'avatar_url' => $authorImg,
                'author'     => [
                    'name'     => $item['author']['name']     ?? ($item['name'] ?? ''),
                    'scr_name' => $item['author']['scr_name'] ?? ($item['name'] ?? ''),
                    'image'    => $item['author']['image']    ?? $authorImg,
                    'flw_cnt'  => (int) ($item['author']['flw_cnt'] ?? 0),
                ],
            ];
        }, $allPosts);

        Log::info('mostEngagementData final', [
            'project_id' => $projectId,
            'total'      => count($posts),
        ]);

        return response()->json([
            'success' => true,
            'data'    => array_values($posts),
            'total'   => count($posts),
        ]);

    } catch (\Exception $e) {
        Log::error('mostEngagementData error', [
            'error'      => $e->getMessage(),
            'project_id' => $request->query('project_id'),
        ]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
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

        // ── Panggil semua data dengan filter Twitter ──
        $postsRaw     = $this->client->mostStatus($projectId, 'twitter', $startDate, $endDate, 0, 23, 50, 'postbyview');
        $retweetsRaw  = $this->client->mostRetweets($projectId, $startDate, $endDate);
        $hashtagsRaw  = $this->client->topHashtags($projectId, 'twit', $startDate, $endDate);
        $sentimentRaw = $this->client->sentimentTotal($projectId, $startDate, $endDate);
        $activeRaw    = $this->client->mostActiveUsers($projectId, $startDate, $endDate);
        $volumeRaw    = $this->client->volumeTotal($projectId, 'twitter', $startDate, $endDate);

        // ── Parse volume ──
        $volume = 0;
        if (isset($volumeRaw['all']['total'])) {
            $volume = (int) $volumeRaw['all']['total'];
        } elseif (isset($volumeRaw['bymedia']['twit'])) {
            $volume = (int) $volumeRaw['bymedia']['twit'];
        }

        // ── Parse sentiment ──
        $positive = 0; $negative = 0; $neutral = 0;
        if (isset($sentimentRaw['pos'], $sentimentRaw['neg'], $sentimentRaw['net'])) {
            $positive = (int) $sentimentRaw['pos'];
            $negative = (int) $sentimentRaw['neg'];
            $neutral  = (int) $sentimentRaw['net'];
        } elseif (isset($sentimentRaw['bymedia']['twit'])) {
            $d        = $sentimentRaw['bymedia']['twit'];
            $positive = (int) ($d['pos'] ?? 0);
            $negative = (int) ($d['neg'] ?? 0);
            $neutral  = (int) ($d['net'] ?? 0);
        }

        // ── Parse hashtags (filter twit only) ──
        $hashtags = [];
        $rawItems = $hashtagsRaw['data']['hashtags'] ?? $hashtagsRaw['data'] ?? $hashtagsRaw['twit'] ?? $hashtagsRaw ?? [];
        foreach ($rawItems as $item) {
            if (!is_array($item)) continue;
            $name  = $item['name'] ?? $item['hashtag'] ?? '';
            $size  = (int) ($item['size'] ?? $item['count'] ?? 0);
            $media = strtolower($item['media'] ?? $item['source'] ?? '');
            if ($media && !in_array($media, ['twit', 'twitter', 'x', ''])) continue;
            if ($name && $size > 0) {
                $hashtags[] = ['name' => ltrim($name, '#'), 'size' => $size];
            }
        }
        usort($hashtags, fn($a, $b) => $b['size'] - $a['size']);

        // ── Parse most active users (filter Twitter only) ──
        $activeUsers = [];
        if (isset($activeRaw['data']['data']) && is_array($activeRaw['data']['data'])) {
            foreach ($activeRaw['data']['data'] as $user) {
                // Filter Twitter only
                $tcode = strtolower($user['tcode'] ?? $user['media'] ?? '');
                if ($tcode && !str_starts_with($tcode, 'tw-') && !in_array($tcode, ['twit', 'twitter'])) {
                    continue;
                }

                $screenName = $user['contentJson']['screen_name'] ?? '';
                if (!$screenName) {
                    preg_match('/@(\w+)/', $user['name'] ?? '', $m);
                    $screenName = $m[1] ?? '';
                }

                if ($screenName) {
                    $activeUsers[] = [
                        'username'  => $screenName,
                        'mentions'  => (int) ($user['mentions']  ?? 0),
                        'replies'   => (int) ($user['replies']   ?? 0),
                        'retweets'  => (int) ($user['retweets']  ?? 0),
                        'followers' => (int) ($user['followers'] ?? $user['contentJson']['followers_count'] ?? 0),
                    ];
                }
            }
        }

        // ── Parse most viewed posts (filter Twitter only) ──
        $posts    = [];
        $rawPosts = is_array($postsRaw) ? $postsRaw : ($postsRaw['data'] ?? []);
        foreach ($rawPosts as $item) {
            if (!is_array($item)) continue;
            $tcode = strtolower($item['tcode'] ?? $item['media'] ?? '');
            if ($tcode && !str_starts_with($tcode, 'tw-') && !in_array($tcode, ['twit', 'twitter'])) {
                continue;
            }
            $author  = $item['author']['scr_name'] ?? $item['name'] ?? 'unknown';
            $content = $item['content'] ?? '';
            $posts[] = [
                'name'          => $author,
                'content'       => substr(strip_tags($content), 0, 150),
                'view_cnt'      => (int) ($item['view_cnt'] ?? $item['freq'] ?? 0),
                'rt'            => (int) ($item['rt'] ?? 0),
                'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',
                'date_created'  => substr($item['date_created'] ?? '', 0, 10),
            ];
        }

        // ── Parse most retweeted (filter Twitter only) ──
        $retweets = [];
        $rawRt    = is_array($retweetsRaw) ? $retweetsRaw : ($retweetsRaw['data'] ?? []);
        foreach ($rawRt as $item) {
            if (!is_array($item)) continue;
            $tcode = strtolower($item['tcode'] ?? $item['media'] ?? '');
            if ($tcode && !str_starts_with($tcode, 'tw-') && !in_array($tcode, ['twit', 'twitter'])) {
                continue;
            }
            $author     = $item['author']['scr_name'] ?? $item['name'] ?? 'unknown';
            $content    = $item['content'] ?? '';
            $retweets[] = [
                'name'          => $author,
                'content'       => substr(strip_tags($content), 0, 150),
                'freq'          => (int) ($item['freq'] ?? $item['rt'] ?? 0),
                'sentiment_str' => $item['sentiment_str'] ?? 'Neutral',
                'date_created'  => substr($item['date_created'] ?? '', 0, 10),
            ];
        }

        // ── Build dataset string untuk AI ──
        $total = $positive + $negative + $neutral ?: 1;
        $lines = [];
        $lines[] = "=== DATA X (TWITTER) PROJECT {$projectId} ===";
        $lines[] = "Periode: {$startDate} s/d {$endDate}";
        $lines[] = "Total Volume: {$volume} posts";
        $lines[] = "Sentimen: Positif " . round($positive / $total * 100) . "% ({$positive}) | Negatif " . round($negative / $total * 100) . "% ({$negative}) | Netral " . round($neutral / $total * 100) . "% ({$neutral})";

        if (!empty($hashtags)) {
            $lines[] = "\n--- TOP HASHTAGS ---";
            foreach (array_slice($hashtags, 0, 20) as $i => $h) {
                $lines[] = ($i + 1) . ". #{$h['name']} ({$h['size']} mentions)";
            }
        }

        if (!empty($activeUsers)) {
            $lines[] = "\n--- MOST ACTIVE USERS ---";
            foreach (array_slice($activeUsers, 0, 10) as $i => $u) {
                $lines[] = ($i + 1) . ". @{$u['username']} | Mentions:{$u['mentions']} Replies:{$u['replies']} RT:{$u['retweets']} Followers:{$u['followers']}";
            }
        }

        if (!empty($retweets)) {
            $lines[] = "\n--- MOST RETWEETED POSTS (" . count($retweets) . " posts) ---";
            foreach (array_slice($retweets, 0, 20) as $i => $post) {
                $lines[] = "[RT" . ($i + 1) . "] @{$post['name']} ({$post['freq']} RT) | {$post['date_created']} | {$post['sentiment_str']}";
                if ($post['content']) $lines[] = "   \"{$post['content']}\"";
            }
        }

        if (!empty($posts)) {
            $lines[] = "\n--- MOST VIEWED POSTS (" . count($posts) . " posts) ---";
            foreach (array_slice($posts, 0, 20) as $i => $post) {
                $lines[] = "[P" . ($i + 1) . "] @{$post['name']} ({$post['view_cnt']} views, {$post['rt']} RT) | {$post['date_created']} | {$post['sentiment_str']}";
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
                    'total_retweets' => count($retweets),
                    'total_hashtags' => count($hashtags),
                    'total_users'    => count($activeUsers),
                    'volume'         => $volume,
                    'sentiment'      => ['positive' => $positive, 'negative' => $negative, 'neutral' => $neutral],
                ],
            ],
        ]);

    } catch (\Exception $e) {
        Log::error('X aiAnalysisData error', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
    }
}

    // ==========================================
    // AI ANALYSIS PROXY (Gemini)
    // ==========================================

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
                        Log::info("✅ Gemini OK (X AI)", ['model' => $model]);
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
            Log::error('X AI Proxy Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    }