<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProjectDailySentiment;
use App\Models\ProjectApiSnapshot;

class RollbackSeptemberSeeder extends Seeder
{
    /**
     * Rollback September 2026 reconciliation data for project 16978.
     * Restores database back to original state (Sept 1-26 only, 155.3k mentions).
     */
    public function run(): void
    {
        $projectId = 16978;

        // 1. Hapus data daily sentiments tanggal 27-30 September
        $deletedSentiments = ProjectDailySentiment::where('project_id', $projectId)
            ->where('date', '>=', '2026-09-27')
            ->delete();

        // 2. Hapus snapshot cache yang berakhiran tanggal 2026-09-30
        $deletedSnapshots = ProjectApiSnapshot::where('project_id', $projectId)
            ->where('end_date', '2026-09-30')
            ->delete();

        $this->command->info("Rollback sukses! Dihapus {$deletedSentiments} baris daily sentiments dan {$deletedSnapshots} baris snapshot.");
    }
}
