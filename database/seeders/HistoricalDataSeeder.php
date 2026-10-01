<?php

namespace Database\Seeders;

use App\Models\ProjectApiSnapshot;
use App\Models\ProjectDailySentiment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HistoricalDataSeeder extends Seeder
{
    /**
     * Run the database seeds to populate all historical snapshots and daily sentiments.
     */
    public function run(): void
    {
        $archiveFile = database_path('seeders/data/historical_snapshots.json.gz');

        if (!file_exists($archiveFile)) {
            $this->command->error("❌ Archive file not found: {$archiveFile}");
            return;
        }

        $this->command->info("📦 Extracting historical snapshot archive...");
        $gzContent = file_get_contents($archiveFile);
        $json = gzdecode($gzContent);

        if (!$json) {
            $this->command->error("❌ Failed to decompress archive file.");
            return;
        }

        $data = json_decode($json, true);
        if (!is_array($data)) {
            $this->command->error("❌ Invalid JSON format in archive.");
            return;
        }

        // 1. Seed Project Daily Sentiments (Juni - September 2026)
        $sentiments = $data['sentiments'] ?? [];
        if (!empty($sentiments)) {
            $this->command->info("📊 Seeding " . count($sentiments) . " daily sentiments...");
            
            foreach (array_chunk($sentiments, 100) as $chunk) {
                ProjectDailySentiment::upsert(
                    $chunk,
                    ['project_id', 'date'],
                    ['positive', 'neutral', 'negative', 'total', 'updated_at']
                );
            }
            $this->command->info("✅ Daily sentiments seeded successfully!");
        }

        // 2. Seed Project API Snapshots
        $snapshots = $data['snapshots'] ?? [];
        if (!empty($snapshots)) {
            $this->command->info("📸 Seeding " . count($snapshots) . " API snapshots...");
            
            $now = now();
            foreach ($snapshots as $snap) {
                ProjectApiSnapshot::updateOrCreate(
                    [
                        'project_id'   => $snap['project_id'],
                        'media'        => strtolower($snap['media']),
                        'endpoint_key' => $snap['endpoint_key'],
                        'start_date'   => $snap['start_date'],
                        'end_date'     => $snap['end_date'],
                    ],
                    [
                        'payload'   => is_string($snap['payload']) ? $snap['payload'] : json_encode($snap['payload']),
                        'synced_at' => $snap['synced_at'] ?? $now,
                    ]
                );
            }
            $this->command->info("✅ API snapshots seeded successfully!");
        }

        $this->command->info("🎉 All historical data (June - September) successfully populated!");
    }
}
