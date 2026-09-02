<?php

namespace App\Console\Commands;

use App\Models\ProjectDailySentiment;
use App\Services\MediaKernelsClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SyncDailySentimentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mk:sync-daily-sentiment 
                            {--days=90 : Number of days in the past to sync (default: 90 days / 3 months)} 
                            {--project= : Specific project ID to sync}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync daily sentiment snapshot data from MediaKernels into local database';

    /**
     * Execute the console command.
     */
    public function handle(MediaKernelsClient $mk)
    {
        $daysCount   = (int) $this->option('days') ?: 90;
        $targetProj  = $this->option('project');

        $this->info("🚀 Starting Daily Sentiment Sync (last {$daysCount} days / " . round($daysCount / 30, 1) . " months)...");

        // 1. Get projects
        $projectIds = [];
        if ($targetProj) {
            $projectIds[] = (int) $targetProj;
        } else {
            try {
                $raw = $mk->listProjects(0, 100);
                $projectIds = collect($raw)->pluck('id')->filter()->unique()->values()->all();
            } catch (\Throwable $e) {
                $this->error("Failed to list projects: " . $e->getMessage());
                return 1;
            }
        }

        if (empty($projectIds)) {
            $this->warn("No projects found to sync.");
            return 0;
        }

        $this->info("Found " . count($projectIds) . " projects to process.");

        // 2. Prepare dates
        $dates = [];
        for ($i = $daysCount - 1; $i >= 0; $i--) {
            $dates[] = now()->subDays($i)->format('Y-m-d');
        }

        $token   = $mk->getToken();
        $baseUrl = rtrim(config('services.mediakernels.base_url'), '/');

        // Chunk dates in batches of 15 days for optimal parallel HTTP pooling
        $dateChunks = array_chunk($dates, 15);

        foreach ($projectIds as $pId) {
            $this->line("Processing Project #{$pId} ({$daysCount} days in " . count($dateChunks) . " batches)...");

            foreach ($dateChunks as $cIdx => $chunk) {
                $urls = [];
                foreach ($chunk as $d) {
                    $urls[$d] = $baseUrl . '/sentiment_total/?' . http_build_query([
                        'project_id' => $pId,
                        'start_date' => $d,
                        'start_time' => 0,
                        'end_date'   => $d,
                        'end_time'   => 23,
                        'token'      => $token,
                    ]);
                }

                // Pool HTTP requests in batch of 15
                $responses = Http::pool(function ($pool) use ($urls) {
                    foreach ($urls as $d => $url) {
                        $pool->as($d)->timeout(30)->acceptJson()->get($url);
                    }
                });

                $records = [];
                foreach ($chunk as $d) {
                    $res = $responses[$d] ?? null;
                    $positive = 0;
                    $neutral  = 0;
                    $negative = 0;

                    if ($res instanceof \Illuminate\Http\Client\Response && $res->successful()) {
                        $json = $res->json();
                        $src  = $json['data'] ?? $json;
                        $positive = (int) ($src['positive'] ?? $src['pos'] ?? $src['1'] ?? 0);
                        $neutral  = (int) ($src['neutral']  ?? $src['neu'] ?? $src['net'] ?? $src['0'] ?? 0);
                        $negative = (int) ($src['negative'] ?? $src['neg'] ?? $src['-1'] ?? 0);
                    }

                    $records[] = [
                        'project_id' => $pId,
                        'date'       => $d,
                        'positive'   => $positive,
                        'neutral'    => $neutral,
                        'negative'   => $negative,
                        'total'      => $positive + $neutral + $negative,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                if (!empty($records)) {
                    ProjectDailySentiment::upsert(
                        $records,
                        ['project_id', 'date'],
                        ['positive', 'neutral', 'negative', 'total', 'updated_at']
                    );
                }

                $this->line("  - Batch " . ($cIdx + 1) . "/" . count($dateChunks) . " synced (" . count($records) . " days).");
            }

            $this->info("✅ Project #{$pId} fully synced ({$daysCount} days).");
        }

        $this->info("🎉 Daily Sentiment Sync completed successfully!");
        return 0;
    }
}
