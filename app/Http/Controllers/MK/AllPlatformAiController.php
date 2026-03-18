<?php

namespace App\Http\Controllers\MK;

use App\Http\Controllers\Controller;
use App\Services\MediaKernelsClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AllPlatformAiController extends Controller
{
    protected MediaKernelsClient $client;

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

        return array_values(array_filter($allProjects, function ($project) use ($assignedProjectIds) {
            return in_array($project['id'] ?? null, $assignedProjectIds);
        }));
    }

    /* ─── Page ─── */
    public function page(Request $request)
    {
        try {
            $projects  = $this->getAllProjects();
            $projectId = $request->query('project_id');

            if (!$projectId && count($projects) > 0) {
                $projectId = $projects[0]['id'] ?? null;
                if ($projectId) {
                    return redirect()->route('mk.all-ai-analysis', [
                        'project_id' => $projectId,
                        'start_date' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                        'end_date'   => $request->query('end_date', now()->format('Y-m-d')),
                    ]);
                }
            }

            return view('mk.ai.all-platform')->with([
                'projectId' => $projectId,
                'startDate' => $request->query('start_date', now()->subDays(6)->format('Y-m-d')),
                'endDate'   => $request->query('end_date', now()->format('Y-m-d')),
                'projects'  => $projects,
            ]);
        } catch (\Exception $e) {
            Log::error('All-platform AI page error', ['error' => $e->getMessage()]);
            return view('mk.ai.all-platform')->with([
                'projectId' => null,
                'startDate' => now()->subDays(6)->format('Y-m-d'),
                'endDate'   => now()->format('Y-m-d'),
                'projects'  => [],
            ]);
        }
    }

    /* ─── Data endpoint — fetches ALL platforms concurrently ─── */
    public function data(Request $request)
    {
        try {
            $projectId = $request->query('project_id');
            $startDate = $request->query('start_date');
            $endDate   = $request->query('end_date');

            if (!$projectId) {
                return response()->json(['success' => false, 'error' => 'Project ID required'], 400);
            }

            // Fetch data from all platforms concurrently
            // Keep it light: only top 15-20 items per platform
            $newsData    = $this->fetchNews($projectId, $startDate, $endDate);
            $twitterData = $this->fetchTwitter($projectId, $startDate, $endDate);
            $fbData      = $this->fetchFacebook($projectId, $startDate, $endDate);
            $igData      = $this->fetchInstagram($projectId, $startDate, $endDate);
            $ytData      = $this->fetchYoutube($projectId, $startDate, $endDate);
            $ttData      = $this->fetchTiktok($projectId, $startDate, $endDate);

            // Sentiment totals
            $sentimentRaw = $this->client->sentimentTotal($projectId, $startDate, $endDate);
            $sentiment    = $this->parseSentimentAll($sentimentRaw);

            // Build combined dataset
            $lines = $this->buildDataset(
                $projectId, $startDate, $endDate, $sentiment,
                $newsData, $twitterData, $fbData, $igData, $ytData, $ttData
            );

            return response()->json([
                'success' => true,
                'data'    => [
                    'dataset' => implode("\n", $lines),
                    'summary' => [
                        'news'      => count($newsData),
                        'twitter'   => count($twitterData),
                        'facebook'  => count($fbData),
                        'instagram' => count($igData),
                        'youtube'   => count($ytData),
                        'tiktok'    => count($ttData),
                        'sentiment' => $sentiment,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('All-platform AI data error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

    /* ─── Proxy (reuses Gemini logic) ─── */
    public function proxy(Request $request)
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
                    $response = Http::withHeaders(['Content-Type' => 'application/json'])
                        ->timeout(60)
                        ->post($endpoint, [
                            'contents'         => $contents,
                            'generationConfig' => [
                                'maxOutputTokens' => $maxTokens,
                                'temperature'     => 0.7,
                            ],
                        ]);

                    if (in_array($response->status(), [429, 404])) continue;
                    if ($response->failed()) continue;

                    $data = $response->json();
                    $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

                    if (!empty($text)) {
                        $usedModel = $model;
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (empty($text)) {
                return response()->json(['error' => 'Semua model Gemini sedang tidak tersedia atau quota habis.'], 429);
            }

            return response()->json([
                'content' => [['type' => 'text', 'text' => $text]],
                'model'   => $usedModel,
            ]);
        } catch (\Exception $e) {
            Log::error('All-platform AI proxy error', ['error' => $e->getMessage()]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /* ════════════════════════════════════════════════════════
       PRIVATE FETCHERS — lightweight, max 15 items each
    ════════════════════════════════════════════════════════ */

    private function fetchNews(string $pid, ?string $sd, ?string $ed): array
    {
        try {
            $raw = $this->client->articles($pid, 'doc', $sd, $ed, 0, 23, 0, 15, true);
            $items = is_array($raw) ? $raw : ($raw['data'] ?? []);
            $result = [];
            foreach (array_slice($items, 0, 15) as $a) {
                if (!is_array($a)) continue;
                $result[] = [
                    'title'     => substr(strip_tags($a['title'] ?? ''), 0, 120),
                    'publisher' => substr($a['publisher'] ?? $a['hostname'] ?? '', 0, 40),
                    'date'      => substr($a['date_created'] ?? '', 0, 10),
                    'sentiment' => $this->normSent($a),
                    'content'   => substr(strip_tags($a['content'] ?? ''), 0, 150),
                ];
            }
            return $result;
        } catch (\Exception $e) { return []; }
    }

    private function fetchTwitter(string $pid, ?string $sd, ?string $ed): array
    {
        try {
            $raw = $this->client->mostStatus($pid, 'twitter', $sd, $ed, 0, 23, 15, 'postbyview');
            $items = is_array($raw) ? $raw : ($raw['data'] ?? []);
            $result = [];
            foreach (array_slice($items, 0, 15) as $item) {
                if (!is_array($item)) continue;
                $tcode = strtolower($item['tcode'] ?? '');
                if ($tcode && !in_array($tcode, ['twit', 'twitter', 'tw-view', 'tw-retweet', 'tw-like', 'tw-reply', ''])) continue;
                $author = $item['author']['scr_name'] ?? $item['name'] ?? '';
                $result[] = [
                    'author'    => $author,
                    'content'   => substr(strip_tags($item['content'] ?? ''), 0, 150),
                    'views'     => (int) ($item['view_cnt'] ?? $item['freq'] ?? 0),
                    'rt'        => (int) ($item['rt'] ?? 0),
                    'date'      => substr($item['date_created'] ?? '', 0, 10),
                    'sentiment' => $this->normSent($item),
                ];
            }
            return $result;
        } catch (\Exception $e) { return []; }
    }

    private function fetchFacebook(string $pid, ?string $sd, ?string $ed): array
    {
        try {
            $raw = $this->client->fbTopStatus($pid, $sd, $ed, 'fblike', 0, 23, 15);
            $items = is_array($raw) ? $raw : ($raw['data'] ?? []);
            $result = [];
            foreach (array_slice($items, 0, 15) as $item) {
                if (!is_array($item)) continue;
                $result[] = [
                    'author'    => $item['from_name'] ?? $item['page_name'] ?? '',
                    'content'   => substr(strip_tags($item['content'] ?? $item['message'] ?? ''), 0, 150),
                    'likes'     => (int) ($item['likes'] ?? $item['freq'] ?? 0),
                    'shares'    => (int) ($item['shares'] ?? 0),
                    'comments'  => (int) ($item['comments'] ?? 0),
                    'date'      => substr($item['date_created'] ?? '', 0, 10),
                    'sentiment' => $this->normSent($item),
                ];
            }
            return $result;
        } catch (\Exception $e) { return []; }
    }

    private function fetchInstagram(string $pid, ?string $sd, ?string $ed): array
    {
        try {
            $raw = $this->client->igTopStatus($pid, $sd, $ed, 'postbylike', 0, 23, 15);
            $items = is_array($raw) ? $raw : ($raw['data'] ?? []);
            $result = [];
            foreach (array_slice($items, 0, 15) as $item) {
                if (!is_array($item)) continue;
                $result[] = [
                    'author'    => $item['username'] ?? $item['user_name'] ?? '',
                    'content'   => substr(strip_tags($item['content'] ?? $item['caption'] ?? ''), 0, 150),
                    'likes'     => (int) ($item['num_likes'] ?? $item['likes'] ?? 0),
                    'comments'  => (int) ($item['num_comments'] ?? $item['comments'] ?? 0),
                    'date'      => substr($item['date_created'] ?? '', 0, 10),
                    'sentiment' => $this->normSent($item),
                ];
            }
            return $result;
        } catch (\Exception $e) { return []; }
    }

    private function fetchYoutube(string $pid, ?string $sd, ?string $ed): array
    {
        try {
            $raw = $this->client->ytbTopStatus($pid, $sd, $ed, 'postbyview', 0, 23, 15);
            $items = is_array($raw) ? $raw : ($raw['data'] ?? []);
            $result = [];
            foreach (array_slice($items, 0, 15) as $item) {
                if (!is_array($item)) continue;
                $result[] = [
                    'channel'   => $item['channel_title'] ?? $item['channel_name'] ?? '',
                    'title'     => substr(strip_tags($item['title'] ?? $item['content'] ?? ''), 0, 120),
                    'views'     => (int) ($item['num_views'] ?? $item['view_cnt'] ?? 0),
                    'likes'     => (int) ($item['num_likes'] ?? $item['likes'] ?? 0),
                    'comments'  => (int) ($item['num_comments'] ?? $item['comments'] ?? 0),
                    'date'      => substr($item['date_created'] ?? '', 0, 10),
                    'sentiment' => $this->normSent($item),
                ];
            }
            return $result;
        } catch (\Exception $e) { return []; }
    }

    private function fetchTiktok(string $pid, ?string $sd, ?string $ed): array
    {
        try {
            $raw = $this->client->tiktokTopStatus($pid, $sd, $ed, 'postbylike', 0, 23, 15);
            $items = is_array($raw) ? $raw : ($raw['data'] ?? []);
            $result = [];
            foreach (array_slice($items, 0, 15) as $item) {
                if (!is_array($item)) continue;
                $result[] = [
                    'author'    => $item['author_nickname'] ?? $item['nickname'] ?? '',
                    'content'   => substr(strip_tags($item['content'] ?? $item['desc'] ?? ''), 0, 150),
                    'views'     => (int) ($item['play_count'] ?? $item['num_views'] ?? $item['freq'] ?? 0),
                    'likes'     => (int) ($item['digg_count'] ?? $item['num_likes'] ?? $item['likes'] ?? 0),
                    'comments'  => (int) ($item['comment_count'] ?? $item['num_comments'] ?? 0),
                    'shares'    => (int) ($item['share_count'] ?? $item['shares'] ?? 0),
                    'date'      => substr($item['date_created'] ?? '', 0, 10),
                    'sentiment' => $this->normSent($item),
                ];
            }
            return $result;
        } catch (\Exception $e) { return []; }
    }

    /* ════════════════════════════════════════════════════════
       HELPERS
    ════════════════════════════════════════════════════════ */

    private function normSent(array $item): string
    {
        $s = strtolower($item['sentiment_str'] ?? $item['sentiment'] ?? $item['class_sentiment'] ?? '');
        if (str_contains($s, 'pos') || $s === '1') return 'positive';
        if (str_contains($s, 'neg') || $s === '-1' || $s === '2') return 'negative';
        return 'neutral';
    }

    private function parseSentimentAll(mixed $raw): array
    {
        $pos = 0; $neg = 0; $neu = 0;
        if (isset($raw['pos'], $raw['neg'], $raw['net'])) {
            $pos = (int) $raw['pos'];
            $neg = (int) $raw['neg'];
            $neu = (int) $raw['net'];
        } elseif (isset($raw['bymedia'])) {
            foreach ($raw['bymedia'] as $media => $d) {
                $pos += (int) ($d['pos'] ?? 0);
                $neg += (int) ($d['neg'] ?? 0);
                $neu += (int) ($d['net'] ?? 0);
            }
        }
        return compact('pos', 'neg', 'neu');
    }

    private function buildDataset(
        string $pid, ?string $sd, ?string $ed, array $sentiment,
        array $news, array $twitter, array $fb, array $ig, array $yt, array $tt
    ): array {
        $total = ($sentiment['pos'] + $sentiment['neg'] + $sentiment['neu']) ?: 1;
        $pPos  = round($sentiment['pos'] / $total * 100);
        $pNeg  = round($sentiment['neg'] / $total * 100);
        $pNeu  = round($sentiment['neu'] / $total * 100);

        $lines = [];
        $lines[] = "=== DATA SEMUA PLATFORM — PROJECT {$pid} ===";
        $lines[] = "Periode: {$sd} s/d {$ed}";
        $lines[] = "Platform yang dicakup: News, X/Twitter, Facebook, Instagram, YouTube, TikTok";
        $lines[] = "Sentimen Global: Positif {$pPos}% ({$sentiment['pos']}) | Negatif {$pNeg}% ({$sentiment['neg']}) | Netral {$pNeu}% ({$sentiment['neu']})";
        $lines[] = "Data per platform: News=" . count($news) . ", Twitter=" . count($twitter)
            . ", Facebook=" . count($fb) . ", Instagram=" . count($ig)
            . ", YouTube=" . count($yt) . ", TikTok=" . count($tt);

        // News
        if (!empty($news)) {
            $lines[] = "\n--- ONLINE NEWS (" . count($news) . " artikel) ---";
            foreach ($news as $i => $a) {
                $lines[] = "[N" . ($i+1) . "] \"{$a['title']}\" | {$a['publisher']} | {$a['date']} | {$a['sentiment']}";
                if ($a['content']) $lines[] = "   \"{$a['content']}\"";
            }
        }

        // Twitter
        if (!empty($twitter)) {
            $lines[] = "\n--- X / TWITTER (" . count($twitter) . " tweets) ---";
        foreach ($twitter as $i => $t) {
                $lines[] = "[T" . ($i+1) . "] @{$t['author']} | {$t['views']} views, {$t['rt']} RT | {$t['date']} | {$t['sentiment']}";
                if ($t['content']) $lines[] = "   \"{$t['content']}\"";
            }
        }

        // Facebook
        if (!empty($fb)) {
            $lines[] = "\n--- FACEBOOK (" . count($fb) . " posts) ---";
            foreach ($fb as $i => $p) {
                $lines[] = "[F" . ($i+1) . "] {$p['author']} | {$p['likes']}L {$p['shares']}S {$p['comments']}C | {$p['date']} | {$p['sentiment']}";
                if ($p['content']) $lines[] = "   \"{$p['content']}\"";
            }
        }

        // Instagram
        if (!empty($ig)) {
            $lines[] = "\n--- INSTAGRAM (" . count($ig) . " posts) ---";
            foreach ($ig as $i => $p) {
                $lines[] = "[I" . ($i+1) . "] @{$p['author']} | {$p['likes']}L {$p['comments']}C | {$p['date']} | {$p['sentiment']}";
                if ($p['content']) $lines[] = "   \"{$p['content']}\"";
            }
        }

        // YouTube
        if (!empty($yt)) {
            $lines[] = "\n--- YOUTUBE (" . count($yt) . " videos) ---";
            foreach ($yt as $i => $v) {
                $lines[] = "[Y" . ($i+1) . "] {$v['channel']} | \"{$v['title']}\" | {$v['views']}V {$v['likes']}L {$v['comments']}C | {$v['date']} | {$v['sentiment']}";
            }
        }

        // TikTok
        if (!empty($tt)) {
            $lines[] = "\n--- TIKTOK (" . count($tt) . " posts) ---";
            foreach ($tt as $i => $p) {
                $lines[] = "[K" . ($i+1) . "] {$p['author']} | {$p['views']}V {$p['likes']}L {$p['comments']}C {$p['shares']}S | {$p['date']} | {$p['sentiment']}";
                if ($p['content']) $lines[] = "   \"{$p['content']}\"";
            }
        }

        $lines[] = "\n=== AKHIR DATASET ===";
        return $lines;
    }
}
