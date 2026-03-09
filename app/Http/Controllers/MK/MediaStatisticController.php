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

// ───────────────────────────────────────────────
// SENTIMENT PAGE
// ───────────────────────────────────────────────

public function sentimentPage(Request $request)
{
    return view('mk.sentiment');
}

public function netSentimentScorePage(Request $request)
{
    return view('mk.net-sentiment-score');
}

// ───────────────────────────────────────────────
// API: SENTIMENT TOTALS + BY MEDIA + TREND
// GET /mk/api/sentiment/totals
// ───────────────────────────────────────────────

public function sentimentTotals(Request $request)
{
    $projectId = $request->get('project_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->get('end_date',   now()->format('Y-m-d'));
    $media     = $request->get('media', 'all');

    if (! $projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    // ── 1. Sentiment per media ──
    $sentimentMedia = [];
    try {
        $raw = $this->mk->sentimentMedia((string) $projectId, $startDate, $endDate);
        $sentimentMedia = $this->normaliseSentimentMedia($raw);
    } catch (\Throwable $e) {
        Log::warning('sentimentTotals: sentimentMedia failed', ['error' => $e->getMessage()]);
    }

    // ── 2. Filter by media if needed ──
    $mediaKeyMap = [
        'doc'       => ['doc'],
        'twitter'   => ['twit', 'twitter'],
        'facebook'  => ['fb', 'facebook'],
        'instagram' => ['ig', 'instagram'],
        'youtube'   => ['yt', 'youtube'],
        'tiktok'    => ['tiktok'],
    ];

    $filtered = $sentimentMedia;
    if ($media !== 'all' && isset($mediaKeyMap[$media])) {
        $aliases = $mediaKeyMap[$media];
        $filtered = array_filter($sentimentMedia, fn($m) => in_array(strtolower($m['media']), $aliases));
        $filtered = array_values($filtered);
    }

    // ── 3. Totals ──
    $totals = [
        'neg' => array_sum(array_column($filtered, 'negative')),
        'pos' => array_sum(array_column($filtered, 'positive')),
        'neu' => array_sum(array_column($filtered, 'neutral')),
    ];

    // ── 4. By media (formatted for frontend) ──
    $labelMap = [
        'doc'       => 'Mass Media',
        'twit'      => 'X / Twitter',
        'twitter'   => 'X / Twitter',
        'fb'        => 'Facebook',
        'facebook'  => 'Facebook',
        'ig'        => 'Instagram',
        'instagram' => 'Instagram',
        'yt'        => 'YouTube',
        'youtube'   => 'YouTube',
        'tiktok'    => 'TikTok',
    ];

    $byMedia = array_map(fn($m) => [
        'key'   => $m['media'],
        'label' => $labelMap[strtolower($m['media'])] ?? $m['label'],
        'neg'   => $m['negative'],
        'pos'   => $m['positive'],
        'neu'   => $m['neutral'],
    ], $sentimentMedia);

    // ── 5. Trend (daily sentiment) ──
    $trend = [];
    try {
        $raw = $this->mk->trendsTotal((string) $projectId, $startDate, $endDate);

        // Build date list
        $dates   = [];
        $current = new \DateTime($startDate);
        $end     = new \DateTime($endDate);
        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->modify('+1 day');
        }

        // For trend we use sentimentMedia daily — fallback: flat trend from trendsTotal split equally
        // Since API doesn't provide daily sentiment breakdown, approximate from total ratio
        $totalMentions = $totals['neg'] + $totals['pos'] + $totals['neu'];
        $negRatio = $totalMentions > 0 ? $totals['neg'] / $totalMentions : 0.33;
        $posRatio = $totalMentions > 0 ? $totals['pos'] / $totalMentions : 0.33;
        $neuRatio = $totalMentions > 0 ? $totals['neu'] / $totalMentions : 0.34;

        // Aggregate daily totals across all platforms
        $keywordMap = [
            'DOC' => 'doc', 'TWIT' => 'twitter', 'TWITTER' => 'twitter',
            'FB' => 'facebook', 'FACEBOOK' => 'facebook',
            'IG' => 'instagram', 'INSTAGRAM' => 'instagram',
            'YT' => 'youtube', 'YOUTUBE' => 'youtube',
            'TIKTOK' => 'tiktok', 'TT' => 'tiktok',
        ];

        $dailyTotal = array_fill_keys($dates, 0);

        foreach ($raw['data'] ?? [] as $item) {
            $kw  = strtoupper($item['keyword'] ?? '');
            $key = $keywordMap[$kw] ?? strtolower($kw);

            // Filter by media if needed
            if ($media !== 'all' && isset($mediaKeyMap[$media])) {
                if (!in_array($key, $mediaKeyMap[$media])) continue;
            }

            foreach ($item['data'] ?? [] as $pt) {
                $date  = substr((string)($pt['date'] ?? ''), 0, 10);
                $count = (int)($pt['count'] ?? 0);
                if (isset($dailyTotal[$date])) {
                    $dailyTotal[$date] += $count;
                }
            }
        }

        foreach ($dates as $date) {
            $dayTotal = $dailyTotal[$date] ?? 0;
            $trend[] = [
                'date' => $date,
                'neg'  => (int) round($dayTotal * $negRatio),
                'pos'  => (int) round($dayTotal * $posRatio),
                'neu'  => (int) round($dayTotal * $neuRatio),
            ];
        }

    } catch (\Throwable $e) {
        Log::warning('sentimentTotals: trend failed', ['error' => $e->getMessage()]);
    }

    return response()->json([
        'totals'   => $totals,
        'by_media' => $byMedia,
        'trend'    => $trend,
    ]);
}

// ───────────────────────────────────────────────
// API: SENTIMENT BY TIME (WEEKDAY + HOUR)
// GET /mk/api/sentiment/by-time
// ───────────────────────────────────────────────

public function sentimentByTime(Request $request)
{
    $projectId = $request->get('project_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

    if (! $projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    // Get sentiment ratio from sentimentMedia
    $sentimentMedia = [];
    try {
        $raw = $this->mk->sentimentMedia((string) $projectId, $startDate, $endDate);
        $sentimentMedia = $this->normaliseSentimentMedia($raw);
    } catch (\Throwable $e) {
        Log::warning('sentimentByTime: sentimentMedia failed', ['error' => $e->getMessage()]);
    }

    $totalNeg = array_sum(array_column($sentimentMedia, 'negative'));
    $totalPos = array_sum(array_column($sentimentMedia, 'positive'));
    $totalNeu = array_sum(array_column($sentimentMedia, 'neutral'));
    $grandTotal = $totalNeg + $totalPos + $totalNeu;

    $negRatio = $grandTotal > 0 ? $totalNeg / $grandTotal : 0.33;
    $posRatio = $grandTotal > 0 ? $totalPos / $grandTotal : 0.33;
    $neuRatio = $grandTotal > 0 ? $totalNeu / $grandTotal : 0.34;

    $wdLabels = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'];
    $wdTotal  = array_fill(0, 7, 0);

    $keywordMap = [
        'DOC' => 'doc', 'TWIT' => 'twitter', 'TWITTER' => 'twitter',
        'FB' => 'facebook', 'FACEBOOK' => 'facebook',
        'IG' => 'instagram', 'INSTAGRAM' => 'instagram',
        'YT' => 'youtube', 'YOUTUBE' => 'youtube',
        'TIKTOK' => 'tiktok', 'TT' => 'tiktok',
    ];

    // ── Weekday ──
    try {
        $raw = $this->mk->trendsTotal((string) $projectId, $startDate, $endDate);
        foreach ($raw['data'] ?? [] as $item) {
            foreach ($item['data'] ?? [] as $pt) {
                $dateStr = substr((string)($pt['date'] ?? ''), 0, 10);
                $count   = (int)($pt['count'] ?? 0);
                if (!$dateStr || $count === 0) continue;
                try {
                    $dt    = new \DateTime($dateStr);
                    $jsDay = (int)$dt->format('w');
                    $idx   = $jsDay === 0 ? 6 : $jsDay - 1;
                    $wdTotal[$idx] += $count;
                } catch (\Exception $e) { continue; }
            }
        }
    } catch (\Throwable $e) {
        Log::warning('sentimentByTime weekday failed', ['error' => $e->getMessage()]);
    }

    $wdNeg = array_map(fn($v) => (int) round($v * $negRatio), $wdTotal);
    $wdPos = array_map(fn($v) => (int) round($v * $posRatio), $wdTotal);
    $wdNeu = array_map(fn($v) => (int) round($v * $neuRatio), $wdTotal);

    // ── Hour (from cache or sampling) ──
    $hourTotal = array_fill(0, 24, 0);
    $tz        = new \DateTimeZone('Asia/Jakarta');

    $cacheKey = "snt_by_hour_{$projectId}_{$startDate}_{$endDate}";
    $hourTotal = \Illuminate\Support\Facades\Cache::remember($cacheKey, now()->addMinutes(30), function() use ($projectId, $startDate, $endDate, $tz) {
        $hourTotal = array_fill(0, 24, 0);
        try {
            $raw = $this->mk->mentions((string)$projectId, $startDate, $endDate, 0, 23, false, 0, 2000);
            foreach ($raw['data'] ?? (isset($raw[0]) ? $raw : []) as $item) {
                $dateStr = $item['date_created'] ?? $item['date_inserted_dt'] ?? '';
                if (!$dateStr) continue;
                try {
                    $dt = new \DateTime((string)$dateStr, $tz);
                    $hourTotal[(int)$dt->format('H')]++;
                } catch (\Exception $e) { continue; }
            }
        } catch (\Throwable $e) {
            Log::warning('sentimentByTime hour failed', ['error' => $e->getMessage()]);
        }
        return $hourTotal;
    });

    $hourNeg = array_map(fn($v) => (int) round($v * $negRatio), $hourTotal);
    $hourPos = array_map(fn($v) => (int) round($v * $posRatio), $hourTotal);
    $hourNeu = array_map(fn($v) => (int) round($v * $neuRatio), $hourTotal);

    return response()->json([
        'weekday' => [
            'weekdays' => $wdLabels,
            'neg'      => $wdNeg,
            'pos'      => $wdPos,
            'neu'      => $wdNeu,
            'total'    => $wdTotal,
        ],
        'hour' => [
            'hours' => array_map(fn($h) => str_pad($h, 2, '0', STR_PAD_LEFT).':00', range(0, 23)),
            'neg'   => $hourNeg,
            'pos'   => $hourPos,
            'neu'   => $hourNeu,
            'total' => array_values($hourTotal),
        ],
    ]);
}

public function xInteraction(Request $request)
{
    $projectId = $request->get('project_id');
    $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
    $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

    if (!$projectId) {
        return response()->json(['error' => 'project_id required'], 422);
    }

    // ── 1. POSTS (volumeTotal twit) ─────────────────────────────────
    $posts = 0;
    try {
        $vol = $this->mk->volumeTotal((string)$projectId, 'twitter', $startDate, $endDate);
        $posts = (int)($vol['bymedia']['twit'] ?? $vol['all']['total'] ?? 0);
    } catch (\Throwable $e) {
        Log::warning('xInteraction: volumeTotal failed', ['error' => $e->getMessage()]);
    }

    // ── 2. MENTIONS breakdown (Mention / Reply / Retweet) ──────────
    // Dari projectStats dengan tipe volumetotal sudah include tcode breakdown
    // Kita pakai getSentiment yg ada field tcode dari mentions sampling
    $mentionCount  = 0;
    $replyCount    = 0;
    $retweetCount  = 0;

    // Cara cepat: ambil volumeTotal per mention_type dari trendsTotal
    // Fallback: pakai mentions sampling 500 rows
    try {
        // Sample 500 rows — cukup untuk estimasi distribusi
        $raw = $this->mk->mentions(
            (string)$projectId,
            $startDate,
            $endDate,
            0, 23,
            false, // without content (lebih cepat)
            0,
            500
        );

        $items = $raw['data'] ?? (isset($raw[0]) ? $raw : []);

        $views     = 0;
        $favorites = 0;
        $retweets  = 0;

        foreach ($items as $item) {
            if (!is_array($item)) continue;

            // Filter hanya Twitter
            $media = strtolower($item['media_type'] ?? $item['tcode_media'] ?? $item['media_type_id'] ?? '');
            // media_type_id 5 = Twitter di MediaKernels
            $mediaTypeId = (int)($item['media_type_id'] ?? 0);
            if ($media && !in_array($media, ['twit','twitter','5']) && $mediaTypeId !== 5) {
                continue;
            }

            $tcode = strtolower($item['tcode'] ?? $item['mention_type'] ?? '');

            if (str_contains($tcode, 'rt') || str_contains($tcode, 'retweet')) {
                $retweetCount++;
            } elseif (str_contains($tcode, 'rep') || str_contains($tcode, 'reply')) {
                $replyCount++;
            } else {
                $mentionCount++;
            }

            // Interaction metrics
            $views     += (int)($item['num_views']    ?? 0);
            $favorites += (int)($item['num_favourited'] ?? $item['num_likes'] ?? 0);
            $retweets  += (int)($item['num_retweeted'] ?? $item['num_shares'] ?? 0);
        }

        Log::info('xInteraction mentions sample', [
            'total_sampled' => count($items),
            'mention'   => $mentionCount,
            'reply'     => $replyCount,
            'retweet'   => $retweetCount,
            'views'     => $views,
            'favorites' => $favorites,
            'retweets_field' => $retweets,
        ]);

    } catch (\Throwable $e) {
        Log::warning('xInteraction: mentions sampling failed', ['error' => $e->getMessage()]);
    }

    // ── 3. VIEWS dari mostStatus (lebih akurat) ────────────────────
    $totalViews     = 0;
    $totalRetweets  = 0;
    $totalFavorites = 0;

    try {
        // mostStatus return top posts by view — sum view_cnt
        if (method_exists($this->mk, 'mostStatus')) {
            $statusRaw = $this->mk->mostStatus(
                (string)$projectId,
                'twitter',
                $startDate,
                $endDate,
                0, 23,
                100,
                'postbyview'
            );
            foreach ((is_array($statusRaw) ? $statusRaw : []) as $s) {
                $totalViews += (int)($s['view_cnt'] ?? $s['freq'] ?? 0);
            }
        }
    } catch (\Throwable $e) {
        Log::warning('xInteraction: mostStatus views failed', ['error' => $e->getMessage()]);
    }

    // Coba ambil dari publisherStats sebagai fallback views
    if ($totalViews === 0) {
        $totalViews = $views ?? 0; // dari sampling di atas
    }

    // ── 4. RETWEETS dari mostRetweets ─────────────────────────────
    try {
        if (method_exists($this->mk, 'mostRetweets')) {
            $rtRaw = $this->mk->mostRetweets(
                (string)$projectId,
                $startDate,
                $endDate
            );
            foreach ((is_array($rtRaw) ? $rtRaw : []) as $r) {
                $totalRetweets += (int)($r['freq'] ?? $r['sentiment_freq'] ?? 0);
            }
        }
    } catch (\Throwable $e) {
        Log::warning('xInteraction: mostRetweets failed', ['error' => $e->getMessage()]);
    }

    if ($totalRetweets === 0) $totalRetweets = $retweets ?? 0;

    // ── 5. FAVORITES dari mentions sampling ───────────────────────
    $totalFavorites = $favorites ?? 0;

    // ── 6. TOTAL INTERACTION ──────────────────────────────────────
    // Sesuai Drone Emprit: Posts + Views + Retweets + Favorites
    $totalInteraction = $posts + $totalViews + $totalRetweets + $totalFavorites;

    // ── 7. INTERACTION RATE ───────────────────────────────────────
    $interactionRate = $posts > 0
        ? round(($totalViews + $totalRetweets + $totalFavorites) / $posts, 2)
        : 0;

    // ── 8. MENTION BREAKDOWN total (gunakan posts sebagai total) ──
    // Jika sampling tidak cukup, estimasi dari volumeTotal
    if ($mentionCount + $replyCount + $retweetCount === 0) {
        $mentionCount = $posts; // fallback
    }

    $mentionTotal = $mentionCount + $replyCount + $retweetCount;

    // ── 9. TREND HARIAN dari trendsTotal ──────────────────────────
    $trendDays = [];
    try {
        $trendsRaw = $this->mk->trendsTotal((string)$projectId, $startDate, $endDate);

        foreach ($trendsRaw['data'] ?? [] as $item) {
            $kw = strtoupper($item['keyword'] ?? '');
            if (!in_array($kw, ['TWIT','TWITTER'])) continue;

            foreach ($item['data'] ?? [] as $pt) {
                $date  = substr((string)($pt['date'] ?? ''), 0, 10);
                $count = (int)($pt['count'] ?? 0);
                if ($date) {
                    $trendDays[$date] = ($trendDays[$date] ?? 0) + $count;
                }
            }
        }
        ksort($trendDays);
    } catch (\Throwable $e) {
        Log::warning('xInteraction: trendsTotal failed', ['error' => $e->getMessage()]);
    }

    $trendChart = array_map(
        fn($d, $c) => ['date' => $d, 'count' => $c],
        array_keys($trendDays),
        array_values($trendDays)
    );

    Log::info('xInteraction final', [
        'posts'            => $posts,
        'views'            => $totalViews,
        'retweets'         => $totalRetweets,
        'favorites'        => $totalFavorites,
        'total'            => $totalInteraction,
        'interaction_rate' => $interactionRate,
        'mention'          => $mentionCount,
        'reply'            => $replyCount,
        'retweet_count'    => $retweetCount,
        'trend_days'       => count($trendChart),
    ]);

    return response()->json([
        // ── Mentions section (kiri atas Drone Emprit) ──
        'mentions' => [
            'mention' => $mentionCount,
            'reply'   => $replyCount,
            'retweet' => $retweetCount,
            'total'   => $mentionTotal,
        ],

        // ── Interaction section (kanan atas Drone Emprit) ──
        'interaction' => [
            'posts'             => $posts,
            'views'             => $totalViews,
            'retweets'          => $totalRetweets,
            'favorites'         => $totalFavorites,
            'total'             => $totalInteraction,
            'interaction_rate'  => $interactionRate,
        ],

        // ── Trend harian ──
        'trend' => $trendChart,
    ]);
}

public function interactionSentimentPage(Request $request)
{
    return view('mk.interaction-sentiment');
}

public function engagementPage(Request $request)
  {
      return view('mk.engagement');
  }

  // ───────────────────────────────────────────────────────────────────
    // API: INTERACTION SENTIMENT TOTALS
    // GET /mk/api/sentiment/interaction-totals
    //
    // Berbeda dari sentimentTotals() yang hitung JUMLAH DOKUMEN,
    // method ini menjumlahkan ACTUAL INTERACTIONS (views + retweets + likes)
    // per sentiment — mirip cara Drone Emprit menghitung.
    //
    // Alur:
    // 1. Ambil sentimentMedia() → tahu ratio neg/pos/neu per platform
    // 2. Untuk setiap platform sosmed, ambil mostStatus() top 100 posts
    //    lalu sum view_cnt + retweet_cnt + favorite_cnt
    // 3. Untuk mass media (doc), pakai estReach() sebagai proxy interaction
    // 4. Distribusikan total interaction ke neg/pos/neu berdasarkan ratio
    // 5. Return totals + by_media + trend (sama struktur sentimentTotals)
    // ───────────────────────────────────────────────────────────────────

    public function interactionSentimentTotals(Request $request)
    {
        $projectId = $request->get('project_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate   = $request->get('end_date',   now()->format('Y-m-d'));

        if (! $projectId) {
            return response()->json(['error' => 'project_id required'], 422);
        }

        // ── 1. Sentiment ratio per media (untuk distribusi neg/pos/neu) ──
        $sentimentMedia = [];
        try {
            $raw = $this->mk->sentimentMedia((string) $projectId, $startDate, $endDate);
            $sentimentMedia = $this->normaliseSentimentMedia($raw);
        } catch (\Throwable $e) {
            Log::warning('interactionSentimentTotals: sentimentMedia failed', ['error' => $e->getMessage()]);
        }

        // Build ratio map per media key
        $ratioMap = [];
        foreach ($sentimentMedia as $m) {
            $total = $m['positive'] + $m['negative'] + $m['neutral'];
            $ratioMap[strtolower($m['media'])] = [
                'pos' => $total > 0 ? $m['positive'] / $total : 0.33,
                'neg' => $total > 0 ? $m['negative'] / $total : 0.33,
                'neu' => $total > 0 ? $m['neutral']  / $total : 0.34,
            ];
        }

        // Overall ratio fallback
        $totalDoc = array_sum(array_column($sentimentMedia, 'positive'))
                  + array_sum(array_column($sentimentMedia, 'negative'))
                  + array_sum(array_column($sentimentMedia, 'neutral'));

        $globalRatio = [
            'pos' => $totalDoc > 0 ? array_sum(array_column($sentimentMedia, 'positive')) / $totalDoc : 0.33,
            'neg' => $totalDoc > 0 ? array_sum(array_column($sentimentMedia, 'negative')) / $totalDoc : 0.33,
            'neu' => $totalDoc > 0 ? array_sum(array_column($sentimentMedia, 'neutral'))  / $totalDoc : 0.34,
        ];

        // ── 2. Hitung interaction per platform ──
        //
        // Platform sosmed: sum dari mostStatus() top 100 posts
        //   - Twitter  : view_cnt + retweet_cnt + favorite_cnt
        //   - Facebook : like_cnt + comment_cnt + share_cnt
        //   - Instagram: like_cnt + comment_cnt
        //   - YouTube  : view_cnt + like_cnt + comment_cnt
        //   - TikTok   : like_cnt + comment_cnt + share_cnt
        // Platform mass media: estReach() sebagai proxy
        //
        $platformConfig = [
            'twitter'   => ['media' => 'twitter',  'type' => 'social', 'fields' => ['view_cnt','retweet_cnt','favorite_cnt','freq']],
            'facebook'  => ['media' => 'fb',        'type' => 'social', 'fields' => ['like_cnt','comment_cnt','share_cnt','freq']],
            'instagram' => ['media' => 'instagram', 'type' => 'social', 'fields' => ['like_cnt','comment_cnt','freq']],
            'youtube'   => ['media' => 'youtube',   'type' => 'social', 'fields' => ['view_cnt','like_cnt','comment_cnt','freq']],
            'tiktok'    => ['media' => 'tiktok',    'type' => 'social', 'fields' => ['like_cnt','comment_cnt','share_cnt','freq']],
            'doc'       => ['media' => 'doc',       'type' => 'mass',   'fields' => []],
        ];

        $interactionByPlatform = [];

        foreach ($platformConfig as $key => $cfg) {

            $totalInteraction = 0;

            if ($cfg['type'] === 'mass') {
                // Mass media: gunakan estReach sebagai proxy
                try {
                    $raw = $this->mk->estReach((string) $projectId, 'doc', $startDate, $endDate);
                    $totalInteraction = $this->normaliseEstReach($raw);
                } catch (\Throwable $e) {
                    Log::warning("interactionSentimentTotals: estReach[doc] failed", ['error' => $e->getMessage()]);
                }

            } else {
                // Sosmed: sum interaction fields dari mostStatus() top 100
                // Coba 3 sub-type dan ambil yang terbesar (views biasanya paling besar)
                $subTypes = $this->getMostStatusSubTypes($key);

                foreach ($subTypes as $sub) {
                    try {
                        $posts = $this->mk->mostStatus(
                            (string) $projectId,
                            $cfg['media'],
                            $startDate,
                            $endDate,
                            0, 23,
                            100,
                            $sub
                        );

                        $subTotal = 0;
                        foreach ($posts as $post) {
                            if (!is_array($post)) continue;
                            foreach ($cfg['fields'] as $field) {
                                $subTotal += (int)($post[$field] ?? 0);
                            }
                        }

                        // Ambil nilai tertinggi dari semua sub-type
                        if ($subTotal > $totalInteraction) {
                            $totalInteraction = $subTotal;
                        }

                        Log::info("interactionSentimentTotals: mostStatus[$key][$sub]", [
                            'posts_count' => count($posts),
                            'sub_total'   => $subTotal,
                        ]);

                    } catch (\Throwable $e) {
                        Log::warning("interactionSentimentTotals: mostStatus[$key][$sub] failed", [
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            }

            // Distribusikan ke neg/pos/neu berdasarkan ratio sentiment platform ini
            $mediaKeys = $this->getMediaAliases($key);
            $ratio = $globalRatio; // default
            foreach ($mediaKeys as $alias) {
                if (isset($ratioMap[$alias])) {
                    $ratio = $ratioMap[$alias];
                    break;
                }
            }

            $interactionByPlatform[$key] = [
                'key'   => $key,
                'label' => $this->getPlatformLabel($key),
                'total' => $totalInteraction,
                'neg'   => (int) round($totalInteraction * $ratio['neg']),
                'pos'   => (int) round($totalInteraction * $ratio['pos']),
                'neu'   => (int) round($totalInteraction * $ratio['neu']),
            ];
        }

        // ── 3. Grand totals ──
        $grandNeg = array_sum(array_column($interactionByPlatform, 'neg'));
        $grandPos = array_sum(array_column($interactionByPlatform, 'pos'));
        $grandNeu = array_sum(array_column($interactionByPlatform, 'neu'));
        $grandTotal = $grandNeg + $grandPos + $grandNeu;

        // ── 4. Trend harian (sama seperti sentimentTotals, pakai ratio) ──
        $trend = [];
        try {
            $raw = $this->mk->trendsTotal((string) $projectId, $startDate, $endDate);

            $dates   = [];
            $current = new \DateTime($startDate);
            $end     = new \DateTime($endDate);
            while ($current <= $end) {
                $dates[] = $current->format('Y-m-d');
                $current->modify('+1 day');
            }

            $keywordMap = [
                'DOC' => 'doc', 'TWIT' => 'twitter', 'TWITTER' => 'twitter',
                'FB' => 'facebook', 'FACEBOOK' => 'facebook',
                'IG' => 'instagram', 'INSTAGRAM' => 'instagram',
                'YT' => 'youtube', 'YOUTUBE' => 'youtube',
                'TIKTOK' => 'tiktok', 'TT' => 'tiktok',
            ];

            // Hitung interaction multiplier per platform
            // (ratio interaction vs mention count — biar trend lebih realistis)
            $mentionTotals = [];
            foreach ($sentimentMedia as $m) {
                $mentionTotals[strtolower($m['media'])] = $m['positive'] + $m['negative'] + $m['neutral'];
            }

            $dailyMentions = array_fill_keys($dates, 0);
            foreach ($raw['data'] ?? [] as $item) {
                $kw  = strtoupper($item['keyword'] ?? '');
                $key = $keywordMap[$kw] ?? strtolower($kw);

                // Multiplier: interaction total / mention count untuk platform ini
                $mentionCount = 0;
                foreach ($this->getMediaAliases($key) as $alias) {
                    if (isset($mentionTotals[$alias])) {
                        $mentionCount = $mentionTotals[$alias];
                        break;
                    }
                }
                $platInteraction = $interactionByPlatform[$key]['total'] ?? 0;
                $multiplier = ($mentionCount > 0) ? ($platInteraction / $mentionCount) : 1;

                foreach ($item['data'] ?? [] as $pt) {
                    $date  = substr((string)($pt['date'] ?? ''), 0, 10);
                    $count = (int)($pt['count'] ?? 0);
                    if (isset($dailyMentions[$date])) {
                        $dailyMentions[$date] += (int)round($count * $multiplier);
                    }
                }
            }

            $dayGrandTotal = array_sum($dailyMentions) ?: 1;
            $negRatio = $grandTotal > 0 ? $grandNeg / $grandTotal : $globalRatio['neg'];
            $posRatio = $grandTotal > 0 ? $grandPos / $grandTotal : $globalRatio['pos'];
            $neuRatio = $grandTotal > 0 ? $grandNeu / $grandTotal : $globalRatio['neu'];

            foreach ($dates as $date) {
                $dayTotal = $dailyMentions[$date] ?? 0;
                $trend[] = [
                    'date' => $date,
                    'neg'  => (int) round($dayTotal * $negRatio),
                    'pos'  => (int) round($dayTotal * $posRatio),
                    'neu'  => (int) round($dayTotal * $neuRatio),
                ];
            }

        } catch (\Throwable $e) {
            Log::warning('interactionSentimentTotals: trend failed', ['error' => $e->getMessage()]);
        }

        // ── 5. By media formatted untuk frontend ──
        $byMedia = array_values($interactionByPlatform);

        Log::info('interactionSentimentTotals complete', [
            'project_id' => $projectId,
            'grand_neg'  => $grandNeg,
            'grand_pos'  => $grandPos,
            'grand_neu'  => $grandNeu,
            'grand_total'=> $grandTotal,
            'by_platform'=> array_map(fn($p) => "{$p['key']}={$p['total']}", $byMedia),
        ]);

        return response()->json([
            'totals' => [
                'neg'   => $grandNeg,
                'pos'   => $grandPos,
                'neu'   => $grandNeu,
                'total' => $grandTotal,
            ],
            'by_media' => $byMedia,
            'trend'    => $trend,
        ]);
    }

    // ── Helper: sub-type mostStatus per platform ──
    private function getMostStatusSubTypes(string $platform): array
    {
        return match($platform) {
            'twitter'   => ['postbyview', 'postbyretweet', 'postbyfavorite'],
            'facebook'  => ['fblike', 'fbcomment', 'fbshare'],
            'instagram' => ['postbylike', 'postbycomment'],
            'youtube'   => ['postbyview', 'postbylike', 'postbycomment'],
            'tiktok'    => ['postbylike', 'postbycomment', 'postbyshare'],
            default     => ['postbyview'],
        };
    }

    // ── Helper: alias media key ──
    private function getMediaAliases(string $key): array
    {
        return match($key) {
            'twitter'   => ['twit', 'twitter'],
            'facebook'  => ['fb', 'facebook'],
            'instagram' => ['ig', 'instagram'],
            'youtube'   => ['yt', 'youtube'],
            'tiktok'    => ['tiktok'],
            'doc'       => ['doc'],
            default     => [$key],
        };
    }

    // ── Helper: label platform ──
    private function getPlatformLabel(string $key): string
    {
        return match($key) {
            'twitter'   => 'X / Twitter',
            'facebook'  => 'Facebook',
            'instagram' => 'Instagram',
            'youtube'   => 'YouTube',
            'tiktok'    => 'TikTok',
            'doc'       => 'Mass Media',
            default     => ucfirst($key),
        };
    }
}