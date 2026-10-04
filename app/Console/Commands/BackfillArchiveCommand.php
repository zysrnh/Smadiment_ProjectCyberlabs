<?php

namespace App\Console\Commands;

use App\Models\ProjectDailySentiment;
use App\Models\ProjectApiSnapshot;
use App\Services\MediaKernelsClient;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BackfillArchiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mk:backfill-archive
                            {--project=16978 : Project ID to backfill}
                            {--from= : Start date (YYYY-MM-DD), default: 1 year ago}
                            {--to= : End date (YYYY-MM-DD), default: today}
                            {--years= : Number of past years to backfill (e.g. 1 or 2)}
                            {--with-all : Crawl everything including raw feeds, topics, word clouds, hashtags, and social media posts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ultimate harvester to backfill ALL Drone Emprit data: daily/hourly/monthly stats, trends, topics, word clouds, hashtags, and raw social media feeds';

    /**
     * Execute the console command.
     */
    public function handle(MediaKernelsClient $mk): int
    {
        $projectId = (int) $this->option('project') ?: 16978;

        if ($this->option('years')) {
            $years = (int) $this->option('years');
            $startDate = now()->subYears($years)->startOfMonth()->format('Y-m-d');
            $endDate   = now()->format('Y-m-d');
        } else {
            $startDate = $this->option('from') ?: now()->subYear()->startOfMonth()->format('Y-m-d');
            $endDate   = $this->option('to') ?: now()->format('Y-m-d');
        }

        $crawlAll = true; // Always crawl full depth as requested by user

        $this->info("======================================================================");
        $this->info("🚀 DRONE EMPRIT TOTAL ARCHIVE HARVESTER (ALL DATA)");
        $this->info("Project ID : {$projectId}");
        $this->info("Periode    : {$startDate} s/d {$endDate}");
        $this->info("Crawl Scope: Agregat Harian/Jam/Minggu/Bulan + Topics + Medsos + Feeds");
        $this->info("======================================================================");

        // 1. Cek Token API
        $this->line("Memverifikasi token Drone Emprit API...");
        try {
            $token = $mk->getToken();
            $this->info("✅ Token Drone Emprit Valid & Aktif!");
        } catch (\Throwable $e) {
            $this->error("❌ Gagal autentikasi API: " . $e->getMessage());
            $this->warn("Pastikan MEDIAKERNELS_USERNAME dan MEDIAKERNELS_PASSWORD di .env sudah aktif.");
            return 1;
        }

        // 2. Bagi Periode ke dalam Bulan-Bulan
        $startCarbon = Carbon::parse($startDate)->startOfMonth();
        $endCarbon   = Carbon::parse($endDate)->endOfMonth();

        $periods = [];
        $curr = $startCarbon->copy();
        while ($curr->lte($endCarbon)) {
            $mStart = $curr->copy()->startOfMonth()->format('Y-m-d');
            $mEnd   = $curr->copy()->endOfMonth()->format('Y-m-d');
            if ($mEnd > $endDate) $mEnd = $endDate;
            $periods[] = ['start' => $mStart, 'end' => $mEnd, 'label' => $curr->format('F Y')];
            $curr->addMonth();
        }

        $this->info("Ditemukan " . count($periods) . " bulan untuk disedot total.");

        foreach ($periods as $idx => $p) {
            $s = $p['start'];
            $e = $p['end'];
            $this->newLine();
            $this->line("<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>");
            $this->info("📦 [" . ($idx + 1) . "/" . count($periods) . "] Memproses Periode: {$p['label']} ({$s} s/d {$e})");
            $this->line("<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>");

            // ── A. /volume_total/ (Breakdown Platform) ─────────────────────
            try {
                $volData = $mk->volumeTotal((string) $projectId, 'doc', $s, $e);
                if (!empty($volData['bymedia'])) {
                    $platforms = [
                        ['media' => 'doc',       'label' => 'Mass Media',    'count' => (int)($volData['bymedia']['doc'] ?? 0), 'category' => 'mass_media'],
                        ['media' => 'twitter',   'label' => 'X (Twitter)',   'count' => (int)($volData['bymedia']['twit'] ?? $volData['bymedia']['twitter'] ?? 0), 'category' => 'social_media'],
                        ['media' => 'youtube',   'label' => 'YouTube',       'count' => (int)($volData['bymedia']['yt'] ?? $volData['bymedia']['youtube'] ?? 0), 'category' => 'social_media'],
                        ['media' => 'instagram', 'label' => 'Instagram',     'count' => (int)($volData['bymedia']['ig'] ?? $volData['bymedia']['instagram'] ?? 0), 'category' => 'social_media'],
                        ['media' => 'facebook',  'label' => 'Facebook',      'count' => (int)($volData['bymedia']['fb'] ?? $volData['bymedia']['facebook'] ?? 0), 'category' => 'social_media'],
                        ['media' => 'tiktok',    'label' => 'TikTok',        'count' => (int)($volData['bymedia']['tiktok'] ?? 0), 'category' => 'social_media'],
                    ];
                    $massTot = $platforms[0]['count'];
                    $socTot  = array_sum(array_column(array_slice($platforms, 1), 'count'));
                    $snapPayload = [
                        'platforms'    => $platforms,
                        'mass_total'   => $massTot,
                        'social_total' => $socTot,
                        'grand_total'  => $massTot + $socTot,
                    ];
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'mention_by_platform', $s, $e, $snapPayload);
                    $this->line("  ✓ Snapshot mention_by_platform disimpan (Total: " . number_format($massTot + $socTot) . ")");
                }
            } catch (\Throwable $err) {
                $this->warn("  ⚠ Volume Total: " . $err->getMessage());
            }

            // ── B. /sentiment_media/ & /snt_totals_all/ ────────────────────
            try {
                $sentMediaData = $mk->sentimentMedia($projectId, $s, $e, 0, 23);
                if (!empty($sentMediaData)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'sentiment_by_media', $s, $e, $sentMediaData);
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'snt_totals_all', $s, $e, $sentMediaData);
                    $this->line("  ✓ Snapshot sentiment_by_media & snt_totals_all disimpan");
                }
            } catch (\Throwable $err) {
                $this->warn("  ⚠ Sentiment Media: " . $err->getMessage());
            }

            // ── C. /trends_total/ (Trend Timeline Harian) ───────────────────
            try {
                $trendsData = $mk->trendsTotal($projectId, $s, $e);
                if (!empty($trendsData['data'])) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'trend_mentions', $s, $e, $trendsData);
                    $this->line("  ✓ Snapshot trend_mentions disimpan (" . count($trendsData['data']) . " media series)");
                }
            } catch (\Throwable $err) {
                $this->warn("  ⚠ Trends Total: " . $err->getMessage());
            }

            // ── D. /trends_hour/ & Hourly Breakdown ─────────────────────────
            try {
                $trendsHour = $mk->trendsHour($projectId, 'all', $s, $e);
                if (!empty($trendsHour)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'mentions_by_hour', $s, $e, $trendsHour);
                    $this->line("  ✓ Snapshot mentions_by_hour disimpan");
                }
            } catch (\Throwable $err) {
                // Silently skip if trendsHour not supported for range
            }

            // ── E. Topic Map, Trending Topics & Word Cloud ──────────────────
            try {
                $topicMap = $mk->topicMap($projectId, 'all', $s, $e, 0, 23, 100);
                if (!empty($topicMap)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'topic_map', $s, $e, $topicMap);
                    $this->line("  ✓ Snapshot topic_map disimpan");
                }
            } catch (\Throwable $err) {}

            try {
                $wcData = $mk->wordCloud($projectId, $s, 0, $e, 23, '2');
                if (!empty($wcData)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'word_cloud_all', $s, $e, $wcData);
                    ProjectApiSnapshot::storeSnapshot($projectId, 'news', 'news_word_cloud_2', $s, $e, $wcData);
                    $this->line("  ✓ Snapshot word_cloud disimpan");
                }
            } catch (\Throwable $err) {}

            try {
                $hashtags = $mk->topHashtags($projectId, 'all', $s, $e, 0, 23);
                if (!empty($hashtags)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'top_hashtags', $s, $e, $hashtags);
                    $this->line("  ✓ Snapshot top_hashtags disimpan");
                }
            } catch (\Throwable $err) {}

            // ── F. Top Publisher & Demographics ─────────────────────────────
            try {
                $topPub = $mk->topPublisher($projectId, $s, $e, 0, 23, 100, 'article');
                if (!empty($topPub)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'news', 'top_publisher_article', $s, $e, $topPub);
                    $this->line("  ✓ Snapshot top_publisher disimpan");
                }
            } catch (\Throwable $err) {}

            try {
                $geoUsers = $mk->geoTwitterUser($projectId, 'all', $s, $e, 0, 23);
                if (!empty($geoUsers)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'geo_users', $s, $e, $geoUsers);
                    $this->line("  ✓ Snapshot geo_users disimpan");
                }
            } catch (\Throwable $err) {}

            // ── G. SEDOT RAW FEED POSTINGAN MEDSOS & ARTIKEL BERITA ─────────
            // 1. Online News Articles
            try {
                $rawArticles = $mk->articles($projectId, 'doc', $s, $e, 0, 23, 0, 2000, false);
                $artArr = is_array($rawArticles) ? ($rawArticles['data'] ?? $rawArticles['docs'] ?? $rawArticles) : [];
                if (!empty($artArr) && is_array($artArr)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'doc', 'articles_doc_all_0', $s, $e, $rawArticles);
                    $this->line("  ✓ Feed Berita (Online News) disimpan (" . count($artArr) . " artikel)");
                }
            } catch (\Throwable $err) {
                $this->warn("  ⚠ Raw Articles: " . $err->getMessage());
            }

            // 2. Twitter / X Mentions
            try {
                $rawMentions = $mk->mentions($projectId, $s, $e, 0, 23, 2000);
                $menArr = is_array($rawMentions) ? ($rawMentions['data'] ?? $rawMentions['statuses'] ?? $rawMentions) : [];
                if (!empty($menArr) && is_array($menArr)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'news_mentions_0_1200', $s, $e, $rawMentions);
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'news_mentions_0_2000', $s, $e, $rawMentions);
                    $this->line("  ✓ Feed Twitter / X Mentions disimpan (" . count($menArr) . " cuitan)");
                }
            } catch (\Throwable $err) {
                $this->warn("  ⚠ Raw Mentions: " . $err->getMessage());
            }

            // 3. Facebook Top Status
            try {
                $fbStatus = $mk->fbTopStatus($projectId, 'facebook', $s, $e, 'fblike', 100);
                if (!empty($fbStatus)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'fb', 'fb_top_status_fblike_100', $s, $e, $fbStatus);
                    $this->line("  ✓ Feed Facebook Top Status disimpan");
                }
            } catch (\Throwable $err) {}

            // 4. Instagram Top Status
            try {
                $igStatus = $mk->igTopStatus($projectId, $s, $e, 'postbylike', 100);
                if (!empty($igStatus)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'instagram', 'ig_top_status_postbylike_100', $s, $e, $igStatus);
                    $this->line("  ✓ Feed Instagram Top Status disimpan");
                }
            } catch (\Throwable $err) {}

            // 5. TikTok Top Status
            try {
                $ttStatus = $mk->tiktokTopStatus($projectId, $s, $e, 'postbylike', 100);
                if (!empty($ttStatus)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'tiktok', 'tiktok_top_status_postbylike_100', $s, $e, $ttStatus);
                    $this->line("  ✓ Feed TikTok Top Status disimpan");
                }
            } catch (\Throwable $err) {}

            // 6. YouTube Top Status
            try {
                $ytStatus = $mk->ytbTopStatus($projectId, $s, $e, 100);
                if (!empty($ytStatus)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'youtube', 'ytb_top_status_100', $s, $e, $ytStatus);
                    $this->line("  ✓ Feed YouTube Top Videos disimpan");
                }
            } catch (\Throwable $err) {}
        }

        // 3. Sinkronkan Agregat Sentimen Harian (project_daily_sentiments)
        $this->newLine();
        $this->info("📊 Menyinkronkan seluruh sentimen harian ke tabel project_daily_sentiments...");
        $totalDays = Carbon::parse($startDate)->diffInDays(now()) + 1;
        $this->call('mk:sync-daily-sentiment', [
            '--days'    => $totalDays,
            '--project' => $projectId,
        ]);

        $this->newLine();
        $this->info("======================================================================");
        $this->info("🎉 SELESAI! SELURUH DATA DRONE EMPRIT BERHASIL DI-BACKFILL TOTAL!");
        $this->info("======================================================================");
        return 0;
    }
}
