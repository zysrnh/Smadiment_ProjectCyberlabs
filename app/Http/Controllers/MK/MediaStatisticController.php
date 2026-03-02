<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MediaStatisticController extends Controller
{
    public function __construct(private MediaKernelsClient $mk) {}

    // ───────────────────────────────────────────────
    // PAGE
    // ───────────────────────────────────────────────

    public function index(Request $request)
    {
        return view('mk.media-statistic');
    }

    // ───────────────────────────────────────────────
    // TAB 1 – GET /mk/api/media-statistic/mention-by-platform
    // ───────────────────────────────────────────────

    public function mentionByPlatform(Request $request)
    {
        $projectId = $request->get('project_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

        if (! $projectId) {
            return response()->json(['error' => 'project_id required'], 422);
        }

        // ── Definisi platform: label + key di dalam bymedia response API ──
        //
        // Dari log aktual, bymedia shape-nya:
        // { "doc":"88065", "fb":"4488", "twit":"180524",
        //   "youtube":"7651", "instagram":"648", "tiktok":"2153" }
        //
        // Perhatikan: youtube & instagram pakai nama penuh, bukan alias pendek.
        // Kita daftarkan semua kemungkinan alias agar robust terhadap perubahan API.
        $platforms = [
            [
                'media'    => 'doc',
                'label'    => 'Mass Media',
                'category' => 'mass_media',
                'aliases'  => ['doc', 'news', 'online'],
            ],
            [
                'media'    => 'twitter',
                'label'    => 'X (Twitter)',
                'category' => 'social_media',
                'aliases'  => ['twit', 'twitter', 'x'],
            ],
            [
                'media'    => 'facebook',
                'label'    => 'Facebook',
                'category' => 'social_media',
                'aliases'  => ['fb', 'facebook'],
            ],
            [
                'media'    => 'instagram',
                'label'    => 'Instagram',
                'category' => 'social_media',
                'aliases'  => ['instagram', 'ig'],   // API pakai 'instagram', bukan 'ig'
            ],
            [
                'media'    => 'youtube',
                'label'    => 'YouTube',
                'category' => 'social_media',
                'aliases'  => ['youtube', 'yt'],      // API pakai 'youtube', bukan 'yt'
            ],
            [
                'media'    => 'tiktok',
                'label'    => 'TikTok',
                'category' => 'social_media',
                'aliases'  => ['tiktok', 'tt'],
            ],
        ];

        $results   = [];
        $massTotal = 0;
        $socTotal  = 0;
        $bymedia   = [];

        // ── Satu kali panggil API — response selalu return semua platform ──
        try {
            $data = $this->mk->volumeTotal((string) $projectId, 'doc', $startDate, $endDate);

            Log::info('mentionByPlatform volumeTotal raw', [
                'keys'    => is_array($data) ? array_keys($data) : gettype($data),
                'preview' => is_array($data) ? array_slice($data, 0, 3, true) : $data,
            ]);

            // Ekstrak bymedia — normalize semua key ke lowercase
            if (isset($data['bymedia']) && is_array($data['bymedia'])) {
                foreach ($data['bymedia'] as $k => $v) {
                    $bymedia[strtolower($k)] = (int) $v;
                }
            }

        } catch (\Throwable $e) {
            Log::warning('mentionByPlatform: volumeTotal failed', [
                'error' => $e->getMessage(),
            ]);
        }

        // ── Petakan setiap platform ke nilai dari bymedia ──
        foreach ($platforms as $plat) {
            $count = 0;

            // Coba setiap alias sampai ketemu yang ada di bymedia
            foreach ($plat['aliases'] as $alias) {
                if (isset($bymedia[strtolower($alias)])) {
                    $count = $bymedia[strtolower($alias)];
                    break;
                }
            }

            $results[] = [
                'media'    => $plat['media'],
                'label'    => $plat['label'],
                'count'    => $count,
                'category' => $plat['category'],
            ];

            if ($plat['category'] === 'mass_media') {
                $massTotal += $count;
            } else {
                $socTotal += $count;
            }
        }

        Log::info('mentionByPlatform result', [
            'bymedia_keys' => array_keys($bymedia),
            'results'      => array_map(fn ($r) => "{$r['media']}={$r['count']}", $results),
            'mass_total'   => $massTotal,
            'social_total' => $socTotal,
        ]);

        return response()->json([
            'platforms'    => $results,
            'mass_total'   => $massTotal,
            'social_total' => $socTotal,
            'grand_total'  => $massTotal + $socTotal,
        ]);
    }

    // ───────────────────────────────────────────────
    // TREND BY MEDIA – GET /mk/api/media-statistic/trend-by-media
    //
    // Fetch trend per platform, bisa semua sekaligus atau
    // filter satu platform via ?media=twitter
    //
    // Response:
    // {
    //   "data": [
    //     { "keyword": "twitter",  "data": [{"date":"2026-02-01","count":1234}, ...] },
    //     { "keyword": "tiktok",   "data": [...] },
    //     { "keyword": "facebook", "data": [...] },
    //     { "keyword": "instagram","data": [...] },
    //     { "keyword": "youtube",  "data": [...] },
    //     { "keyword": "doc",      "data": [...] },
    //   ]
    // }
    // ───────────────────────────────────────────────

    public function trendByMedia(Request $request)
{
    $projectId   = $request->get('project_id');
    $startDate   = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate     = $request->get('end_date',   now()->format('Y-m-d'));
    $mediaFilter = $request->get('media');

    if (! $projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    // trendsTotal TIDAK punya param media — return semua platform sekaligus
    // Panggil sekali saja, lalu petakan per keyword
    try {
        $raw = $this->mk->trendsTotal(
            (string) $projectId,
            $startDate,   // ← FIX: dulu $mediaKey nyasar ke sini
            $endDate
        );

        Log::info('trendByMedia trendsTotal raw', [
            'keys'    => is_array($raw) ? array_keys($raw) : gettype($raw),
            'preview' => is_array($raw) ? array_slice($raw, 0, 2, true) : $raw,
        ]);

    } catch (\Throwable $e) {
        Log::warning('trendByMedia trendsTotal failed', ['error' => $e->getMessage()]);
        $raw = ['data' => []];
    }

    // Map keyword dari response ke label platform yang dikenal
    // trendsTotal return keyword dalam UPPERCASE (DOC, TWIT, FB, dll)
    $keywordMap = [
        'DOC'       => 'doc',
        'TWIT'      => 'twitter',
        'TWITTER'   => 'twitter',
        'FB'        => 'facebook',
        'FACEBOOK'  => 'facebook',
        'IG'        => 'instagram',
        'INSTAGRAM' => 'instagram',
        'YT'        => 'youtube',
        'YOUTUBE'   => 'youtube',
        'TIKTOK'    => 'tiktok',
        'TT'        => 'tiktok',
    ];

    // Rebuild result — group by normalized platform key
    $grouped = [];
    foreach ($raw['data'] ?? [] as $item) {
        $kw  = strtoupper($item['keyword'] ?? '');
        $key = $keywordMap[$kw] ?? strtolower($kw);

        if (! isset($grouped[$key])) {
            $grouped[$key] = [];
        }

        foreach ($item['data'] ?? [] as $pt) {
            $date  = substr((string)($pt['date'] ?? ''), 0, 10);
            $count = (int)($pt['count'] ?? 0);
            if (! $date) continue;

            // merge by date kalau ada duplikat
            $grouped[$key][$date] = ($grouped[$key][$date] ?? 0) + $count;
        }
    }

    $allKeys = ['twitter', 'tiktok', 'facebook', 'instagram', 'youtube', 'doc'];
    $filtered = $mediaFilter ? [$mediaFilter] : $allKeys;

    $result = [];
    foreach ($filtered as $mk) {
        $dateMap = $grouped[$mk] ?? [];
        ksort($dateMap);

        $result[] = [
            'keyword' => $mk,
            'data'    => array_values(array_map(
                fn($d, $c) => ['date' => $d, 'count' => $c],
                array_keys($dateMap),
                array_values($dateMap)
            )),
        ];
    }

    return response()->json(['data' => $result]);
}

    // ───────────────────────────────────────────────
    // TAB 2 – GET /mk/api/media-statistic/sentiment-engagement
    // ───────────────────────────────────────────────

    public function sentimentEngagement(Request $request)
    {
        $projectId = $request->get('project_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

        if (! $projectId) {
            return response()->json(['error' => 'project_id required'], 422);
        }

        // ── 1. Sentiment per media ──────────────────
        $sentimentMedia = [];
        try {
            $raw = $this->mk->sentimentMedia((string) $projectId, $startDate, $endDate);

            Log::info('sentimentMedia raw', [
                'keys'    => is_array($raw) ? array_keys($raw) : gettype($raw),
                'preview' => is_array($raw) ? array_slice($raw, 0, 3, true) : $raw,
            ]);

            $sentimentMedia = $this->normaliseSentimentMedia($raw);

        } catch (\Throwable $e) {
            Log::warning('sentimentMedia failed', ['error' => $e->getMessage()]);
        }

        // ── 2. Overall sentiment totals ─────────────
        $sentimentTotal = [];
        try {
            $raw = $this->mk->sentimentTotal((string) $projectId, $startDate, $endDate);

            Log::info('sentimentTotal raw', [
                'keys'    => is_array($raw) ? array_keys($raw) : gettype($raw),
                'preview' => is_array($raw) ? array_slice($raw, 0, 5, true) : $raw,
            ]);

            $sentimentTotal = $this->normaliseSentimentTotal($raw, $sentimentMedia);

        } catch (\Throwable $e) {
            Log::warning('sentimentTotal failed', ['error' => $e->getMessage()]);
            $sentimentTotal = $this->aggregateSentimentTotal($sentimentMedia);
        }

        // ── 3. Estimated reach per platform ─────────
        $mediaKeys = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];
        $reachData = [];

        foreach ($mediaKeys as $mk) {
            try {
                $raw = $this->mk->estReach((string) $projectId, $mk, $startDate, $endDate);

                Log::info("estReach[$mk] raw", [
                    'type'    => gettype($raw),
                    'keys'    => is_array($raw) ? array_keys($raw) : [],
                    'preview' => is_array($raw) ? array_slice($raw, 0, 3, true) : $raw,
                ]);

                $reachData[$mk] = $this->normaliseEstReach($raw);

            } catch (\Throwable $e) {
                Log::warning("estReach failed for {$mk}", ['error' => $e->getMessage()]);
                $reachData[$mk] = 0;
            }
        }

        return response()->json([
            'sentiment_media'  => $sentimentMedia,
            'sentiment_total'  => $sentimentTotal,
            'reach_by_media'   => $reachData,
        ]);
    }

    // ───────────────────────────────────────────────
    // TAB 3 – GET /mk/api/media-statistic/locations
    // ───────────────────────────────────────────────

    public function locations(Request $request)
    {
        $projectId = $request->get('project_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date',   now()->format('Y-m-d'));
        $media     = $request->get('media', 'twitter');

        if (! $projectId) {
            return response()->json(['error' => 'project_id required'], 422);
        }

        $geoUsers     = [];
        $topLocations = [];
        $geoPositive  = [];
        $geoNegative  = [];

        try {
            $geoUsers = $this->mk->geoTwitterUser((string) $projectId, $media, $startDate, $endDate);
        } catch (\Throwable $e) {
            Log::warning('geoTwitterUser failed', ['error' => $e->getMessage()]);
        }

        try {
            $raw = $this->mk->topAuthorLocation((string) $projectId, $media, $startDate, $endDate);

            if (isset($raw['country']['rows'])) {
                $topLocations = $raw['country']['rows'];
            } elseif (isset($raw['data'])) {
                $topLocations = $raw['data'];
            } elseif (is_array($raw)) {
                $topLocations = $raw;
            }

        } catch (\Throwable $e) {
            Log::warning('topAuthorLocation failed', ['error' => $e->getMessage()]);
        }

        try {
            $geoPositive = $this->mk->geoTwitterUserSentiment(
                (string) $projectId, $media, $startDate, $endDate,
                0, 23, 1
            );
        } catch (\Throwable $e) {
            Log::warning('geoSentiment[positive] failed', ['error' => $e->getMessage()]);
        }

        try {
            $geoNegative = $this->mk->geoTwitterUserSentiment(
                (string) $projectId, $media, $startDate, $endDate,
                0, 23, 2
            );
        } catch (\Throwable $e) {
            Log::warning('geoSentiment[negative] failed', ['error' => $e->getMessage()]);
        }

        return response()->json([
            'geo_users'     => $geoUsers,
            'top_locations' => $topLocations,
            'geo_positive'  => $geoPositive,
            'geo_negative'  => $geoNegative,
        ]);
    }

    // ══════════════════════════════════════════════
    // PRIVATE HELPERS
    // ══════════════════════════════════════════════

    /**
     * Normalise trend response dari MK API menjadi array [{date, count}] yang konsisten.
     *
     * Handles berbagai shape response:
     *   Shape A: { data: [ { keyword, data: [{date,count}] }, ... ] }  ← nested keyword groups
     *   Shape B: { data: [{date, count}] }                             ← flat dalam wrapper
     *   Shape C: [{date, count}]                                       ← flat array langsung
     *   Shape D: { dates: [...], counts: [...] }                       ← parallel arrays
     */
    private function normaliseTrendData(mixed $raw): array
    {
        if (! is_array($raw)) {
            return [];
        }

        // Shape A: { data: [ { keyword, data: [{date,count}] }, ... ] }
        if (
            isset($raw['data'])
            && is_array($raw['data'])
            && isset($raw['data'][0])
            && is_array($raw['data'][0])
            && array_key_exists('data', $raw['data'][0])
        ) {
            $merged = [];
            foreach ($raw['data'] as $item) {
                foreach ($item['data'] ?? [] as $pt) {
                    $date  = substr((string) ($pt['date'] ?? ''), 0, 10);
                    $count = (int) ($pt['count'] ?? 0);
                    if (! $date) {
                        continue;
                    }
                    $merged[$date] = ($merged[$date] ?? 0) + $count;
                }
            }
            ksort($merged);
            return array_values(array_map(
                fn ($d, $c) => ['date' => $d, 'count' => $c],
                array_keys($merged),
                array_values($merged)
            ));
        }

        // Shape B: { data: [{date, count}] }
        if (
            isset($raw['data'])
            && is_array($raw['data'])
            && isset($raw['data'][0]['date'])
        ) {
            return array_values(array_map(fn ($pt) => [
                'date'  => substr((string) ($pt['date'] ?? ''), 0, 10),
                'count' => (int) ($pt['count'] ?? 0),
            ], $raw['data']));
        }

        // Shape C: flat array [{date, count}]
        if (isset($raw[0]) && is_array($raw[0]) && isset($raw[0]['date'])) {
            return array_values(array_map(fn ($pt) => [
                'date'  => substr((string) ($pt['date'] ?? ''), 0, 10),
                'count' => (int) ($pt['count'] ?? 0),
            ], $raw));
        }

        // Shape D: { dates: [...], counts: [...] }
        if (isset($raw['dates']) && isset($raw['counts']) && is_array($raw['dates'])) {
            $out = [];
            foreach ($raw['dates'] as $i => $date) {
                $out[] = [
                    'date'  => substr((string) $date, 0, 10),
                    'count' => (int) ($raw['counts'][$i] ?? 0),
                ];
            }
            return $out;
        }

        return [];
    }

    /**
     * Normalise estReach response ke single integer.
     *
     * Berbagai shape yang ditemukan:
     *   - integer / string angka langsung
     *   - { total: N }  |  { all: N }  |  { reach: N }
     *   - { data: { total: N } }  |  { data: N }
     *   - { bymedia: { twit: N, ... } }  ← sum semua
     *   - [ { count: N }, ... ]           ← array of items, sum count
     */
    private function normaliseEstReach(mixed $raw): int
    {
        if (is_null($raw)) {
            return 0;
        }

        if (is_numeric($raw)) {
            return (int) $raw;
        }

        if (! is_array($raw)) {
            return 0;
        }

        foreach (['total', 'reach', 'all', 'count', 'value'] as $key) {
            if (isset($raw[$key]) && is_numeric($raw[$key])) {
                return (int) $raw[$key];
            }
        }

        if (isset($raw['data'])) {
            if (is_numeric($raw['data'])) {
                return (int) $raw['data'];
            }
            if (is_array($raw['data'])) {
                return $this->normaliseEstReach($raw['data']);
            }
        }

        if (isset($raw['bymedia']) && is_array($raw['bymedia'])) {
            $sum = 0;
            foreach ($raw['bymedia'] as $val) {
                $sum += is_numeric($val) ? (int) $val : 0;
            }
            return $sum;
        }

        if (isset($raw[0]) && is_array($raw[0])) {
            $sum = 0;
            foreach ($raw as $item) {
                foreach (['count', 'total', 'reach', 'value'] as $key) {
                    if (isset($item[$key]) && is_numeric($item[$key])) {
                        $sum += (int) $item[$key];
                        break;
                    }
                }
            }
            return $sum;
        }

        $firstVal = reset($raw);
        if (is_numeric($firstVal)) {
            return (int) array_sum($raw);
        }

        return 0;
    }

    /**
     * Normalise sentimentMedia response.
     *
     * Expected output:
     * [
     *   [ 'media'=>'twit', 'label'=>'X (Twitter)', 'positive'=>N, 'negative'=>N, 'neutral'=>N ],
     *   ...
     * ]
     */
    private function normaliseSentimentMedia(mixed $raw): array
    {
        $labelMap = [
            'doc'     => 'Mass Media',
            'twit'    => 'X (Twitter)',
            'twitter' => 'X (Twitter)',
            'fb'      => 'Facebook',
            'ig'      => 'Instagram',
            'yt'      => 'YouTube',
            'tiktok'  => 'TikTok',
        ];

        $result = [];

        // Shape A: { bymedia: { twit: { pos, neg, net }, ... } }
        if (isset($raw['bymedia']) && is_array($raw['bymedia'])) {
            foreach ($raw['bymedia'] as $mediaKey => $sentiments) {
                if (! is_array($sentiments)) {
                    continue;
                }

                $pos = (int) ($sentiments['pos'] ?? 0);
                $neg = (int) ($sentiments['neg'] ?? 0);
                $neu = (int) ($sentiments['net'] ?? $sentiments['neu'] ?? 0);

                if ($pos + $neg + $neu === 0) {
                    continue;
                }

                $result[] = [
                    'media'    => $mediaKey,
                    'label'    => $labelMap[$mediaKey] ?? ucfirst($mediaKey),
                    'positive' => $pos,
                    'negative' => $neg,
                    'neutral'  => $neu,
                ];
            }
            return $result;
        }

        // Shape B: { data: [ { media/name, positive/pos, ... } ] }
        //       or flat: [ { media/name, positive/pos, ... } ]
        $items = [];
        if (isset($raw['data']) && is_array($raw['data'])) {
            $items = $raw['data'];
        } elseif (isset($raw[0]) && is_array($raw[0])) {
            $items = $raw;
        }

        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }

            $mediaKey = $item['media'] ?? $item['name'] ?? $item['label'] ?? '';
            $pos      = (int) ($item['positive'] ?? $item['pos'] ?? $item['1']  ?? 0);
            $neg      = (int) ($item['negative'] ?? $item['neg'] ?? $item['-1'] ?? $item['2'] ?? 0);
            $neu      = (int) ($item['neutral']  ?? $item['net'] ?? $item['neu'] ?? $item['0'] ?? 0);

            $result[] = [
                'media'    => $mediaKey,
                'label'    => $labelMap[$mediaKey] ?? $mediaKey,
                'positive' => $pos,
                'negative' => $neg,
                'neutral'  => $neu,
            ];
        }

        return $result;
    }

    /**
     * Normalise sentimentTotal response ke [ positive, negative, neutral ].
     */
    private function normaliseSentimentTotal(mixed $raw, array $sentimentMedia): array
    {
        if (! is_array($raw)) {
            return $this->aggregateSentimentTotal($sentimentMedia);
        }

        // { pos: N, neg: N, net: N }
        if (isset($raw['pos']) || isset($raw['neg'])) {
            return [
                'positive' => (int) ($raw['pos'] ?? 0),
                'negative' => (int) ($raw['neg'] ?? 0),
                'neutral'  => (int) ($raw['net'] ?? $raw['neu'] ?? 0),
            ];
        }

        // { positive: N, negative: N, neutral: N }
        if (isset($raw['positive']) || isset($raw['negative'])) {
            return [
                'positive' => (int) ($raw['positive'] ?? 0),
                'negative' => (int) ($raw['negative'] ?? 0),
                'neutral'  => (int) ($raw['neutral']  ?? 0),
            ];
        }

        // { bymedia: { twit: { pos, neg, net }, ... } } — sum semua
        if (isset($raw['bymedia']) && is_array($raw['bymedia'])) {
            $pos = $neg = $neu = 0;
            foreach ($raw['bymedia'] as $sentiments) {
                if (! is_array($sentiments)) {
                    continue;
                }
                $pos += (int) ($sentiments['pos'] ?? 0);
                $neg += (int) ($sentiments['neg'] ?? 0);
                $neu += (int) ($sentiments['net'] ?? $sentiments['neu'] ?? 0);
            }
            return ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
        }

        // { data: {...} } — recurse
        if (isset($raw['data']) && is_array($raw['data'])) {
            return $this->normaliseSentimentTotal($raw['data'], $sentimentMedia);
        }

        // Fallback: sum dari per-media
        return $this->aggregateSentimentTotal($sentimentMedia);
    }

    /**
     * Sum sentiment dari per-media breakdown sebagai fallback total.
     */
    private function aggregateSentimentTotal(array $sentimentMedia): array
    {
        $pos = $neg = $neu = 0;
        foreach ($sentimentMedia as $m) {
            $pos += (int) ($m['positive'] ?? 0);
            $neg += (int) ($m['negative'] ?? 0);
            $neu += (int) ($m['neutral']  ?? 0);
        }
        return ['positive' => $pos, 'negative' => $neg, 'neutral' => $neu];
    }
    // ───────────────────────────────────────────────
// MENTIONS BY WEEKDAY — GET /mk/api/media-statistic/mentions-by-weekday
// Ambil raw mentions → group by platform → aggregate by weekday (Senin–Minggu)
// ───────────────────────────────────────────────

public function mentionsByWeekday(Request $request)
{
    $projectId = $request->get('project_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

    if (! $projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    $wdLabels = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];

    $platforms  = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];
    $platLabels = [
        'doc'       => 'Online News',
        'twitter'   => 'Twitter',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    ];
    $platColors = [
        'doc'       => '#038047',
        'twitter'   => '#1d9bf0',
        'facebook'  => '#1877f2',
        'instagram' => '#e1306c',
        'youtube'   => '#ff0000',
        'tiktok'    => '#2dd4bf',
    ];

    $keywordMap = [
        'DOC'       => 'doc',
        'TWIT'      => 'twitter',
        'TWITTER'   => 'twitter',
        'FB'        => 'facebook',
        'FACEBOOK'  => 'facebook',
        'IG'        => 'instagram',
        'INSTAGRAM' => 'instagram',
        'YT'        => 'youtube',
        'YOUTUBE'   => 'youtube',
        'TIKTOK'    => 'tiktok',
        'TT'        => 'tiktok',
    ];

    // Init accumulator: platform → weekday[0..6]
    $wdAcc   = [];
    $wdTotal = array_fill(0, 7, 0);
    foreach ($platforms as $p) {
        $wdAcc[$p] = array_fill(0, 7, 0);
    }

    try {
        // Pakai trendsTotal — sama seperti trendMentions, lebih akurat & cepat
        $raw = $this->mk->trendsTotal(
            (string) $projectId,
            $startDate,
            $endDate
        );

        Log::info('mentionsByWeekday trendsTotal raw', [
            'project_id' => $projectId,
            'data_count' => is_array($raw['data'] ?? null) ? count($raw['data']) : 0,
        ]);

        foreach ($raw['data'] ?? [] as $item) {
            $kw  = strtoupper($item['keyword'] ?? '');
            $key = $keywordMap[$kw] ?? strtolower($kw);

            if (! isset($wdAcc[$key])) continue;

            foreach ($item['data'] ?? [] as $pt) {
                $dateStr = substr((string) ($pt['date'] ?? ''), 0, 10);
                $count   = (int) ($pt['count'] ?? 0);
                if (! $dateStr || $count === 0) continue;

                try {
                    $dt    = new \DateTime($dateStr);
                    $jsDay = (int) $dt->format('w'); // 0=Minggu, 1=Senin...6=Sabtu
                    $idx   = $jsDay === 0 ? 6 : $jsDay - 1; // Senin=0...Minggu=6

                    $wdAcc[$key][$idx] += $count;
                    $wdTotal[$idx]     += $count;
                } catch (\Exception $e) {
                    continue;
                }
            }
        }

    } catch (\Throwable $e) {
        Log::warning('mentionsByWeekday: trendsTotal failed', [
            'error' => $e->getMessage(),
        ]);
    }

    $result = [];
    foreach ($platforms as $p) {
        $result[] = [
            'key'   => $p,
            'label' => $platLabels[$p],
            'color' => $platColors[$p],
            'data'  => $wdAcc[$p],
        ];
    }

    return response()->json([
        'weekdays'  => $wdLabels,
        'total'     => $wdTotal,
        'platforms' => $result,
    ]);
}

public function trendMentions(Request $request)
{
    $projectId = $request->get('project_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

    if (! $projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    // ── Pakai trendsTotal — return aggregate count per hari per platform ──
    // Jauh lebih cepat vs fetch raw mentions (tidak ada limit rows, tidak timeout)
    // Response shape: { data: [ { keyword: "TWIT", data: [{date, count}] }, ... ] }

    $keywordMap = [
        'DOC'       => 'doc',
        'TWIT'      => 'twitter',
        'TWITTER'   => 'twitter',
        'FB'        => 'facebook',
        'FACEBOOK'  => 'facebook',
        'IG'        => 'instagram',
        'INSTAGRAM' => 'instagram',
        'YT'        => 'youtube',
        'YOUTUBE'   => 'youtube',
        'TIKTOK'    => 'tiktok',
        'TT'        => 'tiktok',
    ];

    $platLabels = [
        'doc'       => 'Online News',
        'twitter'   => 'Twitter',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    ];
    $platColors = [
        'doc'       => '#038047',
        'twitter'   => '#1d9bf0',
        'facebook'  => '#1877f2',
        'instagram' => '#e1306c',
        'youtube'   => '#ff0000',
        'tiktok'    => '#2dd4bf',
    ];

    $platforms = ['doc', 'twitter', 'facebook', 'instagram', 'youtube', 'tiktok'];

    // ── Generate date list untuk fill 0 pada hari tanpa data ──
    $dates   = [];
    $current = new \DateTime($startDate);
    $end     = new \DateTime($endDate);
    while ($current <= $end) {
        $dates[] = $current->format('Y-m-d');
        $current->modify('+1 day');
    }

    // Init grouped per platform per date
    $grouped = [];
    foreach ($platforms as $p) {
        $grouped[$p] = [];
    }

    try {
        $raw = $this->mk->trendsTotal(
            (string) $projectId,
            $startDate,
            $endDate
        );

        Log::info('trendMentions trendsTotal raw', [
            'project_id' => $projectId,
            'keys'       => is_array($raw) ? array_keys($raw) : gettype($raw),
            'data_count' => is_array($raw['data'] ?? null) ? count($raw['data']) : 0,
        ]);

        foreach ($raw['data'] ?? [] as $item) {
            $kw  = strtoupper($item['keyword'] ?? '');
            $key = $keywordMap[$kw] ?? strtolower($kw);

            if (! isset($grouped[$key])) continue;

            foreach ($item['data'] ?? [] as $pt) {
                $date  = substr((string) ($pt['date'] ?? ''), 0, 10);
                $count = (int) ($pt['count'] ?? 0);
                if (! $date) continue;
                $grouped[$key][$date] = ($grouped[$key][$date] ?? 0) + $count;
            }
        }

    } catch (\Throwable $e) {
        Log::warning('trendMentions trendsTotal failed', [
            'project_id' => $projectId,
            'error'      => $e->getMessage(),
        ]);

        return response()->json([
            'error' => 'Gagal mengambil data trend: ' . $e->getMessage(),
            'data'  => [],
            'meta'  => ['start_date' => $startDate, 'end_date' => $endDate],
        ], 500);
    }

    // ── Build result — semua tanggal ter-represent (0 jika tidak ada data) ──
    $grandTotal = 0;
    $result     = [];

    foreach ($platforms as $p) {
        $dayData = [];
        foreach ($dates as $date) {
            $count    = $grouped[$p][$date] ?? 0;
            $grandTotal += $count;
            $dayData[] = ['date' => $date, 'count' => $count];
        }

        $result[] = [
            'key'   => $p,
            'label' => $platLabels[$p],
            'color' => $platColors[$p],
            'data'  => $dayData,
        ];
    }

    Log::info('trendMentions complete', [
        'project_id'  => $projectId,
        'date_range'  => "$startDate – $endDate",
        'grand_total' => $grandTotal,
    ]);

    return response()->json([
        'data' => $result,
        'meta' => [
            'total_fetched' => $grandTotal,
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'days_total'    => count($dates),
            'days_errored'  => 0,
        ],
    ]);
}

// ──────────────────────────────────────────────────────────────────────
// PAGE HANDLER — tambahkan method baru ini setelah trendMentions
// ──────────────────────────────────────────────────────────────────────

public function trendPage(Request $request)
{
    return view('mk.media-statistic-trend');
}
public function mentionsByHour(Request $request)
{
    $projectId = $request->get('project_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

    if (! $projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    $platKeyMap = [
        'doc'       => 'doc',
        'news'      => 'doc',
        'twit'      => 'twitter',
        'twitter'   => 'twitter',
        'fb'        => 'facebook',
        'facebook'  => 'facebook',
        'instagram' => 'instagram',
        'ig'        => 'instagram',
        'youtube'   => 'youtube',
        'yt'        => 'youtube',
        'tiktok'    => 'tiktok',
    ];

    $outputLabels = [
        'doc'       => 'Online News',
        'twitter'   => 'Twitter',
        'facebook'  => 'Facebook',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    ];
    $outputColors = [
        'doc'       => '#038047',
        'twitter'   => '#1d9bf0',
        'facebook'  => '#1877f2',
        'instagram' => '#e1306c',
        'youtube'   => '#ff0000',
        'tiktok'    => '#2dd4bf',
    ];

    $tz       = new \DateTimeZone('Asia/Jakarta');
    $cacheKey = "mentions_by_hour_{$projectId}_{$startDate}_{$endDate}";

    [$hourAcc, $hourTotal] = \Illuminate\Support\Facades\Cache::remember(
        $cacheKey,
        now()->addMinutes(30),
        function () use ($projectId, $startDate, $endDate, $platKeyMap, $outputLabels, $tz) {

            $hourAcc   = [];
            $hourTotal = array_fill(0, 24, 0);

            foreach (array_keys($outputLabels) as $p) {
                $hourAcc[$p] = array_fill(0, 24, 0);
            }

            // Ambil 5 batch × 1000 = 5000 rows
            // Cukup representatif untuk distribusi per jam
            // dan tidak terlalu lama (~30 detik)
            $batchSize  = 1000;
            $maxBatches = 5;

            for ($batch = 0; $batch < $maxBatches; $batch++) {
                $start = $batch * $batchSize;

                try {
                    $raw = $this->mk->mentions(
                        (string) $projectId,
                        $startDate,
                        $endDate,
                        0,
                        23,
                        false,
                        $start,
                        $batchSize
                    );
                } catch (\Throwable $e) {
                    Log::warning('mentionsByHour batch error', [
                        'batch' => $batch,
                        'error' => $e->getMessage(),
                    ]);
                    break;
                }

                $items = $raw['data'] ?? (isset($raw[0]) ? $raw : []);
                $count = count($items);

                if ($count === 0) break;

                foreach ($items as $item) {
                    if (!is_array($item)) continue;

                    $media = strtolower(
                        $item['media_type'] ?? $item['type'] ?? $item['tcode'] ?? ''
                    );

                    $normalKey = $platKeyMap[$media] ?? null;
                    if (!$normalKey || !isset($hourAcc[$normalKey])) continue;

                    $dateStr = $item['date_created'] ?? $item['date_inserted_dt'] ?? '';
                    if (!$dateStr) continue;

                    try {
                        $dt   = new \DateTime((string) $dateStr, $tz);
                        $hour = (int) $dt->format('H');
                    } catch (\Exception $e) {
                        continue;
                    }

                    $hourAcc[$normalKey][$hour]++;
                    $hourTotal[$hour]++;
                }

                // Stop kalau batch-nya kurang dari batchSize (sudah halaman terakhir)
                if ($count < $batchSize) break;
            }

            Log::info('mentionsByHour processed', [
                'total_counted' => array_sum($hourTotal),
                'hour_peak'     => array_search(max($hourTotal), $hourTotal),
                'per_platform'  => array_map('array_sum', $hourAcc),
            ]);

            return [$hourAcc, $hourTotal];
        }
    );

    $result = [];
    foreach ($outputLabels as $key => $label) {
        $result[] = [
            'key'   => $key,
            'label' => $label,
            'color' => $outputColors[$key],
            'data'  => array_values($hourAcc[$key]),
        ];
    }

    return response()->json([
        'hours'     => array_map(
            fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT) . ':00',
            range(0, 23)
        ),
        'total'     => $hourTotal,
        'platforms' => $result,
    ]);
}
}