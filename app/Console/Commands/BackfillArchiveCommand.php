<?php

namespace App\Console\Commands;

use App\Models\ProjectDailySentiment;
use App\Models\ProjectApiSnapshot;
use App\Services\MediaKernelsClient;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Console\Command;
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
                            {--with-articles : Also crawl raw news articles and social media mentions}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deep crawler to backfill historical Drone Emprit (MediaKernels) data into local snapshots and daily sentiments';

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

        $this->info("==========================================================");
        $this->info("🚀 Drone Emprit Archive Deep Backfill");
        $this->info("Project ID : {$projectId}");
        $this->info("Date Range : {$startDate} s/d {$endDate}");
        $this->info("==========================================================");

        // 1. Validasi Token / Kredensial
        $this->line("Memeriksa kredensial Drone Emprit API...");
        try {
            $token = $mk->getToken();
            $this->info("✅ Autentikasi berhasil. Token aktif.");
        } catch (\Throwable $e) {
            $this->error("❌ Gagal autentikasi API: " . $e->getMessage());
            $this->warn("Pastikan MEDIAKERNELS_USERNAME dan MEDIAKERNELS_PASSWORD di .env sudah aktif dengan subscription valid.");
            return 1;
        }

        // 2. Loop Bulan per Bulan untuk Snapshot Agregat
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

        $this->info("Ditemukan " . count($periods) . " bulan untuk di-backfill...");
        $bar = $this->output->createProgressBar(count($periods));
        $bar->start();

        foreach ($periods as $p) {
            $s = $p['start'];
            $e = $p['end'];

            try {
                // A. /volume_total/ (Rincian per platform: doc, twitter, fb, ig, yt, tiktok)
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
                }

                // B. /sentiment_media/ (Sentimen per masing-masing platform)
                $sentMediaData = $mk->sentimentMedia($projectId, $s, $e, 0, 23);
                if (!empty($sentMediaData)) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'sentiment_by_media', $s, $e, $sentMediaData);
                }

                // C. /trends_total/ (Grafik tren timeline harian)
                $trendsData = $mk->trendsTotal($projectId, $s, $e);
                if (!empty($trendsData['data'])) {
                    ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'trend_mentions', $s, $e, $trendsData);
                }

                // D. /articles/ dan /mentions/ (Feed data mentah artikel & cuitan) jika opsi aktif
                if ($this->option('with-articles')) {
                    $rawArticles = $mk->articles($projectId, 'doc', $s, $e, 0, 23, 500);
                    if (!empty($rawArticles)) {
                        ProjectApiSnapshot::storeSnapshot($projectId, 'doc', 'articles_doc_all_0', $s, $e, $rawArticles);
                    }

                    $rawMentions = $mk->mentions($projectId, $s, $e, 0, 23, 1200);
                    if (!empty($rawMentions)) {
                        ProjectApiSnapshot::storeSnapshot($projectId, 'all', 'news_mentions_0_1200', $s, $e, $rawMentions);
                    }
                }

            } catch (\Throwable $err) {
                Log::warning("Backfill error on period {$s} s/d {$e}: " . $err->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // 3. Sinkronkan Sentimen Harian (project_daily_sentiments)
        $this->line("Menjalankan sinkronisasi sentimen harian ke database lokal...");
        $this->call('mk:sync-daily-sentiment', [
            '--days'    => Carbon::parse($startDate)->diffInDays(now()) + 1,
            '--project' => $projectId,
        ]);

        $this->info("🎉 Backfill arsip Drone Emprit selesai sukses!");
        return 0;
    }
}
