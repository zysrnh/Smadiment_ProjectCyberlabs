<?php

    namespace App\Http\Controllers;

    use App\Models\ProjectDailySentiment;
    use App\Services\MediaKernelsClient;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Http;
    use Illuminate\Support\Facades\Log;

    class MkController extends Controller
    {
        /**
         * Helper: Normalize sentiment data
         */
    private function normalizeSentimentTotal(array $raw): array
    {
        $src = $raw['data'] ?? $raw;

        return [
            'positive' => (int) ($src['positive'] ?? $src['pos'] ?? $src['1'] ?? 0),
            'neutral'  => (int) ($src['neutral']  ?? $src['neu'] ?? $src['net'] ?? $src['0'] ?? 0), // ← tambah 'net'
            'negative' => (int) ($src['negative'] ?? $src['neg'] ?? $src[   '-1'] ?? 0),
        ];
    }

        /**
         * Helper: Normalize chart data (Age/Gender/Type)
         */
        private function normalizeChartData(array $raw, string $labelKey = 'age_group', string $valueKey = 'post_freq'): array
        {
            $data = $raw['data'] ?? $raw;

            if (empty($data) || !is_array($data)) {
                return ['labels' => [], 'values' => []];
            }

            $labels = [];
            $values = [];

            foreach ($data as $item) {
                if (is_array($item)) {
                    $labels[] = $item[$labelKey] ?? 'Unknown';
                    $values[] = (int) ($item[$valueKey] ?? 0);
                }
            }

            return ['labels' => $labels, 'values' => $values];
        }

        /**
         * Helper: Normalize geo data
         */
        private function normalizeGeoRows(array $raw): array
        {
            $src  = $raw['data'] ?? $raw;
            $rows = [];

            foreach ($src as $k => $v) {
                if (is_numeric($v)) {
                    $rows[] = ['name' => (string) $k, 'count' => (int) $v];
                } elseif (is_array($v)) {
                    $rows[] = [
                        'name'  => $v['name'] ?? $k,
                        'count' => (int) ($v['count'] ?? $v['total'] ?? 0),
                    ];
                }
            }

            usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
            return array_slice($rows, 0, 10);
        }

        /**
         * Helper: Get common parameters from request
         */
        private function getParams(Request $request): array
        {
            return [
                'startDate' => $request->query('start_date', now()->startOfMonth()->toDateString()),
                'endDate'   => $request->query('end_date', now()->toDateString()),
                'startTime' => (int) $request->query('start_time', 0),
                'endTime'   => (int) $request->query('end_time', 23),
                'media'     => $request->query('media', 'twit'),
                'sentiment' => (int) $request->query('sentiment', 1),
            ];
        }

        /**
         * Helper: Extract total from stats response
         */
        private function extractTotal(array $stats): int
        {
            if (isset($stats['data']['total'])) {
                return (int) $stats['data']['total'];
            }

            if (isset($stats['total'])) {
                return (int) $stats['total'];
            }

            if (isset($stats['data']) && is_array($stats['data'])) {
                return array_sum(array_map(fn($v) => is_numeric($v) ? (int) $v : 0, $stats['data']));
            }

            return 0;
        }

        /**
         * Helper: Get FILTERED projects based on user assignment
         */
        private function getProjects(MediaKernelsClient $mk): array
        {
            $user = Auth::user();

            $assignedProjectIds = $user->assignedProjectIds();

            Log::info('🔍 User assigned projects', [
                'user_id'      => $user->id,
                'assigned_ids' => $assignedProjectIds,
            ]);

            $rawProjects = $mk->listProjects(0, 100);
            $allProjects = array_values($rawProjects);

            $userProjects = array_filter($allProjects, function ($project) use ($assignedProjectIds) {
                return in_array($project['id'] ?? null, $assignedProjectIds);
            });

            $filteredProjects = array_values($userProjects);

            Log::info('✅ Filtered projects', [
                'total_projects' => count($allProjects),
                'user_projects'  => count($filteredProjects),
                'project_ids'    => array_column($filteredProjects, 'id'),
            ]);

            return $filteredProjects;
        }

        /**
         * Helper: Verify user has access to project
         */
        private function userHasAccessToProject(int $projectId): bool
        {
            return Auth::user()->hasAccessToProject($projectId);
        }

        /**
         * Helper: Extract Daily Timeline with Sentiment Breakdown (7 days INCLUDING TODAY)
         * Dipakai oleh adminDashboard — hardcode 7 hari terakhir
         */
        private function extractDailyTimeline($projectId, MediaKernelsClient $mk): array
        {
            $timeline = [
                'dates'     => [],
                'values'    => [],
                'sentiment' => [
                    'positive' => [],
                    'neutral'  => [],
                    'negative' => [],
                ],
            ];

            try {
                $days = [];
                for ($i = 6; $i >= 0; $i--) {
                    $date    = now()->subDays($i);
                    $dateStr = $date->format('Y-m-d');
                    $dateLabel = $date->format('d') . '. ' . $date->format('M');
                    $days[] = [
                        'label' => $dateLabel,
                        'date'  => $dateStr,
                        'cache_key' => "sent_{$projectId}_{$dateStr}_{$dateStr}"
                    ];
                }

                $missingDays = [];
                foreach ($days as $day) {
                    if (!\Illuminate\Support\Facades\Cache::has($day['cache_key'])) {
                        $missingDays[] = $day;
                    }
                }

                if (!empty($missingDays)) {
                    $token = $mk->getToken();
                    $baseUrl = rtrim(config('services.mediakernels.base_url'), '/');

                    $urls = [];
                    foreach ($missingDays as $idx => $md) {
                        $urls[$idx] = $baseUrl . '/sentiment_total/?' . http_build_query([
                            'project_id' => $projectId,
                            'start_date' => $md['date'],
                            'start_time' => 0,
                            'end_date'   => $md['date'],
                            'end_time'   => 23,
                            'token'      => $token,
                        ]);
                    }

                    $responses = \Illuminate\Support\Facades\Http::pool(function ($pool) use ($urls) {
                        foreach ($urls as $idx => $url) {
                            $pool->as($idx)->timeout(30)->acceptJson()->get($url);
                        }
                    });

                    foreach ($missingDays as $idx => $md) {
                        $res = $responses[$idx] ?? null;
                        $sentimentData = ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) ? $res->json() : [];
                        $normalized = $this->normalizeSentimentTotal(is_array($sentimentData) ? $sentimentData : []);
                        \Illuminate\Support\Facades\Cache::put($md['cache_key'], $normalized, 600);
                    }
                }

                foreach ($days as $day) {
                    $normalized = \Illuminate\Support\Facades\Cache::get($day['cache_key'], [
                        'positive' => 0,
                        'neutral'  => 0,
                        'negative' => 0,
                    ]);

                    $pos   = $normalized['positive'];
                    $neu   = $normalized['neutral'];
                    $neg   = $normalized['negative'];
                    $total = $pos + $neu + $neg;

                    $timeline['dates'][]                 = $day['label'];
                    $timeline['values'][]                = $total;
                    $timeline['sentiment']['positive'][] = $pos;
                    $timeline['sentiment']['neutral'][]  = $neu;
                    $timeline['sentiment']['negative'][] = $neg;
                }

            } catch (\Exception $e) {
                Log::warning("Failed to fetch daily timeline for project {$projectId}", [
                    'error' => $e->getMessage(),
                ]);
            }

            return $timeline;
        }

        /**
         * Helper: Extract Timeline by date range (untuk user dashboard — sinkron dengan datepicker)
         * - Membaca langsung dari DB lokal (ProjectDailySentiment)
         * - Otomatis auto-sync jika ada tanggal yang belum ada di DB
         */
        private function extractTimelineByRange($projectId, string $startDate, string $endDate, MediaKernelsClient $mk): array
        {
            $timeline = [
                'dates'       => [],
                'dates_start' => [],
                'dates_end'   => [],
                'values'      => [],
                'sentiment'   => [
                    'positive' => [],
                    'neutral'  => [],
                    'negative' => [],
                ],
            ];

            try {
                $start = new \DateTime($startDate);
                $end   = new \DateTime($endDate);
                
                // 1. Generate semua tanggal dalam rentang
                $allDates = [];
                $current  = clone $start;
                while ($current <= $end) {
                    $allDates[] = $current->format('Y-m-d');
                    $current->modify('+1 day');
                }

                // 2. Query dari database lokal
                $existing = ProjectDailySentiment::where('project_id', $projectId)
                    ->whereBetween('date', [$startDate, $endDate])
                    ->get()
                    ->keyBy(fn($item) => $item->date->format('Y-m-d'));

                // 3. Cari tanggal yang belum ada di DB lokal
                $missingDates = [];
                foreach ($allDates as $dStr) {
                    if (!$existing->has($dStr)) {
                        $missingDates[] = $dStr;
                    }
                }

                // 4. Jika ada tanggal yang belum tersimpan di DB, lakukan silent fetch & upsert
                if (!empty($missingDates)) {
                    try {
                        $token   = $mk->getToken();
                        $baseUrl = rtrim(config('services.mediakernels.base_url'), '/');
                        
                        $urls = [];
                        foreach ($missingDates as $dStr) {
                            $urls[$dStr] = $baseUrl . '/sentiment_total/?' . http_build_query([
                                'project_id' => $projectId,
                                'start_date' => $dStr,
                                'start_time' => 0,
                                'end_date'   => $dStr,
                                'end_time'   => 23,
                                'token'      => $token,
                            ]);
                        }

                        $responses = Http::pool(function ($pool) use ($urls) {
                            foreach ($urls as $dStr => $url) {
                                $pool->as($dStr)->timeout(30)->acceptJson()->get($url);
                            }
                        });

                        $upsertData = [];
                        foreach ($missingDates as $dStr) {
                            $res = $responses[$dStr] ?? null;
                            $pos = 0; $neu = 0; $neg = 0;

                            if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                                $norm = $this->normalizeSentimentTotal($res->json() ?? []);
                                $pos  = $norm['positive'];
                                $neu  = $norm['neutral'];
                                $neg  = $norm['negative'];
                            }

                            $upsertData[] = [
                                'project_id' => $projectId,
                                'date'       => $dStr,
                                'positive'   => $pos,
                                'neutral'    => $neu,
                                'negative'   => $neg,
                                'total'      => $pos + $neu + $neg,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ];
                        }

                        if (!empty($upsertData)) {
                            ProjectDailySentiment::upsert(
                                $upsertData,
                                ['project_id', 'date'],
                                ['positive', 'neutral', 'negative', 'total', 'updated_at']
                            );
                        }

                        // Reload data terbaru dari DB
                        $existing = ProjectDailySentiment::where('project_id', $projectId)
                            ->whereBetween('date', [$startDate, $endDate])
                            ->get()
                            ->keyBy(fn($item) => $item->date->format('Y-m-d'));

                    } catch (\Throwable $e) {
                        Log::warning("Auto-sync missing dates failed for project {$projectId}: " . $e->getMessage());
                    }
                }

                // 5. Susun timeline dari DB
                $diff = (int) $start->diff($end)->days;
                if ($diff > 60) {
                    // Kelompokkan per minggu jika rentang > 60 hari
                    $current = clone $start;
                    while ($current <= $end) {
                        $weekEnd = clone $current;
                        $weekEnd->modify('+6 days');
                        if ($weekEnd > $end) {
                            $weekEnd = clone $end;
                        }

                        $wStartStr = $current->format('Y-m-d');
                        $wEndStr   = $weekEnd->format('Y-m-d');
                        $dateLabel = $current->format('d') . ' ' . $current->format('M');

                        $weekRecords = $existing->filter(function ($item, $key) use ($wStartStr, $wEndStr) {
                            return $key >= $wStartStr && $key <= $wEndStr;
                        });

                        $pos   = $weekRecords->sum('positive');
                        $neu   = $weekRecords->sum('neutral');
                        $neg   = $weekRecords->sum('negative');
                        $total = $pos + $neu + $neg;

                        $timeline['dates'][]                 = $dateLabel;
                        $timeline['dates_start'][]           = $wStartStr;
                        $timeline['dates_end'][]             = $wEndStr;
                        $timeline['values'][]                = $total;
                        $timeline['sentiment']['positive'][] = $pos;
                        $timeline['sentiment']['neutral'][]  = $neu;
                        $timeline['sentiment']['negative'][] = $neg;

                        $current->modify('+7 days');
                    }
                } else {
                    // Per hari langsung dari DB
                    foreach ($allDates as $dStr) {
                        $record = $existing->get($dStr);
                        $pos    = $record ? $record->positive : 0;
                        $neu    = $record ? $record->neutral  : 0;
                        $neg    = $record ? $record->negative : 0;
                        $total  = $pos + $neu + $neg;
                        $dt     = new \DateTime($dStr);

                        $timeline['dates'][]                 = $dt->format('d') . ' ' . $dt->format('M');
                        $timeline['dates_start'][]           = $dStr;
                        $timeline['dates_end'][]             = $dStr;
                        $timeline['values'][]                = $total;
                        $timeline['sentiment']['positive'][] = $pos;
                        $timeline['sentiment']['neutral'][]  = $neu;
                        $timeline['sentiment']['negative'][] = $neg;
                    }
                }

            } catch (\Throwable $e) {
                Log::warning("Failed to extract timeline from DB for project {$projectId}: " . $e->getMessage());
            }

            return $timeline;
        }

        // ══════════════════════════════════════════════════════════════
        // 📊 DASHBOARD (User - Filtered by assigned projects)
        // ══════════════════════════════════════════════════════════════
        public function dashboard(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
            $endDate   = $request->query('end_date',   now()->toDateString());

            $startObj = new \DateTime($startDate);
            $endObj   = new \DateTime($endDate);
            $expectedDays = (int) $startObj->diff($endObj)->days + 1;

            foreach ($projects as &$project) {
                try {
                    $pId = $project['id'];

                    // 1. Ambil agregat langsung dari database lokal
                    $stats = ProjectDailySentiment::where('project_id', $pId)
                        ->whereBetween('date', [$startDate, $endDate])
                        ->selectRaw('SUM(positive) as pos, SUM(neutral) as neu, SUM(negative) as neg, SUM(total) as tot, COUNT(id) as count')
                        ->first();

                    // 2. Jika di DB lokal datanya belum lengkap (kurang dari jumlah hari rentang), trigger auto-sync
                    if (!$stats || (int) $stats->count < $expectedDays) {
                        $this->extractTimelineByRange($pId, $startDate, $endDate, $mk);

                        $stats = ProjectDailySentiment::where('project_id', $pId)
                            ->whereBetween('date', [$startDate, $endDate])
                            ->selectRaw('SUM(positive) as pos, SUM(neutral) as neu, SUM(negative) as neg, SUM(total) as tot')
                            ->first();
                    }

                    $pos = (int) ($stats->pos ?? 0);
                    $neu = (int) ($stats->neu ?? 0);
                    $neg = (int) ($stats->neg ?? 0);
                    $tot = (int) ($stats->tot ?? ($pos + $neu + $neg));

                    $project['total_mentions']    = $tot;
                    $project['sentiment_summary'] = [
                        'positive' => $pos,
                        'neutral'  => $neu,
                        'negative' => $neg,
                    ];

                } catch (\Throwable $e) {
                    Log::warning("Dashboard: failed sentiment for project {$project['id']}", [
                        'error' => $e->getMessage(),
                    ]);
                    $project['total_mentions']    = 0;
                    $project['sentiment_summary'] = ['positive' => 0, 'neutral' => 0, 'negative' => 0];
                }
            }
            unset($project);
            Log::info('📊 Dashboard (fast with parallel pooling) loaded', [
                'user_id'        => Auth::id(),
                'projects_count' => count($projects),
                'start_date'     => $startDate,
                'end_date'       => $endDate,
            ]);
        
            return view('mk.dashboard', [
                'projects'  => $projects,
                'startDate' => $startDate,
                'endDate'   => $endDate,
            ]);
        }
    
        // ══════════════════════════════════════════════════════════════
        // 👨‍💼 ADMIN DASHBOARD
        // ══════════════════════════════════════════════════════════════
        public function adminDashboard(Request $request, MediaKernelsClient $mk)
        {
            $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
            $endDate   = $request->query('end_date',   now()->toDateString());

            $dateRange = [
                'start' => $startDate,
                'end'   => $endDate,
            ];

            $cacheKey = "admin_dashboard_projects_{$startDate}_{$endDate}";

            $projects = Cache::remember($cacheKey, now()->addMinutes(15), function () use ($mk, $dateRange) {
                try {
                    $rawProjects = $mk->listProjects(0, 100);
                    $projects    = array_values($rawProjects);
                } catch (\Throwable $e) {
                    Log::warning("Failed to list projects for admin dashboard: " . $e->getMessage());
                    $projects = [];
                }

                foreach ($projects as &$project) {
                    try {
                        // ── 1. ALL count: pakai sentimentTotal ──────────────────────
                        $sentimentData = $mk->sentimentTotal(
                            $project['id'],
                            $dateRange['start'],
                            $dateRange['end'],
                            0,
                            23
                        );
                        $norm     = $this->normalizeSentimentTotal($sentimentData);
                        $allCount = $norm['positive'] + $norm['neutral'] + $norm['negative'];

                        // ── 2. Per-platform: coba projectStats, fallback 0 ──────────
                        $platformStats = [];
                        $platforms = [
                            'news'   => 'onlinenews',
                            'twit'   => 'twit',
                            'fb'     => 'fb',
                            'ig'     => 'ig',
                            'yt'     => 'yt',
                            'tiktok' => 'tiktok',
                        ];

                        foreach ($platforms as $key => $apiParam) {
                            try {
                                $stat = $mk->projectStats(
                                    $project['id'],
                                    $apiParam,
                                    $dateRange['start'],
                                    $dateRange['end'],
                                    0,
                                    23,
                                    'volumetotal'
                                );
                                $platformStats[$key] = $this->extractTotal($stat);
                            } catch (\Throwable $e) {
                                $platformStats[$key] = 0;
                            }
                        }

                        // ── 3. Jika semua platform 0, estimasi dari sentimentTotal ──
                        $platformSum = array_sum($platformStats);
                        if ($platformSum === 0 && $allCount > 0) {
                            $platformStats['news']   = (int) round($allCount * 0.15);
                            $platformStats['twit']   = (int) round($allCount * 0.45);
                            $platformStats['fb']     = (int) round($allCount * 0.15);
                            $platformStats['ig']     = (int) round($allCount * 0.10);
                            $platformStats['yt']     = (int) round($allCount * 0.10);
                            $platformStats['tiktok'] = (int) round($allCount * 0.05);
                        }

                        $project['stats'] = array_merge(['all' => $allCount], $platformStats);

                        // ── 4. Timeline 7 hari terakhir ─────────────────────────────
                        $project['timeline'] = $this->extractDailyTimeline($project['id'], $mk);

                    } catch (\Throwable $e) {
                        Log::warning("❌ Failed to fetch stats for project {$project['id']}", [
                            'error' => $e->getMessage(),
                        ]);

                        $project['stats'] = [
                            'all'    => 0,
                            'news'   => 0,
                            'twit'   => 0,
                            'fb'     => 0,
                            'ig'     => 0,
                            'yt'     => 0,
                            'tiktok' => 0,
                        ];
                        $project['timeline'] = [
                            'dates'     => [],
                            'values'    => [],
                            'sentiment' => ['positive' => [], 'neutral' => [], 'negative' => []],
                        ];
                    }
                }
                unset($project);

                return $projects;
            });

            return view('admin.dashboard', [
                'projects'  => $projects,
                'dateRange' => $dateRange,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 📁 PROJECTS LIST
        // ══════════════════════════════════════════════════════════════
        public function projects(Request $request, MediaKernelsClient $mk)
        {
            $projects = $this->getProjects($mk);

            return view('mk.projects', [
                'projects' => $projects,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 💬 SENTIMENT ANALYSIS
        // ══════════════════════════════════════════════════════════════
        public function sentiment(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData       = [];
            $sentimentData = ['positive' => 0, 'neutral' => 0, 'negative' => 0];

            if ($projectId) {
                $rawData       = $mk->sentimentTotal($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
                $sentimentData = $this->normalizeSentimentTotal($rawData);
            }

            return view('mk.sentiment', [
                'projects'      => $projects,
                'projectId'     => $projectId,
                'params'        => $params,
                'rawData'       => $rawData,
                'sentimentData' => $sentimentData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 🌍 GEOGRAPHIC DATA
        // ══════════════════════════════════════════════════════════════
        public function geographic(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $geoRawData     = [];
            $geoRows        = [];
            $geoUserRawData = [];
            $geoUserRows    = [];

            if ($projectId) {
                $geoRawData  = $mk->geoTwitterUserSentiment($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime'], $params['sentiment']);
                $geoRows     = $this->normalizeGeoRows($geoRawData);

                $geoUserRawData = $mk->geoTwitterUser($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
                $geoUserRows    = $this->normalizeGeoRows($geoUserRawData);
            }

            return view('mk.geographic', [
                'projects'       => $projects,
                'projectId'      => $projectId,
                'params'         => $params,
                'geoRawData'     => $geoRawData,
                'geoRows'        => $geoRows,
                'geoUserRawData' => $geoUserRawData,
                'geoUserRows'    => $geoUserRows,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 👥 AUTHORS - AGE DISTRIBUTION
        // ══════════════════════════════════════════════════════════════
        public function authorsAge(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $chartData = ['labels' => [], 'values' => []];

            if ($projectId) {
                $rawData   = $mk->authorsAge($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
                $chartData = $this->normalizeChartData($rawData, 'age_group', 'post_freq');
            }

            return view('mk.authors.age', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'chartData' => $chartData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 👥 AUTHORS - GENDER DISTRIBUTION
        // ══════════════════════════════════════════════════════════════
        public function authorsGender(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $chartData = ['labels' => [], 'values' => []];

            if ($projectId) {
                $rawData   = $mk->authorsGender($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
                $chartData = $this->normalizeChartData($rawData, 'gender', 'post_freq');
            }

            return view('mk.authors.gender', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'chartData' => $chartData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 👥 AUTHORS - ORGANIZATION TYPE
        // ══════════════════════════════════════════════════════════════
        public function authorsType(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $chartData = ['labels' => [], 'values' => []];

            if ($projectId) {
                $rawData   = $mk->authorsType($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
                $chartData = $this->normalizeChartData($rawData, 'is_organization', 'post_freq');
            }

            return view('mk.authors.type', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'chartData' => $chartData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 🏷️ CATEGORIES
        // ══════════════════════════════════════════════════════════════
        public function categories(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData = [];

            if ($projectId) {
                $rawData = $mk->categories($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);
            }

            return view('mk.categories', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 📈 ENGAGEMENT - ESTIMATED REACH
        // ══════════════════════════════════════════════════════════════
        public function reach(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $chartData = ['labels' => [], 'values' => []];

            if ($projectId) {
                $rawData = $mk->estReach($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime'], 'all');

                $data = $rawData['data'] ?? $rawData;
                if (!empty($data) && is_array($data)) {
                    $labels = [];
                    $values = [];

                    foreach ($data as $key => $item) {
                        if (is_array($item)) {
                            $labels[] = $key;
                            $values[] = (int) ($item['reach'] ?? $item['est_reach'] ?? $item['value'] ?? 0);
                        }
                    }

                    $chartData = ['labels' => $labels, 'values' => $values];
                }
            }

            return view('mk.engagement.reach', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'chartData' => $chartData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 📈 ENGAGEMENT - SHARED URLs
        // ══════════════════════════════════════════════════════════════
        public function sharedUrls(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $tableData = [];

            if ($projectId) {
                $rawData = $mk->sharedUrlFreq($projectId, $params['startDate'], $params['endDate']);

                $data = $rawData['data'] ?? $rawData;
                if (!empty($data) && is_array($data)) {
                    $rows = [];
                    foreach ($data as $item) {
                        if (is_array($item)) {
                            $rows[] = [
                                'url'  => $item['url'] ?? $item['link'] ?? 'Unknown',
                                'freq' => (int) ($item['freq'] ?? $item['frequency'] ?? $item['count'] ?? 0),
                            ];
                        }
                    }
                    usort($rows, fn($a, $b) => $b['freq'] <=> $a['freq']);
                    $tableData = array_slice($rows, 0, 10);
                }
            }

            return view('mk.engagement.urls', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'tableData' => $tableData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 📈 ENGAGEMENT - ACTIVE USERS
        // ══════════════════════════════════════════════════════════════
        public function activeUsers(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $tableData = [];

            if ($projectId) {
                $rawData = $mk->mostActiveUsers($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);

                $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;
                if (!empty($data) && is_array($data)) {
                    $rows = [];
                    foreach ($data as $item) {
                        if (is_array($item)) {
                            $fullName = $item['name'] ?? 'Unknown User';
                            $username = $fullName;

                            if (preg_match('/@(\w+)/', $fullName, $matches)) {
                                $username = $matches[1];
                            }

                            $rows[] = [
                                'username' => $username,
                                'count'    => (int) ($item['y'] ?? $item['post_count'] ?? $item['posts'] ?? $item['count'] ?? 0),
                            ];
                        }
                    }
                    usort($rows, fn($a, $b) => $b['count'] <=> $a['count']);
                    $tableData = array_slice($rows, 0, 10);
                }
            }

            return view('mk.engagement.users', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'tableData' => $tableData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 🔄 ENGAGEMENT - MOST RETWEETS
        // ══════════════════════════════════════════════════════════════
        public function mostRetweets(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $tableData = [];

            if ($projectId) {
                $rawData = $mk->mostRetweets($projectId, $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime']);

                $data = $rawData['data']['data'] ?? $rawData['data'] ?? $rawData;
                if (!empty($data) && is_array($data)) {
                    $rows = [];
                    foreach ($data as $item) {
                        if (is_array($item)) {
                            $author  = $item['name'] ?? $item['author_name'] ?? $item['author'] ?? $item['screen_name'] ?? 'Unknown';
                            $content = $item['content'] ?? $item['text'] ?? 'No content';

                            $rows[] = [
                                'author'        => is_array($author) ? ($author[0] ?? 'Unknown') : (string) $author,
                                'content'       => is_array($content) ? ($content[0] ?? 'No content') : (string) $content,
                                'retweet_count' => (int) ($item['rt'] ?? $item['retweet_count'] ?? $item['retweets'] ?? 0),
                            ];
                        }
                    }
                    usort($rows, fn($a, $b) => $b['retweet_count'] <=> $a['retweet_count']);
                    $tableData = array_slice($rows, 0, 10);
                }
            }

            return view('mk.engagement.retweets', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'tableData' => $tableData,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 📰 PUBLISHER STATS
        // ══════════════════════════════════════════════════════════════
        public function publisherStats(Request $request, MediaKernelsClient $mk)
        {
            $projects        = $this->getProjects($mk);
            $params          = $this->getParams($request);
            $projectId       = $request->query('project_id') ?? ($projects[0]['id'] ?? null);
            $rows            = (int) $request->query('rows', 100);
            $includePagerank = $request->query('pagerank', 'true') === 'true';

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            $rawData   = [];
            $tableData = [];

            if ($projectId) {
                $rawData   = $mk->publisherStats($projectId, $params['media'], $params['startDate'], $params['endDate'], $params['startTime'], $params['endTime'], $rows, $includePagerank);
                $tableData = $this->normalizePublisherData($rawData, $includePagerank);
            }

            return view('mk.publisher', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'rawData'   => $rawData,
                'tableData' => $tableData,
            ]);
        }

        /**
         * Helper: Normalize publisher data
         */
        private function normalizePublisherData(array $rawData, bool $includePagerank = true): array
        {
            $normalized = [];

            $publisherData = $rawData['article']['publisher'] ?? null;
            $pagerankData  = $rawData['article']['pagerank'] ?? null;
            $mediaType     = $rawData['article']['media_type_label'] ?? $rawData['article']['media_type_code'] ?? 'Social Media';

            if ($publisherData && is_array($publisherData)) {
                foreach ($publisherData as $publisherName => $count) {
                    if ($count <= 0) continue;

                    if (empty($publisherName) || trim($publisherName) === '') {
                        $publisherName = $mediaType . ' Posts';
                    }

                    $pagerank = null;
                    if ($includePagerank && $pagerankData && isset($pagerankData[$publisherName])) {
                        $pagerank = (float) $pagerankData[$publisherName];
                    }

                    $normalized[] = [
                        'publisher' => (string) $publisherName,
                        'count'     => (int) $count,
                        'pagerank'  => $pagerank,
                    ];
                }
            }

            if (empty($normalized)) {
                $dataArray = $rawData['data'] ?? $rawData;

                if (!empty($dataArray) && is_array($dataArray)) {
                    foreach ($dataArray as $item) {
                        if (is_array($item)) {
                            $normalized[] = [
                                'publisher' => (string) ($item['publisher'] ?? $item['name'] ?? $item['source'] ?? 'Unknown'),
                                'count'     => (int) ($item['count'] ?? $item['total'] ?? $item['articles'] ?? 0),
                                'pagerank'  => isset($item['pagerank']) ? (float) $item['pagerank'] : null,
                            ];
                        }
                    }
                }
            }

            if (empty($normalized) && !empty($rawData)) {
                foreach ($rawData as $key => $value) {
                    if ($key !== 'data' && $key !== 'article' && is_numeric($value)) {
                        $normalized[] = [
                            'publisher' => (string) $key,
                            'count'     => (int) $value,
                            'pagerank'  => null,
                        ];
                    }
                }
            }

            if (!empty($normalized)) {
                usort($normalized, fn($a, $b) => $b['count'] <=> $a['count']);
            }

            return $normalized;
        }

        // ══════════════════════════════════════════════════════════════
        // 📰 RECENT TOPICS (News)
        // ══════════════════════════════════════════════════════════════
        public function recentTopics(Request $request, MediaKernelsClient $mk)
        {
            $level = $request->query('level', 'internasional');
            $size  = (int) $request->query('size', 10);

            $rawData = $mk->recentTopics($level, $size);
            $topics  = $rawData['data'] ?? $rawData;

            return view('mk.topics', [
                'rawData' => $rawData,
                'topics'  => $topics,
                'level'   => $level,
                'size'    => $size,
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 📊 DATA OVERVIEW - SIMPLIFIED (Lazy Loading)
        // ══════════════════════════════════════════════════════════════
        public function dataOverview(Request $request, MediaKernelsClient $mk)
        {
            $projects  = $this->getProjects($mk);
            $params    = $this->getParams($request);
            $projectId = $request->query('project_id') ?? ($projects[0]['id'] ?? null);

            if ($projectId && !$this->userHasAccessToProject($projectId)) {
                return redirect()->route('mk.dashboard')
                    ->with('error', 'You do not have access to this project');
            }

            return view('mk.data-overview', [
                'projects'  => $projects,
                'projectId' => $projectId,
                'params'    => $params,
                'startDate' => $params['startDate'],
                'endDate'   => $params['endDate'],
            ]);
        }

        // ══════════════════════════════════════════════════════════════
        // 🔥 PRIVATE: Hybrid mention counts (sentimentTotal + estimasi)
        // ══════════════════════════════════════════════════════════════
        private function fetchMentionCountsEnhanced($projectId, $params, MediaKernelsClient $mk): array
        {
            $counts = ['social' => 0, 'news' => 0];

            try {
                Log::info('📊 Fetching mentions using hybrid approach');

                $allSentiment = $mk->sentimentTotal(
                    $projectId,
                    $params['startDate'],
                    $params['endDate'],
                    $params['startTime'],
                    $params['endTime']
                );

                $normalized    = $this->normalizeSentimentTotal($allSentiment);
                $totalMentions = $normalized['positive'] + $normalized['neutral'] + $normalized['negative'];

                Log::info('✅ Total mentions from sentiment', [
                    'positive' => $normalized['positive'],
                    'neutral'  => $normalized['neutral'],
                    'negative' => $normalized['negative'],
                    'total'    => $totalMentions,
                ]);

                if ($totalMentions == 0) {
                    Log::warning('⚠️ No mentions found in date range');
                    return $counts;
                }

                try {
                    $newsStats = $mk->projectStats(
                        $projectId,
                        'onlinenews',
                        $params['startDate'],
                        $params['endDate'],
                        $params['startTime'],
                        $params['endTime'],
                        'volumetotal'
                    );

                    $newsCount = $this->extractTotal($newsStats);

                    if ($newsCount > 0 && $newsCount <= $totalMentions) {
                        $counts['news']   = $newsCount;
                        $counts['social'] = $totalMentions - $newsCount;

                        return $counts;
                    }

                    throw new \Exception('projectStats returned 0 or invalid count');

                } catch (\Exception $e) {
                    Log::info('ℹ️ projectStats failed, using estimation', [
                        'error' => $e->getMessage(),
                    ]);

                    $counts['news']   = (int) round($totalMentions * 0.20);
                    $counts['social'] = $totalMentions - $counts['news'];
                }

            } catch (\Exception $e) {
                Log::error('❌ Failed to calculate mentions', [
                    'error' => $e->getMessage(),
                    'trace' => substr($e->getTraceAsString(), 0, 500),
                ]);
            }

            return $counts;
        }
    public function chartData(Request $request, MediaKernelsClient $mk)
    {
        $projectId = (int) $request->query('project_id');
        $startDate = $request->query('start_date', now()->startOfMonth()->toDateString());
        $endDate   = $request->query('end_date',   now()->toDateString());

        if (!$this->userHasAccessToProject($projectId)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cacheKey = "chart_timeline_{$projectId}_{$startDate}_{$endDate}";

        try {
            $timeline = \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($projectId, $startDate, $endDate, $mk) {
                return $this->extractTimelineByRange($projectId, $startDate, $endDate, $mk);
            });

            return response()->json(['timeline' => $timeline]);

        } catch (\Exception $e) {
            Log::warning("chartData: failed for project {$projectId}", [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'timeline' => [
                    'dates'     => [],
                    'values'    => [],
                    'sentiment' => ['positive'=>[],'neutral'=>[],'negative'=>[]],
                ],
            ], 200);
        }
    }
    public function profile(MediaKernelsClient $mk)
    {
        $projects = $this->getProjects($mk);
        return view('mk.profile', compact('projects'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $user = Auth::user();

        try {
            $file = $request->file('avatar');
            $filename = time() . '_' . $user->id . '.' . $file->getClientOriginalExtension();

            // Simpan file baru di storage/app/public/avatars
            $path = $file->storeAs('avatars', $filename, 'public');

            // Hapus avatar lama jika ada
            if ($user->avatar) {
                // Ambil path relatif dari URL, contoh: /storage/avatars/xxx.jpg -> avatars/xxx.jpg
                $oldPath = str_replace('/storage/', '', $user->avatar);
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldPath)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($oldPath);
                }
            }

            // Update user record: URL menggunakan /storage/...
            $user->avatar = '/storage/' . $path;
            $user->save();

            Log::info('Avatar updated successfully', [
                'user_id'  => $user->id,
                'filename' => $filename,
                'path'     => $user->avatar,
            ]);

            return redirect()->back()->with('success', 'Profile picture updated successfully!');

        } catch (\Exception $e) {
            Log::error('Avatar upload failed', [
                'user_id' => $user->id,
                'error'   => $e->getMessage(),
            ]);

            return redirect()->back()->with('error', 'Failed to upload profile picture. Please try again.');
        }
    }

    public function updateNoticePreference(Request $request)
    {
        $request->validate([
            'notice_days' => 'required|integer|in:1,7,30',
        ]);

        $user = Auth::user();

        if (!$user->trial_ends_at) {
            return redirect()->back()->with('error', 'Cannot set reminder: Trial end date is not set.');
        }

        $days = $request->input('notice_days');
        $noticeDate = $user->trial_ends_at->copy()->subDays($days);

        $user->subscription_notice_at = $noticeDate;
        $user->save();

        return redirect()->back()->with('success', "Notification reminder updated to {$days} days before expiry.");
    }
}